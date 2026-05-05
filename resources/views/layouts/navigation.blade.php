@php
    $user = auth()->user();

    $socialWorkerLinks = [
        ['route' => 'socialworker.dashboard',    'label' => 'Dashboard'],
        ['route' => 'socialworker.cases.index',  'label' => 'Cases'],
        ['route' => 'socialworker.appointments.index','label' => 'Calendar'],
        ['route' => 'socialworker.messages.index','label' => 'Messages'],
        ['route' => 'socialworker.placements.map','label' => 'Placements'],
    ];

    $carerLinks = [
        ['route' => 'carer.dashboard',       'label' => 'Dashboard'],
        ['route' => 'carer.messages.index',  'label' => 'Messages'],
        ['route' => 'carer.documents.index', 'label' => 'Documents'],
        ['route' => 'carer.calendar',        'label' => 'Calendar'],
        ['route' => 'carer.cases.index',     'label' => 'Cases'],
    ];

    $childLinks = [
        ['route' => 'child.dashboard',      'label' => 'Home'],
        ['route' => 'child.week',           'label' => 'Calendar'],
        ['route' => 'child.messages.index', 'label' => 'Messages'],
        ['route' => 'child.diary.index',    'label' => 'Diary'],
        ['route' => 'profile.edit',         'label' => 'Me'],
    ];

    $isDark = $user?->theme === 'dark';
@endphp

<nav x-data="{ open: false }"
     class="{{ $isDark ? 'bg-slate-900 border-b border-slate-700 text-white' : 'bg-white border-b border-gray-100' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Left: logo + nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <img src="{{ asset('images/CareHub.png') }}" alt="CareHub Logo" class="h-10 w-auto" />
                    <span class="font-semibold {{ $isDark ? 'text-white' : 'text-gray-900' }} text-sm">CareHub</span>
                </a>

                @auth
                    <div class="hidden md:flex items-center gap-1">

                        @if($user->role === 'social_worker')
                            @foreach($socialWorkerLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition
                                          {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                             ? 'bg-indigo-50 text-indigo-700'
                                             : ($isDark ? 'text-gray-300 hover:text-white hover:bg-slate-800' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50') }}">
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
                                class="px-3 py-1.5 rounded-md text-sm font-medium transition
                                        {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                            ? 'bg-indigo-50 text-indigo-700'
                                            : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                    @if($link['route'] === 'carer.messages.index' && ($unreadMessages ?? 0) > 0)
                                        <span class="ml-1 inline-flex items-center justify-center w-4 h-4 text-[10px] bg-red-500 text-white rounded-full">
                                            {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        @endif

                        @if($user->role === 'young_person')
                            @foreach($childLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition
                                          {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                             ? 'bg-indigo-50 text-indigo-700'
                                             : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        @endif

                    </div>
                @endauth
            </div>

            {{-- Right: user dropdown + mobile toggle --}}
            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition
                                               {{ $isDark ? 'text-gray-200 hover:bg-slate-800' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{-- Avatar initials --}}
                                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    {{ $user->name }}
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </x-slot>

                            @if($user->role === 'social_worker')
                                <x-dropdown-link href="{{ route('socialworker.wellbeing.alerts') }}">
                                    Alerts
                                </x-dropdown-link>
                            @endif

                            @if($user->role === 'carer' && isset($case) && $case)
                                <x-dropdown-link href="{{ route('carer.case-file.show', $case->case_reference ?? $case->id) }}">
                                    Case file
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-dropdown>
                    </div>
                @endauth

                {{-- Mobile hamburger --}}
                <div class="-mr-2 flex md:hidden">
                    <button type="button" @click="open = !open"
                            class="inline-flex items-center justify-center p-2 rounded-md
                                   {{ $isDark ? 'text-gray-300 hover:text-white hover:bg-slate-800' : 'text-gray-400 hover:text-gray-500 hover:bg-gray-100' }}
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition
         class="md:hidden border-t border-gray-100 {{ $isDark ? 'bg-slate-900' : 'bg-white' }}">
        @auth
            <div class="px-3 py-2 space-y-0.5">
                @if($user->role === 'social_worker')
                    @foreach($socialWorkerLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-2 px-3 py-2 text-sm rounded-md
                                  {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                     ? 'bg-indigo-50 text-indigo-700 font-medium'
                                     : ($isDark ? 'text-gray-300 hover:bg-slate-800' : 'text-gray-600 hover:bg-gray-50') }}">
                            {{ $link['label'] }}
                            @if($link['route'] === 'socialworker.messages.index' && ($unreadMessages ?? 0) > 0)
                                <span class="inline-flex items-center justify-center w-4 h-4 text-[10px] bg-red-500 text-white rounded-full">
                                    {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                @endif

                @if($user->role === 'carer')
                    @foreach($carerLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-2 px-3 py-2 text-sm rounded-md
                                  {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                     ? 'bg-indigo-50 text-indigo-700 font-medium'
                                     : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $link['label'] }}
                            @if($link['route'] === 'carer.messages.index' && ($unreadMessages ?? 0) > 0)
                                <span class="inline-flex items-center justify-center w-4 h-4 text-[10px] bg-red-500 text-white rounded-full">
                                    {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                @endif

                @if($user->role === 'young_person')
                    @foreach($childLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="block px-3 py-2 text-sm rounded-md
                                  {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                     ? 'bg-indigo-50 text-indigo-700 font-medium'
                                     : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="border-t border-gray-100 px-3 py-3 mt-1">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-medium {{ $isDark ? 'text-white' : 'text-gray-900' }}">{{ $user->name }}</p>
                        <p class="text-xs {{ $isDark ? 'text-gray-400' : 'text-gray-500' }}">{{ $user->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition">
                        Log out
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>