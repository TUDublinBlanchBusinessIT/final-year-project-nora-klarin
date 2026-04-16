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



            {{-- Welcome / Header Card --}}

            <div class="rounded-3xl bg-white shadow-sm border border-gray-100 p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-900">

                            Hi {{ Auth::user()->name }}

                        </h1>

                        <p class="text-sm text-gray-600 mt-1">

                            View your assigned cases, wellbeing alerts, and recent activity.

                        </p>

                    </div>



                    <a href="{{ route('social-worker.wellbeing.alerts') }}"

                       class="relative inline-flex items-center px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 shadow-sm transition">

                        Wellbeing Alerts



                        @if(($wellbeingAlertCount ?? 0) > 0)

                            <span class="ml-2 inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 rounded-full bg-white text-red-600 text-xs font-bold">

                                {{ $wellbeingAlertCount }}

                            </span>

                        @endif

                    </a>

                </div>

            </div>



            {{-- Tabs --}}

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



            {{-- CASES TAB --}}

            <div x-show="tab === 'cases'" x-transition class="space-y-6">



                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <h3 class="text-2xl font-bold text-gray-900">My Cases</h3>

                        <p class="text-sm text-gray-500 mt-1">

                            View and manage all assigned case files.

                        </p>

                    </div>



                    <select

                        x-model="riskFilter"

                        class="rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"

                    >

                        <option value="All">All Risks</option>

                        <option value="high">High</option>

                        <option value="medium">Medium</option>

                        <option value="low">Low</option>

                    </select>

                </div>



                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="rounded-2xl bg-white border border-blue-100 shadow-sm p-5">

                        <p class="text-sm text-gray-500">Assigned Cases</p>

                        <p class="mt-2 text-3xl font-bold text-blue-700">{{ $cases->count() }}</p>

                    </div>



                    <div class="rounded-2xl bg-white border border-red-100 shadow-sm p-5">

                        <p class="text-sm text-gray-500">High Priority</p>

                        <p class="mt-2 text-3xl font-bold text-red-600">{{ $highRiskCount }}</p>

                    </div>



                    <div class="rounded-2xl bg-white border border-yellow-100 shadow-sm p-5">

                        <p class="text-sm text-gray-500">Under Review</p>

                        <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $mediumRiskCount }}</p>

                    </div>

                </div>



                @if($cases->count() > 0)

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        @foreach($cases as $case)

                            @php

                                $caseRisk = strtolower($case->risklevel ?? $case->risk_level ?? '');

                                $caseOpened = $case->openedat ?? $case->created_at ?? null;

                                $caseSummary = $case->summary ?? 'No case summary available yet.';

                                $riskBadgeClass =

                                    $caseRisk === 'high' ? 'bg-red-100 text-red-700 border-red-200' :

                                    ($caseRisk === 'medium' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' :

                                    'bg-green-100 text-green-700 border-green-200');

                            @endphp



                            <div

                                x-show="riskFilter === 'All' || riskFilter === '{{ $caseRisk }}'"

                                x-transition

                                class="h-full"

                            >

                                <div class="block h-full rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition p-6">

                                    <div class="flex items-start justify-between gap-3">

                                        <div>

                                            <div class="text-xl font-bold text-gray-900">

                                                {{ $case->case_reference ?? ('Case #' . $case->id) }}

                                            </div>



                                            <div class="text-sm text-gray-500 mt-1">

                                                {{ $case->youngPerson->name ?? 'Young person not assigned' }}

                                            </div>

                                        </div>



                                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $riskBadgeClass }}">

                                            {{ ucfirst($caseRisk ?: 'unknown') }}

                                        </span>

                                    </div>



                                    <div class="mt-5 grid grid-cols-2 gap-3">

                                        <div class="rounded-2xl bg-gray-50 px-4 py-3">

                                            <div class="text-xs text-gray-500">Status</div>

                                            <div class="mt-1 font-semibold text-gray-800">

                                                {{ $case->status ?? 'N/A' }}

                                            </div>

                                        </div>



                                        <div class="rounded-2xl bg-gray-50 px-4 py-3">

                                            <div class="text-xs text-gray-500">Opened</div>

                                            <div class="mt-1 font-semibold text-gray-800">

                                                {{ !empty($caseOpened) ? \Carbon\Carbon::parse($caseOpened)->format('d M Y') : 'N/A' }}

                                            </div>

                                        </div>

                                    </div>



                                    <div class="mt-5">

                                        <div class="text-sm font-semibold text-gray-700 mb-2">Summary</div>

                                        <p class="text-sm text-gray-600 leading-6">

                                            {{ $caseSummary }}

                                        </p>

                                    </div>



                                    {{-- ACTION BUTTONS ADDED HERE --}}

                                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">

                                        <a

                                            href="{{ route('socialworker.case.show', $case->id) }}"

                                            class="block text-center rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 transition"

                                        >

                                            Open case file

                                        </a>



                                        <a

                                            href="{{ route('socialworker.messages.index', $case) }}"

                                            class="block text-center rounded-2xl bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 font-bold py-3 transition"

                                        >

                                            Open messages 💬

                                        </a>

                                    </div>

                                </div>

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

                <div x-show="tab === 'wellbeing'" x-transition class="space-y-6">



                    <div>

                        <h3 class="text-2xl font-bold text-gray-900">Wellbeing Overview</h3>

                        <p class="text-sm text-gray-500 mt-1">

                            Latest wellbeing summaries for assigned young people.

                        </p>

                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        @foreach($wellbeingData as $data)

                            @php

                                $child = $data['child'] ?? null;

                                $checks = $data['checks'] ?? collect();

                                $latestCheck = $checks->first();

                            @endphp



                            <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100">

                                <div class="flex justify-between items-center mb-4">

                                    <div>

                                        <h4 class="font-semibold text-gray-800 text-lg">

                                            {{ $child->name ?? 'Unknown Child' }}

                                        </h4>



                                        @if($latestCheck)

                                            <span class="text-sm text-gray-500">

                                                Last: {{ $latestCheck->created_at ? $latestCheck->created_at->format('d M Y') : '-' }}

                                            </span>

                                        @else

                                            <span class="text-sm text-gray-500">No checks yet</span>

                                        @endif

                                    </div>



                                    @if($latestCheck)

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold

                                            @if(($latestCheck->risk_level ?? null) === 'critical') bg-red-600 text-white

                                            @elseif(($latestCheck->risk_level ?? null) === 'high') bg-red-100 text-red-700

                                            @elseif(($latestCheck->risk_level ?? null) === 'medium' || ($latestCheck->risk_level ?? null) === 'moderate') bg-yellow-100 text-yellow-700

                                            @else bg-green-100 text-green-700

                                            @endif">

                                            {{ ucfirst($latestCheck->risk_level ?? 'unknown') }}

                                        </span>

                                    @endif

                                </div>



                                <div class="space-y-3">

                                    @if(!empty($data['domainScores']))

                                        @foreach($data['domainScores'] as $domainName => $score)

                                            <div>

                                                <div class="flex justify-between items-center text-sm text-gray-600">

                                                    <span>{{ $domainName }}</span>

                                                    <span>{{ round($score, 1) }}</span>

                                                </div>

                                                <div class="h-2 bg-gray-200 rounded-full mt-1">

                                                    <div

                                                        class="h-2 rounded-full"

                                                        style="width: {{ $score }}%; background-color: {{ $score < 50 ? '#f87171' : '#34d399' }}">

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    @else

                                        <p class="text-gray-400 text-sm">No domain scores yet.</p>

                                    @endif

                                </div>



                                @if($latestCheck)

                                    <div class="mt-4 text-right">

                                        <a href="{{ route('wellbeing.result', $latestCheck) }}" class="text-indigo-600 hover:underline text-sm font-medium">

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

                        backgroundColor: ['#f87171', '#facc15', '#34d399'],

                        borderRadius: 12

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

            });

        });

    </script>



</x-app-layout>