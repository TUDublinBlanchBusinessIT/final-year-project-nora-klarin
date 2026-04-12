<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hi {{ Auth::user()->name }}
            </h2>

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

            {{-- DASHBOARD TAB --}}
            <div x-show="tab === 'dashboard'" x-transition>
                
                {{-- SUMMARY CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="rounded-3xl p-6 shadow-lg bg-white border border-blue-100">
                        <h3 class="text-lg font-bold text-blue-700">Total Cases</h3>
                        <p class="text-2xl mt-2">{{ $cases->count() }}</p>
                    </div>
                    <div class="rounded-3xl p-6 shadow-lg bg-white border border-red-100">
                        <h3 class="text-lg font-bold text-red-700">High Risk</h3>
                        <p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'high')->count() }}</p>
                    </div>
                    <div class="rounded-3xl p-6 shadow-lg bg-white border border-yellow-100">
                        <h3 class="text-lg font-bold text-yellow-700">Medium Risk</h3>
                        <p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'medium')->count() }}</p>
                    </div>
                    <div class="rounded-3xl p-6 shadow-lg bg-white border border-green-100">
                        <h3 class="text-lg font-bold text-green-700">Low Risk</h3>
                        <p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'low')->count() }}</p>
                    </div>
                </div>

                {{-- RISK CHART --}}
                <div class="bg-white p-6 shadow rounded-xl mt-8">
                    <canvas id="riskChart" class="w-full h-64"></canvas>
                </div>
            </div>

            {{-- CASES TAB --}}
            <div x-show="tab === 'cases'" x-transition>
                <div class="mb-6 flex justify-end">
                    <select x-model="riskFilter" class="border rounded-lg px-3 py-2 text-sm shadow-sm">
                        <option value="All">All Risks</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cases as $case)
                        <div x-show="riskFilter === 'All' || riskFilter === '{{ $case->risk_level }}'" x-transition>
                            <a href="{{ route('socialworker.case.show', $case->id) }}" class="block p-6 bg-white rounded-2xl shadow hover:shadow-xl transition border-l-4
                                @if($case->risk_level === 'high') border-red-500
                                @elseif($case->risk_level === 'medium') border-yellow-500
                                @else border-green-500
                                @endif">
                                <div class="font-bold text-lg text-gray-800">
                                    {{ $case->case_reference }}
                                </div>
                            

                                <div class="text-xs text-gray-400">
                                    Opened {{ $case->created_at->format('d M Y') }}
                                </div>                               
                                <div class="text-gray-600 mt-1">{{ $case->summary }}</div>
                                <div class="mt-3 text-sm text-gray-500 space-y-1">
                                    <div>Status: {{ $case->status }}</div>
                                    <div>Risk: {{ $case->risk_level }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

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
    const ctx = document.getElementById('riskChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['High', 'Medium', 'Low'],
            datasets: [{
                label: 'Cases by Risk',
                data: [
                    {{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'high')->count() }},
                    {{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'medium')->count() }},
                    {{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'low')->count() }}
                ],
                backgroundColor: ['#f87171','#facc15','#34d399']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
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