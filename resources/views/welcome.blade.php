<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CareHub</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/CareHub.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50 text-slate-800">
    <div class="min-h-screen flex flex-col">

        {{-- HEADER --}}
        <header class="w-full border-b border-white/60 bg-white/80 backdrop-blur">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

                {{-- LOGO --}}
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/CareHub.png') }}"
                         alt="CareHub Logo"
                         class="h-12 w-auto rounded-xl shadow-sm">

                    <div>
                        <div class="font-extrabold text-xl text-sky-700">CareHub</div>
                        <div class="text-xs text-slate-500">Wellbeing and support in one place</div>
                    </div>
                </div>

                {{-- AUTH BUTTONS --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                       class="rounded-2xl px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 transition">
                        Log in
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-2xl px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow transition">
                        Register
                    </a>
                </div>
            </div>
        </header>

        {{-- MAIN --}}
        <main class="flex-1">

            {{-- HERO --}}
            <section class="max-w-7xl mx-auto px-6 py-16 grid lg:grid-cols-2 gap-10 items-center">

                {{-- LEFT SIDE --}}
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-2 text-sm text-slate-600 shadow-sm">
                        🌟 Safe, simple, supportive
                    </div>

                    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Support when you need it most
                    </h1>

                    <p class="mt-5 text-lg text-slate-600 max-w-xl">
                        CareHub helps young people check in with their wellbeing, stay connected with trusted adults,
                        and find support services nearby in one calm and easy-to-use space.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('register') }}"
                           class="rounded-2xl px-6 py-3 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow transition">
                            Get Started
                        </a>

                        <a href="{{ route('login') }}"
                           class="rounded-2xl px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 transition">
                            Log In
                        </a>
                    </div>
                </div>

                {{-- RIGHT SIDE (FEATURE CARDS) --}}
                <div class="rounded-[2rem] bg-white/90 border border-slate-200 shadow-xl p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="rounded-3xl border border-yellow-100 bg-yellow-50 p-5">
                            <div class="text-2xl">😊</div>
                            <h3 class="mt-3 font-bold text-slate-900">Mood Check-ins</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                Track how you feel and build healthy reflection habits.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5">
                            <div class="text-2xl">👨‍👩‍👧</div>
                            <h3 class="mt-3 font-bold text-slate-900">Trusted People</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                Keep important adults and support contacts in one place.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-pink-100 bg-pink-50 p-5">
                            <div class="text-2xl">💬</div>
                            <h3 class="mt-3 font-bold text-slate-900">Safe Messaging</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                Reach out securely to your carer.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-green-100 bg-green-50 p-5">
                            <div class="text-2xl">🗺️</div>
                            <h3 class="mt-3 font-bold text-slate-900">Find Help Nearby</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                Discover nearby support services when you need guidance.
                            </p>
                        </div>

                    </div>
                </div>

            </section>
        </main>

        {{-- FOOTER --}}
        <footer class="px-6 pb-8">
            <div class="max-w-7xl mx-auto text-sm text-slate-500 text-center">
                Built for young people, carers, and support workers 💙
            </div>
        </footer>

    </div>
</body>
</html>