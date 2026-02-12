<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            try {
                ActivityLog::create([
                    'action' => 'login_failed',
                    'description' => 'Tentativo di accesso fallito per: ' . $request->input('email'),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                    'created_at' => now(),
                ]);
            } catch (Throwable) {
                // Non bloccare la UX di login se il logging ha un problema runtime.
            }

            throw ValidationException::withMessages([
                'email' => __('Credenziali non valide.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'Accesso effettuato',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Non bloccare il login se il logging ha un problema runtime.
        }

        return redirect()->intended(route('dashboard'));
    }

    private function ensureIsNotRateLimited(LoginRequest $request): void
    {
        $maxAttempts = config('archivio.rate_limit.login_attempts', 5);

        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Troppi tentativi di accesso. Riprova tra {$seconds} secondi.",
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')) . '|' . $request->ip());
    }
}
