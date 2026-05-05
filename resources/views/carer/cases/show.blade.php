<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('carer.cases.index') }}" class="hover:text-gray-700">My Cases</a>
                <span>/</span>
                <span class="text-gray-900">{{ $case->case_reference ?? ('Case #' . $case->id) }}</span>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $case->youngPerson->name ?? 'Unassigned' }}
            </h1>
        </div>
    </div>
</x-slot>

<div x-data="{ tab: 'child' }" class="space-y-4">

    {{-- ── Tabs ── --}}
    <div class="flex flex-wrap border-b border-gray-200">
        @foreach([
            'child'        => 'Child info',
            'caseDetails'  => 'Case details',
            'placements'   => 'Placement',
            'medical'      => 'Medical',
            'education'    => 'Education',
            'documents'    => 'Documents',
            'appointments' => 'Appointments',
            'wellbeing'    => 'Wellbeing',
        ] as $key => $label)
            <button
                @click="tab='{{ $key }}'"
                :class="tab==='{{ $key }}' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                class="py-2 px-4 text-sm font-medium border-b-2 transition whitespace-nowrap">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── Child info ── --}}
    <div x-show="tab==='child'" class="bg-white p-6 rounded-xl border border-gray-100">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Child information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 mb-0.5">Name</p>
                <p class="font-medium text-gray-900">{{ $case->youngPerson->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-0.5">Date of birth</p>
                <p class="font-medium text-gray-900">
                    @if($case->youngPerson?->dob)
                        {{ \Carbon\Carbon::parse($case->youngPerson->dob)->format('d M Y') }}
                        <span class="text-gray-400 font-normal ml-1">({{ \Carbon\Carbon::parse($case->youngPerson->dob)->age }} years)</span>
                    @else —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-gray-500 mb-0.5">Email</p>
                <p class="font-medium text-gray-900">{{ $case->youngPerson->email ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Case details ── --}}
    <div x-show="tab==='caseDetails'" class="bg-white p-6 rounded-xl border border-gray-100">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Case details</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 mb-0.5">Case reference</p>
                <p class="font-medium text-gray-900">{{ $case->case_reference ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-0.5">Status</p>
                <p class="font-medium text-gray-900">{{ ucfirst($case->status ?? '—') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-0.5">Risk level</p>
                @php $risk = strtolower($case->risk_level ?? $case->risklevel ?? ''); @endphp
                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                    {{ $risk === 'high' || $risk === 'critical' ? 'bg-red-100 text-red-700'
                       : ($risk === 'medium' || $risk === 'moderate' ? 'bg-yellow-100 text-yellow-700'
                       : 'bg-green-100 text-green-700') }}">
                    {{ ucfirst($risk) ?: '—' }}
                </span>
            </div>
            <div>
                <p class="text-gray-500 mb-0.5">Last reviewed</p>
                <p class="font-medium text-gray-900">
                    {{ $case->last_reviewed_at ? \Carbon\Carbon::parse($case->last_reviewed_at)->format('d M Y') : '—' }}
                </p>
            </div>
            @if($case->summary)
            <div class="sm:col-span-2">
                <p class="text-gray-500 mb-0.5">Summary</p>
                <p class="font-medium text-gray-900">{{ $case->summary }}</p>
            </div>
            @endif
            @if(!$case->users->where('pivot.role', 'social_worker')->isEmpty())
            <div class="sm:col-span-2">
                <p class="text-gray-500 mb-0.5">Assigned social worker</p>
                @foreach($case->users->where('pivot.role', 'social_worker') as $sw)
                    <p class="font-medium text-gray-900">{{ $sw->name }}</p>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Placement ── --}}
    <div x-show="tab==='placements'" class="bg-white p-6 rounded-xl border border-gray-100">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Placement</h2>
        @php $currentPlacement = $case->placements->whereNull('end_date')->first() ?? $case->placements->first(); @endphp
        @if($currentPlacement)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-0.5">Type</p>
                    <p class="font-medium text-gray-900">{{ $currentPlacement->type ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-0.5">Location</p>
                    <p class="font-medium text-gray-900">{{ $currentPlacement->location ?? $currentPlacement->address ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-0.5">Start date</p>
                    <p class="font-medium text-gray-900">
                        {{ $currentPlacement->start_date ? \Carbon\Carbon::parse($currentPlacement->start_date)->format('d M Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 mb-0.5">End date</p>
                    <p class="font-medium text-gray-900">{{ $currentPlacement->end_date ? \Carbon\Carbon::parse($currentPlacement->end_date)->format('d M Y') : 'Current' }}</p>
                </div>
                @if($currentPlacement->notes)
                <div class="sm:col-span-2">
                    <p class="text-gray-500 mb-0.5">Notes</p>
                    <p class="font-medium text-gray-900">{{ $currentPlacement->notes }}</p>
                </div>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-500">No placement assigned.</p>
        @endif
    </div>

    {{-- ── Medical ── --}}
    <div x-show="tab==='medical'" class="bg-white p-6 rounded-xl border border-gray-100">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Medical information</h2>
        @forelse($case->medicalInfos ?? collect() as $info)
            <div class="border border-gray-100 rounded-lg p-4 mb-3 text-sm">
                <p class="font-medium text-gray-900">{{ $info->condition }}</p>
                @if($info->notes)<p class="text-gray-500 mt-1">{{ $info->notes }}</p>@endif
            </div>
        @empty
            <p class="text-sm text-gray-500">No medical information recorded.</p>
        @endforelse
    </div>

    {{-- ── Education ── --}}
    <div x-show="tab==='education'" class="bg-white p-6 rounded-xl border border-gray-100">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Education</h2>
        @forelse($case->educationInfos ?? collect() as $edu)
            <div class="border border-gray-100 rounded-lg p-4 mb-3 text-sm">
                <p class="font-medium text-gray-900">
                    {{ $edu->school_name }}
                    @if($edu->grade)<span class="text-gray-400 font-normal ml-1">· {{ $edu->grade }}</span>@endif
                </p>
                @if($edu->notes)<p class="text-gray-500 mt-1">{{ $edu->notes }}</p>@endif
            </div>
        @empty
            <p class="text-sm text-gray-500">No education records.</p>
        @endforelse
    </div>

    {{-- ── Documents ── --}}
    <div x-show="tab==='documents'" class="bg-white p-6 rounded-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Documents</h2>
            <a href="{{ route('carer.documents.index') }}"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                All my documents →
            </a>
        </div>

        {{-- Upload form --}}
        <form method="POST"
              enctype="multipart/form-data"
              action="{{ route('carer.cases.documents.store', $case) }}"
              class="border border-dashed border-gray-200 rounded-lg p-4 space-y-3">
            @csrf
            <p class="text-xs font-medium text-gray-700">Upload a document</p>
            <div class="flex gap-3 flex-wrap">
                <input name="name" required placeholder="Document name"
                       class="flex-1 min-w-48 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <input type="file" name="file" required
                       class="text-sm text-gray-500 border border-gray-200 rounded-lg px-3 py-2">
                <button type="submit"
                        class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Upload
                </button>
            </div>
        </form>

        @forelse($case->documents as $d)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-sky-50 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $d->name ?? $d->title ?? 'Document' }}</p>
                        <p class="text-xs text-gray-400">Uploaded {{ $d->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
                <a href="{{ \Illuminate\Support\Facades\Storage::url($d->file_path) }}" target="_blank"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">
                    View →
                </a>
            </div>
        @empty
            <p class="text-sm text-gray-500">No documents uploaded.</p>
        @endforelse
    </div>

    {{-- ── Appointments ── --}}
    <div x-show="tab==='appointments'" class="bg-white p-6 rounded-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Appointments</h2>
            <a href="{{ route('carer.calendar') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                Open calendar →
            </a>
        </div>

        @php $next = ($case->appointments ?? collect())->where('start_time', '>=', now())->sortBy('start_time')->first(); @endphp
        @if($next)
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-sm">
                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">Next appointment</p>
                <p class="font-medium text-gray-900">{{ $next->title ?? 'Appointment' }}</p>
                <p class="text-gray-600 mt-0.5">
                    {{ \Carbon\Carbon::parse($next->start_time)->format('d M Y · g:i A') }}
                    @if($next->end_time) – {{ \Carbon\Carbon::parse($next->end_time)->format('g:i A') }} @endif
                </p>
                @if($next->location)<p class="text-gray-500 text-xs mt-0.5">{{ $next->location }}</p>@endif
            </div>
        @endif

        @forelse(($case->appointments ?? collect())->sortByDesc('start_time') as $appt)
            <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                <div class="text-center shrink-0 w-10">
                    <p class="text-[10px] text-gray-400 uppercase leading-none">{{ \Carbon\Carbon::parse($appt->start_time)->format('M') }}</p>
                    <p class="text-lg font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($appt->start_time)->format('d') }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $appt->title ?? 'Appointment' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }}
                        @if($appt->end_time) – {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }} @endif
                        @if($appt->location) · {{ $appt->location }} @endif
                    </p>
                </div>
                @if(\Carbon\Carbon::parse($appt->start_time)->isFuture())
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full shrink-0">Upcoming</span>
                @else
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full shrink-0">Past</span>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500">No appointments scheduled.</p>
        @endforelse
    </div>

    {{-- ── Wellbeing ── --}}
<div x-show="tab==='wellbeing'" class="space-y-4">

    {{-- ── Latest wellbeing snapshot ──────────────────────────────────────── --}}
    @if($latestCheck)
    @php
        $wRisk    = strtolower($latestCheck->risk_level ?? 'low');
        $score    = round($latestCheck->overall_score ?? 0);
        $isCrit   = in_array($wRisk, ['high', 'critical']);
        $isMod    = in_array($wRisk, ['medium', 'moderate']);
        $ringColor = $isCrit ? '#ef4444' : ($isMod ? '#eab308' : '#22c55e');
        $badgeCls  = $isCrit ? 'bg-red-100 text-red-700' : ($isMod ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
        $isProxy   = $latestCheck->check_type === 'carer_proxy';
    @endphp
    <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center gap-5">
        {{-- Score ring --}}
        <div class="shrink-0 w-16 h-16 relative flex items-center justify-center">
            <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none"
                    stroke="{{ $ringColor }}" stroke-width="3"
                    stroke-dasharray="{{ $score }},100" stroke-linecap="round"/>
            </svg>
            <span class="absolute text-sm font-bold text-gray-800">{{ $score }}</span>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <p class="text-sm font-semibold text-gray-900">Latest wellbeing check</p>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ ucfirst($wRisk) }}</span>
                @if($isProxy)
                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-amber-100 text-amber-600">Your submission</span>
                @endif
            </div>
            <p class="text-xs text-gray-400">
                {{ $latestCheck->completed_at?->format('d M Y') ?? $latestCheck->created_at->format('d M Y') }}
                · {{ $isProxy ? 'Carer proxy observation' : 'Young person self-report' }}
            </p>
            @if($latestCheck->domainScores && $latestCheck->domainScores->count() > 0)
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-1.5">
                @foreach($latestCheck->domainScores->take(4) as $ds)
                    @php
                        $pct  = round($ds->average_score ?? 0);
                        $barC = $pct < 35 ? 'bg-red-300' : ($pct < 60 ? 'bg-yellow-300' : 'bg-green-300');
                    @endphp
                    <div>
                        <div class="flex justify-between mb-0.5">
                            <span class="text-[10px] text-gray-500 truncate">{{ $ds->domain->name ?? '' }}</span>
                            <span class="text-[10px] text-gray-400 ml-1 shrink-0">{{ $pct }}</span>
                        </div>
                        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $barC }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        <a href="{{ route('carer.proxy.wellbeing.create', $case) }}"
           class="bg-indigo-600 text-white text-xs font-semibold px-3 py-2 rounded-xl hover:bg-indigo-700 transition shrink-0">
            Submit update →
        </a>
    </div>
    @else
    {{-- No checks yet --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-900">No wellbeing checks yet</p>
            <p class="text-xs text-gray-400 mt-0.5">Submit a proxy observation to help the social worker monitor this young person's wellbeing.</p>
        </div>
        <a href="{{ route('carer.proxy.wellbeing.create', $case) }}"
           class="bg-indigo-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-indigo-700 transition shrink-0">
            Submit update →
        </a>
    </div>
    @endif

    {{-- ── Active goals ─────────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-900">Goals & progress</p>
                <p class="text-xs text-gray-400 mt-0.5">Goals set by the social worker for this young person</p>
            </div>
            @if($activeGoals->isNotEmpty())
                <span class="text-xs text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded-full">
                    {{ $activeGoals->count() }} active
                </span>
            @endif
        </div>

        @forelse($activeGoals as $goal)
        @php
            $tasks      = $tasksByCaseGoal[$goal->case_goal_id] ?? collect();
            $done       = $tasks->filter(fn($t) => $t->completed_at !== null)->count();
            $total      = $tasks->count();
            $pct        = $total > 0 ? round(($done / $total) * 100) : 0;
            $accepted   = !empty($goal->child_accepted_at);
        @endphp
        <div class="px-5 py-4 border-b border-gray-50 last:border-0" x-data="{ expanded: false }">
            <div class="flex items-start gap-3">
                {{-- Progress ring --}}
                <div class="shrink-0 w-10 h-10 relative flex items-center justify-center mt-0.5">
                    <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="4"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                            stroke="{{ $pct === 100 ? '#22c55e' : '#818cf8' }}" stroke-width="4"
                            stroke-dasharray="{{ $pct }},100" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-[9px] font-bold {{ $pct === 100 ? 'text-green-600' : 'text-indigo-600' }}">
                        {{ $pct }}%
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $goal->title }}</p>
                        @if($goal->domain_name)
                            <span class="text-[10px] font-medium bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full">
                                {{ $goal->domain_name }}
                            </span>
                        @endif
                        @if($accepted)
                            <span class="text-[10px] font-medium bg-green-50 text-green-600 px-2 py-0.5 rounded-full">Accepted ✓</span>
                        @else
                            <span class="text-[10px] font-medium bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Not yet accepted</span>
                        @endif
                    </div>

                    @if($goal->description)
                        <p class="text-xs text-gray-500 mb-2">{{ $goal->description }}</p>
                    @endif

                    {{-- Progress bar --}}
                    @if($total > 0)
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-300
                                {{ $pct === 100 ? 'bg-green-400' : 'bg-indigo-400' }}"
                                style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 shrink-0">{{ $done }}/{{ $total }} tasks</span>
                    </div>
                    @endif

                    @if($goal->due_date)
                        @php $overdue = \Carbon\Carbon::parse($goal->due_date)->isPast(); @endphp
                        <p class="text-[10px] {{ $overdue ? 'text-red-500' : 'text-gray-400' }}">
                            {{ $overdue ? 'Overdue · ' : 'Due · ' }}{{ \Carbon\Carbon::parse($goal->due_date)->format('d M Y') }}
                        </p>
                    @endif
                </div>

                @if($total > 0)
                <button @click="expanded = !expanded"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0 mt-1">
                    <span x-text="expanded ? 'Hide tasks' : 'View tasks'"></span>
                </button>
                @endif
            </div>

            {{-- Tasks list --}}
            @if($total > 0)
            <div x-show="expanded" x-transition class="mt-3 pl-13 space-y-2 ml-13">
                <div class="ml-[52px] space-y-2">
                    @foreach($tasks as $task)
                    @php $isDone = !empty($task->completed_at); @endphp
                    <div class="flex items-start gap-2.5 p-2.5 rounded-lg {{ $isDone ? 'bg-green-50' : 'bg-gray-50' }}">
                        <div class="shrink-0 w-4 h-4 rounded border mt-0.5 flex items-center justify-center
                            {{ $isDone ? 'bg-green-400 border-green-400' : 'border-gray-300 bg-white' }}">
                            @if($isDone)
                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium {{ $isDone ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                {{ $task->title }}
                            </p>
                            @if($task->description)
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $task->description }}</p>
                            @endif
                        </div>
                        @if($isDone)
                            <span class="text-[10px] text-green-600 font-medium shrink-0">Done ✓</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-gray-400">No active goals yet.</p>
            <p class="text-xs text-gray-300 mt-0.5">Goals will appear here once the social worker has approved them.</p>
        </div>
        @endforelse
    </div>

    {{-- ── Completed goals ─────────────────────────────────────────────── --}}
    @if($completedGoals->isNotEmpty())
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">Completed goals</p>
        </div>
        @foreach($completedGoals as $goal)
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-50 last:border-0">
            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-700 truncate">{{ $goal->title }}</p>
                @if($goal->domain_name)
                    <span class="text-[10px] text-gray-400">{{ $goal->domain_name }}</span>
                @endif
            </div>
            <span class="text-xs text-gray-400 shrink-0">
                {{ \Carbon\Carbon::parse($goal->completed_at)->format('d M Y') }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
        <div class="pt-4 border-t border-gray-100">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-900">Submit a proxy wellbeing update</p>
        </div>
        <a href="{{ route('carer.proxy.wellbeing.create', $case) }}"
           class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-xl hover:bg-indigo-700 transition shrink-0">
            Submit update →
        </a>
    </div>
</div>


</div>
    </div>

</div>

</x-app-layout>
