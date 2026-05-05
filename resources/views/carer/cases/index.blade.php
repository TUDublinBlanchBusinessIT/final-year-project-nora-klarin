<x-app-layout>

<x-slot name="header">
    <h1 class="text-xl font-semibold text-gray-900">My Cases</h1>
    <p class="text-sm text-gray-500 mt-0.5">Children currently in your care</p>
</x-slot>

<div class="space-y-3">
    @forelse($cases as $case)
        @php
            $yp   = $case->youngPerson;
            $risk = strtolower($case->risk_level ?? '');
            $badgeCls = match($risk) {
                'high', 'critical' => 'bg-red-100 text-red-700',
                'medium', 'moderate' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-green-100 text-green-700',
            };
            $latestCheck = $case->wellbeingChecks
                ->filter(fn($c) => $c->completed_at !== null)
                ->sortByDesc('completed_at')
                ->first();
            $nextAppt = $case->appointments
                ->where('start_time', '>=', now())
                ->sortBy('start_time')
                ->first();
        @endphp
        <a href="{{ route('carer.cases.show', $case) }}"
           class="block bg-white border border-gray-100 rounded-xl p-5 hover:border-indigo-200 hover:shadow-sm transition group">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition">
                            {{ $yp->name ?? 'Unknown' }}
                        </p>
                        <span class="text-xs {{ $badgeCls }} px-2 py-0.5 rounded-full font-medium">
                            {{ ucfirst($risk) ?: 'Unknown' }} risk
                        </span>
                    </div>
                    <p class="text-xs text-gray-400">
                        {{ $case->case_reference ?? ('Case #' . $case->id) }}
                        @if($yp?->dob) · Age {{ \Carbon\Carbon::parse($yp->dob)->age }} @endif
                        @if($case->placements->first()) · {{ $case->placements->first()->type }} @endif
                    </p>
                </div>

                <div class="flex items-center gap-6 shrink-0 text-right">
                    @if($latestCheck)
                        <div>
                            <p class="text-xs text-gray-400">Last check</p>
                            <p class="text-sm font-medium text-gray-700">{{ round($latestCheck->overall_score ?? 0) }}/100</p>
                            <p class="text-xs text-gray-400">{{ $latestCheck->completed_at->diffForHumans() }}</p>
                        </div>
                    @endif
                    @if($nextAppt)
                        <div>
                            <p class="text-xs text-gray-400">Next appt</p>
                            <p class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($nextAppt->start_time)->format('d M') }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($nextAppt->start_time)->format('g:i A') }}</p>
                        </div>
                    @endif
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-white border border-gray-100 rounded-xl p-12 text-center">
            <p class="text-sm text-gray-400">No cases assigned to you yet.</p>
        </div>
    @endforelse
</div>

</x-app-layout>
