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
            <a href="{{ asset('storage/'.$d->file_path) }}"
               class="text-indigo-600 text-sm block">
                {{ $d->title ?? 'Document' }}
            </a>
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

    {{-- Recent Checks Table --}}
    @if($case->wellbeingChecks->isNotEmpty())
    <div class="mt-6">
        <h3 class="font-medium text-gray-900 mb-3">Wellbeing Check History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 border-b">
                    <tr>
                        <th class="py-2">Date</th>
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
                    <tr class="border-b hover:bg-gray-50 {{ $loop->first ? 'bg-blue-50' : '' }}">
                        <td class="py-2 font-medium">
                            {{ $check->created_at->format('d M Y') }}
                            @if($loop->first) <span class="text-blue-600 text-xs">(Latest)</span> @endif
                        </td>
                        <td class="py-2 text-center font-medium">{{ round($check->overall_score, 1) }}</td>
                        <td class="py-2 text-center">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($check->risk_level === 'high') bg-red-100 text-red-700
                                @elseif($check->risk_level === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ ucfirst($check->risk_level) }}
                            </span>
                        </td>
                        <td class="py-2 text-center">{{ $check->emotional_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->behavioural_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->social_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->physical_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->education_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->safety_score ?? '—' }}</td>
                        <td class="py-2 text-center">{{ $check->life_satisfaction_score ?? '—' }}</td>
                        <td class="py-2 text-center">
                            <button
                                @click="showCheckDetails({{ $check->id }})"
                                class="text-indigo-600 hover:text-indigo-800 text-sm underline"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
                                      :class="checkDetails.risk_level === 'high' ? 'bg-red-100 text-red-700' :
                                             checkDetails.risk_level === 'medium' ? 'bg-yellow-100 text-yellow-700' :
                                             'bg-green-100 text-green-700'"
                                      x-text="checkDetails.risk_level_capitalized"></span>
                            </div>
                            <div>
                                <p class="text-gray-500">Submitted By</p>
                                <p class="font-medium" x-text="checkDetails.submitted_by_name || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Domain Scores --}}
                    <div>
                        <h4 class="font-medium mb-2">Domain Scores</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Emotional</p>
                                <p class="font-semibold" x-text="checkDetails.emotional_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Behavioural</p>
                                <p class="font-semibold" x-text="checkDetails.behavioural_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Social</p>
                                <p class="font-semibold" x-text="checkDetails.social_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Physical</p>
                                <p class="font-semibold" x-text="checkDetails.physical_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Education</p>
                                <p class="font-semibold" x-text="checkDetails.education_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Safety</p>
                                <p class="font-semibold" x-text="checkDetails.safety_score || '—'"></p>
                            </div>
                            <div class="bg-white border p-3 rounded text-center">
                                <p class="text-gray-500 text-sm">Life Satisfaction</p>
                                <p class="font-semibold" x-text="checkDetails.life_satisfaction_score || '—'"></p>
                            </div>
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
        visibleDomains: ['overall'], // Start with overall visible
        selectedCheck: null,
        checkDetails: null,

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