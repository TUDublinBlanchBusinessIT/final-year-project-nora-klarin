<div x-show="tab === 'wellbeing'" x-transition class="bg-white p-6 rounded-xl shadow space-y-6">

    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Wellbeing Checks</h3>
    </div>

    {{-- ── Unacknowledged alert banner ──────────────────────────── --}}
    @php
        $openAlerts = $case->wellbeingChecks
            ->flatMap(fn($c) => $c->alerts ?? collect())
            ->whereNull('acknowledged_at')
            ->sortByDesc('created_at');
    @endphp

    @if($openAlerts->isNotEmpty())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 space-y-2">
            <p class="text-sm font-semibold text-red-700">
                ⚠ {{ $openAlerts->count() }} unacknowledged alert{{ $openAlerts->count() > 1 ? 's' : '' }}
            </p>
            @foreach($openAlerts->take(3) as $alert)
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="inline-block text-xs font-bold uppercase px-2 py-0.5 rounded-full mr-2
                            {{ $alert->severity === 'critical' ? 'bg-red-600 text-white' :
                               ($alert->severity === 'high'    ? 'bg-red-100 text-red-700' :
                               ($alert->severity === 'medium'  ? 'bg-amber-100 text-amber-700' :
                                                                  'bg-gray-100 text-gray-600')) }}">
                            {{ ucfirst($alert->severity) }}
                        </span>
                        <span class="text-sm text-red-700">{{ $alert->message }}</span>
                    </div>
                    <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST" class="flex-shrink-0">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="text-xs text-red-500 hover:text-red-700 font-medium whitespace-nowrap transition">
                            Acknowledge
                        </button>
                    </form>
                </div>
            @endforeach
            @if($openAlerts->count() > 3)
                <p class="text-xs text-red-500 font-medium">
                    + {{ $openAlerts->count() - 3 }} more alert{{ $openAlerts->count() - 3 > 1 ? 's' : '' }}
                </p>
            @endif
        </div>
    @endif

    @if($case->wellbeingChecks->isEmpty())
        <p class="text-gray-500 text-sm">No wellbeing checks submitted yet.</p>
    @else

        {{-- ── Check history table ──────────────────────────────── --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm border rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Type</th>
                        <th class="p-3 text-center">Wellbeing</th>
                        <th class="p-3 text-center">Risk</th>
                        <th class="p-3 text-center">Alerts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($case->wellbeingChecks->sortByDesc('completed_at') as $check)
                        @php
                            // Derive risk classification from overall_risk_score
                            $risk = match(true) {
                                ($check->overall_risk_score ?? 0) >= 250 => 'critical',
                                ($check->overall_risk_score ?? 0) >= 150 => 'high',
                                ($check->overall_risk_score ?? 0) >= 80  => 'moderate',
                                default                                   => 'low',
                            };
                            $riskClass = match($risk) {
                                'critical' => 'bg-red-600 text-white',
                                'high'     => 'bg-red-100 text-red-700',
                                'moderate' => 'bg-amber-100 text-amber-700',
                                default    => 'bg-green-100 text-green-700',
                            };
                            $alertCount = $check->alerts?->whereNull('acknowledged_at')->count() ?? 0;
                        @endphp
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-3 text-gray-700">
                                {{ \Carbon\Carbon::parse($check->completed_at)->format('d M Y') }}
                            </td>
                            <td class="p-3">
                                <span class="text-xs text-gray-500 capitalize">{{ $check->check_type }}</span>
                            </td>
                            <td class="p-3 text-center font-semibold text-gray-800">
                                {{ $check->overall_wb_score !== null ? round($check->overall_wb_score, 1) : '—' }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $riskClass }}">
                                    {{ ucfirst($risk) }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                @if($alertCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        {{ $alertCount }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Chart ─────────────────────────────────────────────── --}}
        <div class="flex justify-between items-center pt-2">
            <h4 class="text-base font-semibold text-gray-700">Wellbeing Trend</h4>
            <select id="datasetSelector"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="overall">Overall Score</option>
                <option value="domains">Domain Breakdown</option>
            </select>
        </div>

        <div>
            <canvas id="wellbeingTrend" height="110"></canvas>
        </div>

    @endif

</div>

{{-- ── Chart.js ────────────────────────────────────────────────────── --}}
@once
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endonce
@php
$chartData = $case->wellbeingChecks
    ->sortBy('completed_at')
    ->filter(fn($c) => $c->completed_at)
    ->map(function ($c) {
        return [
            'label' => \Carbon\Carbon::parse($c->completed_at)->format('M d'),
            'overall_wb' => $c->overall_wb_score,
            'domain_scores' => $c->domainScores
                ->mapWithKeys(function ($ds) {
                    return [
                        strtolower($ds->domain->name ?? '') => $ds->average_score
                    ];
                })
                ->toArray(),
        ];
    })
    ->values();
@endphp
<script>
(function () {
    const checks = @json($chartData);
        $case->wellbeingChecks
            ->sortBy('completed_at')
            ->filter(fn($c) => $c->completed_at)
            ->map(fn($c) => [
                'label'          => \Carbon\Carbon::parse($c->completed_at)->format('M d'),
                'overall_wb'     => $c->overall_wb_score,
                'domain_scores'  => $c->domainScores->mapWithKeys(
                    fn($ds) => [strtolower($ds->domain->name ?? '') => $ds->average_score]
                ),
            ])
            ->values()
    );

    const labels      = checks.map(c => c.label)
    const overallData = checks.map(c => c.overall_wb ?? null)

    // Build per-domain arrays
    const domainNames  = ['emotional', 'behavioural', 'social', 'physical', 'education', 'safety']
    const domainColors = {
        emotional:   '#ec4899',
        behavioural: '#f59e0b',
        social:      '#3b82f6',
        physical:    '#22c55e',
        education:   '#8b5cf6',
        safety:      '#ef4444',
    }

    const domainDatasets = domainNames.map(name => ({
        label:       name.charAt(0).toUpperCase() + name.slice(1),
        data:        checks.map(c => c.domain_scores[name] ?? null),
        borderColor: domainColors[name],
        backgroundColor: domainColors[name] + '18',
        borderWidth: 2.5,
        tension:     0.35,
        spanGaps:    true,
    }))

    const ctx = document.getElementById('wellbeingTrend')
    if (!ctx) return

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label:           'Overall Wellbeing',
                data:            overallData,
                borderColor:     '#6366f1',
                backgroundColor: '#6366f118',
                borderWidth:     3,
                tension:         0.35,
                spanGaps:        true,
                pointRadius:     4,
                pointHoverRadius:6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, font: { size: 12 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y !== null ? Math.round(ctx.parsed.y) : '—'}`
                    }
                }
            },
            scales: {
                y: {
                    min: 0, max: 100,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 11 },
                        callback: v => v + '%'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    })

    document.getElementById('datasetSelector').addEventListener('change', function () {
        chart.data.datasets = this.value === 'overall'
            ? [{
                label:           'Overall Wellbeing',
                data:            overallData,
                borderColor:     '#6366f1',
                backgroundColor: '#6366f118',
                borderWidth:     3,
                tension:         0.35,
                spanGaps:        true,
                pointRadius:     4,
                pointHoverRadius:6,
              }]
            : domainDatasets
        chart.update()
    })
})()
</script>
