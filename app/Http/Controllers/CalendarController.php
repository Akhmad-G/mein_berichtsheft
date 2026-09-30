<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class CalendarController extends Controller {
  public function __construct(private ReportRepository $reports) {}

  public function index(Request $request) {
    $azubi = $request->user();
    abort_unless($azubi->isAzubi(), 403); // Ausbilder has no calendar

    $selected = rescue(
      fn() => CarbonImmutable::createFromFormat('!Y-m-d', (string) $request->query('day')),
      null,
      report: false,
    ) ?: CarbonImmutable::today();

    $month = $selected->startOfMonth();
    $gridStart = $month->startOfWeek(CarbonImmutable::MONDAY);
    $gridEnd = $month->endOfMonth()->endOfWeek(CarbonImmutable::SUNDAY)->startOfDay();

    $reports = $this->reports->days($azubi, $gridStart, $gridEnd);

    $cells = collect(CarbonPeriod::create($gridStart, $gridEnd))->map(fn($d) => [
      'date' => CarbonImmutable::instance($d),
      'inMonth' => $d->isSameMonth($month),
      'state' => $d->isWeekend() ? 'free' : $reports[$d->toDateString()]->calendarState(),
    ]);

    $day = $reports[$selected->toDateString()] ?? $this->reports->day($azubi, $selected);
    $week = $this->reports->week($azubi, $selected->isoWeekYear(), $selected->isoWeek());

    $typeCount = $day->type->isWork() ? 0 : $reports
      ->filter(fn($r) => $r->date->isSameMonth($month) && $r->type === $day->type)
      ->count();

    return view('calendar.index', [
      'month' => $month,
      'cells' => $cells,          // [['date', 'inMonth', 'state'], …] Mon-first grid
      'day' => $day,            // App\Data\DayReport
      'week' => $week,           // App\Data\WeeklyReport with days
      'typeCount' => $typeCount,
      'canEdit' => $week->isEditable(),
      'learningSteps' => config('reports.learning_steps', []),
      'today' => CarbonImmutable::today(),
    ]);
  }
}
