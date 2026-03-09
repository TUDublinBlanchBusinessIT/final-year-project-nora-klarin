<!-- resources/views/layouts/app.blade.php -->



<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">



<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'CareHub') }}</title>



    <!-- Tailwind / Vite -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])



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



            <!-- Left side -->

            <div class="flex items-center">



                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">

                    {{ config('app.name', 'CareHub') }}

                </a>



                @auth

                <div class="hidden md:flex ml-10 space-x-4">



                    @if($user->role === 'admin')

                        <a href="{{ route('admin.users.index') }}"

                           class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">

                            Users

                        </a>

                    @endif



                    @if($user->role === 'social_worker')

                        <a href="{{ route('socialworker.dashboard') }}"

                           class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">

                            Dashboard

                        </a>

                    @endif



                    @if($user->role === 'carer')

                        <a href="{{ route('carer.dashboard') }}"

                           class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">

                            Dashboard

                        </a>

                    @endif



                </div>

                @endauth



            </div>



            <!-- Right side -->

            <div class="flex items-center space-x-4">



                @auth



                <span class="text-gray-700 text-sm">

                    {{ $user->name }}

                    ({{ ucfirst(str_replace('_',' ', $user->role)) }})

                </span>



                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button

                        type="submit"

                        class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-medium transition">

                        Logout

                    </button>

                </form>



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



<!-- Page Content -->

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

    {{ $slot }}

</main>



<script>

    feather.replace()

</script>



</body>

</html>

