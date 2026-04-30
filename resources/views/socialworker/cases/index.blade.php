
<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between" x-data="{ risk: '{{ request('risk', 'all') }}' }">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Cases</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $cases->count() }} {{ Str::plural('case', $cases->count()) }} assigned to you
            </p>
        </div>
    </div>
</x-slot>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

{{-- Summary pills --}}
@php
    $getRisk     = fn($c) => strtolower($c->risklevel ?? $c->risk_level ?? '');
    $highCount   = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();
    $mediumCount = $cases->filter(fn($c) => $getRisk($c) === 'medium')->count();
    $lowCount    = $cases->filter(fn($c) => $getRisk($c) === 'low')->count();
@endphp

<div class="flex gap-2 mb-4">
    <button onclick="filterByRisk('all')"
            class="px-3 py-1 rounded-full text-xs font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
        All <span class="ml-1 font-bold">{{ $cases->count() }}</span>
    </button>
    <button onclick="filterByRisk('high')"
            class="px-3 py-1 rounded-full text-xs font-medium border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
        High <span class="ml-1 font-bold">{{ $highCount }}</span>
    </button>
    <button onclick="filterByRisk('medium')"
            class="px-3 py-1 rounded-full text-xs font-medium border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition">
        Medium <span class="ml-1 font-bold">{{ $mediumCount }}</span>
    </button>
    <button onclick="filterByRisk('low')"
            class="px-3 py-1 rounded-full text-xs font-medium border border-green-200 text-green-700 bg-green-50 hover:bg-green-100 transition">
        Low <span class="ml-1 font-bold">{{ $lowCount }}</span>
    </button>
</div>

{{-- Cases table --}}
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
    @if($cases->isEmpty())
        <div class="p-16 text-center">
            <p class="text-4xl mb-3">📁</p>
            <p class="text-gray-500 text-sm">No cases assigned to you yet.</p>
        </div>
    @else
        <table class="min-w-full divide-y divide-gray-100" id="casesTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Reference</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Young person</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">DOB / Age</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Risk</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Last reviewed</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Wellbeing</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="caseRows">
                @foreach($cases as $case)
                    @php
                        $risk   = strtolower($case->risklevel ?? $case->risk_level ?? '');
                        $status = strtolower($case->status ?? '');
                        $dob    = $case->youngPerson?->dob;

                        // Latest wellbeing score
                        $latestWellbeing = null;
                        if ($case->relationLoaded('wellbeingChecks')) {
                            $latestWellbeing = $case->wellbeingChecks->sortByDesc('created_at')->first();
                        }
                        $wScore = $latestWellbeing ? round($latestWellbeing->overall_score ?? 0) : null;
                    @endphp

                    <tr class="hover:bg-gray-50 transition case-row"
                        data-risk="{{ $risk }}"
                        data-status="{{ $status }}">

                        <td class="px-5 py-3.5 text-sm font-medium text-gray-900">
                            {{ $case->case_reference ?? ('Case #' . $case->id) }}
                        </td>

                        <td class="px-5 py-3.5 text-sm text-gray-800">
                            {{ $case->youngPerson->name ?? '—' }}
                        </td>

                        <td class="px-5 py-3.5 text-sm text-gray-500">
                            @if($dob)
                                {{ \Carbon\Carbon::parse($dob)->format('d M Y') }}
                                <span class="text-gray-400">({{ \Carbon\Carbon::parse($dob)->age }} yrs)</span>
                            @else
                                —
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-sm text-gray-600">
                            {{ $case->status ?? '—' }}
                        </td>

                        <td class="px-5 py-3.5">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                @if($risk === 'high') bg-red-100 text-red-700
                                @elseif($risk === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ ucfirst($risk) ?: '—' }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-sm text-gray-500">
                            {{ $case->last_reviewed_at
                                ? \Carbon\Carbon::parse($case->last_reviewed_at)->format('d M Y')
                                : '—' }}
                        </td>

                        <td class="px-5 py-3.5">
                            @if($wScore !== null)
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full
                                            @if($wScore < 40) bg-red-400
                                            @elseif($wScore < 70) bg-yellow-400
                                            @else bg-green-400 @endif"
                                             style="width: {{ $wScore }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 tabular-nums">{{ $wScore }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('socialworker.cases.show', $case) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition">
                                View →
                            </a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="noResults" class="hidden p-8 text-center text-sm text-gray-400">
            No cases match this filter.
        </div>
    @endif
</div>

@push('scripts')
<script>
function filterByRisk(risk) {
    document.getElementById('riskFilter').value = risk;
    filterCases();
}

function filterCases() {
    const risk   = document.getElementById('riskFilter').value;
    const status = document.getElementById('statusFilter').value;
    const rows   = document.querySelectorAll('.case-row');
    let visible  = 0;

    rows.forEach(row => {
        const riskMatch   = risk   === 'all' || row.dataset.risk   === risk;
        const statusMatch = status === 'all' || row.dataset.status.includes(status);
        const show = riskMatch && statusMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const noResults = document.getElementById('noResults');
    if (noResults) noResults.classList.toggle('hidden', visible > 0);
}
</script>
@endpush

</x-app-layout>
