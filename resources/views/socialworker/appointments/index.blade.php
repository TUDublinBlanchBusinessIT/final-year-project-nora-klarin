<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Appointments
            </h1>
            <p class="text-sm text-gray-500">
                All upcoming and past appointments
            </p>
        </div>
    </div>
</x-slot>

<div class="space-y-6">

    {{-- Calendar --}}
    @php
        $month = now()->month;
        $year = now()->year;
        $daysInMonth = now()->daysInMonth;
        $firstDay = now()->firstOfMonth()->dayOfWeekIso; // 1=Monday, 7=Sunday
        $appointmentsByDate = $appointments->groupBy(function($appt) {
            return \Carbon\Carbon::parse($appt->start_time)->format('Y-m-d');
        });
    @endphp

    <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ now()->format('F Y') }} Calendar</h2>
        <div class="grid grid-cols-7 gap-1">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                <div class="text-center text-sm font-medium text-gray-500 py-2">
                    {{ $dayName }}
                </div>
            @endforeach
            @for ($i = 1; $i < $firstDay; $i++)
                <div class="min-h-[80px] border border-gray-200 p-1"></div>
            @endfor
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                    $dayAppointments = $appointmentsByDate->get($date, collect());
                    $isToday = $date == now()->format('Y-m-d');
                @endphp
                <div class="min-h-[80px] border border-gray-200 p-1 {{ $isToday ? 'bg-indigo-50' : '' }}">
                    <div class="text-sm font-medium text-gray-900">{{ $day }}</div>
                    @foreach($dayAppointments as $appt)
                        <div class="text-xs text-gray-600 truncate">{{ $appt->title }}</div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    {{-- Upcoming --}}
    <div class="bg-white border border-gray-100 rounded-xl divide-y">

        <div class="px-5 py-4 flex justify-between items-center">
            <h2 class="text-sm font-medium text-gray-700">Upcoming</h2>
        </div>

        @php
            $upcoming = $appointments->where('start_time', '>=', now());
        @endphp

        @forelse($upcoming as $appt)
            <div class="flex items-start gap-4 px-5 py-4">
                
                {{-- Date --}}
                <div class="text-center w-12">
                    <p class="text-xs text-gray-400 uppercase">
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}
                    </p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}
                    </p>
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        {{ $appt->title }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                        @if($appt->case?->youngPerson)
                            · {{ $appt->case->youngPerson->name }}
                        @endif
                    </p>

                    @if($appt->location)
                        <p class="text-xs text-gray-400 mt-1">
                            📍 {{ $appt->location }}
                        </p>
                    @endif
                </div>

                {{-- Link --}}
                @if($appt->case)
                    <a href="{{ route('socialworker.case.show', $appt->case) }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        Case →
                    </a>
                @endif

            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">
                No upcoming appointments.
            </div>
        @endforelse
    </div>

    {{-- Past --}}
    <div class="bg-white border border-gray-100 rounded-xl divide-y">

        <div class="px-5 py-4">
            <h2 class="text-sm font-medium text-gray-700">Past</h2>
        </div>

        @php
            $past = $appointments->where('start_time', '<', now())->take(10);
        @endphp

        @forelse($past as $appt)
            <div class="px-5 py-4 text-sm text-gray-600">
                {{ $appt->title }} — 
                {{ \Carbon\Carbon::parse($appt->start_time)->format('d M Y, g:i A') }}
            </div>
        @empty
            <div class="px-5 py-6 text-center text-sm text-gray-400">
                No past appointments.
            </div>
        @endforelse
    </div>

</div>

</x-app-layout>