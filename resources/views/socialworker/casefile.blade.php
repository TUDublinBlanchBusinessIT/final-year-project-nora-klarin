<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $case->case_reference }} - {{ $case->youngPerson->name ?? 'Unassigned' }}        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="{ tab: 'child' }" x-cloak class="space-y-4">

                {{-- Tabs --}}
                <nav class="flex space-x-4 border-b border-gray-200">
                    <template x-for="t in ['child','caseDetails','placements','medical','education','documents','appointments', 'wellbeing']" :key="t">
                        <button 
                            @click="tab = t"
                            :class="tab === t ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'" 
                            class="py-2 px-4 border-b-2 font-medium capitalize"
                            x-text="t.replace(/([A-Z])/g, ' $1')"
                        ></button>
                    </template>
                </nav>

                {{-- Tab Contents --}}
                <div class="mt-4 space-y-6">

                    {{-- Child --}}
                    <div x-show="tab === 'child'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                        <p><strong>Name:</strong> {{ $case->youngPerson->name ?? 'N/A' }}</p>
                        <p><strong>DOB:</strong> {{ $case->youngPerson->dob ?? 'N/A' }} ({{ \Carbon\Carbon::parse($case->youngPerson->dob)->age ?? '-' }} yrs)</p>
                        <p><strong>Email:</strong> {{ $case->youngPerson->email ?? '-' }}</p>
                    </div>

                    {{-- Case Details --}}
                    <div x-show="tab === 'caseDetails'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

    <p><strong>Status:</strong> {{ $case->status }}</p>
    <p><strong>Risk Level:</strong> {{ $case->risk_level }}</p>
    <p><strong>Summary:</strong> {{ $case->summary ?? '-' }}</p>
    <p><strong>Last Reviewed:</strong> {{ $case->last_reviewed_at ?? '-' }}</p>

    {{-- Display current carers --}}
    <div>
        <strong>Assigned Carers:</strong>
        @if($case->carers->isEmpty())
            <span class="text-gray-500">No carers assigned.</span>
        @else
            <ul class="list-disc pl-5">
                @foreach($case->carers as $carer)
                <li>
                {{ $carer->name }} 
                (assigned at {{ \Carbon\Carbon::parse($carer->pivot->assigned_at)->format('d M Y H:i') }})
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Assign new carer --}}
    @if(auth()->user()->role === 'social_worker')
        <form action="{{ route('case.assignCarer', $case) }}" method="POST" class="mt-4 flex space-x-2">
            @csrf
            <select name="carer_id" class="border rounded p-2 flex-1">
                <option value="">Select Carer</option>
                @foreach(App\Models\User::where('role', 'carer')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Assign</button>
        </form>
    @endif

</div>
                    <div x-show="tab === 'caseDetails'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                        <p><strong>Status:</strong> {{ $case->status }}</p>
                        <p><strong>Risk Level:</strong> {{ $case->risk_level }}</p>
                        <p><strong>Summary:</strong> {{ $case->summary ?? '-' }}</p>
                        <p><strong>Last Reviewed:</strong> {{ $case->last_reviewed_at ?? '-' }}</p>
                    </div>

                    {{-- Placements --}}
                    <div x-show="tab === 'placements'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                        @php
                            $currentPlacement = $case->placementsHistory->whereNull('end_date')->first();
                        @endphp
                        @if($currentPlacement)
                            <p><strong>Current Placement:</strong></p>
                            <p>Type: {{ $currentPlacement->placement->type }}</p>
                            <p>Location: {{ $currentPlacement->placement->address }}</p>
                            @if($currentPlacement->placement->carer)
                                <p>Carer: {{ $currentPlacement->placement->carer->name }}</p>
                            @endif
                            @if($currentPlacement->notes)
                                <p>Notes: {{ $currentPlacement->notes }}</p>
                            @endif
                        @else
                            <p class="text-gray-500">No current placement assigned.</p>
                        @endif

                        @if(auth()->user()->role === 'social_worker')
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition"
                                    data-bs-toggle="modal" data-bs-target="#addPlacementModal">
                                Manage Placements
                            </button>
                        @endif
                    </div>

                    {{-- Medical --}}
                    <div x-show="tab === 'medical'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded mb-2 hover:bg-indigo-700 transition"
                                data-bs-toggle="modal" data-bs-target="#addMedicalModal">
                            Add Medical Info
                        </button>

                        @if($case->medicalInfos->isEmpty())
                            <p class="text-gray-500">No medical information recorded</p>
                        @else
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($case->medicalInfos as $info)
                                    <li>{{ $info->condition }} - {{ $info->notes }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Education --}}
                    <div x-show="tab === 'education'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded mb-2 hover:bg-indigo-700 transition"
                                data-bs-toggle="modal" data-bs-target="#addEducationModal">
                            Add Education Info
                        </button>

                        @if($case->educationInfos->isEmpty())
                            <p class="text-gray-500">No education information recorded</p>
                        @else
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($case->educationInfos as $edu)
                                    <li>{{ $edu->school_name }} ({{ $edu->grade }}): {{ $edu->notes }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                        {{-- Documents --}}
                        <div x-show="tab === 'documents'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded mb-2 hover:bg-indigo-700 transition"
                                    data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                Upload Document
                            </button>

                            @if($case->documents->isEmpty())
                                <p class="text-gray-500">No documents uploaded</p>
                            @else
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($case->documents as $doc)
                                        <li><a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $doc->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- Appointments --}}
                        <div x-show="tab === 'appointments'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">
                            @php
                                $nextAppointment = $case->appointments->where('start_time', '>=', now())->sortBy('start_time')->first();
                            @endphp
                            @if($nextAppointment)
                                <div class="bg-blue-50 p-4 rounded mb-4">
                                    <strong>Next Appointment:</strong> {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}
                                    <br>
                                    <strong>Location:</strong> {{ $nextAppointment->location ?? 'TBC' }}
                                </div>
                            @endif

                            <h3 class="text-lg font-semibold mb-2">All Appointments</h3>
                            @if($case->appointments->isEmpty())
                                <p class="text-gray-500">No appointments scheduled.</p>
                            @else
                                <ul class="space-y-2">
                                    @foreach($case->appointments->sortByDesc('start_time') as $appointment)
                                        <li class="bg-white p-4 rounded shadow">
                                            <strong>{{ \Carbon\Carbon::parse($appointment->start_time)->format('d M Y H:i') }} - {{ $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('H:i') : 'TBC' }}</strong>
                                            <br>Location: {{ $appointment->location ?? '-' }}
                                            @if($appointment->notes)
                                                <br>Notes: {{ $appointment->notes }}
                                            @endif
                                            <br>Created by: {{ $appointment->creator->name ?? 'System' }}
                                            @if($appointment->carers->count())
                                                <br>Carers:
                                                @foreach($appointment->carers as $carer)
                                                    {{ $carer->name }}{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <a href="{{ route('social-worker.appointments.create', $case) }}" class="inline-block mt-3 bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">
                                Create Appointment
                            </a>
                        </div>

                    </div>

                    {{-- WELLBEING TAB --}}
                <div x-show="tab === 'wellbeing'" x-transition class="bg-white p-6 rounded-xl shadow space-y-6">

                <h3 class="text-lg font-semibold">Wellbeing Checks</h3>

                @if($case->wellbeingChecks->isEmpty())

                <p class="text-gray-500">No wellbeing checks submitted yet.</p>

                @else

                {{-- Table --}}
                <table class="w-full border rounded-lg overflow-hidden">

                <thead class="bg-gray-100">
                <tr>
                <th class="p-3 text-left">Date</th>
                <th class="p-3 text-center">Score</th>
                <th class="p-3 text-center">Risk</th>
                </tr>
                </thead>

                <tbody>

                @foreach($case->wellbeingChecks->sortByDesc('created_at') as $check)

                <tr class="border-t">

                <td class="p-3">
                {{ $check->created_at->format('d M Y') }}
                </td>

                <td class="text-center font-semibold">
                {{ round($check->overall_score,1) }}
                </td>

                <td class="text-center">

                <span class="px-3 py-1 rounded-full text-xs
                @if($check->risk_level === 'low') bg-green-100 text-green-700
                @elseif($check->risk_level === 'medium') bg-yellow-100 text-yellow-700
                @elseif($check->risk_level === 'high') bg-red-100 text-red-700
                @elseif($check->risk_level === 'critical') bg-red-600 text-white
                @endif">

                {{ ucfirst($check->risk_level) }}

                </span>

                </td>

                </tr>

                @endforeach

                </tbody>

                </table>


                {{-- Chart Controls --}}
                <div class="flex justify-between items-center mt-6">

                <h3 class="text-lg font-semibold">Wellbeing Trend</h3>

                <select id="datasetSelector" class="border rounded px-3 py-1">
                <option value="overall">Overall Score</option>
                <option value="domains">Domain Breakdown</option>
                </select>

                </div>


                {{-- Chart --}}
                <div class="mt-6">

                <canvas id="wellbeingTrend" height="100"></canvas>

                </div>

                @endif

                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>

                const labels = [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                "{{ $check->created_at->format('M d') }}",
                @endforeach
                ];

                const overallData = [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->overall_score ?? 0 }},
                @endforeach
                ];

                const domains = {

                emotional: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->emotional_score ?? 'null' }},
                @endforeach
                ],

                behavioural: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->behavioural_score ?? 'null' }},
                @endforeach
                ],

                physical: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->physical_score ?? 'null' }},
                @endforeach
                ],

                safety: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->safety_score ?? 'null' }},
                @endforeach
                ],

                school: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->school_score ?? 'null' }},
                @endforeach
                ],

                relationships: [
                @foreach($case->wellbeingChecks->sortBy('created_at') as $check)
                {{ $check->relationship_score ?? 'null' }},
                @endforeach
                ]

                };


                const ctx = document.getElementById('wellbeingTrend');

                let chart = new Chart(ctx, {

                type: 'line',

                data: {

                labels: labels,

                datasets: [

                {
                label: 'Overall Wellbeing',
                data: overallData,
                borderWidth: 3,
                tension: 0.3
                }

                ]

                },

                options: {

                responsive: true,

                plugins: {
                legend: { position: 'top' }
                },

                scales: {

                y: {
                min: 0,
                max: 100
                }

                }

                }

                });


                document.getElementById('datasetSelector')
                .addEventListener('change', function(){

                if(this.value === "overall"){

                chart.data.datasets = [

                {
                label: 'Overall Wellbeing',
                data: overallData,
                borderWidth: 3,
                tension: 0.3
                }

                ];

                }else{

                chart.data.datasets = [

                { label:'Emotional', data:domains.emotional, tension:0.3 },
                { label:'Behavioural', data:domains.behavioural, tension:0.3 },
                { label:'Physical', data:domains.physical, tension:0.3 },
                { label:'Safety', data:domains.safety, tension:0.3 },
                { label:'School', data:domains.school, tension:0.3 },
                { label:'Relationships', data:domains.relationships, tension:0.3 }

                ];

                }

                chart.update();

                });

                </script>
    @include('socialworker.partials.modals')
</x-app-layout>