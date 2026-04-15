<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hi {{ Auth::user()->name }}
            </h2>

            <div>

                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">

                    Social Worker Dashboard

                </h2>

                <p class="text-sm text-gray-500 mt-1">

                    Manage assigned cases and monitor risk levels

                </p>

            </div>


            <span class="text-sm text-gray-500">

                {{ now()->format('l, jS F') }}

            </span>

        </div>

    </x-slot>

<div x-data="{ tab: 'dashboard', riskFilter: 'All' }">

        {{-- TABS --}}
        <div class="flex border-b border-gray-200 space-x-6 mb-8">
            <button @click="tab = 'dashboard'" :class="tab === 'dashboard' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">Dashboard</button>

            <button @click="tab = 'cases'" :class="tab === 'cases' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">My Cases</button>

            <button @click="tab = 'wellbeing'" :class="tab === 'wellbeing' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">Wellbeing</button>

            <button 
                @click="tab = 'placementsMap'; setTimeout(initPlacementsMap, 200)"
                :class="tab === 'placementsMap' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" 
                class="py-2 px-4 font-semibold border-b-2">
                Placements Map
            </button>
        </div>


    @php

        $getRisk = function ($case) {

            return strtolower($case->risklevel ?? $case->risk_level ?? '');

        };



        $highRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();

        $mediumRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'medium')->count();

        $lowRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'low')->count();

    @endphp



    <div x-data="{ tab: 'dashboard', riskFilter: 'All' }" class="min-h-screen py-8 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">



            <div class="rounded-3xl bg-white shadow-sm border border-gray-100 p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-900">

                            Hi {{ Auth::user()->name }}

                        </h1>

                        <p class="text-sm text-gray-600 mt-1">

                            View your assigned cases, case risk levels, and recent overview.

                        </p>

                    </div>

                </div>

            </div>



            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-2">

                <nav class="flex flex-wrap gap-2">

                    <button

                        @click="tab = 'dashboard'"

                        :class="tab === 'dashboard'

                            ? 'bg-indigo-600 text-white shadow'

                            : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"

                        class="px-4 py-2 rounded-xl text-sm font-medium transition"

                    >

                        Dashboard

                    </button>



                    <button

                        @click="tab = 'cases'"

                        :class="tab === 'cases'

                            ? 'bg-indigo-600 text-white shadow'

                            : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"

                        class="px-4 py-2 rounded-xl text-sm font-medium transition"

                    >

                        My Cases

                    </button>



                    @isset($wellbeingData)

                        <button

                            @click="tab = 'wellbeing'"

                            :class="tab === 'wellbeing'

                                ? 'bg-indigo-600 text-white shadow'

                                : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"

                            class="px-4 py-2 rounded-xl text-sm font-medium transition"

                        >

                            Wellbeing

                        </button>

                    @endisset

                </nav>

            </div>



            {{-- DASHBOARD TAB --}}
            <div x-show="tab === 'dashboard'" x-transition>
                
                {{-- SUMMARY CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="rounded-3xl p-6 shadow-lg bg-white border border-blue-100">
                        <h3 class="text-lg font-bold text-blue-700">Total Cases</h3>
                        <p class="text-2xl mt-2">{{ $cases->count() }}</p>

            <div x-show="tab === 'dashboard'" x-transition class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-blue-100">

                        <h3 class="text-lg font-semibold text-blue-700">Total Cases</h3>

                        <p class="text-gray-600 mt-1 text-sm">Assigned to you</p>

                        <div class="mt-4 text-3xl font-bold text-gray-900">

                            {{ $cases->count() }}

                        </div>

                    </div>



                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-red-100">

                        <h3 class="text-lg font-semibold text-red-700">High Risk</h3>

                        <p class="text-gray-600 mt-1 text-sm">Need attention</p>

                        <div class="mt-4 text-3xl font-bold text-gray-900">

                            {{ $highRiskCount }}

                        </div>

                    </div>



                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-yellow-100">

                        <h3 class="text-lg font-semibold text-yellow-700">Medium Risk</h3>

                        <p class="text-gray-600 mt-1 text-sm">Monitor closely</p>

                        <div class="mt-4 text-3xl font-bold text-gray-900">

                            {{ $mediumRiskCount }}

                        </div>

                    </div>



                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-green-100">

                        <h3 class="text-lg font-semibold text-green-700">Low Risk</h3>

                        <p class="text-gray-600 mt-1 text-sm">Stable cases</p>

                        <div class="mt-4 text-3xl font-bold text-gray-900">

                            {{ $lowRiskCount }}

                        </div>

                    </div>

                </div>

                {{-- RISK CHART --}}
                <div class="bg-white p-6 shadow rounded-xl mt-8">
                    <canvas id="riskChart" class="w-full h-64"></canvas>
                </div>


                @if($cases->count() > 0)

                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-indigo-100">

                        <div class="flex items-center justify-between mb-4">

                            <h3 class="text-xl font-bold text-indigo-700">Risk Overview</h3>

                        </div>



                        <div class="h-80">

                            <canvas id="riskChart"></canvas>

                        </div>

                    </div>

                @else

                    <div class="rounded-3xl p-8 shadow-sm bg-white border border-dashed border-gray-300 text-center">

                        <div class="text-4xl mb-3">📁</div>

                        <h3 class="text-lg font-semibold text-gray-900">No assigned cases yet</h3>

                        <p class="text-sm text-gray-500 mt-2">

                            Once a case is assigned to this social worker, it will appear here with its risk summary.

                        </p>

                    </div>

                @endif

            </div>



            {{-- CASES TAB --}}

            <div x-show="tab === 'cases'" x-transition class="space-y-6">

                <div class="flex justify-end">

                    <select

                        x-model="riskFilter"

                        class="border rounded-xl px-4 py-2 text-sm shadow-sm"

                    >

                        <option value="All">All Risks</option>

                        <option value="high">High</option>

                        <option value="medium">Medium</option>

                        <option value="low">Low</option>

                    </select>

                </div>



                @if($cases->count() > 0)

                    <div class="rounded-3xl p-6 shadow-sm bg-white border border-gray-100 overflow-x-auto">

                        <h4 class="text-lg font-semibold mb-4 text-gray-900">Assigned Cases</h4>



                        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">

                            <thead class="bg-gray-100">

                                <tr>

                                    <th class="px-4 py-3 text-left">Case Reference</th>

                                    <th class="px-4 py-3 text-left">Risk Level</th>

                                    <th class="px-4 py-3 text-left">Status</th>

                                    <th class="px-4 py-3 text-left">Opened At</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($cases as $case)

                                    @php

                                        $caseRisk = strtolower($case->risklevel ?? $case->risk_level ?? '');

                                        $caseOpened = $case->openedat ?? $case->created_at ?? null;

                                    @endphp

                                    <tr

                                        class="border-t"

                                        x-show="riskFilter === 'All' || riskFilter === '{{ $caseRisk }}'"

                                    >

                                        <td class="px-4 py-3">

                                            <a href="{{ route('socialworker.case.show', $case->id) }}"

                                               class="text-indigo-600 hover:underline font-medium">

                                                {{ $case->case_reference ?? ('Case #' . $case->id) }}

                                            </a>

                                        </td>

                                        <td class="px-4 py-3">{{ $case->risklevel ?? $case->risk_level ?? '-' }}</td>

                                        <td class="px-4 py-3">{{ $case->status ?? '-' }}</td>

                                        <td class="px-4 py-3">

                                            {{ !empty($caseOpened) ? \Carbon\Carbon::parse($caseOpened)->format('d M Y') : '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        @foreach($cases as $case)

                            @php

                                $caseRisk = strtolower($case->risklevel ?? $case->risk_level ?? '');

                                $caseSummary = $case->summary ?? 'No summary available.';

                            @endphp

                            <div

                                x-show="riskFilter === 'All' || riskFilter === '{{ $caseRisk }}'"

                                x-transition

                            >

                                <a

                                    href="{{ route('socialworker.case.show', $case->id) }}"

                                    class="block p-6 bg-white rounded-2xl shadow-sm hover:shadow-lg transition border-l-4

                                    @if($caseRisk === 'high') border-red-500

                                    @elseif($caseRisk === 'medium') border-yellow-500

                                    @else border-green-500

                                    @endif"

                                >

                                    <div class="font-bold text-lg text-gray-800">

                                        {{ $case->case_reference ?? ('Case #' . $case->id) }}

                                    </div>



                                    @if(!empty($case->created_at))

                                        <div class="text-xs text-gray-400 mt-1">

                                            Opened {{ $case->created_at->format('d M Y') }}

                                        </div>

                                    @endif



                                    <div class="text-gray-600 mt-2">

                                        {{ $caseSummary }}

                                    </div>



                                    <div class="mt-4 text-sm text-gray-500 space-y-1">

                                        <div>Status: {{ $case->status ?? '-' }}</div>

                                        <div>Risk: {{ $case->risklevel ?? $case->risk_level ?? '-' }}</div>

                                    </div>

                                </a>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-3xl p-8 shadow-sm bg-white border border-dashed border-gray-300 text-center">

                        <div class="text-4xl mb-3">🗂️</div>

                        <h3 class="text-lg font-semibold text-gray-900">No cases to show</h3>

                        <p class="text-sm text-gray-500 mt-2">

                            This social worker has not been linked to any case files yet.

                        </p>

                    </div>

                @endif

            </div>



            {{-- WELLBEING TAB --}}

            @isset($wellbeingData)

                <div x-show="tab === 'wellbeing'" x-transition>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        @foreach($wellbeingData as $data)

                            @php

                                $child = $data['child'] ?? null;

                                $checks = $data['checks'] ?? collect();

                                $latestCheck = $checks->last();

                            @endphp



                            <div class="p-4 bg-white rounded-2xl shadow-sm">

                                <div class="flex justify-between items-center mb-3">

                                    <h4 class="font-semibold text-gray-800">{{ $child->name ?? 'Unknown Child' }}</h4>

                                    @if($latestCheck && !empty($latestCheck->week_start))

                                        <span class="text-sm text-gray-500">Last: {{ \Carbon\Carbon::parse($latestCheck->week_start)->format('d M Y') }}</span>

                                    @else

                                        <span class="text-sm text-gray-500">No checks yet</span>

                                    @endif

                                </div>



                                <div class="space-y-2">

                                    @if(!empty($data['domainScores']))

                                        @foreach($data['domainScores'] as $domainName => $score)

                                            <div>

                                                <div class="flex justify-between items-center text-sm text-gray-600">

                                                    <span>{{ $domainName }}</span>

                                                    <span>{{ round($score, 1) }}</span>

                                                </div>

                                                <div class="h-2 bg-gray-200 rounded-full mt-1">

                                                    <div class="h-2 rounded-full" style="width: {{ $score }}%; background-color: {{ $score < 50 ? '#f87171' : '#34d399' }}"></div>

                                                </div>

                                            </div>

                                        @endforeach

                                    @else

                                        <p class="text-gray-400 text-sm">No domain scores yet.</p>

                                    @endif

                                </div>



                                @if($latestCheck)

                                    <div class="mt-3 text-right">

                                        <a href="{{ route('wellbeing.result', $latestCheck) }}" class="text-indigo-600 hover:underline text-sm">

                                            View Details

                                        </a>

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </div>

            @endisset



        </div>

    </div>

    {{-- WELLBEING TAB --}}
<div x-show="tab === 'wellbeing'" x-transition>

    <div class="bg-white p-6 rounded-xl shadow">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Wellbeing Trends (All Cases)</h3>

            <select id="dashboardDatasetSelector" class="border rounded px-3 py-1">
                <option value="overall">Overall</option>
                <option value="domains">Domains</option>
            </select>
        </div>

        <canvas id="dashboardWellbeingChart" height="100"></canvas>

    </div>

</div>
    {{-- PLACEMENTS MAP TAB --}}
<div x-show="tab === 'placementsMap'" x-transition>

    <div class="bg-white p-6 rounded-xl shadow">

        <h3 class="text-lg font-semibold mb-4">All Placements</h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <div class="lg:col-span-2">
                <div id="placementsMap" style="height: 420px; width: 100%; border-radius: 16px;"></div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl">
                <h4 class="font-semibold">Placement Details</h4>
                <div id="placementDetails" class="mt-3 text-sm text-gray-600">
                    Click a marker to view details
                </div>
            </div>

        </div>

    </div>

</div>

    {{-- CHART JS --}}


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        document.addEventListener("DOMContentLoaded", function () {

            const canvas = document.getElementById('riskChart');

            if (!canvas) return;



            const ctx = canvas.getContext('2d');



            new Chart(ctx, {

                type: 'bar',

                data: {

                    labels: ['High', 'Medium', 'Low'],

                    datasets: [{

                        label: 'Cases by Risk',

                        data: [

                            {{ $highRiskCount }},

                            {{ $mediumRiskCount }},

                            {{ $lowRiskCount }}

                        ],

                        backgroundColor: ['#f87171', '#facc15', '#34d399']

                    }]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {

                            display: false

                        }

                    },

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {

                                stepSize: 1,

                                precision: 0

                            }

                        }

                    }

                }
            }
        }
    });
});
</script>
<script>
const wbLabels = [
    @foreach($wellbeingChecks as $check)
        "{{ $check->created_at->format('M d') }}",
    @endforeach
];

const wbOverall = [
    @foreach($wellbeingChecks as $check)
        {{ $check->overall_score ?? 0 }},
    @endforeach
];

const wbDomains = {
    emotional: [
        @foreach($wellbeingChecks as $check)
            {{ $check->emotional_score ?? 'null' }},
        @endforeach
    ],
    behavioural: [
        @foreach($wellbeingChecks as $check)
            {{ $check->behavioural_score ?? 'null' }},
        @endforeach
    ],
    physical: [
        @foreach($wellbeingChecks as $check)
            {{ $check->physical_score ?? 'null' }},
        @endforeach
    ],
    safety: [
        @foreach($wellbeingChecks as $check)
            {{ $check->safety_score ?? 'null' }},
        @endforeach
    ],
    school: [
        @foreach($wellbeingChecks as $check)
            {{ $check->school_score ?? 'null' }},
        @endforeach
    ],
    relationships: [
        @foreach($wellbeingChecks as $check)
            {{ $check->relationship_score ?? 'null' }},
        @endforeach
    ]
};

const wbCtx = document.getElementById('dashboardWellbeingChart');

let wbChart = new Chart(wbCtx, {
    type: 'line',
    data: {
        labels: wbLabels,
        datasets: [{
            label: 'Overall Wellbeing',
            data: wbOverall,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                min: 0,
                max: 100
            }
        }
    }
});

document.getElementById('dashboardDatasetSelector')
.addEventListener('change', function(){

    if(this.value === "overall"){

        wbChart.data.datasets = [{
            label: 'Overall Wellbeing',
            data: wbOverall,
            tension: 0.3
        }];

    } else {

        wbChart.data.datasets = [
            { label:'Emotional', data: wbDomains.emotional, tension:0.3 },
            { label:'Behavioural', data: wbDomains.behavioural, tension:0.3 },
            { label:'Physical', data: wbDomains.physical, tension:0.3 },
            { label:'Safety', data: wbDomains.safety, tension:0.3 },
            { label:'School', data: wbDomains.school, tension:0.3 },
            { label:'Relationships', data: wbDomains.relationships, tension:0.3 }
        ];

    }

    wbChart.update();
});
</script>

    <div class="bg-white p-6 rounded-xl shadow">

        <h3 class="text-lg font-semibold mb-4">All Placements</h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <div class="lg:col-span-2">
                <div id="placementsMap" style="height: 420px; width: 100%; border-radius: 16px;"></div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl">
                <h4 class="font-semibold">Placement Details</h4>
                <div id="placementDetails" class="mt-3 text-sm text-gray-600">
                    Click a marker to view details
                </div>
            </div>

        </div>

    </div>

</div>
<script>
const placements = @json($placements);

let placementsMap;
let placementInfoWindow;

function initPlacementsMap() {

    if (placementsMap) return;

    placementsMap = new google.maps.Map(document.getElementById("placementsMap"), {
        zoom: 11,
        center: { lat: 53.3498, lng: -6.2603 }
    });

    placementInfoWindow = new google.maps.InfoWindow();

    placements.forEach(place => {

        if (!place.latitude || !place.longitude) return;

        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(place.latitude),
                lng: parseFloat(place.longitude)
            },
            map: placementsMap,
            title: place.type
        });

        marker.addListener("click", () => {

            document.getElementById('placementDetails').innerHTML = `
                <div class="space-y-2">
                    <div class="font-semibold text-gray-900">${place.type ?? 'Placement'}</div>
                    <div><strong>Location:</strong> ${place.location ?? 'N/A'}</div>

                    <a href="https://www.google.com/maps?q=${place.latitude},${place.longitude}" 
                       target="_blank"
                       class="inline-block mt-3 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm">
                       Open in Google Maps
                    </a>
                </div>
            `;

            placementInfoWindow.setContent(`
                <div>
                    <strong>${place.type ?? 'Placement'}</strong><br>
                    ${place.location ?? 'No location'}
                </div>
            `);

            placementInfoWindow.open(placementsMap, marker);
        });

    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initPlacementsMap" async defer></script>
</x-app-layout>

            });

        });

    </script>

</x-app-layout>

