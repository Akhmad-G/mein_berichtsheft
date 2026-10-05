<?php

namespace App\Http\Controllers;

use App\Data\WeeklyReport;
use App\Enums\WeekStatus;
use App\Models\User;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WeeklyReportController extends Controller
{
    public function __construct(private ReportRepository $reports) {}

    /** List + selected week. Without route params: current week (Azubi) / oldest waiting (Ausbilder). */
    public function index(Request $request, ?User $user = null, ?int $year = null, ?int $week = null)
    {
        $viewer = $request->user();
        $year ??= (int) $request->query('year', now()->isoWeekYear());
        $filter = in_array($request->query('filter'), ['open', 'signed'], true) ? $request->query('filter') : 'all';

        $weeks = $this->weeksFor($viewer, $year)
            ->when($filter === 'open', fn ($c) => $c->reject(fn (WeeklyReport $w) => $w->isSigned()))
            ->when($filter === 'signed', fn ($c) => $c->filter(fn (WeeklyReport $w) => $w->isSigned()))
            ->values();

        if ($user && $week) {
            Gate::authorize('view-reports', $user);
            $selected = $this->reports->week($user, $year, $week);
        } else {
            $first = $viewer->isAusbilder()
                ? $weeks->where('status', WeekStatus::Submitted)->sortBy('week')->first()
                : $weeks->first(fn (WeeklyReport $w) => $w->week === now()->isoWeek());
            $first ??= $weeks->first();
            $selected = $first ? $this->reports->week($first->azubi, $first->year, $first->week) : null;
        }

        return view('weekly-reports.index', [
            'weeks'  => $weeks,     // Collection<WeeklyReport> (no day contents)
            'week'   => $selected,  // ?WeeklyReport with days — null → empty state
            'filter' => $filter,    // all|open|signed
            'year'   => $year,
        ]);
    }

    public function submit(Request $request, User $user, int $year, int $week)
    {
        Gate::authorize('edit-reports', $user);

        $report = $this->reports->week($user, $year, $week, withDays: false);

        if (! $report->canSubmit()) {
            return back()->withErrors(['week' => 'Die Woche ist noch nicht vollständig erfasst.']);
        }

        // take the number only after the commit succeeded
        $newNumber = $report->number === null;
        $report->number ??= $user->next_berichtsnummer;
        $report->status = WeekStatus::Submitted;
        $report->submittedAt = CarbonImmutable::now();

        $this->reports->saveWeek($report, 'submit');

        if ($newNumber) {
            $user->increment('next_berichtsnummer');
        }

        return back()->with('status', "KW {$week} ist zur Unterschrift gegeben.");
    }

    public function sign(Request $request, User $user, int $year, int $week)
    {
        Gate::authorize('sign-reports', $user);

        $report = $this->reports->week($user, $year, $week, withDays: false);

        if ($report->status !== WeekStatus::Submitted) {
            return back();
        }

        $report->status       = WeekStatus::Signed;
        $report->signedAt     = CarbonImmutable::now();
        $report->signedById   = $request->user()->id;
        $report->signedByName = $request->user()->name;

        $this->reports->saveWeek($report, 'sign');

        return back()->with('status', "KW {$week} unterschrieben.");
    }

    /** Print sheet; PDF via the browser print dialog ("Als PDF speichern"). */
    public function print(User $user, int $year, int $week)
    {
        Gate::authorize('view-reports', $user);

        return view('weekly-reports.print', ['week' => $this->reports->week($user, $year, $week)]);
    }

    // ── internal ────────────────────────────────────────────────

    private function weeksFor(User $viewer, int $year)
    {
        if ($viewer->isAusbilder()) {
            return $viewer->azubis()->whereNotNull('gitlab_path')->orderBy('nachname')->get()
                ->flatMap(fn (User $azubi) => $this->reports->weeks($azubi, $year))
                ->reject(fn (WeeklyReport $w) => $w->status === WeekStatus::Draft)
                ->sortByDesc('week');
        }

        $weeks = $this->reports->weeks($viewer, $year);

        // current week is listed even before the first file exists
        $current = now()->isoWeek();
        if ($year === now()->isoWeekYear() && ! $weeks->contains(fn (WeeklyReport $w) => $w->week === $current)) {
            $weeks->prepend(new WeeklyReport($viewer, $year, $current));
        }

        return $weeks;
    }
}
