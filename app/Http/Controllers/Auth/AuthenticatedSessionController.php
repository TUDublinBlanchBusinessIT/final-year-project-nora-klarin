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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not verify your login details.',
            ])->onlyInput('email');
        }

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

