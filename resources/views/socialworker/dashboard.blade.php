<x-app-layout>

@php
    $getRisk     = fn($c) => strtolower($c->risklevel ?? $c->risk_level ?? '');
    $highCount   = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();
    $mediumCount = $cases->filter(fn($c) => $getRisk($c) === 'medium')->count();
    $lowCount    = $cases->filter(fn($c) => $getRisk($c) === 'low')->count();
    $unread      = $conversations->sum(fn($c) => $c->unread_count ?? 0);

    // High-risk cases not reviewed today
    $highRiskCases = $cases->filter(function ($c) {
        if (strtolower($c->risk_level ?? $c->risklevel ?? '') !== 'high') return false;
        if ($c->last_reviewed_at && \Carbon\Carbon::parse($c->last_reviewed_at)->isToday()) return false;
        return true;
    });

    // DB alerts: unacknowledged only (acknowledged_at is the correct column per schema)
    $alertsCollection = isset($alerts) && is_iterable($alerts)
        ? collect($alerts)->filter(fn($a) => empty(is_array($a) ? ($a['reviewed_at'] ?? null) : ($a->reviewed_at ?? null)))
        : collect();

    $totalAlerts = $highRiskCases->count() + $alertsCollection->count();
@endphp

<x-slot name="header">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
                {{ Auth::user()->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ now()->format('l, jS F Y') }}
                &middot; {{ $cases->count() }} active {{ Str::plural('case', $cases->count()) }}
            </p>
        </div>

        @if($totalAlerts > 0)
            <div x-data="{ open: false }" class="relative shrink-0">
                <button @click="open = !open"
                        class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3
                               hover:bg-red-100 transition focus:outline-none focus:ring-2 focus:ring-red-300 group">
                    <span class="relative flex h-3 w-3 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-red-700 leading-none">
                            {{ $totalAlerts }} active {{ Str::plural('alert', $totalAlerts) }}
                        </p>
                        <p class="text-[11px] text-red-400 mt-0.5">Click to review</p>
                    </div>
                    <svg class="w-4 h-4 text-red-400 ml-1 transition-transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
                     style="width:340px">

                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active alerts</p>
                        <span class="text-xs text-gray-400">{{ $totalAlerts }} total</span>
                    </div>

                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">

                        {{-- High-risk cases: link to case, ✓ marks reviewed today --}}
                        @foreach($highRiskCases as $hrCase)
                            <div class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition group">
                                <span class="mt-1.5 w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('socialworker.cases.show', $hrCase) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-indigo-600 block truncate">
                                        {{ $hrCase->youngPerson->name ?? ('Case #'.$hrCase->id) }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">High-risk
                                        @if($hrCase->last_reviewed_at)
                                            &middot; Last reviewed {{ \Carbon\Carbon::parse($hrCase->last_reviewed_at)->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('socialworker.cases.markReviewed', $hrCase) }}" class="shrink-0">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Mark reviewed — removes from alerts today"
                                            class="text-gray-300 hover:text-green-500 transition text-base leading-none px-1">✓</button>
                                </form>
                            </div>
                        @endforeach

                        {{-- Wellbeing alerts from DB --}}
                        @foreach($alertsCollection as $alert)
                            @php
                                $aId    = is_array($alert) ? ($alert['id']       ?? null)     : ($alert->id       ?? null);
                                $aRoute = is_array($alert) ? ($alert['route']    ?? '#')      : ($alert->route    ?? '#');
                                $aMsg   = is_array($alert) ? ($alert['message']  ?? '')       : ($alert->message  ?? '');
                                $aSev   = is_array($alert) ? ($alert['severity'] ?? 'medium') : ($alert->severity ?? 'medium');
                                $aDot   = $aSev === 'critical' ? 'bg-red-600'
                                        : ($aSev === 'high'   ? 'bg-red-400'
                                        : ($aSev === 'medium' ? 'bg-yellow-400' : 'bg-gray-300'));
                            @endphp
                            <div class="flex items-start gap-3 px-4 py-3 hover:bg-yellow-50 transition group">
                                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $aDot }}"></span>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $aRoute }}" class="text-sm text-gray-900 hover:text-indigo-600 block truncate">
                                        {{ $aMsg }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($aSev) }}</p>
                                </div>
                                @if($aId)
                                    {{-- PATCH to acknowledge — sets acknowledged_at in alerts table --}}
                                    <form method="POST" action="{{ route('socialworker.alerts.acknowledge', $aId) }}" class="shrink-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Acknowledge — removes from alerts"
                                                class="text-gray-300 hover:text-green-500 transition text-base leading-none px-1">✓</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach

                    </div>

                    <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-[11px] text-gray-400">✓ to acknowledge &amp; remove</p>
                        <a href="{{ route('socialworker.wellbeing.alerts') }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">All →</a>
                    </div>
                </div>
            </div>

        @else
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3 shrink-0">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-xs font-medium text-green-700">No active alerts</p>
            </div>
        @endif
    </div>
</x-slot>

{{-- ── Stat cards ────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('socialworker.cases.index') }}"
       class="bg-white border border-gray-100 rounded-xl p-4 hover:border-indigo-200 hover:shadow-sm transition group">
        <p class="text-xs text-gray-400 mb-1 group-hover:text-indigo-500 transition">Total cases</p>
        <p class="text-2xl font-bold text-gray-900">{{ $cases->count() }}</p>
        <p class="text-[11px] text-gray-300 mt-1 group-hover:text-indigo-300 transition">View all →</p>
    </a>
    <a href="{{ route('socialworker.cases.index') }}?risk=high"
       class="bg-red-50 border border-red-100 rounded-xl p-4 hover:border-red-300 hover:shadow-sm transition group">
        <p class="text-xs text-red-500 mb-1">High risk</p>
        <p class="text-2xl font-bold text-gray-900">{{ $highCount }}</p>
        <p class="text-[11px] text-red-300 mt-1 group-hover:text-red-400 transition">Filter →</p>
    </a>
    <a href="{{ route('socialworker.cases.index') }}?risk=medium"
       class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 hover:border-yellow-300 hover:shadow-sm transition group">
        <p class="text-xs text-yellow-600 mb-1">Medium risk</p>
        <p class="text-2xl font-bold text-gray-900">{{ $mediumCount }}</p>
        <p class="text-[11px] text-yellow-300 mt-1 group-hover:text-yellow-400 transition">Filter →</p>
    </a>
    <a href="{{ route('socialworker.messages.index') }}"
       class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 hover:border-indigo-300 hover:shadow-sm transition group">
        <p class="text-xs text-indigo-500 mb-1">Unread messages</p>
        <p class="text-2xl font-bold text-gray-900">{{ $unread }}</p>
        <p class="text-[11px] text-indigo-300 mt-1 group-hover:text-indigo-400 transition">Go to messages →</p>
    </a>
</div>

{{-- ── Tabs: Appointments | Wellbeing ─────────────────────────────────────── --}}
<div x-data="{ tab: 'appointments' }" class="mb-6">

    <div class="flex border-b border-gray-200 mb-4">
        <button @click="tab='appointments'"
                :class="tab==='appointments' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
            Appointments & Calendar
        </button>
        <button @click="tab='wellbeing'"
                :class="tab==='wellbeing' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
            Wellbeing overview
        </button>
    </div>

    {{-- Appointments tab --}}
    <div x-show="tab==='appointments'" class="space-y-4">

        {{-- Upcoming appointments list --}}
        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100">
            <div class="px-5 py-3 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700">Next 7 days</p>
                <a href="{{ route('socialworker.appointments.index') }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
            </div>
            @forelse($upcomingAppointments as $appt)
                <div class="flex items-center gap-4 px-5 py-3">
                    <div class="text-center shrink-0 w-10">
                        <p class="text-[10px] text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                        <p class="text-lg font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                            @if($appt->case?->youngPerson) &middot; {{ $appt->case->youngPerson->name }} @endif
                            @if($appt->location) &middot; {{ $appt->location }} @endif
                        </p>
                    </div>
                    @if($appt->case)
                        <a href="{{ route('socialworker.cases.show', $appt->case) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">Case →</a>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">No appointments in the next 7 days.</div>
            @endforelse
        </div>

        {{-- Weekly calendar preview --}}
        @php
            $calendarDays = collect();
            for ($i = 0; $i < 7; $i++) {
                $day = now()->startOfDay()->addDays($i);
                $appointmentsForDay = $upcomingAppointments->filter(fn($appt) =>
                    \Carbon\Carbon::parse($appt->start_time)->isSameDay($day)
                );
                $calendarDays->push([
                    'label' => $day->format('D j'),
                    'appointments' => $appointmentsForDay,
                ]);
            }
        @endphp

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Weekly calendar</p>
                    <p class="text-xs text-gray-400">Next 7 days planned appointments</p>
                </div>
                <a href="{{ route('socialworker.appointments.index') }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all appointments →</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($calendarDays as $day)
                    <div class="rounded-2xl border border-gray-100 p-3 bg-slate-50">
                        <p class="text-xs font-semibold text-slate-500 mb-2">{{ $day['label'] }}</p>
                        @if($day['appointments']->isEmpty())
                            <p class="text-[11px] text-slate-400">No appointments</p>
                        @else
                            <div class="space-y-2">
                                @foreach($day['appointments']->take(2) as $appt)
                                    <div class="rounded-xl bg-white p-2 border border-gray-100">
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}</p>
                                    </div>
                                @endforeach
                                @if($day['appointments']->count() > 2)
                                    <p class="text-[11px] text-slate-400">+ {{ $day['appointments']->count() - 2 }} more</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Schedule appointment suggestions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $scheduleSuggestions = $cases->map(function ($case) {
                    $hasAppt = $case->appointments
                        ->filter(fn($a) => $a->start_time >= now() && $a->start_time <= now()->addDays(30))
                        ->isNotEmpty();
                    
                    if ($hasAppt) return null;
                    
                    $risk = $case->risk_level ?? 'low';
                    $daysUntil = match($risk) {
                        'high' => 7,
                        'medium' => 14,
                        default => 30,
                    };
                    
                    return [
                        'case' => $case,
                        'risk' => $risk,
                        'daysUntil' => $daysUntil,
                        'color' => match($risk) {
                            'high' => 'border-red-200 bg-red-50',
                            'medium' => 'border-yellow-200 bg-yellow-50',
                            default => 'border-green-200 bg-green-50',
                        },
                        'textColor' => match($risk) {
                            'high' => 'text-red-600',
                            'medium' => 'text-yellow-600',
                            default => 'text-green-600',
                        },
                    ];
                })->filter()->take(2);
            @endphp

            @foreach($scheduleSuggestions as $sug)
                <div class="border-2 rounded-xl p-4 {{ $sug['color'] }}">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $sug['case']->youngPerson->name ?? 'Case #' . $sug['case']->id }}</p>
                            <p class="text-xs {{ $sug['textColor'] }} font-semibold mt-0.5">{{ ucfirst($sug['risk']) }} risk — Due in {{ $sug['daysUntil'] }} days</p>
                        </div>
                    </div>
                    <a href="{{ route('socialworker.appointments.create', $sug['case']) }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        Schedule →
                    </a>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Wellbeing tab --}}
    <div x-show="tab==='wellbeing'" class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100">
        <div class="px-5 py-3">
            <p class="text-sm font-medium text-gray-700">Latest check per child — domain breakdown</p>
        </div>
        @forelse($wellbeingData as $entry)
            @php
                $child  = $entry['child'];
                $check  = $entry['check'];
                $caseId = $check->case_file_id ?? 0;
                $name   = $child->name ?? ('Case #'.$caseId);
                $score  = round($check->overall_score ?? 0);
                $wRisk  = $check->risk_level ?? 'low';
                $badgeCls = in_array($wRisk,['high','critical']) ? 'bg-red-100 text-red-700'
                          : ($wRisk === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
            @endphp
            <div class="px-5 py-4">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('socialworker.cases.show', $caseId) }}"
                       class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $name }}</a>
                    <div class="flex items-center gap-2">
                        <span class="text-xs {{ $badgeCls }} px-2 py-0.5 rounded-full">{{ ucfirst($wRisk) }}</span>
                        <span class="text-xs text-gray-400">{{ $score }}/100</span>
                        <span class="text-xs text-gray-300">{{ $check->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
                {{-- Domain mini-bars --}}
                @if($entry['domainScores']->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-1.5">
                        @foreach($entry['domainScores'] as $domain => $ds)
                            @php
                                $pct = round($ds);
                                $barC = $pct < 35 ? 'bg-red-300' : ($pct < 60 ? 'bg-yellow-300' : 'bg-green-300');
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-[10px] text-gray-500 truncate">{{ $domain }}</span>
                                    <span class="text-[10px] text-gray-400 ml-1 shrink-0">{{ $pct }}</span>
                                </div>
                                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $barC }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400">No domain scores recorded.</p>
                @endif
            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">No wellbeing checks completed yet.</div>
        @endforelse

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 px-5 py-4">
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <p class="text-sm font-medium text-gray-700 mb-0.5">Cases by risk</p>
                <p class="text-xs text-gray-400 mb-4">Current caseload</p>
                <canvas id="riskChart" height="180"></canvas>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <p class="text-sm font-medium text-gray-700 mb-0.5">Wellbeing over time</p>
                <p class="text-xs text-gray-400 mb-4">Overall score per check</p>
                <canvas id="trendChart" height="180"></canvas>
                <p id="trendEmpty" class="hidden text-xs text-gray-400 text-center pt-4">No check data yet.</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-0.5">Domain trends</p>
                        <p class="text-xs text-gray-400">Average wellbeing by domain</p>
                    </div>
                </div>
                <canvas id="domainChart" height="180"></canvas>
                <div class="mt-4 space-y-3">
                    @if(isset($domainWeekChanges) && $domainWeekChanges->isNotEmpty())
                        @foreach($domainWeekChanges as $change)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-700">{{ $change['label'] }}</span>
                                <span class="font-semibold {{ $change['change'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $change['change'] < 0 ? '↓' : '↑' }}
                                    {{ $change['change'] > 0 ? '+' : '' }}{{ $change['change'] }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400">No weekly domain changes available yet.</p>
                    @endif
                </div>
            </div>
        </div>

        @if(count($wellbeingData) > 0)
            <div class="bg-white border border-gray-100 rounded-xl p-5 mb-6">
                <p class="text-sm font-medium text-gray-700 mb-4">Overall wellbeing score per child</p>
                @foreach($wellbeingData as $entry)
                    @php
                        $score    = round($entry['check']->overall_score ?? 0);
                        $wRisk    = $entry['check']->risk_level ?? 'low';
                        $wCaseId  = $entry['check']->case_file_id ?? 0;
                        $wName    = $entry['child']->name ?? ('Case #'.$wCaseId);
                        $barCls   = in_array($wRisk,['high','critical']) ? 'bg-red-400' : ($wRisk === 'medium' ? 'bg-yellow-400' : 'bg-green-400');
                        $badgeCls = in_array($wRisk,['high','critical']) ? 'bg-red-100 text-red-700' : ($wRisk === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
                    @endphp
                    <div class="flex items-center gap-4 mb-3">
                        <a href="{{ route('socialworker.cases.show', $wCaseId) }}"
                           class="text-sm text-gray-700 w-40 truncate hover:text-indigo-600 shrink-0" title="{{ $wName }}">
                            {{ $wName }}
                        </a>
                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $barCls }}" style="width:{{ $score }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500 w-16 text-right tabular-nums shrink-0">{{ $score }}/100</span>
                        <span class="text-xs px-2 py-0.5 rounded-full w-16 text-center shrink-0 {{ $badgeCls }}">{{ ucfirst($wRisk) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartData = {!! json_encode($chartData ?? [
        'trendsOverTime' => ['labels' => [], 'datasets' => []],
        'trendsByDomain' => ['labels' => [], 'datasets' => []],
        'casesByRisk' => ['labels' => [], 'datasets' => []],
    ]) !!};

    const riskEl = document.getElementById('riskChart');
    if (riskEl && chartData.casesByRisk.labels.length > 0) {
        new Chart(riskEl, {
            type: 'bar',
            data: chartData.casesByRisk,
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    const trendEl = document.getElementById('trendChart');
    const trendEmpty = document.getElementById('trendEmpty');
    if (trendEl) {
        if (chartData.trendsOverTime.labels.length > 0 && chartData.trendsOverTime.datasets.length > 0) {
            new Chart(trendEl, {
                type: 'line',
                data: chartData.trendsOverTime,
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: { min: 0, max: 100, grid: { color: '#f3f4f6' } }
                    }
                }
            });
        } else {
            trendEl.style.display = 'none';
            trendEmpty?.classList.remove('hidden');
        }
    }

    const domainEl = document.getElementById('domainChart');
    if (domainEl) {
        if (chartData.trendsByDomain.labels.length > 0 && chartData.trendsByDomain.datasets.length > 0) {
            new Chart(domainEl, {
                type: 'bar',
                data: chartData.trendsByDomain,
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' } }
                    }
                }
            });
        } else {
            domainEl.style.display = 'none';
        }
    }

</script>
@endpush

</x-app-layout>
