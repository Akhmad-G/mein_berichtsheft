<?php

namespace App\Http\Controllers;

use App\Enums\WeekStatus;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for managing and reviewing apprentice overviews (Ausbilder only).
 */
class AzubiController extends Controller
{
    /**
     * @param ReportRepository $reports
     */
    public function __construct(private ReportRepository $reports) {}

    /**
     * Display the apprentice overview and review dashboard.
     *
     * @param Request $request
     * @param User|null $user Selected apprentice to display details for.
     * @return View
     */
    public function index(Request $request, ?User $user = null): View
    {
        $viewer = $request->user();
        abort_unless($viewer->isAusbilder(), 403);

        if ($user) {
            Gate::authorize('sign-reports', $user);
        }

        $year = now()->isoWeekYear();

        $rows = $viewer->azubis()->whereNotNull('gitlab_path')->orderBy('nachname')->get()
            ->map(function (User $azubi) use ($year) {
                $weeks = $this->reports->weeks($azubi, $year);

                return (object) [
                    'user'         => $azubi,
                    'weeks'        => $weeks,
                    'pending'      => $weeks->where('status', WeekStatus::Submitted)->count(),
                    'signed'       => $weeks->where('status', WeekStatus::Signed)->count(),
                    'recordedDays' => $weeks->sum('recordedDays'),
                ];
            });

        if ($request->query('sort') === 'pending') {
            $rows = $rows->sortByDesc('pending')->values();
        }

        $selected = $user ? $rows->first(fn ($row) => $row->user->is($user)) : $rows->first();

        $recentWeeks = $selected
            ? $selected->weeks->reject(fn ($w) => $w->status === WeekStatus::Draft)->take(5)->values()
            : collect();

        return view('azubis.index', [
            'rows'        => $rows,         // [{user, weeks, pending, signed, recordedDays}, …]
            'selected'    => $selected,     // one of $rows or null
            'recentWeeks' => $recentWeeks,  // Collection<WeeklyReport>
            'year'        => $year,
        ]);
    }
}
