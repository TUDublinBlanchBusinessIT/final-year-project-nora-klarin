<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'CareHub') }}</title>



    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://unpkg.com/feather-icons"></script>

</head>



<body class="bg-gray-100 font-sans antialiased min-h-screen">



@php

    $user = auth()->user();

@endphp



<!-- Navigation -->

<nav class="bg-white shadow">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">



            <!-- Left -->

            <div class="flex items-center">

                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">

                    {{ config('app.name', 'CareHub') }}

                </a>



                @auth

                    <div class="hidden md:flex ml-10 space-x-4">



                        @if($user->role === 'admin')

                            <a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm">

                                Users

                            </a>

                        @endif



                        @if($user->role === 'social_worker')

                            <a href="{{ route('socialworker.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm">

                                Dashboard

                            </a>

                        @endif



                        @if($user->role === 'carer')

                            <a href="{{ route('carer.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm">

                                Dashboard

                            </a>

                        @endif



                    </div>

                @endauth

            </div>



            <!-- Right (FIXED DROPDOWN) -->

            <div class="flex items-center">



                @auth

                <div x-data="{ open: false }" class="relative">



                    <!-- Trigger -->

                    <button @click="open = !open"

                        class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none">



                        {{ $user->name }}

                        <span class="text-gray-400 text-xs">

                            ({{ ucfirst(str_replace('_', ' ', $user->role)) }})

                        </span>



                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"

                                  d="M19 9l-7 7-7-7" />

                        </svg>

                    </button>



                    <!-- Dropdown -->

                    <div x-show="open"

                         @click.outside="open = false"

                         x-transition

                         class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">



                        <a href="{{ route('profile.edit') }}"

                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">

                            Profile

                        </a>



                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"

                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">

                                Log Out

                            </button>

                        </form>



                    </div>

                </div>

                @endauth



            </div>



        </div>

    </div>

</nav>



<!-- Page Heading -->

@isset($header)

<header class="bg-white shadow">

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{ $header }}

    </div>

</header>

@endisset



<!-- Content -->

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

    {{ $slot }}

</main>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>



<script>

    feather.replace()

</script>



</body>

</html>