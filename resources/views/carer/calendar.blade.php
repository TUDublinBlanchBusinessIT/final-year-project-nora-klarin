@php
    use Carbon\Carbon;

    $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
    $calendarEnd   = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

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
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Appointments from your case file and your own events</p>
        </div>
        <a href="{{ route('carer.dashboard') }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition shrink-0">
            ← Dashboard
        </a>
    </div>
</x-slot>

@if(session('status'))
    <div class="bg-green-50 border border-green-200 rounded-[14px] px-4 py-3 text-sm text-green-800 mb-4">
        {{ session('status') }}
    </div>
@endif

{{-- ── Month header bar (prominent, like the old design) ─────────────────── --}}
<div class="bg-indigo-600 rounded-[14px] px-6 py-5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-indigo-200 text-xs font-medium uppercase tracking-wide">Your schedule</p>
        <p class="text-white text-3xl font-bold mt-0.5">{{ $monthStart->format('F Y') }}</p>
        <p class="text-indigo-200 text-sm mt-1">Click any day to add an event</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('carer.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
           class="px-4 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-sm font-medium transition">
            ← Prev
        </a>
        <a href="{{ route('carer.calendar', ['month' => now()->month, 'year' => now()->year]) }}"
           class="px-4 py-2 rounded-lg bg-white text-indigo-700 text-sm font-semibold hover:bg-indigo-50 transition shadow-sm">
            Today
        </a>
        <a href="{{ route('carer.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
           class="px-4 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-sm font-medium transition">
            Next →
        </a>
    </div>
</div>

{{-- ── Main layout ─────────────────────────────────────────────────────────── --}}
<div x-data="{
    modalOpen: false,
    selectedDate: '',
    selectedDateLabel: '',
    openDay(dateStr, label) {
        this.selectedDate = dateStr;
        this.selectedDateLabel = label;
        this.modalOpen = true;
        // Imperatively set datetime-local inputs after the modal is visible
        this.$nextTick(() => {
            const start = document.getElementById('modal_start_time');
            const end   = document.getElementById('modal_end_time');
            if (start) start.value = dateStr + 'T09:00';
            if (end)   end.value   = dateStr + 'T10:00';
        });
    }
}" class="grid grid-cols-1 xl:grid-cols-4 gap-4">

    {{-- ── Calendar grid ─────────────────────────────────────────────── --}}
    <div class="xl:col-span-3 bg-white border border-gray-200 rounded-[14px] overflow-hidden">

        {{-- Day name headers --}}
        <div class="grid grid-cols-7 border-b border-gray-100">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                <div class="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        {{-- Day cells --}}
        <div class="grid grid-cols-7 divide-x divide-y divide-gray-100">
            @foreach($days as $day)
                @php
                    $dateKey   = $day->format('Y-m-d');
                    $dayEvents = $eventsByDate[$dateKey] ?? collect();
                    $inMonth   = $day->month === $monthStart->month;
                    $isToday   = $day->isToday();
                    $label     = $day->format('l, jS F Y');
                @endphp
                <div
                    class="min-h-[130px] p-2 group cursor-pointer transition
                           {{ $inMonth ? 'bg-white hover:bg-indigo-50/40' : 'bg-slate-50 hover:bg-slate-100/60' }}
                           {{ $isToday ? 'ring-2 ring-inset ring-indigo-500' : '' }}"
                    @click="openDay('{{ $dateKey }}', '{{ $label }}')"
                >
                    {{-- Day number --}}
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="{{ $isToday
                            ? 'w-6 h-6 flex items-center justify-center rounded-full bg-indigo-600 text-white text-xs font-bold'
                            : 'text-xs font-semibold ' . ($inMonth ? 'text-gray-700' : 'text-gray-300') }}">
                            {{ $day->day }}
                        </span>
                        {{-- Add hint on hover --}}
                        <span class="opacity-0 group-hover:opacity-100 transition text-gray-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </span>
                    </div>

                    {{-- Events --}}
                    <div class="space-y-1">
                        @foreach($dayEvents->take(3) as $event)
                            @php
                                $isCaseAppt = !empty($event->case_id) || !empty($event->case_file_id);
                            @endphp
                            <div class="rounded-md px-1.5 py-1 text-[11px] leading-tight truncate border
                                {{ $isCaseAppt
                                    ? 'bg-indigo-50 border-indigo-100 text-indigo-700'
                                    : 'bg-amber-50 border-amber-100 text-amber-700' }}"
                                 @click.stop>
                                <p class="font-medium truncate">{{ $event->title ?? 'Appointment' }}</p>
                                <p class="opacity-75">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</p>
                            </div>
                        @endforeach
                        @if($dayEvents->count() > 3)
                            <p class="text-[10px] text-gray-400 pl-1">+{{ $dayEvents->count() - 3 }} more</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="px-4 py-3 border-t border-gray-100 flex items-center gap-5">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-indigo-100 border border-indigo-200"></span>
                <span class="text-xs text-gray-500">Case appointment</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-amber-100 border border-amber-200"></span>
                <span class="text-xs text-gray-500">Personal event</span>
            </div>
            <p class="text-xs text-gray-400 ml-auto">Click a day to add an event</p>
        </div>
    </div>

    {{-- ── Slim sidebar ──────────────────────────────────────────────── --}}
    <div class="space-y-3">

        {{-- Upcoming --}}
        <div class="bg-white border border-gray-200 rounded-[14px]">
            <div class="px-4 py-3 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-700">Upcoming</p>
            </div>
            @forelse($appointments->take(8) as $appt)
                <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 last:border-0 hover:bg-slate-50 transition">
                    <div class="text-center shrink-0 w-8">
                        <p class="text-[9px] text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}</p>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-xs text-gray-400">No upcoming events.</div>
            @endforelse
        </div>

        {{-- Quick add prompt --}}
        <button
            @click="openDay('{{ now()->format('Y-m-d') }}', '{{ now()->format('l, jS F Y') }}')"
            class="w-full bg-white border border-dashed border-gray-300 rounded-[14px] px-4 py-4 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/30 transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add event today
        </button>
    </div>

    {{-- ── Add event modal ───────────────────────────────────────────── --}}
    <div
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
        @click.away="modalOpen = false"
        style="display:none"
    >
        <div class="bg-white w-full max-w-md rounded-[14px] shadow-xl overflow-hidden" @click.stop>

            {{-- Modal header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Add event</p>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="selectedDateLabel"></p>
                </div>
                <button @click="modalOpen = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 transition text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('carer.calendar.store') }}" class="px-5 py-4 space-y-4">
                @csrf

                {{-- Hidden date pre-filled from clicked day --}}
                <input type="hidden" name="_selected_date" :value="selectedDate">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required autofocus
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                           placeholder="e.g. School meeting, GP appointment">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="modal_start_time" name="start_time" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">End <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="modal_end_time" name="end_time" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                           placeholder="Optional">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Link to case file <span class="text-gray-400 font-normal">(optional)</span></label>
                    <select name="case_file_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">No case link</option>
                        @foreach($caseIds as $caseId)
                            <option value="{{ $caseId }}">Case #{{ $caseId }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false"
                            class="text-sm text-gray-500 hover:text-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Save event
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}

</x-app-layout>
