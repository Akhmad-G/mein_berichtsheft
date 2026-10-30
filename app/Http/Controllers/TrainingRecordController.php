<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for generating the full printable training record (Ausbildungsnachweis).
 */
class TrainingRecordController extends Controller
{
    /**
     * @param ReportRepository $reports
     */
    public function __construct(private ReportRepository $reports) {}

    /**
     * Render the complete printable record with cover page and all signed weekly reports.
     *
     * @param User $user The apprentice whose training record is to be printed.
     * @return View
     */
    public function print(User $user): View
    {
        Gate::authorize('view-reports', $user);

        $weeks = $this->reports->allWeeks($user)
            ->filter(fn ($week) => $week->isSigned())
            ->sortBy([['year', 'asc'], ['week', 'asc']])
            ->map(fn ($week) => $this->reports->week($user, $week->year, $week->week))
            ->values();

        return view('training-record.print', ['azubi' => $user, 'weeks' => $weeks]);
    }
}
