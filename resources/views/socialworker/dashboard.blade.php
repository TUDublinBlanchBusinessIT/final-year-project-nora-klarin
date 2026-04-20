<x-app-layout>
    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

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

            {{-- CHART --}}
            <div class="bg-white p-6 shadow rounded-xl">
                <canvas id="riskChart" class="w-full h-64"></canvas>
            </div>
        </div>


            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-2">

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
