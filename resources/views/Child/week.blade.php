<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📅</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Week</h2>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-yellow-50 via-pink-50 to-blue-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

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
                            Your goal:
                            <span class="font-extrabold">
                                {{ $goalLabel ?? $weeklyGoal->goal_key }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Saved for week starting {{ $weeklyGoal->week_start }}
                        </div>
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
                        <div class="rounded-2xl px-4 py-4 text-center shadow-sm border
                            {{ !empty($day['is_today'])
                                ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-100'
                                : 'bg-gray-50 border-gray-200' }}">

                            <div class="text-sm font-bold {{ !empty($day['is_today']) ? 'text-indigo-700' : 'text-gray-700' }}">
                                {{ $day['label'] }}
                            </div>

                            <div class="text-xs {{ !empty($day['is_today']) ? 'text-indigo-500' : 'text-gray-500' }}">
                                {{ $day['display_date'] ?? \Carbon\Carbon::parse($day['date'])->format('j M') }}
                            </div>

                            <div class="mt-3 text-3xl">
                                @if($day['mood'])
                                    {{ $moodEmoji[$day['mood']] ?? '✅' }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>

                            <div class="mt-2 text-xs {{ !empty($day['is_today']) ? 'text-indigo-700 font-semibold' : 'text-gray-600' }}">
                                {{ $day['mood'] ? ucfirst($day['mood']) : 'No check-in' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-sm text-gray-600">
                    Tip: Tap a mood on your dashboard to fill missing days.
                </div>
            </div>

 {{-- Appointments: 30-Day Calendar --}}
<div class="rounded-3xl p-6 shadow-xl bg-white/95 border border-indigo-100">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="text-xl font-extrabold text-gray-800">🗓️ Appointments</h3>
            <p class="text-gray-600 mt-1">Here are your appointments for the next 30 days.</p>
        </div>

        <span class="text-xs sm:text-sm px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
            Next 30 days
        </span>
    </div>

    @php
        $today = \Carbon\Carbon::today();
        $endDate = $today->copy()->addDays(29);

        $calendarStart = $today->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $calendarEnd = $endDate->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $calendarDays = [];
        $date = $calendarStart->copy();

        while ($date <= $calendarEnd) {
            $calendarDays[] = $date->copy();
            $date->addDay();
        }

        $appointmentsByDate = collect($appointments ?? [])->groupBy(function ($appt) {
            if (!empty($appt->start_time)) {
                return \Carbon\Carbon::parse($appt->start_time)->toDateString();
            }

            if (!empty($appt->date)) {
                return \Carbon\Carbon::parse($appt->date)->toDateString();
            }

            return null;
        });

        $startMonthLabel = $calendarStart->format('F');
        $endMonthLabel = $calendarEnd->format('F');
        $startYearLabel = $calendarStart->format('Y');
        $endYearLabel = $calendarEnd->format('Y');

        if ($startMonthLabel === $endMonthLabel && $startYearLabel === $endYearLabel) {
            $calendarMonthLabel = $calendarStart->format('F Y');
        } elseif ($startYearLabel === $endYearLabel) {
            $calendarMonthLabel = $calendarStart->format('F') . ' – ' . $calendarEnd->format('F Y');
        } else {
            $calendarMonthLabel = $calendarStart->format('F Y') . ' – ' . $calendarEnd->format('F Y');
        }
    @endphp

    <div class="mt-6 mb-3 text-lg font-bold text-gray-800">
        {{ $calendarMonthLabel }}
    </div>

    <div class="rounded-3xl bg-white/95 shadow-xl border border-indigo-100 overflow-hidden">
        <div class="grid grid-cols-7 bg-indigo-50 border-b border-indigo-100 text-sm font-semibold text-indigo-700">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                <div class="p-4 text-center">{{ $dayName }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7">
            @foreach($calendarDays as $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayAppointments = $appointmentsByDate[$dateKey] ?? collect();
                    $isInRange = $day->betweenIncluded($today, $endDate);
                    $isToday = $day->isToday();
                @endphp

                <div class="min-h-[150px] border border-gray-100 p-3 {{ $isInRange ? 'bg-white' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold {{ $isInRange ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ $day->day }}
                        </span>

                        @if($isToday)
                            <span class="text-[10px] px-2 py-1 rounded-full bg-indigo-600 text-white">Today</span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @forelse($dayAppointments->take(3) as $appt)
                            <div class="rounded-xl px-2 py-2 text-xs bg-indigo-50 border border-indigo-100">
                                <div class="font-semibold text-indigo-800 truncate">
                                    {{ $appt->title ?? 'Appointment' }}
                                </div>

                                <div class="text-gray-600 mt-1">
                                    @if(!empty($appt->start_time))
                                        {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}
                                    @elseif(!empty($appt->time))
                                        {{ \Carbon\Carbon::parse($appt->time)->format('H:i') }}
                                    @else
                                        All day
                                    @endif
                                </div>
                            </div>
                        @empty
                            @if($isInRange)
                                <div class="text-xs text-gray-400">
                                    No appointments
                                </div>
                            @endif
                        @endforelse

                        @if($dayAppointments->count() > 3)
                            <div class="text-[11px] text-gray-500">
                                +{{ $dayAppointments->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
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