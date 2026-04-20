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