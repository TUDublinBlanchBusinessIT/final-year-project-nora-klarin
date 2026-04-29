<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📅</span>
            <h2 class="font-semibold text-xl leading-tight">My Week</h2>
        </div>
    </x-slot>

    <div class="theme-page min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

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
            <div class="theme-card rounded-3xl p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h3 class="text-2xl font-extrabold">This Week Overview ✨</h3>
                    <span class="text-sm opacity-70">
                        {{ \Carbon\Carbon::parse($start)->format('j M') }} → {{ \Carbon\Carbon::parse($end)->format('j M') }}
                    </span>
                </div>

                <div class="mt-5 rounded-2xl border border-purple-100 bg-purple-50 px-5 py-4 text-gray-800">
                    <div class="font-bold text-purple-800">🎯 Weekly Goal</div>

                    @if($weeklyGoal)
                        <div class="mt-1">
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
                            No goal saved yet —
                            <a class="underline font-semibold" href="{{ route('child.goals') }}">pick one in My Goals</a>.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mood Timeline --}}
            <div class="theme-card rounded-3xl p-6 shadow-xl">
                <h3 class="text-xl font-extrabold">🌟 Mood Check-ins</h3>
                <p class="opacity-70 mt-1">Here’s how your week has been going.</p>

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
                                ? 'bg-indigo-50 border-indigo-200 ring-2 ring-indigo-100 text-gray-800'
                                : 'theme-card' }}">

                            <div class="text-sm font-bold {{ !empty($day['is_today']) ? 'text-indigo-700' : '' }}">
                                {{ $day['label'] }}
                            </div>

                            <div class="text-xs opacity-60">
                                {{ $day['display_date'] ?? \Carbon\Carbon::parse($day['date'])->format('j M') }}
                            </div>

                            <div class="mt-3 text-3xl">
                                @if($day['mood'])
                                    {{ $moodEmoji[$day['mood']] ?? '✅' }}
                                @else
                                    <span class="opacity-30">—</span>
                                @endif
                            </div>

                            <div class="mt-2 text-xs {{ !empty($day['is_today']) ? 'text-indigo-700 font-semibold' : 'opacity-70' }}">
                                {{ $day['mood'] ? ucfirst($day['mood']) : 'No check-in' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-sm opacity-70">
                    Tip: Tap a mood on your dashboard to fill missing days.
                </div>
            </div>

            {{-- Appointments --}}
            <div class="theme-card rounded-3xl p-6 shadow-xl">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-xl font-extrabold">🗓️ Appointments</h3>
                        <p class="opacity-70 mt-1">Here are your appointments for the next 30 days.</p>
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
                        return !empty($appt->date)
                            ? \Carbon\Carbon::parse($appt->date)->toDateString()
                            : null;
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

                <div class="mt-6 mb-3 text-lg font-bold">
                    {{ $calendarMonthLabel }}
                </div>

                <div class="theme-card rounded-3xl shadow-xl overflow-hidden">
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

                            <div class="min-h-[150px] border border-slate-200 p-3 {{ $isInRange ? 'theme-card' : 'bg-slate-100 text-slate-400' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold {{ $isInRange ? '' : 'opacity-50' }}">
                                        {{ $day->day }}
                                    </span>

                                    @if($isToday)
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-indigo-600 text-white">Today</span>
                                    @endif
                                </div>

                                <div class="space-y-2">
                                    @forelse($dayAppointments->take(3) as $appt)
                                        <div class="rounded-xl px-2 py-2 text-xs bg-indigo-50 border border-indigo-100 text-gray-800">
                                            <div class="font-semibold text-indigo-800 truncate">
                                                {{ $appt->title ?? 'Appointment' }}
                                            </div>

                                            <div class="text-gray-600 mt-1">
                                                @if(!empty($appt->time))
                                                    {{ \Carbon\Carbon::parse($appt->time)->format('H:i') }}
                                                @else
                                                    All day
                                                @endif
                                            </div>

                                            @if(!empty($appt->notes))
                                                <div class="text-gray-500 mt-1 line-clamp-2">
                                                    {{ $appt->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        @if($isInRange)
                                            <div class="text-xs opacity-50">
                                                No appointments
                                            </div>
                                        @endif
                                    @endforelse

                                    @if($dayAppointments->count() > 3)
                                        <div class="text-[11px] opacity-60">
                                            +{{ $dayAppointments->count() - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 text-xs opacity-60">
                    Only carers can add or change appointments.
                </div>
            </div>

            <div>
                <a href="{{ route('child.dashboard') }}" class="underline opacity-75 hover:opacity-100">
                    ← Back to Child Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>