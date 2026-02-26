<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CareHub') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (requires Popper) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<body class="h-full font-sans antialiased text-gray-900">
    <div class="min-h-full">

        {{-- Navigation --}}
        @include('layouts.navigation')

        {{-- Optional Header --}}
        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>
</html>