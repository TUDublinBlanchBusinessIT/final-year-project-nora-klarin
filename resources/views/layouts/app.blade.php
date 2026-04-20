
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
</body>
</html>

    @stack('scripts')

    <script>
        feather.replace()
    </script>

</body>
</html>
