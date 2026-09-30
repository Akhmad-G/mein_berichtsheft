<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller {
  public function index(Request $request, GitLabServiceInterface $gitLabService) {
    $user = $request->user();

    if (!$user->isAzubi()) {
      abort(403, 'Nur Azubis dürfen den Kalender sehen.');
    }

    $month = Carbon::parse($request->query('month', today()->format('Y-m')))->startOfMonth();
    $selectedDate = Carbon::parse($request->query('date', today()->toDateString()));
    $selectedFilename = $selectedDate->toDateString() . ' Tagesbericht.json';

    $reports = collect($gitLabService->listReports($user))
      ->where('type', 'tagesbericht');

    $reportByName = $reports->keyBy('name');
    $selectedReport = $reportByName->get($selectedFilename);

    $data = [];

    if ($selectedReport) {
      $data = $gitLabService->getReport($user, $selectedReport['path']);
    }

    $tabs = [
      [
        'key' => 'kalender',
        'label' => 'Kalender',
        'href' => route('kalender'),
        'badge' => null,
      ],
      [
        'key' => 'wochen',
        'label' => 'Wochenberichte',
        'href' => route('wochenberichte.index'),
        'badge' => null,
      ],
    ];

    $start = $month->copy()->startOfMonth()->startOfWeek();
    $days = collect(range(0, 34))->map(function (int $offset) use ($start, $month, $reportByName) {
      $date = $start->copy()->addDays($offset);
      $filename = $date->toDateString() . ' Tagesbericht.json';

      return [
        'date' => $date,
        'art' => $reportByName->has($filename) ? 'bericht' : ($date->isWeekend() ? 'frei' : 'offen'),
        'imMonat' => $date->isSameMonth($month),
      ];
    });

    $weekStart = $selectedDate->copy()->startOfWeek();
    $weekdays = collect(range(0, 4))->map(function (int $offset) use ($reportByName, $weekStart) {
      $date = $weekStart->copy()->addDays($offset);
      $filename = $date->toDateString() . ' Tagesbericht.json';

      return [
        'date' => $date,
        'art' => $reportByName->has($filename) ? 'bericht' : 'offen',
      ];
    });

    $day = (object) [
      'date' => $selectedDate,
      'taetigkeiten' => $data['taetigkeiten'] ?? '',
      'dauer' => $data['dauer'] ?? '',
      'abteilung' => $data['abteilung'] ?? '',
      'statusStempel' => $selectedReport ? 'bericht' : 'offen',
      'statusText' => $selectedReport ? 'Erfasst' : 'Offen',
      'updated_at' => null,
      'learningSteps' => collect(),
    ];

    return view('kalender.index', [
      'tabs' => $tabs,
      'month' => $month,
      'days' => $days,
      'day' => $day,
      'weekdays' => $weekdays,
      'recordedWeekdays' => $weekdays->where('art', 'bericht')->count(),
      'learningSteps' => collect(),
      'canWrite' => true,
    ]);
  }

  public function update(Request $request, string $date, GitLabServiceInterface $gitLabService) {
    $user = $request->user();

    if (!$user->isAzubi()) {
      abort(403, 'Nur Azubis dürfen Tagesberichte speichern.');
    }

    $data = $request->validate([
      'taetigkeiten' => ['required', 'string', 'max:600'],
      'dauer' => ['nullable', 'string', 'max:255'],
      'abteilung' => ['nullable', 'string', 'max:255'],
    ]);

    $date = Carbon::parse($date);
    $filename = $date->toDateString() . ' Tagesbericht.json';

    $payload = [
      'datum' => $date->toDateString(),
      'wochentag' => $date->translatedFormat('l'),
      'taetigkeiten' => $data['taetigkeiten'],
      'dauer' => $data['dauer'] ?? null,
      'abteilung' => $data['abteilung'] ?? null,
    ];

    $existingReport = collect($gitLabService->listReports($user))
      ->firstWhere('name', $filename);

    $gitLabService->saveReport(
      $user,
      $filename,
      $payload,
      $existingReport ? 'update' : 'create',
    );

    return redirect()->route('tagesbericht', [
      'date' => $date->toDateString(),
    ]);
  }
}
