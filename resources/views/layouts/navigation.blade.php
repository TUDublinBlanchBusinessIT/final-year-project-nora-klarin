@php
    $user = auth()->user();

    $socialWorkerLinks = [
        ['route' => 'socialworker.dashboard', 'label' => 'Dashboard'],
        ['route' => 'socialworker.cases.index', 'label' => 'Cases'],
        ['route' => 'socialworker.messages.index', 'label' => 'Messages'],
        ['route' => 'socialworker.placements.map', 'label' => 'Placements'],
    ];

    $carerLinks = [
        ['route' => 'carer.dashboard', 'label' => 'Dashboard'],
    ];

    $childLinks = [
        ['route' => 'child.dashboard', 'label' => 'Home'],
        ['route' => 'child.week', 'label' => 'Calendar'],
        ['route' => 'child.messages.index', 'label' => 'Messages'],
        ['route' => 'child.diary.index', 'label' => 'Diary'],
        ['route' => 'profile.edit', 'label' => 'Me'],
    ];

    $isDark = $user?->theme === 'dark';
@endphp

<nav x-data="{ open: false }"
     class="{{ $isDark ? 'bg-slate-900 border-b border-slate-700 text-white' : 'bg-white border-b border-gray-100' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/CareHub.png') }}" alt="CareHub Logo" class="h-10 w-auto" />
                    <span class="font-semibold {{ $isDark ? 'text-white' : 'text-gray-900' }} text-sm">CareHub</span>
                </a>

                @auth
                    <div class="hidden md:flex items-center gap-1">
                        @if($user->role === 'social_worker')
                            @foreach($socialWorkerLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                    @if($link['route'] === 'socialworker.messages.index' && ($unreadMessages ?? 0) > 0)
                                        <span class="ml-1 inline-flex items-center justify-center w-4 h-4 text-[10px] bg-red-500 text-white rounded-full">
                                            {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        @endif

                        @if($user->role === 'carer')
                            @foreach($carerLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs($link['route']) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        @endif

                        @if($user->role === 'young_person')
                            @foreach($childLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endauth
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none transition ease-in-out duration-150 {{ $isDark ? 'text-gray-200 bg-slate-900 hover:text-white' : 'text-gray-500 bg-white hover:text-gray-700' }}">
                                    {{ $user->name }}
                                </button>
                            </x-slot>

                            @if($user->role === 'social_worker')
                                <x-dropdown-link href="{{ route('socialworker.wellbeing.alerts') }}">
                                    Alerts
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-dropdown>
                    </div>
                @endauth

                <div class="-mr-2 flex md:hidden">
                    <button type="button" @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md {{ $isDark ? 'text-gray-300 hover:text-white hover:bg-slate-800' : 'text-gray-400 hover:text-gray-500 hover:bg-gray-100' }} focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition class="md:hidden border-t border-gray-100 {{ $isDark ? 'bg-slate-900 text-white' : 'bg-white text-gray-700' }}">
        @auth
            @if($user->role === 'social_worker')
                @foreach($socialWorkerLinks as $link)
                    <a href="{{ route($link['route']) }}" class="block px-3 py-2 text-sm rounded-md hover:bg-gray-50">{{ $link['label'] }}</a>
                @endforeach
            @endif

            @if($user->role === 'carer')
                @foreach($carerLinks as $link)
                    <a href="{{ route($link['route']) }}" class="block px-3 py-2 text-sm rounded-md hover:bg-gray-50">{{ $link['label'] }}</a>
                @endforeach
            @endif

            @if($user->role === 'young_person')
                @foreach($childLinks as $link)
                    <a href="{{ route($link['route']) }}" class="block px-3 py-2 text-sm rounded-md hover:bg-gray-50">{{ $link['label'] }}</a>
                @endforeach
            @endif

            <div class="border-t border-gray-200 px-3 py-3">
                <div class="font-medium {{ $isDark ? 'text-white' : 'text-gray-900' }}">{{ $user->name }}</div>
                <div class="text-sm {{ $isDark ? 'text-gray-300' : 'text-gray-500' }}">{{ $user->email }}</div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-md">
                        Log out
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>
