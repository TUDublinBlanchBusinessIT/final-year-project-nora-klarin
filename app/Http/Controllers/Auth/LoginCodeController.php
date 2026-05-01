<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class LoginCodeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('login_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-login-code');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('login_otp_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (
            !$user ||
            !$user->login_code ||
            !$user->login_code_expires_at ||
            now()->gt($user->login_code_expires_at) ||
            $request->code !== $user->login_code
        ) {
            return back()->withErrors([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        $remember = $request->session()->pull('remember_after_otp', false);

        $user->update([
            'login_code' => null,
            'login_code_expires_at' => null,
            'last_login_at' => now(),
        ]);

        $request->session()->forget('login_otp_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resend(Request $request): RedirectResponse
{
    $userId = $request->session()->get('login_otp_user_id');

    if (!$userId) {
        return redirect()->route('login');
    }

    $user = User::find($userId);

    if (!$user) {
        return redirect()->route('login');
    }

    // ⏱️ Prevent spam (30 second cooldown)
    if ($request->session()->has('last_otp_resend_at')) {
        $lastResend = $request->session()->get('last_otp_resend_at');

        if (now()->diffInSeconds($lastResend) < 30) {
            return back()->withErrors([
                'code' => 'Please wait 30 seconds before requesting another code.',
            ]);
        }
    }

    // 🔐 Generate new code
    $code = (string) random_int(100000, 999999);

    $user->update([
        'login_code' => $code,
        'login_code_expires_at' => now()->addMinutes(10),
    ]);

    // 📧 Send email
    Mail::raw(
        "Your new CareHub verification code is: {$code}\n\nThis code will expire in 10 minutes.",
        function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your new CareHub verification code');
        }
    );

    // 🕒 Store resend time
    $request->session()->put('last_otp_resend_at', now());

    return back()->with('status', 'A new verification code has been sent to your email.');
}
}