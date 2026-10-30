<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controller for displaying the user profile and training record cover sheet.
 */
class ProfileController extends Controller {
  /**
   * @param ReportRepository $reports
   */
  public function __construct(private ReportRepository $reports) {
  }

  /**
   * Display the profile page with user details and training overview stats.
   *
   * @param Request $request
   * @return View
   */
  public function show(Request $request): View {
    $viewer = $request->user();

    if ($viewer->isAusbilder()) {
      $azubis = $viewer->azubis()->orderBy('nachname')->get();
      $azubi = $azubis->firstWhere('id', (int) $request->query('azubi')) ?? $azubis->first();
      $details = [
        'Name' => $viewer->name,
        'E-Mail' => $viewer->email,
        'Betrieb' => $viewer->ausbildungsbetrieb,
        'Azubis' => $azubis->count(),
      ];
    } else {
      $azubis = collect();
      $azubi = $viewer;
      $details = [
        'Name' => $viewer->name,
        'E-Mail' => $viewer->email,
        'Ausbildungsberuf' => $viewer->ausbildungsberuf,
        'Betrieb' => $viewer->ausbildungsbetrieb,
        'Abteilung' => $viewer->abteilung,
        'Ausbildungsbeginn' => $viewer->ausbildungsbeginn?->format('d.m.Y'),
        'Ausbilder' => $viewer->ausbilder?->name,
      ];
    }

    $signedWeeks = $azubi?->gitlab_path
      ? $this->reports->allWeeks($azubi)->filter(fn($w) => $w->isSigned())->count()
      : 0;

    return view('profile.show', [
      'details' => $details,      // label => value
      'azubi' => $azubi,        // ?User — whose record is shown
      'azubis' => $azubis,       // Ausbilder only, for the switcher
      'signedWeeks' => $signedWeeks,
    ]);
  }
}