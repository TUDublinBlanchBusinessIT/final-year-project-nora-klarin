<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticateCredentialsOnly();

        $login = $request->login();

        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'We could not verify your login details.',
            ])->onlyInput('login');
        }

        if (in_array($user->role, ['social_worker', 'carer']) && empty($user->email)) {
            return back()->withErrors([
                'login' => 'Staff accounts must have an email address for verification.',
            ])->onlyInput('login');
        }

        if ($user->role === 'young_person' && $user->dob && $user->dob->age < 10) {
            return back()->withErrors([
                'login' => 'For younger children, a carer should complete wellbeing checks on their behalf.',
            ])->onlyInput('login');
        }

        if ($user->role === 'young_person' && empty($user->email)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        if (empty($user->email)) {
            return back()->withErrors([
                'login' => 'We need an email address to complete the verification code step.',
            ])->onlyInput('login');
        }

        // Send verification code only on first login
        if ($user->last_login_at === null) {
            $code = (string) random_int(100000, 999999);

            $user->update([
                'login_code' => $code,
                'login_code_expires_at' => now()->addMinutes(10),
            ]);

            Mail::raw(
                "Your CareHub verification code is: {$code}\n\nThis code will expire in 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your CareHub verification code');
                }
            );

            $request->session()->put('login_otp_user_id', $user->id);
            $request->session()->put('remember_after_otp', $request->boolean('remember'));

            return redirect()->route('login.code.show')
                ->with('status', 'We sent a 6-digit verification code to your email.');
        } else {
            // Subsequent logins: direct login without code
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('dashboard', absolute: false));
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    protected function authenticated(Request $request, $user)
{
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.users.index');
        case 'social_worker':
            return redirect()->route('socialworker.dashboard');
        case 'carer':
            return redirect()->route('carer.dashboard');
        case 'young_person':
            return redirect()->route('child.dashboard');
        default:
            return redirect('/login'); 
    }
}

}

