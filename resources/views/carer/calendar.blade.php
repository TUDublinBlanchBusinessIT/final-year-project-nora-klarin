@php

    use Carbon\Carbon;



    $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);

    $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);



    $days = [];

    $date = $calendarStart->copy();



    while ($date <= $calendarEnd) {

        $days[] = $date->copy();

        $date->addDay();

    }



    $prevMonth = $monthStart->copy()->subMonth();

    $nextMonth = $monthStart->copy()->addMonth();

@endphp



<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Calendar</h2>

                <p class="text-sm text-gray-500 mt-1">Appointments and personal events</p>

            </div>



            <a href="{{ route('carer.dashboard') }}"

               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow border border-gray-200">

                ← Back to dashboard

            </a>

        </div>

    </x-slot>



    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">



            @if(session('status'))

                <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">

                    {{ session('status') }}

                </div>

            @endif



            <div class="bg-gradient-to-r from-indigo-600 to-sky-500 rounded-3xl p-6 shadow-lg text-white">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <div class="text-sm opacity-90">Your schedule</div>

                        <div class="text-3xl font-bold mt-1">{{ $monthStart->format('F Y') }}</div>

                        <div class="text-sm mt-2 opacity-90">Appointments from your case file and your own events</div>

                    </div>



                    <div class="flex gap-3">

                        <a href="{{ route('carer.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"

                           class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-sm">

                            ← Prev

                        </a>



                        <a href="{{ route('carer.calendar', ['month' => now()->month, 'year' => now()->year]) }}"

                           class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">

                            Today

                        </a>



                        <a href="{{ route('carer.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"

                           class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-sm">

                            Next →

                        </a>

                    </div>

                </div>

            </div>



            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

                <div class="xl:col-span-3 rounded-3xl bg-white/95 shadow-xl border border-indigo-100 overflow-hidden">

                    <div class="grid grid-cols-7 bg-indigo-50 border-b border-indigo-100 text-sm font-semibold text-indigo-700">

                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)

                            <div class="p-4 text-center">{{ $dayName }}</div>

                        @endforeach

                    </div>



                    <div class="grid grid-cols-7">

                        @foreach($days as $day)

                            @php

                                $dateKey = $day->format('Y-m-d');

                                $dayEvents = $eventsByDate[$dateKey] ?? collect();

                                $isCurrentMonth = $day->month === $monthStart->month;

                                $isToday = $day->isToday();

                            @endphp



                            <div class="min-h-[150px] border border-gray-100 p-3 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }}">

                                <div class="flex items-center justify-between mb-2">

                                    <span class="text-sm font-semibold {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }}">

                                        {{ $day->day }}

                                    </span>



                                    @if($isToday)

                                        <span class="text-[10px] px-2 py-1 rounded-full bg-indigo-600 text-white">Today</span>

                                    @endif

                                </div>



                                <div class="space-y-2">

                                    @foreach($dayEvents->take(3) as $event)

                                        <div class="rounded-xl px-2 py-2 text-xs bg-indigo-50 border border-indigo-100">

                                            <div class="font-semibold text-indigo-800 truncate">

                                                {{ $event->title ?? 'Appointment' }}

                                            </div>

                                            <div class="text-gray-600 mt-1">

                                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}

                                            </div>

                                        </div>

                                    @endforeach



                                    @if($dayEvents->count() > 3)

                                        <div class="text-[11px] text-gray-500">

                                            +{{ $dayEvents->count() - 3 }} more

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>



                <div class="space-y-6">

                    <div class="rounded-3xl bg-white/95 shadow-lg border border-pink-100 p-6">

                        <h3 class="text-lg font-extrabold text-pink-700">Add New Event</h3>

                        <p class="text-sm text-gray-600 mt-1">Create your own carer event</p>



                        <form method="POST" action="{{ route('carer.calendar.store') }}" class="mt-5 space-y-4">

                            @csrf



                            <div>

    <label class="text-sm font-medium text-gray-700">Case File</label>

    <select name="case_file_id"

            class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"

            required>

        <option value="">Select case file</option>



        @foreach($caseIds as $caseId)

            <option value="{{ $caseId }}">Case #{{ $caseId }}</option>

        @endforeach

    </select>

</div>



                            <div>

                                <label class="text-sm font-medium text-gray-700">Title</label>

                                <input type="text" name="title" class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>

                            </div>



                            <div>

                                <label class="text-sm font-medium text-gray-700">Location</label>

                                <input type="text" name="location" class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            </div>



                            <div>

                                <label class="text-sm font-medium text-gray-700">Start</label>

                                <input type="datetime-local" name="start_time" class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>

                            </div>



                            <div>

                                <label class="text-sm font-medium text-gray-700">End</label>

                                <input type="datetime-local" name="end_time" class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>

                            </div>



                            <div>

                                <label class="text-sm font-medium text-gray-700">Notes</label>

                                <textarea name="notes" rows="3" class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>

                            </div>



                            <button class="w-full px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow">

                                Save Event

                            </button>

                        </form>

                    </div>



                    <div class="rounded-3xl bg-white/95 shadow-lg border border-blue-100 p-6">

                        <h3 class="text-lg font-extrabold text-blue-700">Upcoming</h3>

                        <div class="mt-4 space-y-3">

                            @forelse($appointments->take(5) as $appt)

                                <div class="rounded-2xl border p-4 hover:bg-gray-50">

                                    <div class="font-semibold text-gray-900">{{ $appt->title ?? 'Appointment' }}</div>

                                    <div class="text-sm text-gray-600 mt-1">

                                        {{ \Carbon\Carbon::parse($appt->start_time)->format('D d M, H:i') }}

                                    </div>

                                    @if(!empty($appt->location))

                                        <div class="text-xs text-gray-500 mt-1">📍 {{ $appt->location }}</div>

                                    @endif

                                </div>

                            @empty

                                <div class="rounded-xl border border-dashed p-6 text-center text-gray-600">

                                    No upcoming events.

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>



        </div>

    </div>

</x-app-layout>