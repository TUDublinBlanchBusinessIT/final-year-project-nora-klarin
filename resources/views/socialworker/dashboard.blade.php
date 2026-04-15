<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between w-full">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hi {{ Auth::user()->name }}
        </h2>

            <div class="flex items-center gap-3">
                <img src="{{ asset('images/CareHub.png') }}"
                     alt="CareHub Logo"
                     class="h-10 w-10 object-contain">
</div>
        <span class="text-sm text-gray-500">
            {{ now()->format('l, jS F') }}
        </span>
    </div>
</x-slot>

<div x-data="{ tab: 'dashboard', riskFilter: 'All' }">
<div class="rounded-3xl p-6 shadow-sm bg-white border border-indigo-100 cursor-pointer"
     @click="tab='messages'">

    <h3 class="text-lg font-semibold text-indigo-700">Messages</h3>
    <p class="text-gray-600 mt-1 text-sm">Unread messages</p>

    <div class="mt-4 text-3xl font-bold text-gray-900">
        {{ $conversations->sum(fn($c) => $c->unread_count ?? 0) }}
    </div>

</div>
{{-- TABS --}}
<div class="flex border-b border-gray-200 space-x-6 mb-8">
    <button @click="tab = 'dashboard'" :class="tab === 'dashboard' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">Dashboard</button>
    <button @click="tab = 'cases'" :class="tab === 'cases' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">My Cases</button>
    <button @click="tab = 'wellbeing'" :class="tab === 'wellbeing' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">Wellbeing</button>
    <button @click="tab = 'placementsMap'; setTimeout(initPlacementsMap, 200)" :class="tab === 'placementsMap' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" class="py-2 px-4 font-semibold border-b-2">
        Placements Map
    </button>
    <button 
    @click="tab = 'messages'" 
    :class="tab === 'messages' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" 
    class="py-2 px-4 font-semibold border-b-2">
    Messages
</button>
</div>

@php
    $getRisk = fn($case) => strtolower($case->risklevel ?? $case->risk_level ?? '');
    $highRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();
    $mediumRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'medium')->count();
    $lowRiskCount = $cases->filter(fn($c) => $getRisk($c) === 'low')->count();
@endphp

<div class="min-h-screen py-8 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

{{-- DASHBOARD --}}
<div x-show="tab === 'dashboard'" x-transition class="space-y-6">

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="rounded-3xl p-6 shadow-sm bg-white border border-blue-100">
        <h3 class="text-lg font-semibold text-blue-700">Total Cases</h3>
        <p class="text-gray-600 mt-1 text-sm">Assigned to you</p>
        <div class="mt-4 text-3xl font-bold text-gray-900">{{ $cases->count() }}</div>
    </div>

    <div class="rounded-3xl p-6 shadow-sm bg-white border border-red-100">
        <h3 class="text-lg font-semibold text-red-700">High Risk</h3>
        <p class="text-gray-600 mt-1 text-sm">Need attention</p>
        <div class="mt-4 text-3xl font-bold text-gray-900">{{ $highRiskCount }}</div>
    </div>

    <div class="rounded-3xl p-6 shadow-sm bg-white border border-yellow-100">
        <h3 class="text-lg font-semibold text-yellow-700">Medium Risk</h3>
        <p class="text-gray-600 mt-1 text-sm">Monitor closely</p>
        <div class="mt-4 text-3xl font-bold text-gray-900">{{ $mediumRiskCount }}</div>
    </div>

    <div class="rounded-3xl p-6 shadow-sm bg-white border border-green-100">
        <h3 class="text-lg font-semibold text-green-700">Low Risk</h3>
        <p class="text-gray-600 mt-1 text-sm">Stable cases</p>
        <div class="mt-4 text-3xl font-bold text-gray-900">{{ $lowRiskCount }}</div>
    </div>

</div>

<div class="bg-white p-6 shadow rounded-xl mt-8">
    <canvas id="riskChart" class="w-full h-64"></canvas>
</div>

@if($cases->count() > 0)
<div class="rounded-3xl p-6 shadow-sm bg-white border border-indigo-100">
    <h3 class="text-xl font-bold text-indigo-700 mb-4">Risk Overview</h3>
</div>
@else
<div class="rounded-3xl p-8 shadow-sm bg-white border border-dashed border-gray-300 text-center">
    <div class="text-4xl mb-3">📁</div>
    <h3 class="text-lg font-semibold text-gray-900">No assigned cases yet</h3>
</div>
@endif

</div>

{{-- CASES --}}
<div x-show="tab === 'cases'" x-transition class="space-y-6">

<div class="flex justify-end">
    <select x-model="riskFilter" class="border rounded-xl px-4 py-2 text-sm shadow-sm">
        <option value="All">All Risks</option>
        <option value="high">High</option>
        <option value="medium">Medium</option>
        <option value="low">Low</option>
    </select>
</div>

@if($cases->count() > 0)
<div class="rounded-3xl p-6 shadow-sm bg-white border border-gray-100 overflow-x-auto">
<table class="min-w-full">
<tbody>
@foreach($cases as $case)
@php $caseRisk = strtolower($case->risklevel ?? $case->risk_level ?? ''); @endphp
<tr x-show="riskFilter === 'All' || riskFilter === '{{ $caseRisk }}'">
<td>{{ $case->case_reference ?? ('Case #' . $case->id) }}</td>
<td>{{ $caseRisk }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif

</div>

{{-- WELLBEING --}}
@isset($wellbeingData)
<div x-show="tab === 'wellbeing'" x-transition>
<div class="bg-white p-6 rounded-xl shadow">
<canvas id="dashboardWellbeingChart"></canvas>
</div>
</div>
@endisset

{{-- MESSAGES TAB --}}
<div x-show="tab === 'messages'" x-transition>

    <div class="bg-white p-6 rounded-xl shadow">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Messages</h3>

            <a href="{{ route('social_worker.messages.create') }}"
               class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                New Message
            </a>
        </div>

    </div>

</div>

{{-- MAP --}}
<div x-show="tab === 'placementsMap'" x-transition>
<div class="bg-white p-6 rounded-xl shadow">
<div id="placementsMap" style="height:420px;"></div>
</div>
</div>

</div>
</div>

{{-- CHART FIX --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const canvas = document.getElementById('riskChart');
    if (!canvas) return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['High','Medium','Low'],
            datasets: [{
                data: [{{ $highRiskCount }}, {{ $mediumRiskCount }}, {{ $lowRiskCount }}],
                backgroundColor: ['#f87171','#facc15','#34d399']
            }]
        }
    });

});
</script>

</div>
</x-app-layout>