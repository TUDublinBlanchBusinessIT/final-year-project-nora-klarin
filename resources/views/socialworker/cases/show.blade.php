<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('socialworker.cases.index') }}" class="hover:text-gray-700">Cases</a>
                <span>/</span>
                <span class="text-gray-900">
                    {{ $case->case_reference ?? ('Case #' . $case->id) }}
                </span>
            </div>

            <h1 class="text-xl font-semibold text-gray-900">
                {{ $case->youngPerson->name ?? 'Unassigned' }}
            </h1>
                        <a href="{{ route('socialworker.cases.report', $case) }}"
            target="_blank"
            class="bg-slate-100 text-slate-700 text-sm px-3 py-1.5 rounded-lg hover:bg-slate-200 flex items-left gap-1.5">
                Generate report
            </a>
        </div>

        @if(auth()->user()->role === 'social_worker')
            <a href="{{ route('socialworker.appointments.create', $case) }}"
               class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
                + New appointment
            </a>
        @endif
    </div>
</x-slot>

{{-- SINGLE ALPINE ROOT --}}
<div x-data="caseShow()" x-init="init()" class="space-y-4">

    {{-- ================= TABS ================= --}}
    <div class="flex flex-wrap border-b border-gray-200">
        @foreach([
            'personal' => 'Personal info',
            'caseDetails' => 'Case details',
            'placements' => 'Placements',
            'medical' => 'Medical',
            'education' => 'Education',
            'documents' => 'Documents',
            'appointments' => 'Appointments',
            'wellbeing' => 'Wellbeing',
            'goals' => 'Goals'
        ] as $key => $label)
            <button
                @click="tab='{{ $key }}'"
                :class="tab==='{{ $key }}'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'text-gray-500 border-transparent'"
                class="py-2 px-4 text-sm font-medium border-b-2"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ================= PERSONAL ================= --}}
    <div x-show="tab==='personal'" class="bg-white p-6 rounded-xl">
        <h2 class="font-semibold mb-3">Personal information</h2>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Name</p>
                <p>{{ $case->youngPerson->name ?? '—' }}</p>
            </div>

            <div>
                <p class="text-gray-500">DOB</p>
                <p>
                    @if($case->youngPerson?->dob)
                        {{ \Carbon\Carbon::parse($case->youngPerson->dob)->format('d M Y') }}
                    @else — @endif
                </p>
            </div>

            <div>
                <p class="text-gray-500">Email</p>
                <p>{{ $case->youngPerson->email ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ================= CASE ================= --}}
    <div x-show="tab==='caseDetails'" class="bg-white p-6 rounded-xl">
        <h2 class="font-semibold mb-3">Case details</h2>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Status</p>
                <p>{{ $case->status ?? '—' }}</p>
            </div>

            <div>
                <p class="text-gray-500">Risk level</p>
                @php $risk = strtolower($case->risk_level ?? ''); @endphp

                <span class="px-2 py-1 text-xs rounded-full
                    @if($risk==='high') bg-red-100 text-red-700
                    @elseif($risk==='medium') bg-yellow-100 text-yellow-700
                    @else bg-green-100 text-green-700 @endif">
                    {{ ucfirst($risk) ?: '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ================= PLACEMENTS (KEPT MODAL) ================= --}}
    <div x-show="tab==='placements'" class="bg-white p-6 rounded-xl space-y-4">

        <h2 class="font-semibold">Placements</h2>

        @php $current = $case->placements->whereNull('end_date')->first(); @endphp

        @if($current)
            <div class="bg-indigo-50 p-4 rounded-lg text-sm">
                <p><b>Current:</b> {{ $current->type }}</p>
                <p>{{ $current->address ?? '—' }}</p>
            </div>
        @endif

        @if(auth()->user()->role === 'social_worker')
        <div x-data="{ open: false }">

            <button @click="open=true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                Add placement
            </button>

            <div x-show="open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-2xl p-6 rounded-xl" @click.away="open=false">

                    <h2 class="font-semibold mb-4">Add Placement</h2>

                    <form method="POST"
                          action="{{ route('socialworker.cases.placements.store', $case) }}"
                          class="space-y-3">
                        @csrf

                        <input name="type" class="w-full border p-2 rounded" placeholder="Type">
                        <input name="location" class="w-full border p-2 rounded" placeholder="Location">

                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="start_date" class="border p-2 rounded">
                            <input type="date" name="end_date" class="border p-2 rounded">
                        </div>

                        <textarea name="notes" class="w-full border p-2 rounded" placeholder="Notes"></textarea>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="open=false" class="text-gray-600">Cancel</button>
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= MEDICAL (KEPT MODAL) ================= --}}
    <div x-show="tab==='medical'" class="bg-white p-6 rounded-xl space-y-4">

        <h2 class="font-semibold">Medical</h2>

        @forelse($case->medicalInfos as $m)
            <div class="border p-3 rounded text-sm">
                <b>{{ $m->condition }}</b>
                <p>{{ $m->notes }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No medical records.</p>
        @endforelse

        @if(auth()->user()->role === 'social_worker')
        <div x-data="{ open: false }">

            <button @click="open=true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                Add medical info
            </button>

            <div x-show="open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-lg p-6 rounded-xl" @click.away="open=false">

                    <h2 class="font-semibold mb-4">Add Medical Info</h2>

                    <form method="POST"
                          action="{{ route('socialworker.cases.medical.store', $case) }}"
                          class="space-y-3">
                        @csrf

                        <input name="condition" class="w-full border p-2 rounded" placeholder="Condition">
                        <textarea name="notes" class="w-full border p-2 rounded" placeholder="Notes"></textarea>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="open=false">Cancel</button>
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= EDUCATION (KEPT MODAL) ================= --}}
    <div x-show="tab==='education'" class="bg-white p-6 rounded-xl space-y-4">

        <h2 class="font-semibold">Education</h2>

        @forelse($case->educationInfos as $e)
            <div class="border p-3 rounded text-sm">
                <b>{{ $e->school_name }}</b>
                <p>{{ $e->grade }}</p>
                <p>{{ $e->notes }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No education records.</p>
        @endforelse

        @if(auth()->user()->role === 'social_worker')
        <div x-data="{ open: false }">

            <button @click="open=true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                Add education info
            </button>

            <div x-show="open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-lg p-6 rounded-xl" @click.away="open=false">

                    <h2 class="font-semibold mb-4">Add Education Info</h2>

                    <form method="POST"
                          action="{{ route('socialworker.cases.education.store', $case) }}"
                          class="space-y-3">
                        @csrf

                        <input name="school_name" class="w-full border p-2 rounded" placeholder="School">
                        <input name="grade" class="w-full border p-2 rounded" placeholder="Grade">
                        <textarea name="notes" class="w-full border p-2 rounded" placeholder="Notes"></textarea>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="open=false">Cancel</button>
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= DOCUMENTS (KEPT MODAL) ================= --}}
    <div x-show="tab==='documents'" class="bg-white p-6 rounded-xl space-y-4">

        <h2 class="font-semibold">Documents</h2>

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

        @if(auth()->user()->role === 'social_worker')
        <div x-data="{ open: false }">

            <button @click="open=true"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                Upload document
            </button>

            <div x-show="open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-lg p-6 rounded-xl" @click.away="open=false">

                    <h2 class="font-semibold mb-4">Upload Document</h2>

                    <form method="POST"
                          enctype="multipart/form-data"
                          action="{{ route('socialworker.cases.documents.store', $case) }}"
                          class="space-y-3">
                        @csrf

                        <input name="title" class="w-full border p-2 rounded" placeholder="Title">
                        <input type="file" name="file" class="w-full border p-2 rounded">

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="open=false">Cancel</button>
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Upload</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= APPOINTMENTS ================= --}}
    <div x-show="tab==='appointments'" x-transition class="bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Appointments</h2>
        @php $next = $case->appointments->where('start_time', '>=', now())->sortBy('start_time')->first(); @endphp
        @if($next)
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm">
            <p class="font-medium text-blue-800">Next appointment</p>
            <p class="text-gray-700 mt-1">{{ \Carbon\Carbon::parse($next->start_time)->format('d M Y H:i') }}</p>
            <p class="text-gray-500">{{ $next->location ?? 'Location TBC' }}</p>
        </div>
        @endif

        @if($case->appointments->isEmpty())
            <p class="text-sm text-gray-500">No appointments scheduled.</p>
        @else
        <ul class="space-y-2">
            @foreach($case->appointments->sortByDesc('start_time') as $appt)
            <li class="border border-gray-100 rounded-lg p-4 text-sm">
                <p class="font-medium text-gray-900">
                    {{ \Carbon\Carbon::parse($appt->start_time)->format('d M Y H:i') }}
                    – {{ $appt->end_time ? \Carbon\Carbon::parse($appt->end_time)->format('H:i') : 'TBC' }}
                </p>
                <p class="text-gray-500 mt-0.5">{{ $appt->location ?? '—' }}</p>
                @if($appt->notes)<p class="text-gray-500">{{ $appt->notes }}</p>@endif
                @if($appt->carers->count())
                <p class="text-gray-400 text-xs mt-1">Carers: {{ $appt->carers->pluck('name')->join(', ') }}</p>
                @endif
            </li>
            @endforeach
        </ul>
        @endif

        @if(auth()->user()->role === 'social_worker')
        <a href="{{ route('socialworker.appointments.create', $case) }}"
           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            Create appointment
        </a>
        @endif
    </div>


 {{-- ================= WELLBEING ================= --}}
<div x-show="tab==='wellbeing'" class="bg-white p-6 rounded-xl space-y-4">

    <h2 class="font-semibold text-gray-900">Wellbeing Trend</h2>

    {{-- Chart Controls --}}
    <div class="flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2 flex-wrap">
            <button
                @click="toggleDomain('overall')"
                :class="visibleDomains.includes('overall') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Overall
            </button>
            <button
                @click="toggleDomain('emotional')"
                :class="visibleDomains.includes('emotional') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Emotional
            </button>
            <button
                @click="toggleDomain('behavioural')"
                :class="visibleDomains.includes('behavioural') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Behavioural
            </button>
            <button
                @click="toggleDomain('social')"
                :class="visibleDomains.includes('social') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Social
            </button>
            <button
                @click="toggleDomain('physical')"
                :class="visibleDomains.includes('physical') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Physical
            </button>
            <button
                @click="toggleDomain('education')"
                :class="visibleDomains.includes('education') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Education
            </button>
            <button
                @click="toggleDomain('safety')"
                :class="visibleDomains.includes('safety') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Safety
            </button>
            <button
                @click="toggleDomain('life_satisfaction')"
                :class="visibleDomains.includes('life_satisfaction') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1 rounded-lg text-sm font-medium transition"
            >
                Life Satisfaction
            </button>
        </div>

        <div class="text-sm text-gray-500">
            @if($case->wellbeingChecks->isNotEmpty())
                Last check: {{ $case->wellbeingChecks->sortBy('created_at')->last()->created_at->format('d M Y') }}
            @endif
        </div>
    </div>

    {{-- Chart --}}
    <div class="h-80">
        <canvas id="wellbeingTrend"></canvas>
    </div>

@if($case->wellbeingChecks->isNotEmpty())
<div class="mt-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-medium text-gray-900">Wellbeing check history</h3>
        <div class="flex items-center gap-3 text-xs text-gray-400">
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400 inline-block"></span> Self-report
            </span>
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Carer proxy
            </span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b">
                <tr>
                    <th class="py-2 pr-3">Date</th>
                    <th class="py-2 pr-3">Type</th>
                    <th class="py-2 pr-3">Submitted by</th>
                    <th class="py-2 text-center">Overall</th>
                    <th class="py-2 text-center">Risk</th>
                    <th class="py-2 text-center">Emotional</th>
                    <th class="py-2 text-center">Behavioural</th>
                    <th class="py-2 text-center">Social</th>
                    <th class="py-2 text-center">Physical</th>
                    <th class="py-2 text-center">Education</th>
                    <th class="py-2 text-center">Safety</th>
                    <th class="py-2 text-center">Life Sat.</th>
                    <th class="py-2 text-center">Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($case->wellbeingChecks->sortByDesc('created_at') as $check)
                @php
                    $isProxy  = $check->check_type === 'carer_proxy';
                    $rowBg    = $loop->first ? 'bg-blue-50' : ($isProxy ? 'bg-amber-50/40' : '');
                    $typeBadge = $isProxy
                        ? 'bg-amber-100 text-amber-700'
                        : ($check->check_type === 'intake'
                            ? 'bg-purple-100 text-purple-700'
                            : 'bg-indigo-50 text-indigo-600');
                    $typeLabel = $isProxy ? 'Carer proxy' : ucfirst($check->check_type ?? 'Scheduled');
                    $submitter = $check->submittedBy?->name
                        ?? ($isProxy ? 'Carer' : 'Young person');
                @endphp
                <tr class="border-b hover:bg-gray-50 {{ $rowBg }}">
                    <td class="py-2 pr-3 font-medium whitespace-nowrap">
                        {{ $check->created_at->format('d M Y') }}
                        @if($loop->first)
                            <span class="text-blue-600 text-xs ml-1">(Latest)</span>
                        @endif
                    </td>
                    <td class="py-2 pr-3">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $typeBadge }}">
                            {{ $typeLabel }}
                        </span>
                    </td>
                    <td class="py-2 pr-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $submitter }}
                    </td>
                    <td class="py-2 text-center font-medium">
                        {{ $check->overall_score !== null ? round($check->overall_score, 1) : '—' }}
                    </td>
                    <td class="py-2 text-center">
                        @php $rl = strtolower($check->risk_level ?? ''); @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full
                            @if(in_array($rl, ['high','critical'])) bg-red-100 text-red-700
                            @elseif(in_array($rl, ['medium','moderate'])) bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ ucfirst($rl) ?: '—' }}
                        </span>
                    </td>
                    <td class="py-2 text-center text-gray-600">{{ $check->emotional_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->behavioural_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->social_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->physical_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->education_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->safety_score ?? '—' }}</td>
                    <td class="py-2 text-center text-gray-600">{{ $check->life_satisfaction_score ?? '—' }}</td>
                    <td class="py-2 text-center">
                        <button
                            @click="showCheckDetails({{ $check->id }})"
                            class="text-indigo-600 hover:text-indigo-800 text-xs underline">
                            View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $selfReportCount = $case->wellbeingChecks->whereIn('check_type', ['intake', 'scheduled'])->count();
        $proxyCount      = $case->wellbeingChecks->where('check_type', 'carer_proxy')->count();
    @endphp
    @if($proxyCount > 0)
    <div class="mt-3 flex items-center gap-4 text-xs text-gray-400 border-t border-gray-100 pt-3">
        <span>{{ $selfReportCount }} self-report {{ Str::plural('check', $selfReportCount) }}</span>
        <span>·</span>
        <span>{{ $proxyCount }} carer {{ Str::plural('proxy', $proxyCount) }}</span>
        <span>·</span>
        <span class="text-gray-500">Risk level reflects the most recent check regardless of type</span>
    </div>
    @endif
</div>
@endif

    {{-- Check Details Modal --}}
    <div
        x-show="selectedCheck !== null"
        x-transition
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.away="selectedCheck = null"
    >
        <div class="bg-white w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6 rounded-xl" @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Wellbeing Check Details</h3>
                <button @click="selectedCheck = null" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <template x-if="checkDetails">
                <div class="space-y-4">
                    {{-- Check Info --}}
                    <div class="bg-gray-50 p-4 rounded-lg">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Date</p>
            <p class="font-medium" x-text="checkDetails.created_at_formatted"></p>
        </div>
        <div>
            <p class="text-gray-500">Overall Score</p>
            <p class="font-medium" x-text="checkDetails.overall_score"></p>
        </div>
        <div>
            <p class="text-gray-500">Risk Level</p>
            <span class="px-2 py-1 text-xs rounded-full"
                  :class="checkDetails.risk_level === 'high' || checkDetails.risk_level === 'critical'
                      ? 'bg-red-100 text-red-700'
                      : checkDetails.risk_level === 'medium' || checkDetails.risk_level === 'moderate'
                          ? 'bg-yellow-100 text-yellow-700'
                          : 'bg-green-100 text-green-700'"
                  x-text="checkDetails.risk_level_capitalized"></span>
        </div>
        <div>
            <p class="text-gray-500">Submitted By</p>
            <div class="flex items-center gap-2 mt-0.5">
                <p class="font-medium" x-text="checkDetails.submitted_by_name || '—'"></p>
                <span x-show="checkDetails.is_proxy"
                      class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">
                    Carer proxy
                </span>
            </div>
        </div>
    </div>

    {{-- Proxy notice --}}
    <div x-show="checkDetails.is_proxy"
         class="mt-3 flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
        <p class="text-xs text-amber-700">
            This check was submitted by a carer using the HBSC proxy-report observation methodology.
            Scores reflect carer observations rather than young person self-report.
        </p>
    </div>
</div>

                    {{-- Domain Changes --}}
                    <div x-show="checkDetails.domain_changes && checkDetails.domain_changes.length > 0">
                        <h4 class="font-medium mb-2">Domain Changes (vs Previous Check)</h4>
                        <div class="space-y-2">
                            <template x-for="change in checkDetails.domain_changes" :key="change.domain">
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <span class="capitalize" x-text="change.domain"></span>
                                    <span :class="change.change < 0 ? 'text-red-600' : 'text-green-600'"
                                          x-text="change.change > 0 ? '+' + change.change : change.change"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Alerts/Triggers --}}
                    <div x-show="checkDetails.alerts && checkDetails.alerts.length > 0">
                        <h4 class="font-medium mb-2">Alerts Triggered</h4>
                        <div class="space-y-2">
                            <template x-for="alert in checkDetails.alerts" :key="alert.id">
                                <div class="border-l-4 border-red-500 bg-red-50 p-3">
                                    <p class="font-medium text-red-800" x-text="alert.title"></p>
                                    <p class="text-red-700 text-sm" x-text="alert.description"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!checkDetails" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="text-gray-500 mt-2">Loading check details...</p>
            </div>
        </div>
    </div>

</div>
{{-- ================= GOALS ================= --}}
<div x-show="tab==='goals'" class="space-y-6">

    {{-- Pending suggestions from wellbeing checks --}}
    @if($pendingGoals->isNotEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h3 class="font-medium text-amber-900 mb-3">
            Suggested goals — awaiting approval
            <span class="ml-2 bg-amber-200 text-amber-800 text-xs px-2 py-0.5 rounded-full">{{ $pendingGoals->count() }}</span>
        </h3>
        <div class="space-y-3">
            @foreach($pendingGoals as $suggestion)
            <div class="bg-white border border-amber-100 rounded-lg p-4 flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="font-medium text-gray-900 text-sm">{{ $suggestion->title }}</p>
                    @if($suggestion->description)
                        <p class="text-gray-500 text-xs mt-1">{{ $suggestion->description }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-2">
                        @if($suggestion->domain_name)
                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full">{{ $suggestion->domain_name }}</span>
                        @endif
                        @if($suggestion->triggered_by_tags)
                            <span class="text-xs text-gray-400">triggered by: {{ $suggestion->triggered_by_tags }}</span>
                        @endif
                        <span class="text-xs text-gray-400">from check {{ \Carbon\Carbon::parse($suggestion->suggested_at)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="$dispatch('open-approve', {
                            id: {{ $suggestion->case_goal_id }},
                            title: '{{ addslashes($suggestion->title) }}',
                            desc: '{{ addslashes($suggestion->description ?? '') }}'
                        })"
                        class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700">
                        Review & approve
                    </button>
                    <form method="POST" action="{{ route('socialworker.goals.dismiss', $suggestion->case_goal_id) }}">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 text-xs px-2 py-1.5 hover:text-gray-600">Dismiss</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Active goals --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium text-gray-900">Active goals</h3>
            <button @click="showNewGoal=true" class="bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-lg hover:bg-indigo-700">
                + New goal
            </button>
        </div>

        @forelse($activeGoals as $goal)
        <div class="border border-gray-100 rounded-lg p-4 mb-3" x-data="{ expanded: false }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="font-medium text-sm text-gray-900">{{ $goal->title }}</p>
                        @if($goal->domain_name)
                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full">{{ $goal->domain_name }}</span>
                        @endif
                        @if($goal->child_accepted_at)
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full">accepted by child</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">not yet accepted</span>
                        @endif
                    </div>
                    @if($goal->description)
                        <p class="text-xs text-gray-500 mt-1">{{ $goal->description }}</p>
                    @endif
                    @php
                        $tasks = $tasksByCaseGoal[$goal->case_goal_id] ?? collect();
                        $done  = $tasks->whereNotNull('completed_at')->count();
                        $total = $tasks->count();
                    @endphp
                    @if($total > 0)
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $total > 0 ? round(($done/$total)*100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $done }}/{{ $total }} tasks</span>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($goal->due_date)
                        <span class="text-xs text-gray-400">Due {{ \Carbon\Carbon::parse($goal->due_date)->format('d M') }}</span>
                    @endif
                    <button @click="expanded=!expanded" class="text-indigo-600 text-xs hover:underline">
                        <span x-text="expanded ? 'Hide tasks' : 'Tasks (' + {{ $total }} + ')'"></span>
                    </button>
                    <form method="POST" action="{{ route('socialworker.goals.complete', $goal->case_goal_id) }}">
                        @csrf
                        <button class="text-xs text-green-600 hover:text-green-800">Mark complete</button>
                    </form>
                </div>

                {{-- Goal approval/reframe modal --}}
<div x-data="{
        open: false,
        caseGoalId: null,
        title: '',
        desc: '',
    }"
    @open-approve.window="
        open = true;
        caseGoalId = $event.detail.id;
        title = $event.detail.title;
        desc  = $event.detail.desc;
    "
    x-show="open"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    @click.away="open=false">

    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-2xl" @click.stop>
        <h3 class="font-semibold text-gray-900 mb-1">Approve & personalise goal</h3>
        <p class="text-xs text-gray-500 mb-4">
            Rewrite the goal title and description in child-friendly language before sending.
            The template wording is pre-filled — please edit it.
        </p>

        <form method="POST" :action="`/social-worker/goals/${caseGoalId}/approve`" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Goal title <span class="text-red-500">*</span>
                </label>
                <input name="title" x-model="title" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300"
                    placeholder="e.g. Making friends at school">
                <p class="text-xs text-gray-400 mt-1">Write this as the child will see it — warm, personal, achievable.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" x-model="desc" rows="3"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300"
                    placeholder="A short encouraging sentence about what this journey is about."></textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Due date (optional)</label>
                <input type="date" name="due_date"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="open=false" class="text-sm text-gray-500 px-3 py-2">Cancel</button>
                <button class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
                    ✅ Approve & send to child
                </button>
            </div>
        </form>
    </div>
</div>
            </div>
            {{-- Tasks panel --}}
<div x-show="expanded" x-transition class="mt-3 border-t pt-3 space-y-3">

    {{-- AI-suggested tasks awaiting SW review --}}
    @php $pendingTasks = $pendingTasksByCaseGoal[$goal->case_goal_id] ?? collect(); @endphp
    @if($pendingTasks->isNotEmpty())
    <div class="rounded-lg bg-violet-50 border border-violet-200 p-3">
        <p class="text-xs font-semibold text-violet-800 mb-2">
            ✨ AI-suggested tasks — review before sending to child
        </p>
        <div class="space-y-2">
            @foreach($pendingTasks as $pt)
            <div class="bg-white border border-violet-100 rounded-lg p-3"
                 x-data="{ editing: false, title: '{{ addslashes($pt->title) }}', desc: '{{ addslashes($pt->description ?? '') }}' }">

                {{-- View mode --}}
                <div x-show="!editing">
                    <p class="text-xs font-medium text-gray-800">{{ $pt->title }}</p>
                    @if($pt->description)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pt->description }}</p>
                    @endif
                    <div class="flex gap-2 mt-2">
                        {{-- Edit then publish --}}
                        <button @click="editing=true"
                            class="text-xs text-violet-700 border border-violet-300 px-2 py-1 rounded hover:bg-violet-50">
                            ✏️ Edit
                        </button>
                        {{-- Publish as-is --}}
                        <form method="POST" action="{{ route('socialworker.goals.tasks.publish', $pt->id) }}">
                            @csrf
                            <input type="hidden" name="title" :value="title">
                            <input type="hidden" name="description" :value="desc">
                            <button class="text-xs text-green-700 border border-green-300 px-2 py-1 rounded hover:bg-green-50">
                                ✅ Send to child
                            </button>
                        </form>
                        {{-- Discard --}}
                        <form method="POST" action="{{ route('socialworker.goals.tasks.delete', $pt->id) }}">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 border border-red-200 px-2 py-1 rounded hover:bg-red-50">
                                🗑 Discard
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Edit mode --}}
                <div x-show="editing">
                    <form method="POST" action="{{ route('socialworker.goals.tasks.publish', $pt->id) }}" class="space-y-2">
                        @csrf
                        <input name="title" x-model="title"
                            class="w-full text-xs border rounded px-2 py-1.5" placeholder="Task title">
                        <textarea name="description" x-model="desc" rows="2"
                            class="w-full text-xs border rounded px-2 py-1.5" placeholder="Description (optional)"></textarea>
                        <div class="flex gap-2">
                            <button class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                ✅ Save & send to child
                            </button>
                            <button type="button" @click="editing=false"
                                class="text-xs text-gray-500 px-2 py-1 hover:underline">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Published tasks (child can see these) --}}
    @if($tasks->isEmpty() && $pendingTasks->isEmpty())
        <p class="text-xs text-gray-400 mb-2">No tasks yet.</p>
    @elseif($tasks->isNotEmpty())
        <p class="text-xs font-medium text-gray-500 mb-1">Sent to child:</p>
        <ul class="space-y-1.5 mb-3">
            @foreach($tasks as $task)
            <li class="flex items-start gap-2">
                <form method="POST" action="{{ $task->completed_at
                    ? route('child.tasks.uncomplete', $task->id)
                    : route('socialworker.tasks.complete', $task->id) }}">
                    @csrf
                    <button class="mt-0.5 w-4 h-4 rounded border flex-shrink-0
                        {{ $task->completed_at ? 'bg-green-100 border-green-400' : 'border-gray-300' }}">
                    </button>
                </form>
                <div class="flex-1">
                    <p class="text-xs {{ $task->completed_at ? 'line-through text-gray-400' : 'text-gray-700' }}">
                        {{ $task->title }}
                        @if($task->ai_suggested)
                            <span class="text-violet-400 ml-1">✨</span>
                        @endif
                    </p>
                    @if($task->description)
                        <p class="text-xs text-gray-400">{{ $task->description }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('socialworker.goals.tasks.delete', $task->id) }}">
                    @csrf @method('DELETE')
                    <button class="text-gray-300 hover:text-red-400 text-xs">✕</button>
                </form>
            </li>
            @endforeach
        </ul>
    @endif

    {{-- Manual task add --}}
    <form method="POST" action="{{ route('socialworker.goals.tasks.store', $goal->case_goal_id) }}"
          class="flex gap-2 pt-2 border-t">
        @csrf
        <input name="title" class="flex-1 text-xs border rounded px-2 py-1" placeholder="Add a task manually...">
        <button class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded hover:bg-gray-200">Add</button>
    </form>
</div>
            {{-- Tasks panel --}}
            <div x-show="expanded" x-transition class="mt-3 border-t pt-3">
            </div>
        </div>
        @empty
            <p class="text-sm text-gray-400">No active goals. Approve a suggestion above or create one.</p>
        @endforelse
    </div>

    {{-- Completed goals --}}
    @if($completedGoals->isNotEmpty())
    <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h3 class="font-medium text-gray-900 mb-3">Completed goals</h3>
        <div class="space-y-2">
            @foreach($completedGoals as $goal)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm text-gray-700">{{ $goal->title }}</p>
                    @if($goal->domain_name)
                        <span class="text-xs text-gray-400">{{ $goal->domain_name }}</span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($goal->completed_at)->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- New goal modal --}}
    <div x-show="showNewGoal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.away="showNewGoal=false">
        <div class="bg-white w-full max-w-md p-6 rounded-xl" @click.stop>
            <h3 class="font-semibold mb-4">Create goal</h3>
            <form method="POST" action="{{ route('socialworker.goals.store', $case) }}" class="space-y-3">
                @csrf
                <input name="title" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Goal title" required>
                <textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Description (optional)" rows="2"></textarea>
                <select name="domain_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">No domain</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="due_date" class="w-full border rounded-lg px-3 py-2 text-sm">
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showNewGoal=false" class="text-sm text-gray-500">Cancel</button>
                    <button class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= CHART ================= --}}
@if($case->wellbeingChecks->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function caseShow() {

    const sorted = @json(
        $case->wellbeingChecks->sortBy('created_at')->values()
    );

    const labels = sorted.map(c =>
        new Date(c.created_at).toLocaleDateString('en-GB', { day:'2-digit', month:'short' })
    );

    const overall = sorted.map(c => c.overall_score);

    const domains = {
        emotional: sorted.map(c => c.emotional_score),
        behavioural: sorted.map(c => c.behavioural_score),
        social: sorted.map(c => c.social_score),
        physical: sorted.map(c => c.physical_score),
        education: sorted.map(c => c.education_score),
        safety: sorted.map(c => c.safety_score),
        life_satisfaction: sorted.map(c => c.life_satisfaction_score),
    };

    const domainColors = {
        overall: '#4F46E5', // indigo-600
        emotional: '#EF4444', // red-500
        behavioural: '#F59E0B', // amber-500
        social: '#10B981', // emerald-500
        physical: '#3B82F6', // blue-500
        education: '#8B5CF6', // violet-500
        safety: '#F97316', // orange-500
        life_satisfaction: '#06B6D4', // cyan-500
    };

    const domainLabels = {
        overall: 'Overall wellbeing',
        emotional: 'Emotional',
        behavioural: 'Behavioural',
        social: 'Social',
        physical: 'Physical',
        education: 'Education',
        safety: 'Safety',
        life_satisfaction: 'Life Satisfaction',
    };

    return {
        tab: '{{ $tab }}',
        chart: null,
        visibleDomains: ['overall'], 
        selectedCheck: null,
        checkDetails: null,
        showNewGoal: false,


    init() {
        // Initialize chart if starting on wellbeing tab
        if (this.tab === 'wellbeing') {
            this.$nextTick(() => this.createChart());
        }

        this.$watch('tab', val => {
            if (val === 'wellbeing' && !this.chart) {
                this.$nextTick(() => this.createChart());
            } else if (val !== 'wellbeing' && this.chart) {
                // Destroy chart when leaving wellbeing tab
                this.chart.destroy();
                this.chart = null;
            }
        });
    },

        createChart() {
            const ctx = document.getElementById('wellbeingTrend');
            if (!ctx || this.chart) return;

            // Check if the canvas is actually visible (in case tab changed)
            const isVisible = ctx.offsetParent !== null;
            if (!isVisible) {
                // Try again after a short delay
                setTimeout(() => this.createChart(), 100);
                return;
            }

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: this.buildDatasets()
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Wellbeing Score'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    }
                }
            });
        },

        buildDatasets() {
            const datasets = [];

            // Always include overall if visible
            if (this.visibleDomains.includes('overall')) {
                datasets.push({
                    label: domainLabels.overall,
                    data: overall,
                    borderColor: domainColors.overall,
                    backgroundColor: domainColors.overall + '20',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: sorted.map((_, index) => index === sorted.length - 1 ? 6 : 3),
                    pointHoverRadius: 6,
                    pointBackgroundColor: domainColors.overall,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                });
            }

            // Add domain datasets
            Object.entries(domains).forEach(([key, data]) => {
                if (this.visibleDomains.includes(key)) {
                    datasets.push({
                        label: domainLabels[key],
                        data: data,
                        borderColor: domainColors[key],
                        backgroundColor: domainColors[key] + '20',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: sorted.map((_, index) => index === sorted.length - 1 ? 5 : 2),
                        pointHoverRadius: 4,
                        pointBackgroundColor: domainColors[key],
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1,
                    });
                }
            });

            return datasets;
        },

        toggleDomain(domain) {
            if (domain === 'overall') {
                // Overall is always visible when toggled on, but can be toggled off
                if (this.visibleDomains.includes('overall')) {
                    this.visibleDomains = this.visibleDomains.filter(d => d !== 'overall');
                } else {
                    this.visibleDomains.push('overall');
                }
            } else {
                // For domains, toggle on/off
                if (this.visibleDomains.includes(domain)) {
                    this.visibleDomains = this.visibleDomains.filter(d => d !== domain);
                } else {
                    this.visibleDomains.push(domain);
                }
            }

            // Ensure at least overall is visible
            if (this.visibleDomains.length === 0) {
                this.visibleDomains = ['overall'];
            }

            this.updateChart();
        },

        updateChart() {
            if (!this.chart) {
                this.createChart();
                return;
            }

            // Destroy and recreate the chart to ensure immediate visual update
            this.chart.destroy();
            this.chart = null;
            
            this.$nextTick(() => {
                this.createChart();
            });
        },

        async showCheckDetails(checkId) {
            this.selectedCheck = checkId;
            this.checkDetails = null;

            try {
                const response = await fetch(`/social-worker/wellbeing-check/${checkId}/details`);
                if (response.ok) {
                    this.checkDetails = await response.json();
                } else {
                    console.error('Failed to load check details');
                }
            } catch (error) {
                console.error('Error loading check details:', error);
            }
        }
    }
}
</script>
@endpush
@endif

</x-app-layout>