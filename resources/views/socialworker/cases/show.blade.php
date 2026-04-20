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
<div x-data="caseShow()" x-init="initChart()" class="space-y-4">

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

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b">
                <tr>
                    <th class="py-2">Date</th>
                    <th class="py-2 text-center">Score</th>
                    <th class="py-2 text-center">Risk</th>
                </tr>
            </thead>

            <tbody>
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                <tr class="border-b">
                    <td class="py-2">{{ $check->created_at->format('d M Y') }}</td>
                    <td class="py-2 text-center">{{ round($check->overall_score, 1) }}</td>
                    <td class="py-2 text-center">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($check->risk_level === 'high') bg-red-100 text-red-700
                            @elseif($check->risk_level === 'medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ ucfirst($check->risk_level) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Controls --}}
    <div class="flex justify-between items-center">
        <p class="font-medium text-sm text-gray-900">Trend</p>

        <select
            class="border rounded-lg px-3 py-1 text-sm"
            @change="updateChart($event.target.value)"
        >
            <option value="overall">Overall</option>
            <option value="domains">Domains</option>
        </select>
    </div>

    <div class="h-64">
        <canvas id="wellbeingTrend"></canvas>
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

    return {
        tab: 'personal',
        chart: null,

        initChart() {
            const ctx = document.getElementById('wellbeingTrend');
            if (!ctx) return;

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Overall wellbeing',
                        data: overall,
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { min: 0, max: 100 }
                    }
                }
            });
        },

        updateChart(type) {
            if (!this.chart) return;

            if (type === 'overall') {
                this.chart.data.datasets = [{
                    label: 'Overall wellbeing',
                    data: overall,
                    borderWidth: 2,
                    tension: 0.3
                }];
            }

            if (type === 'domains') {
                this.chart.data.datasets = Object.entries(domains).map(([key, data]) => ({
                    label: key.replace('_', ' '),
                    data: data,
                    borderWidth: 2,
                    tension: 0.3
                }));
            }

            this.chart.update();
        }
    }
}
</script>
@endpush
@endif

</x-app-layout>