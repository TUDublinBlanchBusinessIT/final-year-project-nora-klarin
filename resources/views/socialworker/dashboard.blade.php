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
        $highRiskCount = $cases->filter(function ($c) {
            return strtolower($c->risklevel ?? '') === 'high';
        })->count();

        $mediumRiskCount = $cases->filter(function ($c) {
            return strtolower($c->risklevel ?? '') === 'medium';
        })->count();

        $lowRiskCount = $cases->filter(function ($c) {
            return strtolower($c->risklevel ?? '') === 'low';
        })->count();
    @endphp

    <div
        x-data="{ tab: 'dashboard', riskFilter: 'All' }"
        class="min-h-screen py-8 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50"
    >
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
                </nav>
            </div>

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
                                    <th class="px-4 py-3 text-left">Case ID</th>
                                    <th class="px-4 py-3 text-left">Risk Level</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Opened At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cases as $case)
                                    <tr
                                        class="border-t"
                                        x-show="riskFilter === 'All' || riskFilter === '{{ strtolower($case->risklevel ?? '') }}'"
                                    >
                                        <td class="px-4 py-3">
                                            <a href="{{ route('socialworker.case.show', $case->id) }}"
                                               class="text-indigo-600 hover:underline font-medium">
                                                {{ $case->id }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">{{ $case->risklevel ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $case->status ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ !empty($case->openedat) ? \Carbon\Carbon::parse($case->openedat)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($cases as $case)
                            <div
                                x-show="riskFilter === 'All' || riskFilter === '{{ strtolower($case->risklevel ?? '') }}'"
                                x-transition
                            >
                                <a
                                    href="{{ route('socialworker.case.show', $case->id) }}"
                                    class="block p-6 bg-white rounded-2xl shadow-sm hover:shadow-lg transition border-l-4
                                    @if(strtolower($case->risklevel ?? '') === 'high') border-red-500
                                    @elseif(strtolower($case->risklevel ?? '') === 'medium') border-yellow-500
                                    @else border-green-500
                                    @endif"
                                >
                                    <div class="font-bold text-lg text-gray-800">
                                        Case #{{ $case->id }}
                                    </div>

                                    <div class="text-gray-600 mt-2">
                                        {{ $case->summary ?? 'No summary available.' }}
                                    </div>

                                    <div class="mt-4 text-sm text-gray-500 space-y-1">
                                        <div>Status: {{ $case->status ?? '-' }}</div>
                                        <div>Risk: {{ $case->risklevel ?? '-' }}</div>
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

        </div>
    </div>

    @if($cases->count() > 0)
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
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>