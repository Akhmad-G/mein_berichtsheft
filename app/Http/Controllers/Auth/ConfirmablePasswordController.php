<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller for confirming user password before accessing sensitive areas.
 */
class ConfirmablePasswordController extends Controller {
  /**
   * Show the confirm password view.
   *
   * @return View
   */
  public function show(): View {
    return view('auth.confirm-password');
  }

  /**
   * Confirm the user's password.
   *
   * @param Request $request
   * @return RedirectResponse
   *
   * @throws ValidationException If provided password is invalid.
   */
  public function store(Request $request): RedirectResponse {
    if (!Auth::guard('web')->validate(['email' => $request->user()->email, 'password' => $request->password,])) {
      throw ValidationException::withMessages(['password' => __('auth.password'),]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended(route('dashboard', absolute: false));
  }
}
