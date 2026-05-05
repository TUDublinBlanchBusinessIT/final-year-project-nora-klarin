@php
    use Carbon\Carbon;

    $now         = Carbon::now();
    $monthStart  = $monthStart ?? $now->copy()->startOfMonth();
    $monthEnd    = $monthEnd ?? $now->copy()->endOfMonth();
    $prevMonth   = $monthStart->copy()->subMonth();
    $nextMonth   = $monthStart->copy()->addMonth();

    $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
    $calendarEnd   = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

    $days = [];
    $d = $calendarStart->copy();
    while ($d <= $calendarEnd) {
        $days[] = $d->copy();
        $d->addDay();
    }

    $appointmentsByDate = $appointments->groupBy(
        fn($a) => Carbon::parse($a->start_time)->format('Y-m-d')
    );

    $upcoming = $appointments->filter(fn($a) => Carbon::parse($a->start_time)->gte($now))->sortBy('start_time');
    $past     = $appointments->filter(fn($a) => Carbon::parse($a->start_time)->lt($now))->sortByDesc('start_time')->take(10);
@endphp

<x-app-layout>

<x-slot name="header">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Calendar</h1>
            <p class="text-sm text-gray-500">Manage appointments and case visits</p>
        </div>
    </div>
</x-slot>

<div class="bg-indigo-600 rounded-[14px] px-6 py-5 mb-5 flex justify-between items-center">
    <div>
        <p class="text-indigo-200 text-xs uppercase">Schedule overview</p>
        <p class="text-white text-3xl font-bold">{{ $monthStart->format('F Y') }}</p>
        <p class="text-indigo-200 text-sm">
            {{ $appointments->count() }} total · {{ $upcoming->count() }} upcoming
        </p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('socialworker.appointments.index', ['month'=>$prevMonth->month,'year'=>$prevMonth->year]) }}"
           class="px-4 py-2 bg-white/20 text-white rounded-lg">← Prev</a>

        <a href="{{ route('socialworker.appointments.index', ['month'=>now()->month,'year'=>now()->year]) }}"
           class="px-4 py-2 bg-white text-indigo-700 rounded-lg font-semibold">Today</a>

        <a href="{{ route('socialworker.appointments.index', ['month'=>$nextMonth->month,'year'=>$nextMonth->year]) }}"
           class="px-4 py-2 bg-white/20 text-white rounded-lg">Next →</a>
    </div>
</div>

<div x-data="{
    modalOpen:false,
    selectedDate:'',
    selectedLabel:'',
    openDay(date,label){
        this.selectedDate=date;
        this.selectedLabel=label;
        this.modalOpen=true;

        this.$nextTick(()=>{
            document.getElementById('start_time').value = date + 'T09:00';
            document.getElementById('end_time').value = date + 'T10:00';
        });
    }
}" class="grid xl:grid-cols-4 gap-4">

{{-- 🗓 Calendar --}}
<div class="xl:col-span-3 bg-white rounded-[14px] border overflow-hidden">

    {{-- Days --}}
    <div class="grid grid-cols-7 border-b">
        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
            <div class="py-3 text-center text-xs font-semibold text-gray-500">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Cells --}}
    <div class="grid grid-cols-7 divide-x divide-y">
        @foreach($days as $day)
            @php
                $dateKey = $day->format('Y-m-d');
                $events = $appointmentsByDate[$dateKey] ?? collect();
                $inMonth = $day->month === $monthStart->month;
                $isToday = $day->isToday();
            @endphp

            <div class="p-2 min-h-[130px] cursor-pointer {{ $inMonth?'bg-white':'bg-slate-50' }} hover:bg-indigo-50"
                 @click="openDay('{{ $dateKey }}','{{ $day->format('l jS F') }}')">

                <div class="mb-1">
                    <span class="{{ $isToday?'bg-indigo-600 text-white rounded-full w-6 h-6 flex items-center justify-center':'text-xs' }}">
                        {{ $day->day }}
                    </span>
                </div>

                <div class="space-y-1">
                    @foreach($events->take(3) as $appt)
                        @php
                            $risk = strtolower($appt->case?->risk_level ?? '');
                            $bg = match($risk){
                                'high'=>'bg-red-50 text-red-700',
                                'medium'=>'bg-amber-50 text-amber-700',
                                default=>'bg-indigo-50 text-indigo-700'
                            };
                        @endphp
                        <div class="text-[11px] px-1 py-1 rounded border {{ $bg }}">
                            <p class="truncate font-medium">{{ $appt->title }}</p>
                            <p class="opacity-70">{{ Carbon::parse($appt->start_time)->format('H:i') }}</p>
                        </div>
                    @endforeach

                    @if($events->count()>3)
                        <p class="text-[10px] text-gray-400">+{{ $events->count()-3 }} more</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- 📋 Sidebar --}}
<div class="space-y-3">

    {{-- Upcoming --}}
    <div class="bg-white border rounded-[14px]">
        <div class="px-4 py-3 border-b font-semibold text-sm">Upcoming</div>

        @forelse($upcoming->take(8) as $appt)
            <div class="flex gap-3 px-4 py-2 border-b text-sm hover:bg-gray-50">
                <div class="w-8 text-center">
                    <p class="text-xs">{{ Carbon::parse($appt->start_time)->format('M') }}</p>
                    <p class="font-bold">{{ Carbon::parse($appt->start_time)->format('d') }}</p>
                </div>

                <div class="flex-1">
                    <p class="font-medium">{{ $appt->title }}</p>
                    <p class="text-xs text-gray-400">
                        {{ Carbon::parse($appt->start_time)->format('g:i A') }}
                        @if($appt->case?->youngPerson) · {{ $appt->case->youngPerson->name }} @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-400 text-sm">No upcoming</div>
        @endforelse
    </div>

</div>

<div x-show="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-[14px] w-full max-w-md p-5">

        <h2 class="font-semibold text-lg mb-2">New Appointment</h2>
        <p class="text-xs text-gray-400 mb-4" x-text="selectedLabel"></p>

        <form method="POST" action="{{ route('socialworker.appointments.store') }}">
            @csrf

            <input type="hidden" name="date" :value="selectedDate">
            <select name="case_file_id" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3">

                <option value="">Select case</option>

                @foreach($cases as $case)
                    <option value="{{ $case->id }}">
                        Case #{{ $case->case_reference }} · {{ $case->youngPerson->name ?? 'No young person' }} 
                    </option>
                @endforeach
            </select>
            <input name="title" placeholder="Title" class="w-full border p-2 mb-3 rounded" required>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <input id="start_time" type="datetime-local" name="start_time" class="border p-2 rounded">
                <input id="end_time" type="datetime-local" name="end_time" class="border p-2 rounded">
            </div>

            <input name="location" placeholder="Location" class="w-full border p-2 mb-3 rounded">

            <div class="flex justify-end gap-2">
                <button type="button" @click="modalOpen=false" class="text-gray-500">Cancel</button>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>

    </div>
</div>

</div>

</x-app-layout>