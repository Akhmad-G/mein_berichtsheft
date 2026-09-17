<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller {
  /**
   * Display the registration view.
   */
  public function create(): View {
    return view('auth.register');
  }

  /**
   * Handle an incoming registration request.
   *
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse {
    $validated = $request->validate(['vorname' => ['required', 'string', 'max:255'], 'nachname' => ['required', 'string', 'max:255'], 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class], 'password' => ['required', 'confirmed', Rules\Password::defaults()], 'ausbildungsberuf' => ['required', 'string', 'max:255'], 'ausbildungsbetrieb' => ['required', 'string', 'max:255'], 'abteilung' => ['nullable', 'string', 'max:255'], 'ausbildungsbeginn' => ['required', 'date'], 'gitlab_einverstanden' => ['accepted'],]);

    $user = User::create(['vorname' => $validated['vorname'], 'nachname' => $validated['nachname'], 'email' => $validated['email'], 'password' => Hash::make($validated['password']), 'ausbildungsberuf' => $validated['ausbildungsberuf'], 'ausbildungsbetrieb' => $validated['ausbildungsbetrieb'], 'abteilung' => $validated['abteilung'] ?? null, 'ausbildungsbeginn' => $validated['ausbildungsbeginn'],

      'role' => UserRole::Azubi, 'ausbilder_id' => '1',]);

    $user->assignGitLabPathIfMissing();

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('dashboard'));
  }
}
