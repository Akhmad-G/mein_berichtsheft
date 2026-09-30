<?php

namespace App\View\Composers;

use App\Enums\WeekStatus;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

/**
 * $tabs for <x-notebook.tabs>. The role decides the tabs:
 * Azubi — Kalender + Wochenberichte, Ausbilder — Wochenberichte + Meine Azubis.
 */
class TabsComposer
{
    public function __construct(private ReportRepository $reports) {}

    public function compose(View $view): void
    {
        $user = auth()->user();
        $year = now()->isoWeekYear();

        if ($user->isAusbilder()) {
            $azubis  = $user->azubis()->whereNotNull('gitlab_path')->get();
            $pending = $azubis->sum(fn ($a) => $this->reports->weeks($a, $year)->where('status', WeekStatus::Submitted)->count());

            $view->with('tabs', [
                [
                  'key' => 'weekly-reports',
                  'label' => 'Wochenberichte',
                  'href' => route('weekly-reports.index'),
                  'badge' => $pending ? "{$pending} offen" : null
                ],
                [
                  'key' => 'azubis',
                  'label' => 'Meine Azubis',
                  'href' => route('azubis.index'),
                  'badge' => $azubis->count() ?: null
                ],
            ]);

            return;
        }

        $weeks   = $user->gitlab_path ? $this->reports->weeks($user, $year) : collect();
        $pending = $weeks->where('status', WeekStatus::Submitted)->count();

        // open days of the current week up to today
        $today   = CarbonImmutable::today();
        $current = $weeks->firstWhere('week', $today->isoWeek());
        $elapsed = min(5, $today->isWeekend() ? 5 : $today->dayOfWeekIso);
        $open    = max(0, $elapsed - ($current?->recordedDays ?? 0));

        $view->with('tabs', [
            ['key' => 'calendar',       'label' => 'Kalender',       'href' => route('calendar'),             'badge' => $open ? "{$open} offen" : null],
            ['key' => 'weekly-reports', 'label' => 'Wochenberichte', 'href' => route('weekly-reports.index'), 'badge' => $pending ? "{$pending} wartet" : null],
        ]);
    }
}
