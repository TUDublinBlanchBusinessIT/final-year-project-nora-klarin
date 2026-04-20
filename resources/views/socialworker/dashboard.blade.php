
<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
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
        @php
            $highRiskCases = $cases->filter(fn($c) => strtolower($c->risklevel ?? $c->risk_level ?? '') === 'high');
            $alertsCollection = isset($alerts) && is_iterable($alerts) ? collect($alerts) : collect();
            $totalAlerts = $highRiskCases->count() + $alertsCollection->count();
        @endphp
       @if($totalAlerts > 0)
            <div x-data="{ open: false }" class="relative shrink-0">

                {{-- Trigger --}}
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
                    <svg class="w-4 h-4 text-red-400 ml-1 transition-transform group-hover:text-red-600"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     @click.outside="open = false"
                     class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active alerts</p>
                        <span class="text-xs text-gray-400">{{ $totalAlerts }} total</span>
                    </div>

                    <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">

                        @foreach($highRiskCases as $hrCase)
                            <a href="{{ route('socialworker.cases.show', $hrCase) }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition group">
                                <span class="mt-1.5 w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $hrCase->youngPerson->name ?? ('Case #' . $hrCase->id) }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        High-risk case
                                        @if($hrCase->last_reviewed_at)
                                            &middot; Reviewed {{ \Carbon\Carbon::parse($hrCase->last_reviewed_at)->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-red-400 shrink-0 mt-0.5 transition"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach

                        @foreach($alertsCollection as $alert)
                            @php
                                $alertRoute    = is_array($alert) ? ($alert['route'] ?? route('socialworker.wellbeing.alerts')) : ($alert->route ?? route('socialworker.wellbeing.alerts'));
                                $alertMessage  = is_array($alert) ? ($alert['message'] ?? '') : ($alert->message ?? '');
                                $alertSeverity = is_array($alert) ? ($alert['severity'] ?? 'medium') : ($alert->severity ?? 'medium');
                                $alertType     = is_array($alert) ? ($alert['type'] ?? 'Wellbeing alert') : ($alert->type ?? 'Wellbeing alert');
                            @endphp
                            <a href="{{ $alertRoute }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-yellow-50 transition group">
                                <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0
                                    {{ $alertSeverity === 'critical' ? 'bg-red-600' : ($alertSeverity === 'high' ? 'bg-red-400' : ($alertSeverity === 'medium' ? 'bg-yellow-400' : 'bg-gray-300')) }}">
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 truncate">{{ $alertMessage }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($alertType) }}</p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-indigo-400 shrink-0 mt-0.5 transition"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach

                    </div>

                    <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100">
                        <a href="{{ route('socialworker.wellbeing.alerts') }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            View all alerts →
                        </a>
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
@php
    $getRisk     = fn($c) => strtolower($c->risklevel ?? $c->risk_level ?? '');
    $highCount   = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();
    $mediumCount = $cases->filter(fn($c) => $getRisk($c) === 'medium')->count();
    $lowCount    = $cases->filter(fn($c) => $getRisk($c) === 'low')->count();
    $unread      = $conversations->sum(fn($c) => $c->unread_count ?? 0);
    
    $upcomingAppointments = collect();
    foreach ($cases as $case) {
        if ($case->relationLoaded('appointments')) {
            $upcoming = $case->appointments
                ->where('start_time', '>=', now())
                ->where('start_time', '<=', now()->addDays(7))
                ->each(fn($a) => $a->case = $case);
            $upcomingAppointments = $upcomingAppointments->concat($upcoming);
        }
    }
    $upcomingAppointments = $upcomingAppointments->sortBy('start_time')->take(5);
@endphp
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
        <p class="text-[11px] text-red-300 mt-1 group-hover:text-red-400 transition">Filter cases →</p>
    </a>

    <a href="{{ route('socialworker.cases.index') }}?risk=medium"
       class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 hover:border-yellow-300 hover:shadow-sm transition group">
        <p class="text-xs text-yellow-600 mb-1">Medium risk</p>
        <p class="text-2xl font-bold text-gray-900">{{ $mediumCount }}</p>
        <p class="text-[11px] text-yellow-300 mt-1 group-hover:text-yellow-400 transition">Filter cases →</p>
    </a>

    <a href="{{ route('socialworker.messages.index') }}"
       class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 hover:border-indigo-300 hover:shadow-sm transition group">
        <p class="text-xs text-indigo-500 mb-1">Unread messages</p>
        <p class="text-2xl font-bold text-gray-900">{{ $unread }}</p>
        <p class="text-[11px] text-indigo-300 mt-1 group-hover:text-indigo-400 transition">Go to messages →</p>
    </a>

</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────────── --}}
<div x-data="{ tab: 'chart' }">

    <div class="flex border-b border-gray-200 mb-5 gap-0 overflow-x-auto">
        @foreach([
            ['id' => 'chart',     'label' => 'Risk overview'],
            ['id' => 'wellbeing', 'label' => 'Wellbeing trend'],
            ['id' => 'upcoming',  'label' => 'Upcoming appointments'],
        ] as $t)
            <button @click="tab='{{ $t['id'] }}'"
                    :class="tab==='{{ $t['id'] }}'
                        ? 'border-indigo-500 text-indigo-600'
                        : 'text-gray-500 border-transparent hover:text-gray-700'"
                    class="whitespace-nowrap py-2 px-4 text-sm font-medium border-b-2 transition">
                {{ $t['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Risk chart --}}
    <div x-show="tab==='chart'" x-transition
         class="bg-white border border-gray-100 rounded-xl p-6">
        <p class="text-sm font-medium text-gray-700 mb-1">Cases by risk level</p>
        <p class="text-xs text-gray-400 mb-5">Snapshot of your current caseload</p>
        <div class="max-w-md">
            <canvas id="riskChart" height="120"></canvas>
        </div>
        <div class="flex gap-6 mt-6 text-sm text-gray-500">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-300 inline-block"></span> High risk</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-300 inline-block"></span> Medium risk</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-300 inline-block"></span> Low risk</span>
        </div>
    </div>

    {{-- Wellbeing --}}
    <div x-show="tab==='wellbeing'" x-transition
         class="bg-white border border-gray-100 rounded-xl p-6 space-y-6">

        <div>
            <p class="text-sm font-medium text-gray-700 mb-4">Overall wellbeing score per child</p>
            @forelse($wellbeingData as $entry)
                @php
                    $score  = round($entry['check']->overall_score ?? 0);
                    $risk   = $entry['check']->risk_level ?? 'low';
                    $caseId = $entry['check']->case_file_id ?? 0;
                @endphp
                <div class="flex items-center gap-4 mb-3">
                    <a href="{{ route('socialworker.cases.show', $caseId) }}"
                       class="text-sm text-gray-800 w-36 truncate hover:text-indigo-600 shrink-0">
                        {{ $entry['child']->name ?? '—' }}
                    </a>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            @if(in_array($risk,['high','critical'])) bg-red-400
                            @elseif($risk==='medium') bg-yellow-400
                            @else bg-green-400 @endif"
                             style="width:{{ $score }}%"></div>
                    </div>
                    <span class="text-sm text-gray-500 w-16 text-right tabular-nums shrink-0">{{ $score }}/100</span>
                    <span class="text-xs px-2 py-0.5 rounded-full w-16 text-center shrink-0
                        @if(in_array($risk,['high','critical'])) bg-red-100 text-red-700
                        @elseif($risk==='medium') bg-yellow-100 text-yellow-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ ucfirst($risk) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">No wellbeing data available yet.</p>
            @endforelse
        </div>

        @if(count($wellbeingData) > 0)
        <div class="border-t border-gray-100 pt-6">
            <p class="text-sm font-medium text-gray-700 mb-1">Average score by domain</p>
            <p class="text-xs text-gray-400 mb-4">Across all cases — latest check per child</p>
            <div class="max-w-lg"><canvas id="domainChart" height="160"></canvas></div>
        </div>
        <div class="border-t border-gray-100 pt-6">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-medium text-gray-700">Wellbeing over time</p>
                <select id="childSelector"
                        class="border border-gray-200 rounded-lg px-3 py-1 text-xs text-gray-600 bg-white">
                    <option value="all">All children</option>
                    @foreach($wellbeingTrendData ?? [] as $item)
                        @if(count($item['scores']) > 0)
                            <option value="{{ $item['case_id'] }}">{{ $item['name'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-gray-400 mb-4">Overall wellbeing score per check</p>
            <canvas id="trendChart" height="120"></canvas>
        </div>
        @endif
    </div>
</div>


{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('riskChart').getContext('2d');


    {{-- ── Tab: Upcoming appointments ──────────────────────────────────── --}}
    <div x-show="tab==='upcoming'" x-transition
         class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100">

        <div class="px-5 py-4 flex items-center justify-between">
            <p class="text-sm font-medium text-gray-700">Next 7 days</p>
            <a href="{{ route('socialworker.appointments.index') }}"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
        </div>

        @forelse($upcomingAppointments as $appt)
            <div class="flex items-start gap-4 px-5 py-4">
                <div class="text-center shrink-0 w-10">
                    <p class="text-xs text-gray-400 uppercase">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                    <p class="text-lg font-bold text-gray-900 leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        {{ $appt->title ?? $appt->type ?? 'Appointment' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                        @if($appt->case?->youngPerson)
                            · {{ $appt->case->youngPerson->name }}
                        @endif
                    </p>
                </div>
                @if($appt->case)
                    <a href="{{ route('socialworker.cases.show', $appt->case) }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">Case →</a>
                @endif
            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">
                No appointments in the next 7 days.
            </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Risk chart ────────────────────────────────────────────────────────
    const riskCtx = document.getElementById('riskChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'bar',
            data: {
                labels: ['High risk', 'Medium risk', 'Low risk'],
                datasets: [{
                    data: [{{ $highCount }}, {{ $mediumCount }}, {{ $lowCount }}],
                    backgroundColor: ['#fca5a5', '#fde68a', '#6ee7b7'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.raw} ${ctx.raw === 1 ? 'case' : 'cases'}` } }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6' }, border: { display: false } }
                }
            }
        });
    }


    let domainChartInstance = null;
    let trendChartInstance  = null;

    const trendData = @json($wellbeingTrendData);
    const dbDomains = @json($domainScores);

    const palette = [
        '#818cf8','#34d399','#fb923c','#f472b6','#60a5fa',
        '#a78bfa','#2dd4bf','#facc15','#f87171','#4ade80'
    ];

    function buildTrendDatasets(filterCaseId) {
        return trendData
            .filter(item => filterCaseId === 'all' || String(item.case_id) === String(filterCaseId))
            .filter(item => item.scores.length > 0)
            .map((item, i) => ({
                label: item.name,
                data: item.scores.map(s => ({ x: s.date, y: s.score })),
                borderColor: palette[i % palette.length],
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 5,
            }));
    }

    function initWellbeingCharts() {

        // Domain chart
        const domainCtx = document.getElementById('domainChart');
        if (domainCtx && !domainChartInstance) {

            const domainTotals = {};
            const domainCounts = {};

            @foreach($wellbeingData as $entry)
                @foreach($entry['domainScores'] as $domainName => $score)
                    domainTotals[{{ json_encode($domainName) }}] = (domainTotals[{{ json_encode($domainName) }}] || 0) + {{ $score ?? 0 }};
                    domainCounts[{{ json_encode($domainName) }}] = (domainCounts[{{ json_encode($domainName) }}] || 0) + 1;
                @endforeach
            @endforeach

            let domainLabels, domainValues;

            if (dbDomains && dbDomains.length > 0) {
                domainLabels = dbDomains.map(d => d.domain);
                domainValues = dbDomains.map(d => Math.round(d.avg_score ?? 0));
            } else {
                domainLabels = Object.keys(domainTotals);
                domainValues = domainLabels.map(k => Math.round(domainTotals[k] / domainCounts[k]));
            }

            const barColors = domainValues.map(v =>
                v < 35 ? '#fca5a5' : v < 60 ? '#fde68a' : '#6ee7b7'
            );

            domainChartInstance = new Chart(domainCtx, {
                type: 'bar',
                data: {
                    labels: domainLabels,
                    datasets: [{
                        label: 'Avg score',
                        data: domainValues,
                        backgroundColor: barColors,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.raw}/100` } }
                    },
                    scales: {
                        x: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' }, border: { display: false } },
                        y: { grid: { display: false }, border: { display: false } }
                    }
                }
            });
        }

        // Trend chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx && !trendChartInstance) {

            const allDates = [...new Set(
                trendData.flatMap(item => item.scores.map(s => s.date))
            )].sort();

            trendChartInstance = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: allDates,
                    datasets: buildTrendDatasets('all'),
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.raw.y}/100` } }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                        y: { min: 0, max: 100, grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { stepSize: 25 } }
                    }
                }
            });

            document.getElementById('childSelector')?.addEventListener('change', function () {
                trendChartInstance.data.datasets = buildTrendDatasets(this.value);
                trendChartInstance.update();
            });
        }
    }

    // Hook into the Alpine tab buttons — fire initWellbeingCharts when the
    // wellbeing tab button is clicked, before Alpine shows the panel.
    document.querySelectorAll('[\\@click]').forEach(btn => {
        if (btn.getAttribute('@click')?.includes("'wellbeing'")) {
            btn.addEventListener('click', () => {
                // Small delay so Alpine has time to set display:block
                setTimeout(initWellbeingCharts, 50);
            });
        }
    });

    // Also init immediately in case the page loads with wellbeing tab active
    if (document.querySelector('[x-show]')?.style.display !== 'none') {
        initWellbeingCharts();
    }

});
</script>
@endpush

</x-app-layout>
