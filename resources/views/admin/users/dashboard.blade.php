<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h2>
    </x-slot>

    <div class="py-6 space-y-8">

        {{-- ── Stat Cards ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            @php
                $cards = [
                    ['label' => 'Total Users',      'value' => $stats['total_users'],     'color' => 'indigo',  'icon' => '👥'],
                    ['label' => 'Total Cases',      'value' => $stats['total_cases'],     'color' => 'sky',     'icon' => '📁'],
                    ['label' => 'Open Cases',       'value' => $stats['open_cases'],      'color' => 'emerald', 'icon' => '✅'],
                    ['label' => 'High-Risk Cases',  'value' => $stats['high_risk_cases'], 'color' => 'red',     'icon' => '⚠️'],
                    ['label' => 'Children',         'value' => $stats['total_children'],  'color' => 'violet',  'icon' => '🧒'],
                    ['label' => 'Carers',           'value' => $stats['total_carers'],    'color' => 'amber',   'icon' => '🏠'],
                    ['label' => 'Social Workers',   'value' => $stats['total_sw'],        'color' => 'teal',    'icon' => '💼'],
                    ['label' => 'Closed Cases',     'value' => $stats['closed_cases'],    'color' => 'gray',    'icon' => '🔒'],
                ];
            @endphp

            @foreach($cards as $card)
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                <span class="text-3xl">{{ $card['icon'] }}</span>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $card['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Quick Links ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}"
               class="bg-indigo-600 text-white rounded-xl p-4 text-center font-medium hover:bg-indigo-700 transition shadow">
                Manage Users
            </a>
            <a href="{{ route('admin.cases.index') }}"
               class="bg-sky-600 text-white rounded-xl p-4 text-center font-medium hover:bg-sky-700 transition shadow">
                All Cases
            </a>
            <a href="{{ route('admin.social-workers') }}"
               class="bg-teal-600 text-white rounded-xl p-4 text-center font-medium hover:bg-teal-700 transition shadow">
                Social Workers
            </a>
            <a href="{{ route('admin.placements-map') }}"
               class="bg-emerald-600 text-white rounded-xl p-4 text-center font-medium hover:bg-emerald-700 transition shadow">
                Placements Map
            </a>
        </div>

        {{-- ── Recent Cases ─────────────────────────────────────────────────── --}}
        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Recent Cases</h3>
                <a href="{{ route('admin.cases.index') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>
            </div>
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opened</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentCases as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-mono text-gray-800">{{ $case->case_reference }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ optional($case->youngPerson)->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $case->status === 'open' ? 'bg-emerald-100 text-emerald-800' : ($case->status === 'closed' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($case->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $case->risk_level === 'high' ? 'bg-red-100 text-red-700' : ($case->risk_level === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($case->risk_level) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ optional($case->opened_at)->format('d M Y') ?? optional($case->created_at)->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>