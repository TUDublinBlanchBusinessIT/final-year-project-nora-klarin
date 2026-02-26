<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hi {{ Auth::user()->name }}!
            </h2>

            <span class="text-sm text-gray-500">
                {{ now()->format('l, jS F') }}
            </span>
        </div>
    </x-slot>

<div 
    x-data="{ tab: 'dashboard', riskFilter: 'All' }"
    class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50"
>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- TABS --}}
        <div class="flex border-b border-gray-200 space-x-6 mb-8">
            <button 
                @click="tab = 'dashboard'"
                :class="tab === 'dashboard' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'"
                class="py-2 px-4 font-semibold border-b-2">
                Dashboard
            </button>

            <button 
                @click="tab = 'cases'"
                :class="tab === 'cases' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'"
                class="py-2 px-4 font-semibold border-b-2">
                My Cases
            </button>
        </div>

        {{-- ================= DASHBOARD TAB ================= --}}
        <div x-show="tab === 'dashboard'" x-transition>

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="rounded-3xl p-6 shadow-lg bg-white border border-blue-100">
                    <h3 class="text-lg font-bold text-blue-700">Total Cases</h3>
                    <p class="text-2xl mt-2">{{ $cases->count() }}</p>
                </div>

                <div class="rounded-3xl p-6 shadow-lg bg-white border border-red-100">
                    <h3 class="text-lg font-bold text-red-700">High Risk</h3>
<p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'high')->count() }}</p>                </div>
                <div class="rounded-3xl p-6 shadow-lg bg-white border border-yellow-100">
                    <h3 class="text-lg font-bold text-yellow-700">Medium Risk</h3>
<p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'medium')->count() }}</p>                </div>

                <div class="rounded-3xl p-6 shadow-lg bg-white border border-green-100">
                    <h3 class="text-lg font-bold text-green-700">Low Risk</h3>
<p class="text-2xl mt-2">{{ $cases->filter(fn($c) => strtolower($c->risk_level) === 'low')->count() }}</p>                </div>
            </div>

            {{-- CHART --}}
            <div class="bg-white p-6 shadow rounded-xl">
                <canvas id="riskChart" class="w-full h-64"></canvas>
            </div>
        </div>


        {{-- ================= CASES TAB ================= --}}
        <div x-show="tab === 'cases'" x-transition>

            {{-- FILTER --}}
            <div class="mb-6 flex justify-end">
                <select 
                    x-model="riskFilter"
                    class="border rounded-lg px-3 py-2 text-sm shadow-sm"
                >
                    <option value="All">All Risks</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            {{-- CASE GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cases as $case)
                    <div
                        x-show="riskFilter === 'All' || riskFilter === '{{ $case->risk_level }}'"
                        x-transition
                    >
                        <a 
                            href="{{ route('socialworker.case.show', $case->id) }}"
                            class="block p-6 bg-white rounded-2xl shadow hover:shadow-xl transition border-l-4
                            @if($case->risk_level === 'high') border-red-500
                            @elseif($case->risk_level === 'medium') border-yellow-500
                            @else border-green-500
                            @endif"
                        >
                            <div class="font-bold text-lg text-gray-800">
                                Case #{{ $case->id }}
                            </div>

                            <div class="text-gray-600 mt-1">
                                {{ $case->summary }}
                            </div>

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
                plugins: { legend: { display: false } }
            }
        });
    });
</script>

</x-app-layout>