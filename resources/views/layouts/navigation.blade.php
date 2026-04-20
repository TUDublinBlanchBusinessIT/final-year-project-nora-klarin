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
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-14 items-center">

            {{-- LEFT: Logo + Nav --}}
            <div class="flex items-center gap-6">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/CareHub.png') }}" class="h-8 w-8 object-contain">
                    <span class="font-semibold text-gray-900 text-sm">CareHub</span>
                </a>

                {{-- Desktop Nav --}}
                @auth
                    <div class="hidden md:flex items-center gap-1">

                        @if($user->role === 'social_worker')
                            @foreach($socialWorkerLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="px-3 py-1.5 rounded-md text-sm font-medium transition
                                   {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'*')
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    
                                    {{ $link['label'] }}

                                    {{-- Unread badge --}}
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
                                   {{ request()->routeIs($link['route'])
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        @endif

                    </div>
                @endauth
            </div>

            {{-- RIGHT: Alerts + User --}}
            <div class="flex items-center gap-4">

                @auth

                    {{-- Alerts (only social worker) --}}
                    @if($user->role === 'social_worker')
                        <a href="{{ route('socialworker.wellbeing.alerts') }}"
                           class="relative p-1.5 text-gray-400 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>

                            @if(($highRiskAlertCount ?? 0) > 0)
                                <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </a>
                    @endif

                    {{-- User --}}
                    <span class="hidden sm:block text-xs text-gray-400">
                        {{ $user->name }}
                        <span class="mx-1 text-gray-300">·</span>
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            Log out
                        </button>
                    </form>

                @endauth

                {{-- Mobile Toggle --}}
                <button @click="open = !open"
                        class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition
         class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">

        @auth
            @if($user->role === 'social_worker')
                @foreach($socialWorkerLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            @endif

            @if($user->role === 'carer')
                @foreach($carerLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            @endif

            <div class="pt-2 border-t">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                        Log out
                    </button>
                </form>
            </div>
        @endauth

    </div>
</nav>