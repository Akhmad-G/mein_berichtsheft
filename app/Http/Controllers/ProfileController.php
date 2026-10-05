<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

/** Profile page: personal data (left) + cover sheet of the training record (right). */
class ProfileController extends Controller {
  public function __construct(private ReportRepository $reports) {
  }

  public function show(Request $request) {
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