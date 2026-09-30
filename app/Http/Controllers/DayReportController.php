<?php

namespace App\Http\Controllers;

use App\Data\DayReport;
use App\Enums\DayType;
use App\Http\Requests\DayReportRequest;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

class DayReportController extends Controller
{
    public function __construct(private ReportRepository $reports) {}

    public function update(DayReportRequest $request, string $date)
    {
        $azubi = $request->user();
        $date  = CarbonImmutable::createFromFormat('!Y-m-d', $date);
        $type  = DayType::from($request->validated('type'));

        // "Speichern" → this day; "Für Zeitraum eintragen" → all weekdays from–to
        $dates = ! $type->isWork() && $request->boolean('range')
            ? collect(CarbonPeriod::create($request->validated('from'), $request->validated('to')))
                ->filter(fn ($d) => $d->isWeekday())
                ->map(fn ($d) => CarbonImmutable::instance($d))
                ->values()
            : collect([$date]);

        $locked = $dates
            ->map(fn ($d) => [$d->isoWeekYear(), $d->isoWeek()])
            ->unique(fn ($yw) => implode('-', $yw))
            ->map(fn ($yw) => $this->reports->week($azubi, $yw[0], $yw[1], withDays: false))
            ->reject(fn ($week) => $week->isEditable());

        if ($locked->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'type' => 'KW ' . $locked->pluck('week')->join(', ') . ' ist bereits eingereicht und kann nicht mehr geändert werden.',
            ]);
        }

        $days = $dates->map(fn (CarbonImmutable $d) => $type->isWork()
            ? new DayReport(
                date: $d,
                type: $type,
                activities: $request->validated('activities'),
                duration: $request->validated('duration'),
                department: $request->validated('department'),
                learningSteps: $request->validated('learning_steps') ?? [],
            )
            : new DayReport(date: $d, type: $type, note: $request->validated('note')));

        $this->reports->saveDays($azubi, $days);

        $status = $type->isWork()
            ? 'Gespeichert.'
            : $type->label() . ' eingetragen' . ($days->count() > 1 ? " ({$days->count()} Tage)." : '.');

        return redirect()->route('calendar', ['day' => $date->toDateString()])->with('status', $status);
    }
}
