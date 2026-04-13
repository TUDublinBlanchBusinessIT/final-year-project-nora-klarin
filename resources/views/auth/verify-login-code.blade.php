<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Login Code - CareHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
        <h1 class="text-3xl font-extrabold text-slate-900 text-center">Enter verification code</h1>
        <p class="mt-3 text-center text-slate-600">
            We sent a 6-digit code to your email. Enter it below to finish logging in.
        </p>

        @if (session('status'))
            <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('code'))
            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('code') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.code.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="code" class="block text-sm font-semibold text-slate-700 mb-2">
                    Verification code
                </label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    placeholder="123456"
                    required
                    class="block w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-2xl px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition"
            >
                Verify and log in
            </button>
        </form>

        <form method="POST" action="{{ route('login.code.resend') }}" class="mt-4">
            @csrf
            <button
                type="submit"
                class="w-full rounded-2xl px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition"
            >
                Resend code
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 underline">
                Back to login
            </a>
        </div>
    </div>
</body>
</html>