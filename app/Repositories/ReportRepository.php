<?php

namespace App\Repositories;

use App\Contracts\GitLabServiceInterface;
use App\Data\DayReport;
use App\Data\WeeklyReport;
use App\Models\User;
use App\Support\ReportPath;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Repository for reading and persisting daily and weekly training reports in GitLab.
 *
 * Designed to be registered as scoped (one instance per request lifecycle)
 * to benefit from memoized repository tree listings.
 */
class ReportRepository
{
    /**
     * Memoized file trees indexed by year folder path.
     *
     * @var array<string, array<string, string>> year folder path => [file_path => blob_sha]
     */
    private array $trees = [];

    /**
     * @param GitLabServiceInterface $gitlab
     */
    public function __construct(private GitLabServiceInterface $gitlab) {}

    /**
     * Retrieve a daily report for an apprentice on a specific date.
     *
     * @param User $azubi
     * @param CarbonInterface $date
     * @return DayReport
     */
    public function day(User $azubi, CarbonInterface $date): DayReport
    {
        $date = CarbonImmutable::instance($date)->startOfDay();
        $data = $this->read($azubi, $date->isoWeekYear(), ReportPath::dayFile($azubi, $date));

        return $data ? DayReport::fromArray($data, $date) : new DayReport($date);
    }

    /**
     * Retrieve all weekday (Mon–Fri) reports between two dates for an apprentice.
     *
     * @param User $azubi
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @return Collection<string, DayReport> Keyed by 'YYYY-MM-DD'
     */
    public function days(User $azubi, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return collect(CarbonPeriod::create($from, $to))
            ->filter(fn ($d) => $d->isWeekday())
            ->mapWithKeys(fn ($d) => [$d->toDateString() => $this->day($azubi, $d)]);
    }

    /**
     * Retrieve a weekly report for an apprentice by year and week number.
     *
     * @param User $azubi
     * @param int $year
     * @param int $week
     * @param bool $withDays Whether to load full day reports or just count files.
     * @return WeeklyReport
     */
    public function week(User $azubi, int $year, int $week, bool $withDays = true): WeeklyReport
    {
        $report = WeeklyReport::fromArray($azubi, $year, $week, $this->read($azubi, $year, ReportPath::weekFile($azubi, $year, $week)));

        if ($withDays) {
            $report->days = $this->days($azubi, $report->start(), $report->end());
            $report->recordedDays = $report->days->where('exists', true)->count();
        } else {
            $folder = ReportPath::weekFolder($azubi, $year, $week) . '/';
            $report->recordedDays = collect($this->files($azubi, $year))->keys()
                ->filter(fn ($p) => str_starts_with($p, $folder) && ! str_ends_with($p, ReportPath::WEEK_FILE))
                ->count();
        }

        return $report;
    }

    /**
     * Retrieve all weekly reports for a specific year, sorted newest week first.
     *
     * @param User $azubi
     * @param int $year
     * @return Collection<int, WeeklyReport>
     */
    public function weeks(User $azubi, int $year): Collection
    {
        return collect($this->files($azubi, $year))->keys()
            ->map(fn ($path) => ReportPath::parse($path))
            ->filter()
            ->groupBy('week')
            ->map(function (Collection $files, int $week) use ($azubi, $year) {
                $weekFile = $files->firstWhere('kind', 'week');
                $report = WeeklyReport::fromArray($azubi, $year, $week, $weekFile ? $this->read($azubi, $year, $weekFile['path']) : null);
                $report->recordedDays = $files->where('kind', 'day')->count();

                return $report;
            })
            ->sortByDesc('week')
            ->values();
    }

    /**
     * Retrieve all weekly reports since the beginning of training up to current year.
     *
     * @param User $azubi
     * @return Collection<int, WeeklyReport>
     */
    public function allWeeks(User $azubi): Collection
    {
        $to   = now()->isoWeekYear();
        $from = min($to, $azubi->ausbildungsbeginn?->isoWeekYear() ?? $to);

        return collect(range($to, $from))->flatMap(fn (int $year) => $this->weeks($azubi, $year))->values();
    }

    /**
     * Atomically save a collection of day reports in a single commit.
     * Empty work days are automatically removed from storage.
     *
     * @param User $azubi
     * @param Collection<int|string, DayReport> $days
     * @return void
     */
    public function saveDays(User $azubi, Collection $days): void
    {
        $actions = $days->map(function (DayReport $day) use ($azubi) {
            $path   = ReportPath::dayFile($azubi, $day->date);
            $exists = isset($this->files($azubi, $day->date->isoWeekYear())[$path]);

            if ($day->isEmpty()) {
                return $exists ? ['action' => 'delete', 'file_path' => $path] : null;
            }

            return ['action' => $exists ? 'update' : 'create', 'file_path' => $path, 'content' => $this->json($day->toArray())];
        })->filter()->values()->all();

        $range = $days->first()->date->toDateString() . ($days->count() > 1 ? '..' . $days->last()->date->toDateString() : '');
        $this->gitlab->commit("Day reports {$range} for {$azubi->gitlab_path}", $actions);

        $days->map(fn (DayReport $d) => $d->date->isoWeekYear())->unique()->each(fn ($y) => $this->forget($azubi, $y));
    }

    /**
     * Save/update a weekly report status summary file in GitLab.
     *
     * @param WeeklyReport $week
     * @param string $action Action label for commit message (e.g. 'submit', 'sign').
     * @return void
     */
    public function saveWeek(WeeklyReport $week, string $action): void
    {
        $path   = ReportPath::weekFile($week->azubi, $week->year, $week->week);
        $exists = isset($this->files($week->azubi, $week->year)[$path]);

        $this->gitlab->commit(
            sprintf('%s KW %02d/%d for %s', ucfirst($action), $week->week, $week->year, $week->azubi->gitlab_path),
            [['action' => $exists ? 'update' : 'create', 'file_path' => $path, 'content' => $this->json($week->toArray())]],
        );

        $this->forget($week->azubi, $week->year);
    }

    // ── internal ────────────────────────────────────────────────

    /**
     * Fetch and cache mapping of file paths to blob SHAs for a given year.
     *
     * @param User $azubi
     * @param int $year
     * @return array<string, string> path => sha of all files in a year folder
     */
    private function files(User $azubi, int $year): array
    {
        $folder = ReportPath::yearFolder($azubi, $year);

        return $this->trees[$folder] ??= collect($this->gitlab->listTree($folder, recursive: true))
            ->where('type', 'blob')
            ->pluck('id', 'path')
            ->all();
    }

    /**
     * Read JSON content of a file by path using the memoized tree sha.
     *
     * @param User $azubi
     * @param int $year
     * @param string $path
     * @return array<string, mixed>|null
     */
    private function read(User $azubi, int $year, string $path): ?array
    {
        $sha = $this->files($azubi, $year)[$path] ?? null;

        return $sha ? $this->gitlab->readBlob($sha) : null;
    }

    /**
     * Invalidate memoized tree cache for a year folder.
     *
     * @param User $azubi
     * @param int $year
     * @return void
     */
    private function forget(User $azubi, int $year): void
    {
        unset($this->trees[ReportPath::yearFolder($azubi, $year)]);
    }

    /**
     * Encode data array into formatted JSON string with trailing newline.
     *
     * @param array<string, mixed> $data
     * @return string
     */
    private function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    }
}
