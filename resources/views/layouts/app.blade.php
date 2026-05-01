@php
    $theme = auth()->check() ? auth()->user()->theme : 'calm';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="theme-{{ $theme }} {{ $theme === 'dark' ? 'dark' : '' }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CareHub') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script src="https://unpkg.com/feather-icons"></script>

    @stack('styles')
    @yield('head')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body class="bg-gray-50 font-sans antialiased">

    <div class="min-h-screen">

        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white border-b">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-7xl mx-auto px-6 py-6">
            {{ $slot }}
        </main>

    </div>

    @stack('scripts')

    <script>
        feather.replace()
    </script>

</body>
</html>
