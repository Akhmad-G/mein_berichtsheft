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
  protected array $weekdays = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];

  public function index(Request $request, GitLabServiceInterface $gitLabService) {
    $user = $request->user();
    $year = (int) $request->query('year', today()->isoWeekYear);
    $filter = $request->query('filter', 'alle');

    $azubis = $user->isAusbilder()
      ? $user->azubis()->whereNotNull('gitlab_path')->get()
      : collect([$user]);

    $weeks = $azubis
      ->flatMap(function (User $owner) use ($gitLabService) {
        return collect($gitLabService->listReports($owner))
          ->where('type', 'wochenbericht')
          ->map(fn(array $entry) => $this->makeWeekFromEntry($entry, $owner, $gitLabService));
      })
      ->filter()
      ->sortByDesc(fn(object $week) => $week->week_start?->timestamp ?? 0)
      ->values();

    $weeks = $weeks
      ->filter(fn(object $week) => (int) ($week->year ?? today()->year) === $year)
      ->when($filter === 'offen', fn(Collection $items) => $items->reject(fn(object $week) => $week->isSigned))
      ->when($filter === 'signiert', fn(Collection $items) => $items->filter(fn(object $week) => $week->isSigned))
      ->values();

    $week = $weeks->first();

    if (!$week) {
      $weekStart = today()->setISODate($year, today()->isoWeek())->startOfWeek();

      $week = $this->emptyWeek($user->isAusbilder() ? $azubis->first() ?? $user : $user, $weekStart);
      $weeks = collect([$week]);
    }

    $offen = $weeks->reject(fn(object $week) => $week->isSigned)->count();
    $signiert = $weeks->filter(fn(object $week) => $week->isSigned)->count();

    $tabs = $user->isAusbilder()
      ? [
        ['key' => 'weeks', 'label' => 'Wochenberichte', 'href' => route('wochenberichte.index'), 'badge' => $offen . ' offen'],
        ['key' => 'azubis', 'label' => 'Meine Azubis', 'href' => '#', 'badge' => $azubis->count()],
      ]
      : [
        ['key' => 'kalender', 'label' => 'Kalender', 'href' => route('kalender'), 'badge' => null],
        ['key' => 'weeks', 'label' => 'Wochenberichte', 'href' => route('wochenberichte.index'), 'badge' => $signiert . ' signiert'],
      ];

    return view('wochenberichte.index', [
      'tabs' => $tabs,
      'weeks' => $weeks,
      'week' => $week,
      'filter' => $filter,
      'year' => $year,
    ]);
  }
  
  private function makeWeekFromEntry(array $entry, User $owner, GitLabServiceInterface $gitLabService): ?object {
    $path = $entry['path'] ?? null;

    if (!$path) {
      return null;
    }

    $report = $gitLabService->getReport($owner, $path);

    return $this->makeWeekFromReport($report, $owner, $path);
  }

  private function makeWeekFromReport(array $report, User $owner, string $path): object {
    $weekStart = isset($report['week_start'])
      ? Carbon::parse($report['week_start'])->startOfDay()
      : $this->weekStartFromFilename(basename($path));

    $weekEnd = isset($report['week_end'])
      ? Carbon::parse($report['week_end'])->startOfDay()
      : $weekStart->copy()->addDays(4);

    $azubiSignedAt = data_get($report, 'unterschriften.azubi.signed_at');
    $ausbilderSignedAt = data_get($report, 'unterschriften.ausbilder.signed_at');

    $submittedAt = $azubiSignedAt ? $this->parseSignatureDate($azubiSignedAt) : null;
    $signedAt = $ausbilderSignedAt ? $this->parseSignatureDate($ausbilderSignedAt) : null;

    $days = collect($this->weekdays)->map(function (string $day, int $index) use ($report, $weekStart) {
      $data = $report['days'][$day] ?? [];
      $date = isset($data['datum'])
        ? Carbon::parse($data['datum'])
        : $weekStart->copy()->addDays($index);

      return (object) [
        'date' => $date,
        'taetigkeiten' => $data['taetigkeiten'] ?? '',
        'dauer' => $data['dauer'] ?? '',
        'abteilung' => $data['abteilung'] ?? '',
        'learningSteps' => collect(),
      ];
    });

    $recordedDay = $days->filter(fn(object $day) => filled($day->taetigkeiten))->count();
    $isSigned = $signedAt !== null;
    $isSubmitted = $submittedAt !== null;

    return (object) [
      'id' => GitLabPath::encode($path),
      'path' => GitLabPath::encode($path),
      'realPath' => $path,
      'kw' => $weekStart->isoWeek(),
      'year' => $weekStart->isoWeekYear(),
      'week_start' => $weekStart,
      'week_end' => $weekEnd,
      'period' => $weekStart->format('d.m.') . '–' . $weekEnd->format('d.m.Y'),
      'recordedDays' => $recordedDay,
      'statusStamp' => $isSigned ? 'signiert' : ($isSubmitted ? 'wartet' : 'offen'),
      'statusText' => $isSigned ? 'Signiert' : ($isSubmitted ? 'Wartet' : 'Offen'),
      'isSigned' => $isSigned,
      'kannEinreichen' => !$isSubmitted && $recordedDay > 0,
      'eingereicht_am' => $submittedAt,
      'unterschrieben_am' => $signedAt,
      'days' => $days,
      'azubi' => $owner,
      'ausbilder' => $owner->ausbilder,
    ];
  }

  private function emptyWeek(User $owner, Carbon $weekStart): object {
    $weekEnd = $weekStart->copy()->addDays(4);

    return (object) [
      'id' => null,
      'path' => null,
      'realPath' => null,
      'kw' => $weekStart->isoWeek(),
      'year' => $weekStart->isoWeekYear(),
      'week_start' => $weekStart,
      'week_end' => $weekEnd,
      'period' => $weekStart->format('d.m.') . '–' . $weekEnd->format('d.m.Y'),
      'recordedDays' => 0,
      'statusStamp' => 'offen',
      'statusText' => 'Offen',
      'isSigned' => false,
      'kannEinreichen' => false,
      'eingereicht_am' => null,
      'unterschrieben_am' => null,
      'days' => collect($this->weekdays)->map(fn(string $day, int $index) => (object) [
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
