<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800">
            Wellbeing Alerts - High/Critical
        </h2>
    </x-slot>

    <div class="space-y-6">

        @forelse($checks as $childId => $childChecks)
            @php $latestCheck = $childChecks->first(); @endphp

            <div class="p-4 bg-white shadow rounded-2xl border">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">
                            {{ $latestCheck->child->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Last Check: {{ \Carbon\Carbon::parse($latestCheck->week_start)->format('d M Y') }}
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($latestCheck->risk_level === 'high') bg-red-100 text-red-700
                        @elseif($latestCheck->risk_level === 'critical') bg-red-600 text-white
                        @endif">
                        {{ ucfirst($latestCheck->risk_level) }}
                    </span>
                </div>

                <div class="mt-3 space-y-2">
                    <h4 class="font-medium text-gray-700">Domain Scores:</h4>
                    @foreach($latestCheck->domainScores as $domainScore)
                        <div>
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span>{{ $domainScore->domain->name }}</span>
                                <span>{{ round($domainScore->score, 1) }}</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full mt-1">
                                <div class="h-2 bg-red-500 rounded-full"
                                     style="width: {{ $domainScore->score }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 text-right">
                    <a href="{{ route('child.wellbeing.result', $latestCheck) }}"
                       class="text-indigo-600 hover:underline text-sm">
                        View Full Details
                    </a>
                </div>
            </div>

        @empty
            <div class="p-6 bg-gray-50 rounded-2xl text-gray-600 text-center">
                No high or critical wellbeing alerts at the moment.
            </div>
        @endforelse

    </div>
</x-app-layout>