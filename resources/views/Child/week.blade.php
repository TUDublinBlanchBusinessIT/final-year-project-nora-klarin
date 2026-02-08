<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📅</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Week</h2>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-yellow-50 via-pink-50 to-blue-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Success / Errors --}}
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <div class="font-bold mb-1">Please fix:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Week Range --}}
            <div class="rounded-3xl p-6 shadow-xl bg-white/95 border border-blue-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h3 class="text-2xl font-extrabold text-gray-800">This Week Overview ✨</h3>
                    <span class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($start)->format('j M') }} → {{ \Carbon\Carbon::parse($end)->format('j M') }}
                    </span>
                </div>

                {{-- Weekly Goal --}}
                <div class="mt-5 rounded-2xl border border-purple-100 bg-purple-50 px-5 py-4">
                    <div class="font-bold text-purple-800">🎯 Weekly Goal</div>

                    @if($weeklyGoal)
                        <div class="mt-1 text-gray-800">
                            Your goal: <span class="font-extrabold">{{ $weeklyGoal->goal_key }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Saved for week starting {{ $weeklyGoal->week_start }}</div>
                    @else
                        <div class="mt-1 text-gray-700">
                            No goal saved yet — go to
                            <a class="underline font-semibold" href="{{ route('child.goals') }}">My Goals</a>
                            to pick one.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mood Timeline --}}
            <div class="rounded-3xl p-6 shadow-xl bg-white/95 border border-yellow-100">
                <h3 class="text-xl font-extrabold text-gray-800">🌟 Mood Check-ins</h3>
                <p class="text-gray-600 mt-1">Here’s how your week has been going.</p>

                @php
                    $moodEmoji = [
                        'happy' => '😊',
                        'calm' => '😌',
                        'okay' => '😐',
                        'worried' => '😟',
                        'sad' => '😢',
                    ];
                @endphp

                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-4">
                    @foreach($days as $day)
                        <div class="rounded-2xl border bg-gray-50 px-4 py-4 text-center shadow-sm">
                            <div class="text-sm font-bold text-gray-700">{{ $day['label'] }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($day['date'])->format('j M') }}</div>

                            <div class="mt-3 text-3xl">
                                @if($day['mood'])
                                    {{ $moodEmoji[$day['mood']] ?? '✅' }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>

                            <div class="mt-2 text-xs text-gray-600">
                                {{ $day['mood'] ? ucfirst($day['mood']) : 'No check-in' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-sm text-gray-600">
                    Tip: Tap a mood on your dashboard to fill missing days.
                </div>
            </div>

            {{-- Appointments (READ-ONLY for Child) --}}
            <div class="rounded-3xl p-6 shadow-xl bg-white/95 border border-indigo-100">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800">🗓️ Appointments</h3>
                        <p class="text-gray-600 mt-1">
                            These are appointments scheduled for you.
                        </p>
                    </div>
                    <span class="text-xs sm:text-sm px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                        This week
                    </span>
                </div>

                {{-- List Appointments --}}
                <div class="mt-6">
                    @if(empty($appointments) || $appointments->count() === 0)
                        <div class="rounded-2xl border bg-gray-50 px-4 py-4 text-gray-600">
                            No appointments scheduled for you this week.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($appointments as $appt)
                                <div class="rounded-2xl border bg-white px-5 py-4 shadow-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-extrabold text-gray-800">
                                                {{ $appt->title }}
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ \Carbon\Carbon::parse($appt->date)->format('D j M') }}
                                                @if($appt->time)
                                                    • {{ \Carbon\Carbon::parse($appt->time)->format('H:i') }}
                                                @endif
                                            </div>

                                            @if($appt->notes)
                                                <div class="text-sm text-gray-700 mt-2">
                                                    {{ $appt->notes }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Child can't edit/delete --}}
                                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-semibold">
                                            Scheduled
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-4 text-xs text-gray-500">
                    Only carers can add or change appointments.
                </div>
            </div>

            <div>
                <a href="{{ route('child.dashboard') }}" class="underline text-gray-600 hover:text-gray-900">
                    ← Back to Child Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
