<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Support\Facades\Gate;

/** Ausbildungsnachweis: cover + all signed weeks, printable (PDF via browser print dialog). */
class TrainingRecordController extends Controller
{
    public function __construct(private ReportRepository $reports) {}

    public function print(User $user)
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
