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
 * Reads/writes day and weekly reports in GitLab.
 * Register as scoped (one instance per request) — the tree listing is memoized.
 */
class ReportRepository
{
    /** @var array<string, array<string, string>> year folder → [path => sha] */
    private array $trees = [];

    public function __construct(private GitLabServiceInterface $gitlab) {}

    public function day(User $azubi, CarbonInterface $date): DayReport
    {
        $date = CarbonImmutable::instance($date)->startOfDay();
        $data = $this->read($azubi, $date->isoWeekYear(), ReportPath::dayFile($azubi, $date));

        return $data ? DayReport::fromArray($data, $date) : new DayReport($date);
    }

    /** @return Collection<string, DayReport> Mon–Fri between $from and $to, keyed Y-m-d */
    public function days(User $azubi, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return collect(CarbonPeriod::create($from, $to))
            ->filter(fn ($d) => $d->isWeekday())
            ->mapWithKeys(fn ($d) => [$d->toDateString() => $this->day($azubi, $d)]);
    }

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

    /** @return Collection<int, WeeklyReport> weeks of a year, newest first — week.json + day count, no day contents */
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

    /** @return Collection<int, WeeklyReport> all weeks since the start of training, newest first */
    public function allWeeks(User $azubi): Collection
    {
        $to   = now()->isoWeekYear();
        $from = min($to, $azubi->ausbildungsbeginn?->isoWeekYear() ?? $to);

        return collect(range($to, $from))->flatMap(fn (int $year) => $this->weeks($azubi, $year))->values();
    }

    /** One commit for all days (a vacation range can span several weeks). Empty work days are deleted. */
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

    /** @return array<string, string> path => sha of all files in a year folder */
    private function files(User $azubi, int $year): array
    {
        $folder = ReportPath::yearFolder($azubi, $year);

        return $this->trees[$folder] ??= collect($this->gitlab->listTree($folder, recursive: true))
            ->where('type', 'blob')
            ->pluck('id', 'path')
            ->all();
    }

    private function read(User $azubi, int $year, string $path): ?array
    {
        $sha = $this->files($azubi, $year)[$path] ?? null;

        return $sha ? $this->gitlab->readBlob($sha) : null;
    }

    private function forget(User $azubi, int $year): void
    {
        unset($this->trees[ReportPath::yearFolder($azubi, $year)]);
    }

    private function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    }
}
