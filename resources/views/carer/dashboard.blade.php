<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">

                👋 Hi {{ Auth::user()->name ?? ($user->name ?? 'there') }}!

            </h2>



            <div class="flex items-center gap-4">

                {{-- Bell dropdown (keeps dropdown UI but neutral content) --}}

                <div x-data="{ open: false }" class="relative">

                    <button

                        type="button"

                        @click="open = !open"

                        class="relative text-2xl rounded-full px-2 py-1 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-300"

                        aria-label="Open reminders"

                    >

                        🔔

                        @if(isset($reminderCount) && $reminderCount > 0)

                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">

                                {{ $reminderCount }}

                            </span>

                        @endif

                    </button>



                    <div

                        x-show="open"

                        @click.outside="open = false"

                        x-transition

                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden z-50"

                    >

                        <div class="px-4 py-3 border-b bg-gray-50">

                            <div class="font-extrabold text-gray-800">Reminders</div>

                            <div class="text-xs text-gray-500">Things to check</div>

                        </div>



                        <div class="px-4 py-4 space-y-3">

                            {{-- Neutral fallback content --}}

                            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">

                                <div class="font-bold text-gray-800">No reminders</div>

                                <div class="text-sm text-gray-600 mt-1">You have no reminders at the moment.</div>

                            </div>



                            <button

                                type="button"

                                @click="open = false"

                                class="w-full text-sm text-gray-600 hover:text-gray-900 underline"

                            >

                                Close

                            </button>

                        </div>

                    </div>

                </div>



                <span class="text-sm text-gray-500">

                    {{ now()->format('l, jS F') }}

                </span>

            </div>

        </div>

    </x-slot>



    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">



            {{-- HERO --}}

            <div class="rounded-2xl p-6">

                <div class="bg-gradient-to-r from-indigo-600 to-sky-500 rounded-2xl p-6 shadow-md text-white">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                        <div>

                            <p class="text-sm font-medium opacity-90">Welcome back</p>

                            <h1 class="text-3xl font-bold mt-1">{{ $user->name ?? Auth::user()->name ?? 'Carer' }}</h1>

                            <p class="text-sm mt-2 opacity-90">Here’s what’s coming up.</p>

                        </div>



                        <div class="flex gap-3">

                            <a href="{{ route('carer.calendar') }}"

                               class="px-4 py-2 rounded-xl bg-white/20 text-white hover:bg-white/30 text-sm backdrop-blur">

                                View Calendar

                            </a>



                            <a href="{{ route('carer.messages.index') }}"

                               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">

                                Open Messages

                            </a>



                            <a href="{{ route('carer.documents.index') }}"

                               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">

                                Documents

                            </a>

                        </div>

                    </div>

                </div>

            </div>



            {{-- TOP CARDS --}}

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100">

                    <h3 class="text-lg font-extrabold text-blue-700">Upcoming appointments</h3>

                    <p class="text-gray-600 mt-2">Next 5 shown below</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $appointments->count() ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">

                    <h3 class="text-lg font-extrabold text-indigo-700">Unread messages</h3>

                    <p class="text-gray-600 mt-2">From your social worker</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $unreadCount ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-yellow-100">

                    <h3 class="text-lg font-extrabold text-yellow-700">Alerts</h3>

                    <p class="text-gray-600 mt-2">Latest 5 shown</p>

                    <div class="mt-4">

                        <div class="text-3xl font-semibold text-gray-900">{{ $alerts->count() ?? 0 }}</div>

                    </div>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-pink-100">

                    <h3 class="text-lg font-extrabold text-pink-700">Quick Links</h3>

                    <div class="mt-4 space-y-2">

                        <a href="{{ route('carer.calendar') }}" class="block rounded-2xl bg-pink-50 hover:bg-pink-100 px-4 py-3 font-semibold text-pink-700 transition">📅 View calendar</a>

                        <a href="{{ route('carer.messages.index') }}" class="block rounded-2xl bg-blue-50 hover:bg-blue-100 px-4 py-3 font-semibold text-blue-700 transition">💬 Messages</a>

                    </div>

                </div>

            </div>



            {{-- MAIN AREA: appointments (left) + alerts (right) --}}

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Appointments (big left panel) --}}

                <div class="lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-white/95 backdrop-blur border border-indigo-100">

                    <div class="flex items-center justify-between">

                        <h3 class="text-2xl font-extrabold text-indigo-700">Upcoming Appointments</h3>

                        <a href="{{ route('carer.calendar') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>

                    </div>



                    <div class="mt-6 space-y-3">

                        @forelse($appointments as $appt)

                            <div class="rounded-xl border p-4 hover:bg-gray-50">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <div class="font-medium text-gray-900">

                                            {{ \Carbon\Carbon::parse($appt->starttime)->format('D d M Y, H:i') }}

                                        </div>

                                        <div class="text-sm text-gray-600 mt-1">

                                            {{ $appt->notes ?? 'No notes provided' }}

                                        </div>

                                    </div>

                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">

                                        Scheduled

                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">

                                No upcoming appointments.

                            </div>

                        @endforelse

                    </div>

                </div>



                {{-- Alerts (right) --}}

                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">

                    <h3 class="text-lg font-extrabold text-indigo-700">🗂️ Recent Alerts</h3>

                    <p class="text-sm text-gray-600 mt-1">Your latest alerts</p>



                    <div class="mt-4 space-y-3">

                        @forelse($alerts as $a)

                            <div class="rounded-2xl border overflow-hidden hover:bg-gray-50 transition">

                                <div class="p-4">

                                    <div class="flex items-start justify-between gap-3">

                                        <div>

                                            <div class="text-sm font-semibold text-gray-900">{{ $a->title ?? 'Update' }}</div>

                                            <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($a->createdat ?? $a->created_at)->diffForHumans() }}</div>

                                        </div>



                                        <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 whitespace-nowrap">Recent</span>

                                    </div>



                                    <div class="text-sm text-gray-600 mt-2">

                                        {{ \Illuminate\Support\Str::limit($a->description ?? $a->desc ?? '', 140) }}

                                    </div>



                                    <div class="mt-3 flex gap-3">

                                        <a href="{{ route('carer.messages.index') }}" class="text-sm text-indigo-600 hover:underline">Message social worker →</a>

                                        <a href="{{ route('carer.calendar') }}" class="text-sm text-gray-600 hover:underline">Check calendar →</a>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">No alerts.</div>

                        @endforelse

                    </div>

                </div>

            </div>



            {{-- Bottom cards --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="rounded-3xl p-6 shadow-lg bg-gradient-to-br from-green-50 to-blue-50 border border-green-100">

                    <h3 class="text-lg font-extrabold text-green-700">🎯 My Goal This Week</h3>

                    <p class="text-gray-700 mt-2">Pick one small thing to work on.</p>

                </div>



                <div class="rounded-3xl p-6 shadow-lg bg-gradient-to-br from-yellow-50 to-pink-50 border border-yellow-100">

                    <h3 class="text-lg font-extrabold text-pink-700">🌈 Something Positive</h3>

                    <p class="text-gray-700 mt-2">“You don’t have to do everything. Just one small step.”</p>

                </div>

            </div>



        </div>

    </div>

</x-app-layout>