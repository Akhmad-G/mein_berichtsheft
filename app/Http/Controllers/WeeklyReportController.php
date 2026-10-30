<?php

namespace App\Http\Controllers;

use App\Data\WeeklyReport;
use App\Enums\WeekStatus;
use App\Models\User;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller for managing weekly reports workflow (browsing, submitting, signing, and exporting).
 */
class WeeklyReportController extends Controller
{
    /**
     * @param ReportRepository $reports
     */
    public function __construct(private ReportRepository $reports) {}

    /**
     * Display weekly reports list and the selected weekly report details.
     *
     * @param Request $request
     * @param User|null $user
     * @param int|null $year
     * @param int|null $week
     * @return View
     */
    public function index(Request $request, ?User $user = null, ?int $year = null, ?int $week = null): View
    {
        $viewer = $request->user();
        $year ??= (int) $request->query('year', now()->isoWeekYear());
        $filter = in_array($request->query('filter'), ['open', 'signed'], true) ? $request->query('filter') : 'all';

        $weeks = $this->weeksFor($viewer, $year)
            ->when($filter === 'open', fn ($collection) => $collection
              ->reject(fn (WeeklyReport $weeklyReports) => $weeklyReports->isSigned()))
            ->when($filter === 'signed', fn ($collection) => $collection
              ->filter(fn (WeeklyReport $weeklyReports) => $weeklyReports->isSigned()))
            ->values();

        if ($user && $week) {
            Gate::authorize('view-reports', $user);
            $selected = $this->reports->week($user, $year, $week);
        } else {
            $first = $viewer->isAusbilder()
                ? $weeks->where('status', WeekStatus::Submitted)->sortBy('week')->first()
                : $weeks->first(fn (WeeklyReport $w) => $w->week === now()->isoWeek());
            $first ??= $weeks->first();
            $selected = $first ? $this->reports->week($first->azubi, $first->year, $first->week) : null;
        }

        return view('weekly-reports.index', [
            'weeks'  => $weeks,     // Collection<WeeklyReport> (no day contents)
            'week'   => $selected,  // ?WeeklyReport with days — null → empty state
            'filter' => $filter,    // all|open|signed
            'year'   => $year,
        ]);
    }

    /**
     * Submit a completed weekly report for trainer signature.
     *
     * @param Request $request
     * @param User $user
     * @param int $year
     * @param int $week
     * @return RedirectResponse
     */
    public function submit(Request $request, User $user, int $year, int $week): RedirectResponse
    {
        Gate::authorize('edit-reports', $user);

        $report = $this->reports->week($user, $year, $week, withDays: false);

        if (! $report->canSubmit()) {
            return back()->withErrors(['week' => 'Die Woche ist noch nicht vollständig erfasst.']);
        }


        // take the number only after the commit succeeded
        $newNumber = $report->number === null;
        $report->number ??= $user->next_berichtsnummer;
        $report->status = WeekStatus::Submitted;
        $report->submittedAt = CarbonImmutable::now();
        $report->submittedSignature = $this->signatureFromRequest($request);

        $this->reports->saveWeek($report, 'submit');


        if ($newNumber) {
          $user->increment('next_berichtsnummer');
        }

        return back()->with('status', "KW {$week} ist zur Unterschrift gegeben.");
    }

    /**
     * Sign and approve a submitted weekly report (Ausbilder only).
     *
     * @param Request $request
     * @param User $user
     * @param int $year
     * @param int $week
     * @return RedirectResponse
     */
    public function sign(Request $request, User $user, int $year, int $week): RedirectResponse
    {
        Gate::authorize('sign-reports', $user);

        $report = $this->reports->week($user, $year, $week, withDays: false);

        if ($report->status !== WeekStatus::Submitted) {
            return back();
        }

        $report->status       = WeekStatus::Signed;
        $report->signedAt     = CarbonImmutable::now();
        $report->signedById   = $request->user()->id;
        $report->signedByName = $request->user()->name;
        $report->signedSignature = $this->signatureFromRequest($request);

        $this->reports->saveWeek($report, 'sign');

        return back()->with('status', "KW {$week} unterschrieben.");
    }

    /**
     * Render the single weekly report print sheet for browser printing.
     *
     * @param User $user
     * @param int $year
     * @param int $week
     * @return View
     */
    public function print(User $user, int $year, int $week): View
    {
        Gate::authorize('view-reports', $user);

        return view('weekly-reports.print', ['week' => $this->reports->week($user, $year, $week)]);
    }

    /**
     * Generate and download a PDF document of the weekly report.
     *
     * @param User $user
     * @param int $year
     * @param int $week
     * @return Response
     */
    public function pdf(User $user, int $year, int $week): Response
    {
      $report = $this->reports->week($user, $year, $week);
      Gate::authorize('view-reports', $user);

      return Pdf::loadView('weekly-reports.pdf', ['week' => $report])
        ->setPaper('a4')
        ->download("Wochenbericht_KW{$week}_{$year}.pdf");
    }

    // ── internal ────────────────────────────────────────────────

    /**
     * Extract and validate vector signature data from the incoming request.
     *
     * @param Request $request
     * @return array{viewBox: string, paths: array<int, string>}
     */
    private function signatureFromRequest(Request $request): array
    {
      $signature = json_decode((string) $request->input('signature'), true);

      logger()->debug('Signature received', [
        'raw' => $request->input('signature'),
        'decoded' => $signature,
        'paths_count' => is_array($signature['paths'] ?? null) ? count($signature['paths']) : null,
      ]);

      abort_unless(
        is_array($signature)
        && ($signature['viewBox'] ?? null) === '0 0 900 260'
        && isset($signature['paths'])
        && is_array($signature['paths'])
        && count($signature['paths']) > 0,
        422
      );

      return [
        'viewBox' => '0 0 900 260',
        'paths' => array_values(array_filter(
          $signature['paths'],
          fn ($path) => is_string($path) && preg_match('/^[ML0-9 .-]+$/', $path)
        )),
      ];
    }

    /**
     * Load list of weekly reports relevant for the given viewer (Azubi vs Ausbilder).
     *
     * @param User $viewer
     * @param int $year
     * @return Collection<int, WeeklyReport>
     */
    private function weeksFor(User $viewer, int $year): Collection
    {
        if ($viewer->isAusbilder()) {
            return $viewer->azubis()->whereNotNull('gitlab_path')->orderBy('nachname')->get()
                ->flatMap(fn (User $azubi) => $this->reports->weeks($azubi, $year))
                ->reject(fn (WeeklyReport $w) => $w->status === WeekStatus::Draft)
                ->sortByDesc('week');
        }

        $weeks = $this->reports->weeks($viewer, $year);

        // current week is listed even before the first file exists
        $current = now()->isoWeek();
        if ($year === now()->isoWeekYear() && ! $weeks->contains(fn (WeeklyReport $w) => $w->week === $current)) {
            $weeks->prepend(new WeeklyReport($viewer, $year, $current));
        }

        return $weeks;
    }
}
