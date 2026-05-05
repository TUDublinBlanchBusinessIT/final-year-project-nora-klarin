<x-app-layout>

@php
    $getRisk   = fn($c) => strtolower($c->risk_level ?? $c->risklevel ?? '');
    $highCount = $cases->filter(fn($c) => $getRisk($c) === 'high')->count();
    $unread    = $conversations->sum(fn($c) => $c->unread_count ?? 0);

    $highRiskCases = $cases->filter(function ($c) {
        if (strtolower($c->risk_level ?? $c->risklevel ?? '') !== 'high') return false;
        if ($c->last_reviewed_at && \Carbon\Carbon::parse($c->last_reviewed_at)->isToday()) return false;
        return true;
    });

    $alertsCollection = collect($alerts ?? []);
    $totalAlerts      = $highRiskCases->count() + $alertsCollection->count();
    $notifCount       = $notificationCount ?? 0;
@endphp

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
                {{ Auth::user()->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ now()->format('l, jS F Y') }}
                &middot; {{ $cases->count() }} active {{ Str::plural('case', $cases->count()) }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">

            {{-- Notification bell --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="relative p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notifCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-indigo-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                    @endif
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Notifications</p>
                        @if($notifCount > 0)
                            <form method="POST" action="{{ route('socialworker.notifications.markAllRead') }}">
                                @csrf
                                <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
                            </form>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        @forelse($notifications ?? [] as $notif)
                            @php
                                $nData    = $notif->data;
                                $nType    = $nData['type'] ?? '';
                                $nSummary = $nData['summary'] ?? 'Notification';
                                $nCase    = $nData['case_file_id'] ?? null;
                                $nHref    = $nCase ? route('socialworker.cases.show', $nCase) : '#';
                                $nIcon    = match($nType) {
                                    'wellbeing_completed' => '📋',
                                    'wellbeing_concern'   => '📊',
                                    'goal_completed'      => '🎯',
                                    'document_uploaded'   => '📎',
                                    'overdue_wellbeing'   => '⏰',
                                    'support_request'     => '🆘',
                                    'message_received'    => '💬',
                                    default               => '🔔',
                                };
                            @endphp
                            <a href="{{ $nHref }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition"
                               onclick="markNotifRead('{{ $notif->id }}', event)">
                                <span class="shrink-0 mt-0.5" style="font-size:15px">{{ $nIcon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 truncate">{{ $nSummary }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0 mt-2"></span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-gray-400">No new notifications.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Alert button --}}
            @if($totalAlerts > 0)
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-xl px-3 py-2 hover:bg-red-100 transition focus:outline-none focus:ring-2 focus:ring-red-300">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-red-700">{{ $totalAlerts }} {{ Str::plural('alert', $totalAlerts) }}</span>
                        <svg class="w-3.5 h-3.5 text-red-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                        <div class="px-4 py-2.5 bg-red-50 border-b border-red-100 flex items-center justify-between">
                            <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Active alerts</p>
                            <span class="text-xs text-red-400">{{ $totalAlerts }} total</span>
                        </div>

                        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                            @foreach($highRiskCases as $hrCase)
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition">
                                    <span class="mt-2 w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('socialworker.cases.show', $hrCase) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-indigo-600 block truncate">
                                            {{ $hrCase->youngPerson->name ?? ('Case #'.$hrCase->id) }}
                                        </a>
                                        <p class="text-xs text-red-500 mt-0.5 font-medium">High risk — needs review</p>
                                        @if($hrCase->last_reviewed_at)
                                            <p class="text-xs text-gray-400">Last reviewed {{ \Carbon\Carbon::parse($hrCase->last_reviewed_at)->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('socialworker.cases.markReviewed', $hrCase) }}" class="shrink-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-gray-300 hover:text-green-500 transition text-base px-1">✓</button>
                                    </form>
                                </div>
                            @endforeach

                            @foreach($alertsCollection as $alert)
                                @php
                                    $aSev   = $alert['severity'] ?? 'high';
                                    $aDot   = $aSev === 'critical' ? 'bg-red-600' : 'bg-red-400';
                                    $aLabel = $alert['type'] === 'tag_override' ? 'Safeguarding alert' : 'Critical response';
                                @endphp
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition">
                                    <span class="mt-2 w-2 h-2 rounded-full shrink-0 {{ $aDot }}"></span>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ $alert['route'] }}" class="text-sm text-gray-900 hover:text-indigo-600 block truncate">
                                            {{ $alert['message'] }}
                                        </a>
                                        <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $aLabel }} · {{ ucfirst($aSev) }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('socialworker.alerts.acknowledge', $alert['id']) }}" class="shrink-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-gray-300 hover:text-green-500 transition text-base px-1">✓</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-[11px] text-gray-400">✓ to acknowledge</p>
                            <a href="{{ route('socialworker.wellbeing.alerts') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">All alerts →</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-3 py-2 shrink-0">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs font-medium text-green-700">No active alerts</p>
                </div>
            @endif
        </div>
    </div>
</x-slot>

{{-- ── Stat cards ───────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
    <a href="{{ route('socialworker.cases.index') }}"
       class="bg-white border border-gray-100 rounded-xl p-4 hover:border-indigo-200 hover:shadow-sm transition group">
        <p class="text-xs text-gray-400 mb-1 group-hover:text-indigo-500 transition">Total cases</p>
        <p class="text-2xl font-bold text-gray-900">{{ $cases->count() }}</p>
        <p class="text-[11px] text-gray-300 mt-1 group-hover:text-indigo-300 transition">View all →</p>
    </a>
    <a href="{{ route('socialworker.cases.index') }}?risk=high"
       class="bg-red-50 border border-red-100 rounded-xl p-4 hover:border-red-300 hover:shadow-sm transition">
        <p class="text-xs text-red-500 mb-1">High risk</p>
        <p class="text-2xl font-bold text-gray-900">{{ $highCount }}</p>
    </a>
    <a href="{{ route('socialworker.messages.index') }}"
       class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 hover:border-indigo-300 hover:shadow-sm transition">
        <p class="text-xs text-indigo-500 mb-1">Unread messages</p>
        <p class="text-2xl font-bold text-gray-900">{{ $unread }}</p>
        <p class="text-[11px] text-indigo-300 mt-1">Go to messages →</p>
    </a>
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────────── --}}
<div x-data="{ tab: 'appointments' }" class="mb-6">

    <div class="flex border-b border-gray-200 mb-4">
        <button @click="tab='appointments'"
                :class="tab==='appointments' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
            Appointments & Calendar
        </button>
        {{-- Trigger chart resize on tab switch so hidden canvas renders correctly --}}
        <button @click="tab='wellbeing'; $nextTick(() => { window.trendChart?.resize(); window.domainChart?.resize(); })"
                :class="tab==='wellbeing' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
            Wellbeing overview
        </button>
    </div>

    {{-- Appointments tab --}}
    <div x-show="tab==='appointments'" class="space-y-4">

        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100">
            <div class="px-5 py-3 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700">Next 7 days</p>
                <a href="{{ route('socialworker.appointments.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
            </div>
            @forelse($upcomingAppointments as $appt)
                <div class="flex items-center gap-4 px-5 py-3">
                    <div class="text-center shrink-0 w-10">
                        <p class="text-[10px] text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                        <p class="text-lg font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                            @if($appt->case?->youngPerson) &middot; {{ $appt->case->youngPerson->name }} @endif
                            @if($appt->location) &middot; {{ $appt->location }} @endif
                        </p>
                    </div>
                    @if($appt->case)
                        <a href="{{ route('socialworker.cases.show', $appt->case) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">Case →</a>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">No appointments in the next 7 days.</div>
            @endforelse
        </div>

        @php
            $calendarDays = collect();
            for ($i = 0; $i < 7; $i++) {
                $day = now()->startOfDay()->addDays($i);
                $calendarDays->push([
                    'label'        => $day->format('D j'),
                    'appointments' => $upcomingAppointments->filter(fn($a) => \Carbon\Carbon::parse($a->start_time)->isSameDay($day)),
                ]);
            }
        @endphp

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Weekly calendar</p>
                    <p class="text-xs text-gray-400">Next 7 days</p>
                </div>
                <a href="{{ route('socialworker.appointments.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($calendarDays as $day)
                    <div class="rounded-2xl border border-gray-100 p-3 bg-slate-50">
                        <p class="text-xs font-semibold text-slate-500 mb-2">{{ $day['label'] }}</p>
                        @if($day['appointments']->isEmpty())
                            <p class="text-[11px] text-slate-400">No appointments</p>
                        @else
                            <div class="space-y-2">
                                @foreach($day['appointments']->take(2) as $appt)
                                    <div class="rounded-xl bg-white p-2 border border-gray-100">
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $appt->title ?? 'Appointment' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}</p>
                                    </div>
                                @endforeach
                                @if($day['appointments']->count() > 2)
                                    <p class="text-[11px] text-slate-400">+ {{ $day['appointments']->count() - 2 }} more</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $scheduleSuggestions = $cases->map(function ($case) {
                    $hasAppt = $case->appointments->filter(fn($a) => $a->start_time >= now() && $a->start_time <= now()->addDays(30))->isNotEmpty();
                    if ($hasAppt) return null;
                    $risk = $case->risk_level ?? 'low';
                    return [
                        'case'      => $case,
                        'risk'      => $risk,
                        'daysUntil' => match($risk) { 'high' => 7, 'medium' => 14, default => 30 },
                        'color'     => match($risk) { 'high' => 'border-red-200 bg-red-50', 'medium' => 'border-yellow-200 bg-yellow-50', default => 'border-green-200 bg-green-50' },
                        'textColor' => match($risk) { 'high' => 'text-red-600', 'medium' => 'text-yellow-600', default => 'text-green-600' },
                    ];
                })->filter()->take(2);
            @endphp
            @foreach($scheduleSuggestions as $sug)
                <div class="border-2 rounded-xl p-4 {{ $sug['color'] }}">
                    <p class="text-sm font-medium text-gray-900">{{ $sug['case']->youngPerson->name ?? 'Case #' . $sug['case']->id }}</p>
                    <p class="text-xs {{ $sug['textColor'] }} font-semibold mt-0.5 mb-2">{{ ucfirst($sug['risk']) }} risk — due in {{ $sug['daysUntil'] }} days</p>
                    <a href="{{ route('socialworker.appointments.create', $sug['case']) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Schedule →</a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Wellbeing tab --}}
    <div x-show="tab==='wellbeing'" class="space-y-4">

        {{-- ── Wellbeing summary: priority cases + score ring grid ── --}}
        @php
            // Sort by risk severity then score ascending (worst first)
            $riskOrder = ['critical' => 0, 'high' => 1, 'moderate' => 2, 'medium' => 2, 'low' => 3];
            $sortedWellbeing = collect($wellbeingData)->sortBy([
                fn($e) => $riskOrder[strtolower($e['check']->risk_level ?? 'low')] ?? 3,
                fn($e) => $e['check']->overall_score ?? 100,
            ]);
            $needsAttention = $sortedWellbeing->filter(fn($e) => in_array(strtolower($e['check']->risk_level ?? ''), ['high','critical']));
            $others         = $sortedWellbeing->filter(fn($e) => !in_array(strtolower($e['check']->risk_level ?? ''), ['high','critical']));
        @endphp

        {{-- Needs attention banner --}}
        @if($needsAttention->isNotEmpty())
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <p class="text-sm font-medium text-gray-900">Needs attention</p>
                <span class="ml-auto text-xs text-gray-400">{{ $needsAttention->count() }} {{ Str::plural('case', $needsAttention->count()) }}</span>
            </div>
            @foreach($needsAttention as $entry)
                @php
                    $child  = $entry['child'];
                    $check  = $entry['check'];
                    $caseId = $check->case_file_id ?? 0;
                    $score  = round($check->overall_score ?? 0);
                    $wRisk  = strtolower($check->risk_level ?? 'low');
                    $isCrit = $wRisk === 'critical';
                    // Only show domains that scored below 45 — the problem areas
                    $lowDomains = $entry['domainScores']->filter(fn($s) => $s < 45)->sortBy(fn($s) => $s)->take(3);
                @endphp
                <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0 {{ $isCrit ? 'bg-red-50' : '' }} hover:bg-gray-50 transition">
                    {{-- Score ring --}}
                    <div class="shrink-0 w-12 h-12 relative flex items-center justify-center">
                        <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none"
                                stroke="{{ $isCrit ? '#ef4444' : '#f97316' }}" stroke-width="3"
                                stroke-dasharray="{{ $score }},100" stroke-linecap="round"/>
                        </svg>
                        <span class="absolute text-[10px] font-bold {{ $isCrit ? 'text-red-600' : 'text-orange-600' }}">{{ $score }}</span>
                    </div>
                    {{-- Name + risk + low domains --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('socialworker.cases.show', $caseId) }}"
                               class="text-sm font-semibold text-gray-900 hover:text-indigo-600 truncate">
                                {{ $child->name ?? ('Case #'.$caseId) }}
                            </a>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full shrink-0
                                {{ $isCrit ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ ucfirst($wRisk) }}
                            </span>
                        </div>
                        @if($lowDomains->isNotEmpty())
                            <div class="flex items-center gap-3 flex-wrap">
                                @foreach($lowDomains as $domain => $score)
                                    <span class="text-[11px] text-red-600 font-medium">
                                        {{ $domain }} {{ round($score) }}
                                    </span>
                                @endforeach
                                @if($entry['domainScores']->filter(fn($s) => $s < 45)->count() > 3)
                                    <span class="text-[11px] text-gray-400">+{{ $entry['domainScores']->filter(fn($s) => $s < 45)->count() - 3 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="shrink-0 flex flex-col items-end gap-1">
                        <span class="text-xs text-gray-400">{{ $check->completed_at?->diffForHumans() }}</span>
                        <a href="{{ route('socialworker.cases.show', $caseId) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            View case →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- All other cases — compact score grid --}}
        @if($others->isNotEmpty())
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100">
                <p class="text-sm font-medium text-gray-700">All cases</p>
                <p class="text-xs text-gray-400 mt-0.5">Sorted by score — tap any case to view details</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-px bg-gray-100">
                @foreach($others as $entry)
                    @php
                        $child  = $entry['child'];
                        $check  = $entry['check'];
                        $caseId = $check->case_file_id ?? 0;
                        $score  = round($check->overall_score ?? 0);
                        $wRisk  = strtolower($check->risk_level ?? 'low');
                        $ringColor = $wRisk === 'moderate' || $wRisk === 'medium' ? '#eab308' : '#22c55e';
                    @endphp
                    <a href="{{ route('socialworker.cases.show', $caseId) }}"
                       class="bg-white px-4 py-4 hover:bg-indigo-50 transition group flex flex-col items-center gap-2 text-center">
                        {{-- Mini ring --}}
                        <div class="w-10 h-10 relative flex items-center justify-center shrink-0">
                            <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="3.5"/>
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                    stroke="{{ $ringColor }}" stroke-width="3.5"
                                    stroke-dasharray="{{ $score }},100" stroke-linecap="round"/>
                            </svg>
                            <span class="absolute text-[9px] font-bold text-gray-700">{{ $score }}</span>
                        </div>
                        <div class="min-w-0 w-full">
                            <p class="text-xs font-medium text-gray-800 truncate group-hover:text-indigo-600">
                                {{ $child->name ?? ('Case #'.$caseId) }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $check->completed_at?->diffForHumans(null, true) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(empty($wellbeingData))
            <div class="bg-white border border-gray-100 rounded-xl px-5 py-12 text-center text-sm text-gray-400">
                No wellbeing checks completed yet.
            </div>
        @endif

        {{-- Charts side by side --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Trend chart with case filter --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Wellbeing over time</p>
                        <p class="text-xs text-gray-400">Overall score per check</p>
                    </div>
                </div>

                {{-- Case filter: add cases to chart --}}
                <div class="flex flex-wrap gap-1.5 mb-4" id="caseFilters">
                    @foreach($wellbeingTrendData as $i => $item)
                        @php
                            $palette = ['#818cf8','#34d399','#fb923c','#f472b6','#60a5fa','#a78bfa','#2dd4bf','#facc15'];
                            $colour  = $palette[$i % count($palette)];
                        @endphp
                        <button type="button"
                                onclick="toggleCase({{ $i }})"
                                id="caseBtn{{ $i }}"
                                data-active="false"
                                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full border text-xs font-medium transition
                                       border-gray-200 text-gray-400 bg-white hover:border-gray-400 hover:text-gray-700"
                                style="--case-color: {{ $colour }}">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $colour }}; opacity:0.35" id="caseDot{{ $i }}"></span>
                            {{ $item['name'] }}
                        </button>
                    @endforeach
                </div>

                <canvas id="trendChart" height="220"></canvas>
                <p id="trendEmpty" class="hidden text-xs text-gray-400 text-center pt-4">Select a case above to view trends.</p>
            </div>

            {{-- Domain bar chart --}}
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <div class="mb-3">
                    <p class="text-sm font-medium text-gray-700">Domain averages</p>
                    <p class="text-xs text-gray-400">Average wellbeing score by domain across all cases</p>
                </div>
                <canvas id="domainChart" height="220"></canvas>
                @if(isset($domainWeekChanges) && $domainWeekChanges->isNotEmpty())
                    <div class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                        <p class="text-xs font-medium text-gray-500 mb-2">Week-on-week changes</p>
                        @foreach($domainWeekChanges as $change)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-700">{{ $change['label'] }}</span>
                                <span class="font-semibold {{ $change['change'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $change['change'] < 0 ? '↓' : '↑' }}{{ $change['change'] > 0 ? '+' : '' }}{{ $change['change'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

<form id="markNotifForm" method="POST" action="" style="display:none">@csrf @method('PATCH')</form>

@push('scripts')
{{-- Chart.js + date adapter (required for time scale) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function markNotifRead(id, e) {
    e.preventDefault();
    const form  = document.getElementById('markNotifForm');
    const href  = e.currentTarget.href;
    form.action = '{{ url("/social-worker/notifications") }}/' + id + '/read';
    form.onsubmit = () => { setTimeout(() => { window.location = href; }, 50); return true; };
    form.submit();
}

const chartData = @json($chartData ?? ['trendsOverTime' => ['datasets' => []], 'trendsByDomain' => ['labels' => [], 'datasets' => []]]);

// ── Trend chart ───────────────────────────────────────────────────────────────
// Uses a category x-axis with date strings as labels — avoids all time-scale
// adapter issues. Dates are collected from all datasets, sorted, deduplicated,
// and used as shared labels. Each dataset maps its scores onto those labels.

const trendEl    = document.getElementById('trendChart');
const trendEmpty = document.getElementById('trendEmpty');

const rawDatasets = chartData.trendsOverTime?.datasets ?? [];

// Collect every unique date across all cases, sorted ascending
const allDates = [...new Set(
    rawDatasets.flatMap(ds => (ds.data || []).map(pt => pt.x))
)].sort();

// Format dates for display: '2025-02-10' -> '10 Feb'
function fmtDate(d) {
    const dt = new Date(d + 'T12:00:00'); // noon avoids UTC/local day boundary issues
    return dt.toLocaleDateString('en-IE', { day: 'numeric', month: 'short' });
}

const displayLabels = allDates.map(fmtDate);

// Build each dataset as a sparse array aligned to allDates
// Missing dates get null so Chart.js draws gaps (spanGaps connects them)
const allCaseData = rawDatasets.map(ds => {
    const pointMap = {};
    (ds.data || []).forEach(pt => { pointMap[pt.x] = pt.y; });
    return {
        label:            ds.label,
        borderColor:      ds.borderColor,
        backgroundColor:  'transparent',
        tension:          0,
        spanGaps:         true,
        pointRadius:      3,
        pointHoverRadius: 5,
        data: allDates.map(d => pointMap[d] ?? null),
    };
});

const activeIndices = new Set();

if (trendEl) {
    window.trendChart = new Chart(trendEl, {
        type: 'line',
        data: { labels: displayLabels, datasets: [] },
        options: {
            responsive: true,
            animation: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + '/100',
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks: {
                        maxTicksLimit: 12,
                        font: { size: 11 },
                        color: '#9ca3af',
                        // Show every Nth label to avoid crowding
                        callback: function(val, idx) {
                            const step = Math.ceil(displayLabels.length / 10);
                            return idx % step === 0 ? displayLabels[idx] : '';
                        }
                    },
                },
                y: {
                    min: 0,
                    max: 100,
                    grid:  { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: { stepSize: 20, font: { size: 11 }, color: '#9ca3af' },
                }
            }
        }
    });

    trendEl.style.display = 'none';
    trendEmpty?.classList.remove('hidden');
}

function toggleCase(index) {
    if (!window.trendChart || !allCaseData[index]) return;

    const btn = document.getElementById('caseBtn' + index);
    const dot = document.getElementById('caseDot' + index);
    const isActive = activeIndices.has(index);

    if (isActive) {
        activeIndices.delete(index);
        btn.dataset.active = 'false';
        btn.classList.remove('border-gray-700', 'text-gray-900', 'font-semibold');
        btn.classList.add('border-gray-200', 'text-gray-400');
        dot.style.opacity = '0.35';
    } else {
        activeIndices.add(index);
        btn.dataset.active = 'true';
        btn.classList.remove('border-gray-200', 'text-gray-400');
        btn.classList.add('border-gray-700', 'text-gray-900', 'font-semibold');
        dot.style.opacity = '1';
    }

    window.trendChart.data.datasets = Array.from(activeIndices)
        .sort((a, b) => a - b)
        .map(i => allCaseData[i]);

    const anyActive = activeIndices.size > 0;
    trendEl.style.display = anyActive ? '' : 'none';
    trendEmpty?.classList.toggle('hidden', anyActive);

    window.trendChart.update('none');
}

// ── Domain bar chart ──────────────────────────────────────────────────────────
const domainEl = document.getElementById('domainChart');
if (domainEl) {
    const hasData = (chartData.trendsByDomain?.labels?.length ?? 0) > 0;

    if (hasData) {
        window.domainChart = new Chart(domainEl, {
            type: 'bar',
            data: chartData.trendsByDomain,
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid:   { display: false },
                        border: { display: false },
                        ticks:  { font: { size: 11 }, color: '#9ca3af' },
                    },
                    y: {
                        beginAtZero: true,
                        max:  100,
                        grid: { color: '#f3f4f6' },
                        border: { display: false },
                        ticks: { stepSize: 25, font: { size: 11 }, color: '#9ca3af' },
                    }
                }
            }
        });
    } else {
        domainEl.style.display = 'none';
    }
}
</script>
@endpush

</x-app-layout>