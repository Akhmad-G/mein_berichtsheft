<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for handling user login and session termination.
 */
class AuthenticatedSessionController extends Controller {
  /**
   * Display the login view.
   *
   * @return View
   */
  public function create(): View {
    return view('auth.login');
  }

  /**
   * Handle an incoming authentication request.
   *
   * @param LoginRequest $request
   * @return RedirectResponse
   */
  public function store(LoginRequest $request): RedirectResponse {
    $request->authenticate();

    $request->session()->regenerate();

    $user = $request->user();

    $redirectRoute = $user->isAusbilder()
      ? 'weekly-reports.index'
      : 'calendar';

    return redirect()->intended(route($redirectRoute, absolute: false));
  }

  /**
   * Destroy an authenticated session.
   *
   * @param Request $request
   * @return RedirectResponse
   */
  public function destroy(Request $request): RedirectResponse {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }
}
