<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Case #{{ $case->id }} - {{ $case->youngPerson->name ?? 'Unassigned' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="{ tab: 'child' }" x-cloak class="space-y-4">

                {{-- Tabs --}}
                <nav class="flex space-x-4 border-b border-gray-200">
                    <template x-for="t in ['child','caseDetails','placements','medical','education','documents','appointments']" :key="t">
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

            </div>
        </div>
    </div>

    {{-- Include Bootstrap Modals --}}
    @include('socialworker.partials.modals')
</x-app-layout>