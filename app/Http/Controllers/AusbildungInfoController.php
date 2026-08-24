<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AusbildungInfoController extends Controller
{
  public function create(): View
  {
    return view('auth.ausbildung-info');
  }
  
  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'vorname' => ['required', 'string', 'max:255'],
      'nachname' => ['required', 'string', 'max:255'],
      'ausbildungsberuf' => ['required', 'string', 'max:255'],
      'ausbildungsbetrieb' => ['required', 'string', 'max:255'],
      'ausbildungsbeginn' => ['required', 'date'],
    ]);
    
    $request->user()->update([
      ...$validated,
      'name' => $validated['vorname'].' '.$validated['nachname'],
      'ausbildung_info_completed_at' => now(),
    ]);
    
    return redirect(route('dashboard'))->with('success', 'Ausbildungsdaten gespeichert!');
  }
}
