<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KalenderController extends Controller {
  public function index(Request $request, GitLabServiceInterface $gitLabService) {
    $user = $request->user();

    if (!$user->isAzubi()) {
      abort(403, 'Nur Azubis dürfen den Kalender sehen.');
    }

    $monat = Carbon::parse($request->query('monat', today()->format('Y-m')))->startOfMonth();
    $selectedDate = Carbon::parse($request->query('datum', today()->toDateString()));
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

    $start = $monat->copy()->startOfMonth()->startOfWeek();
    $tage = collect(range(0, 34))->map(function (int $offset) use ($start, $monat, $reportByName) {
      $datum = $start->copy()->addDays($offset);
      $filename = $datum->toDateString() . ' Tagesbericht.json';

      return [
        'datum' => $datum,
        'art' => $reportByName->has($filename) ? 'bericht' : ($datum->isWeekend() ? 'frei' : 'offen'),
        'imMonat' => $datum->isSameMonth($monat),
      ];
    });

    $weekStart = $selectedDate->copy()->startOfWeek();
    $wochentage = collect(range(0, 4))->map(function (int $offset) use ($reportByName, $weekStart) {
      $datum = $weekStart->copy()->addDays($offset);
      $filename = $datum->toDateString() . ' Tagesbericht.json';

      return [
        'datum' => $datum,
        'art' => $reportByName->has($filename) ? 'bericht' : 'offen',
      ];
    });

    $tag = (object) [
      'datum' => $selectedDate,
      'taetigkeiten' => $data['taetigkeiten'] ?? '',
      'dauer' => $data['dauer'] ?? '',
      'abteilung' => $data['abteilung'] ?? '',
      'statusStempel' => $selectedReport ? 'bericht' : 'offen',
      'statusText' => $selectedReport ? 'Erfasst' : 'Offen',
      'updated_at' => null,
      'lernschritte' => collect(),
    ];

    return view('kalender.index', [
      'tabs' => $tabs,
      'monat' => $monat,
      'tage' => $tage,
      'tag' => $tag,
      'wochentage' => $wochentage,
      'wocheErfasst' => $wochentage->where('art', 'bericht')->count(),
      'lernschritte' => collect(),
      'darfSchreiben' => true,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create() {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id) {
    //
  }


  public function update(Request $request, string $datum, GitLabServiceInterface $gitLabService) {
    $user = $request->user();

    if (!$user->isAzubi()) {
      abort(403, 'Nur Azubis dürfen Tagesberichte speichern.');
    }

    $data = $request->validate([
      'taetigkeiten' => ['required', 'string', 'max:600'],
      'dauer' => ['nullable', 'string', 'max:255'],
      'abteilung' => ['nullable', 'string', 'max:255'],
    ]);

    $datum = Carbon::parse($datum);
    $filename = $datum->toDateString() . ' Tagesbericht.json';

    $payload = [
      'date' => $datum->toDateString(),
      'wochentag' => $datum->translatedFormat('l'),
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
      'datum' => $datum->toDateString(),
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {
    //
  }
}
