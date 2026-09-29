<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use App\Support\GitLabPath;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WochenberichtController extends Controller {
  protected array $wochentage = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];

  public function index(Request $request, GitLabServiceInterface $gitLabService) {
    $user = $request->user();
    $jahr = (int) $request->query('jahr', today()->isoWeekYear);
    $filter = $request->query('filter', 'alle');

    $azubis = $user->isAusbilder()
      ? $user->azubis()->whereNotNull('gitlab_path')->get()
      : collect([$user]);

    $wochen = $azubis
      ->flatMap(function (User $owner) use ($gitLabService) {
        return collect($gitLabService->listReports($owner))
          ->where('type', 'wochenbericht')
          ->map(fn(array $entry) => $this->makeWocheFromEntry($entry, $owner, $gitLabService));
      })
      ->filter()
      ->sortByDesc(fn(object $woche) => $woche->week_start?->timestamp ?? 0)
      ->values();

    $wochen = $wochen
      ->filter(fn(object $woche) => (int) ($woche->jahr ?? today()->year) === $jahr)
      // $hasSignature = !empty($report['unterschriften']['ausbilder']);
      ->when($filter === 'offen', fn(Collection $items) => $items->reject(fn(object $woche) => $woche->istUnterschrieben))
      ->when($filter === 'signiert', fn(Collection $items) => $items->filter(fn(object $woche) => $woche->istUnterschrieben))
      ->values();

    $woche = $wochen->first();

    if (!$woche) {
      $weekStart = today()->setISODate($jahr, today()->isoWeek())->startOfWeek();

      $woche = $this->emptyWoche($user->isAusbilder() ? $azubis->first() ?? $user : $user, $weekStart);
      $wochen = collect([$woche]);
    }

    $offen = $wochen->reject(fn(object $woche) => $woche->istUnterschrieben)->count();
    $signiert = $wochen->filter(fn(object $woche) => $woche->istUnterschrieben)->count();

    $tabs = $user->isAusbilder()
      ? [
        ['key' => 'wochen', 'label' => 'Wochenberichte', 'href' => route('wochenberichte.index'), 'badge' => $offen . ' offen'],
        ['key' => 'azubis', 'label' => 'Meine Azubis', 'href' => '#', 'badge' => $azubis->count()],
      ]
      : [
        ['key' => 'kalender', 'label' => 'Kalender', 'href' => route('kalender'), 'badge' => null],
        ['key' => 'wochen', 'label' => 'Wochenberichte', 'href' => route('wochenberichte.index'), 'badge' => $signiert . ' signiert'],
      ];

    return view('wochenberichte.index', [
      'tabs' => $tabs,
      'wochen' => $wochen,
      'woche' => $woche,
      'filter' => $filter,
      'jahr' => $jahr,
    ]);
  }

  public function create() {
    if (!auth()->user()->isAzubi()) {
      abort(403, 'Nur Azubis dürfen Wochenberichte erstellen.');
    }

    return view('wochenberichte.create');
  }

  // AJAX: Wird über die Schaltfläche „Aus Tagesberichten übernehmen“ aufgerufen
  public function uebernehmen(Request $request, GitLabServiceInterface $gitLabService) {
    $request->validate(['week' => 'required|string']);

    $weekStart = $this->parseWeekStart($request->query('week'));

    $tagesberichte = $gitLabService->getReportsForWeek($request->user(), $weekStart);

    $result = [];
    foreach ($this->wochentage as $tag) {
      $result[$tag] = ['taetigkeiten' => $tagesberichte[$tag]['taetigkeiten'] ?? '', 'gelernt' => $tagesberichte[$tag]['gelernt'] ?? '', 'probleme' => $tagesberichte[$tag]['probleme'] ?? '',];
    }

    return response()->json($result);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request, GitLabServiceInterface $gitLabService) {
    if (!auth()->user()->isAzubi()) {
      abort(403, 'Nur Azubis dürfen Wochenberichte erstellen.');
    }

    $validated = $request->validate(['week' => 'required|string', 'tage' => 'required|array', 'tage.*.taetigkeiten' => 'nullable|string', 'tage.*.gelernt' => 'nullable|string', 'tage.*.probleme' => 'nullable|string', 'tage.*.ausbildungsplan' => 'nullable|string',]);

    $user = $request->user();
    $weekStart = $this->parseWeekStart($validated['week']);
    $weekEnd = $weekStart->copy()->addDays(4);

    [$year, $weekNumber] = explode('-W', $validated['week']);

    $tage = [];
    foreach ($this->wochentage as $index => $tag) {
      $date = $weekStart->copy()->addDays($index);
      $tage[$tag] = ['date' => $date->format('Y-m-d'), 'taetigkeiten' => $validated['tage'][$tag]['taetigkeiten'] ?? '', 'gelernt' => $validated['tage'][$tag]['gelernt'] ?? '', 'probleme' => $validated['tage'][$tag]['probleme'] ?? '', 'ausbildungsplan' => $validated['tage'][$tag]['ausbildungsplan'] ?? '',];
    }

    $data = ['berichtsnummer' => $user->nextBerichtsnummer(), 'kalenderwoche' => "KW {$weekNumber} / {$year}", 'week_start' => $weekStart->format('Y-m-d'), 'week_end' => $weekEnd->format('Y-m-d'), 'user' => ['name' => $user->name, 'ausbildungsberuf' => $user->ausbildungsberuf, 'ausbildungsbetrieb' => $user->ausbildungsbetrieb,], 'tage' => $tage, 'unterschriften' => ['azubi' => null, 'ausbilder' => null,], 'created_at' => now()->toIso8601String(),];

    $filename = sprintf('%s-KW%02d Wochenbericht.json', $year, (int) $weekNumber);

    $gitLabService->saveReport($user, $filename, $data);

    return redirect()->route('dashboard')->with('success', 'Wochenbericht gespeichert.');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $path, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($path);
    $reportOwner = $this->authorizeReportPath($realPath);

    $report = $gitLabService->getReport($reportOwner, $realPath);

    $hasSignature = !empty($report['unterschriften']['ausbilder']);

    $canManage = auth()->user()->isAzubi() && auth()->id() === $reportOwner->id && !$hasSignature;

    return view('wochenberichte.show', ['report' => $report, 'path' => GitLabPath::encode($realPath), 'canManage' => $canManage,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($id);
    $reportOwner = $this->authorizeReportPath($realPath);

    $report = $gitLabService->getReport($reportOwner, $realPath);

    $hasSignature = !empty($report['unterschriften']['ausbilder']);

    if (!auth()->user()->isAzubi() || auth()->id() !== $reportOwner->id || $hasSignature) {
      abort(403, 'Signierte Wochenberichte dürfen nicht bearbeitet werden.');
    }


    return view('wochenberichte.edit', ['report' => $report, 'path' => $id,]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($id);
    $reportOwner = $this->authorizeReportPath($realPath);

    $existing = $gitLabService->getReport($reportOwner, $realPath);

    $hasSignature = !empty($existing['unterschriften']['ausbilder']);

    if (!auth()->user()->isAzubi() || auth()->id() !== $reportOwner->id || $hasSignature) {
      abort(403, 'Signierte Wochenberichte dürfen nicht bearbeitet werden.');
    }

    $validated = $request->validate(['week' => 'required|string', 'tage' => 'required|array', 'tage.*.taetigkeiten' => 'nullable|string', 'tage.*.gelernt' => 'nullable|string', 'tage.*.probleme' => 'nullable|string', 'tage.*.ausbildungsplan' => 'nullable|string',]);


    $weekStart = $this->parseWeekStart($validated['week']);
    $weekEnd = $weekStart->copy()->addDays(4);

    [$year, $weekNumber] = explode('-W', $validated['week']);

    $tage = [];
    foreach ($this->wochentage as $index => $tag) {
      $date = $weekStart->copy()->addDays($index);

      $tage[$tag] = ['date' => $date->format('Y-m-d'), 'taetigkeiten' => $validated['tage'][$tag]['taetigkeiten'] ?? '', 'gelernt' => $validated['tage'][$tag]['gelernt'] ?? '', 'probleme' => $validated['tage'][$tag]['probleme'] ?? '', 'ausbildungsplan' => $validated['tage'][$tag]['ausbildungsplan'] ?? '',];
    }

    $data = ['berichtsnummer' => $existing['berichtsnummer'] ?? null, 'kalenderwoche' => "KW {$weekNumber} / {$year}", 'week_start' => $weekStart->format('Y-m-d'), 'week_end' => $weekEnd->format('Y-m-d'), 'user' => $existing['user'] ?? ['name' => $reportOwner->name, 'ausbildungsberuf' => $reportOwner->ausbildungsberuf, 'ausbildungsbetrieb' => $reportOwner->ausbildungsbetrieb,], 'tage' => $tage, 'unterschriften' => $existing['unterschriften'] ?? ['azubi' => null, 'ausbilder' => null,], 'created_at' => $existing['created_at'] ?? now()->toIso8601String(), 'updated_at' => now()->toIso8601String(),];

    $gitLabService->saveReport($reportOwner, basename($realPath), $data, 'update');

    return redirect()->route('wochenberichte.show', ['path' => $id])->with('success', 'Wochenbericht aktualisiert.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($id);
    $reportOwner = $this->authorizeReportPath($realPath);

    $report = $gitLabService->getReport($reportOwner, $realPath);

    $hasSignature = !empty($report['unterschriften']['ausbilder']);

    if (!auth()->user()->isAzubi() || auth()->id() !== $reportOwner->id || $hasSignature) {
      abort(403, 'Signierte Wochenberichte dürfen nicht gelöscht werden.');
    }

    $gitLabService->deleteReport($reportOwner, $realPath);

    return redirect()->route('dashboard')->with('success', 'Wochenbericht gelöscht.');
  }

  private function makeWocheFromEntry(array $entry, User $owner, GitLabServiceInterface $gitLabService): ?object {
    $path = $entry['path'] ?? null;

    if (!$path) {
      return null;
    }

    $report = $gitLabService->getReport($owner, $path);

    return $this->makeWocheFromReport($report, $owner, $path);
  }

  private function makeWocheFromReport(array $report, User $owner, string $path): object {
    $weekStart = isset($report['week_start'])
      ? Carbon::parse($report['week_start'])->startOfDay()
      : $this->weekStartFromFilename(basename($path));

    $weekEnd = isset($report['week_end'])
      ? Carbon::parse($report['week_end'])->startOfDay()
      : $weekStart->copy()->addDays(4);

    $azubiSignedAt = data_get($report, 'unterschriften.azubi.signed_at');
    $ausbilderSignedAt = data_get($report, 'unterschriften.ausbilder.signed_at');

    $eingereichtAm = $azubiSignedAt ? $this->parseSignatureDate($azubiSignedAt) : null;
    $unterschriebenAm = $ausbilderSignedAt ? $this->parseSignatureDate($ausbilderSignedAt) : null;

    $tage = collect($this->wochentage)->map(function (string $tag, int $index) use ($report, $weekStart) {
      $data = $report['tage'][$tag] ?? [];
      $datum = isset($data['date'])
        ? Carbon::parse($data['date'])
        : $weekStart->copy()->addDays($index);

      return (object) [
        'datum' => $datum,
        'taetigkeiten' => $data['taetigkeiten'] ?? '',
        'dauer' => $data['dauer'] ?? '',
        'abteilung' => $data['abteilung'] ?? '',
        'lernschritte' => collect(),
      ];
    });

    $erfassteTage = $tage->filter(fn(object $tag) => filled($tag->taetigkeiten))->count();
    $istUnterschrieben = $unterschriebenAm !== null;
    $istEingereicht = $eingereichtAm !== null;

    return (object) [
      'id' => GitLabPath::encode($path),
      'path' => GitLabPath::encode($path),
      'realPath' => $path,
      'kw' => $weekStart->isoWeek(),
      'jahr' => $weekStart->isoWeekYear(),
      'week_start' => $weekStart,
      'week_end' => $weekEnd,
      'zeitraum' => $weekStart->format('d.m.') . '–' . $weekEnd->format('d.m.Y'),
      'erfassteTage' => $erfassteTage,
      'statusStempel' => $istUnterschrieben ? 'signiert' : ($istEingereicht ? 'wartet' : 'offen'),
      'statusText' => $istUnterschrieben ? 'Signiert' : ($istEingereicht ? 'Wartet' : 'Offen'),
      'istUnterschrieben' => $istUnterschrieben,
      'kannEinreichen' => !$istEingereicht && $erfassteTage > 0,
      'eingereicht_am' => $eingereichtAm,
      'unterschrieben_am' => $unterschriebenAm,
      'tage' => $tage,
      'azubi' => $owner,
      'ausbilder' => $owner->ausbilder,
    ];
  }

  private function emptyWoche(User $owner, Carbon $weekStart): object {
    $weekEnd = $weekStart->copy()->addDays(4);

    return (object) [
      'id' => null,
      'path' => null,
      'realPath' => null,
      'kw' => $weekStart->isoWeek(),
      'jahr' => $weekStart->isoWeekYear(),
      'week_start' => $weekStart,
      'week_end' => $weekEnd,
      'zeitraum' => $weekStart->format('d.m.') . '–' . $weekEnd->format('d.m.Y'),
      'erfassteTage' => 0,
      'statusStempel' => 'offen',
      'statusText' => 'Offen',
      'istUnterschrieben' => false,
      'kannEinreichen' => false,
      'eingereicht_am' => null,
      'unterschrieben_am' => null,
      'tage' => collect($this->wochentage)->map(fn(string $tag, int $index) => (object) [
        'datum' => $weekStart->copy()->addDays($index),
        'taetigkeiten' => '',
        'dauer' => '',
        'abteilung' => '',
        'lernschritte' => collect(),
      ]),
      'azubi' => $owner,
      'ausbilder' => $owner->ausbilder,
    ];
  }

  private function weekStartFromFilename(string $filename): Carbon {
    if (preg_match('/(?<year>\d{4})-KW(?<week>\d{1,2})/', $filename, $matches)) {
      return Carbon::now()
        ->setISODate((int) $matches['year'], (int) $matches['week'])
        ->startOfWeek();
    }

    if (preg_match('/KW(?<week>\d{1,2}).*?(?<year>\d{4})/', $filename, $matches)) {
      return Carbon::now()
        ->setISODate((int) $matches['year'], (int) $matches['week'])
        ->startOfWeek();
    }

    return today()->startOfWeek();
  }

  private function parseSignatureDate(string $value): ?Carbon {
    foreach (['d.m.Y H:i', 'd.m.Y', Carbon::ATOM] as $format) {
      try {
        return Carbon::createFromFormat($format, $value);
      } catch (\Throwable) {
        //
      }
    }

    try {
      return Carbon::parse($value);
    } catch (\Throwable) {
      return null;
    }
  }

  private function parseWeekStart(mixed $week) {
    [$year, $weekNumber] = explode('-W', $week);

    return Carbon::now()->setISODate((int) $year, (int) $weekNumber)->startOfWeek();
  }

  public function sign(Request $request, string $path, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($path);
    $reportOwner = $this->authorizeReportPath($realPath);

    $validated = $request->validate(['signature' => 'required|string|starts_with:data:image/png;base64,',]);

    $user = $request->user();

    if (!$user->isAzubi() && !$user->isAusbilder()) {
      abort(403, 'Diese Rolle darf nicht unterschreiben.');
    }

    if ($user->isAzubi() && $user->id !== $reportOwner->id) {
      abort(403, 'Azubis dürfen nur eigene Wochenberichte unterschreiben.');
    }

    if ($user->isAusbilder() && $reportOwner->ausbilder_id !== $user->id) {
      abort(403, 'Ausbilder dürfen nur Wochenberichte eigener Azubis unterschreiben.');
    }

    $signatureKey = $user->isAusbilder() ? 'ausbilder' : 'azubi';

    $existing = $gitLabService->getReport($reportOwner, $realPath);

    $existing['unterschriften'] ??= ['azubi' => null, 'ausbilder' => null,];

    $existing['unterschriften'][$signatureKey] = ['name' => $user->name, 'signed_at' => now()->format('d.m.Y H:i'), 'image' => $validated['signature'],];

    $filename = basename($realPath);

    $gitLabService->saveReport($reportOwner, $filename, $existing, 'update');

    return response()->json(['success' => true]);
  }

  public function pdf(string $path, GitLabServiceInterface $gitLabService) {
    $realPath = GitLabPath::decode($path);
    $reportOwner = $this->authorizeReportPath($realPath);

    $report = $gitLabService->getReport($reportOwner, $realPath);

    $pdf = Pdf::loadView('wochenberichte.pdf', ['report' => $report, 'owner' => $reportOwner,])->setPaper('a4');

    $filename = pathinfo(basename($realPath), PATHINFO_FILENAME) . '.pdf';

    return $pdf->download($filename);
  }

  protected function authorizeReportPath(string $realPath): User {
    $owner = User::query()->whereNotNull('gitlab_path')->where('gitlab_path', '!=', '')->get()->first(fn(User $user) => str_starts_with($realPath, $user->gitlab_path . '/'));

    if (!$owner) {
      abort(403, 'Berichtsinhaber konnte nicht ermittelt werden.');
    }

    $currentUser = auth()->user();

    if ($currentUser->isAzubi() && $currentUser->id === $owner->id) {
      return $owner;
    }

    if ($currentUser->isAusbilder() && $owner->ausbilder_id === $currentUser->id) {
      return $owner;
    }

    abort(403, 'Nicht berechtigt.');
  }
}
