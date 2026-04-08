<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            Case #{{ $case->id }} - {{ $case->youngPerson->name ?? 'Unassigned' }}

        </h2>

    </x-slot>



    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="{ tab: 'child' }" x-cloak class="space-y-4">



                <nav class="flex space-x-4 border-b border-gray-200">

                    <template x-for="t in ['child','caseDetails','placements','medical','education','documents','appointments']" :key="t">

                        <button

                            type="button"

                            @click="tab = t"

                            :class="tab === t ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'"

                            class="py-2 px-4 border-b-2 font-medium capitalize"

                            x-text="t.replace(/([A-Z])/g, ' $1')"

                        ></button>

                    </template>

                </nav>



                <div class="mt-4 space-y-6">



                    {{-- Child --}}

                    <div x-show="tab === 'child'" x-transition class="bg-white p-6 rounded-xl shadow space-y-2">

                        <p><strong>Name:</strong> {{ $case->youngPerson->name ?? 'N/A' }}</p>

                        <p>

                            <strong>DOB:</strong>

                            {{ $case->youngPerson->dob ?? 'N/A' }}

                            ({{ !empty($case->youngPerson?->dob) ? \Carbon\Carbon::parse($case->youngPerson->dob)->age . ' yrs' : '-' }})

                        </p>

                        <p><strong>Email:</strong> {{ $case->youngPerson->email ?? '-' }}</p>

                    </div>



                    {{-- Case Details --}}

                    <div x-show="tab === 'caseDetails'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        <p><strong>Status:</strong> {{ $case->status ?? '-' }}</p>

                        <p><strong>Risk Level:</strong> {{ $case->risklevel ?? $case->risk_level ?? '-' }}</p>

                        <p><strong>Summary:</strong> {{ $case->summary ?? '-' }}</p>

                        <p><strong>Last Reviewed:</strong> {{ $case->last_reviewed_at ?? '-' }}</p>



                        <div>

                            <strong>Assigned Carers:</strong>

                            @if($case->carers->isEmpty())

                                <span class="text-gray-500">No carers assigned.</span>

                            @else

                                <ul class="list-disc pl-5 mt-2">

                                    @foreach($case->carers as $carer)

                                        <li>

                                            {{ $carer->name }}

                                            @if(!empty($carer->pivot->assigned_at))

                                                (assigned at {{ \Carbon\Carbon::parse($carer->pivot->assigned_at)->format('d M Y H:i') }})

                                            @endif

                                        </li>

                                    @endforeach

                                </ul>

                            @endif

                        </div>



                        @if(auth()->user()->role === 'social_worker')

                            <form action="{{ route('case.assignCarer', $case) }}" method="POST" class="mt-4 flex gap-2">

                                @csrf

                                <select name="carer_id" class="border rounded p-2 flex-1">

                                    <option value="">Select Carer</option>

                                    @foreach(App\Models\User::where('role', 'carer')->get() as $user)

                                        <option value="{{ $user->id }}">{{ $user->name }}</option>

                                    @endforeach

                                </select>



                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">

                                    Assign

                                </button>

                            </form>

                        @endif

                    </div>



                    {{-- Placements --}}

                    <div x-show="tab === 'placements'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        @php

                            $currentPlacement = $case->placements->whereNull('end_date')->first();

                        @endphp



                        @if($currentPlacement)

                            <div class="space-y-2">

                                <p><strong>Current Placement:</strong></p>

                                <p>Type: {{ $currentPlacement->type ?? '-' }}</p>

                                <p>Location: {{ $currentPlacement->address ?? '-' }}</p>



                                @if($currentPlacement->carer)

                                    <p>Carer: {{ $currentPlacement->carer->name }}</p>

                                @endif



                                @if($currentPlacement->notes)

                                    <p>Notes: {{ $currentPlacement->notes }}</p>

                                @endif

                            </div>

                        @else

                            <p class="text-gray-500">No current placement assigned.</p>

                        @endif



                        @if(auth()->user()->role === 'social_worker')

                            <button

                                type="button"

                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition"

                                data-bs-toggle="modal"

                                data-bs-target="#addPlacementModal">

                                Add Placement

                            </button>

                        @endif

                    </div>



                    {{-- Medical --}}

                    <div x-show="tab === 'medical'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        @if($case->medicalInfos->isEmpty())

                            <p class="text-gray-500">No medical information recorded.</p>

                        @else

                            <ul class="list-disc pl-5 space-y-1">

                                @foreach($case->medicalInfos as $info)

                                    <li>{{ $info->condition }} - {{ $info->notes }}</li>

                                @endforeach

                            </ul>

                        @endif



                        @if(auth()->user()->role === 'social_worker')

                            <button

                                type="button"

                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition"

                                data-bs-toggle="modal"

                                data-bs-target="#addMedicalModal">

                                Add Medical Info

                            </button>

                        @endif

                    </div>



                    {{-- Education --}}

                    <div x-show="tab === 'education'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        @if($case->educationInfos->isEmpty())

                            <p class="text-gray-500">No education information recorded.</p>

                        @else

                            <ul class="list-disc pl-5 space-y-1">

                                @foreach($case->educationInfos as $edu)

                                    <li>{{ $edu->school_name }} ({{ $edu->grade }}): {{ $edu->notes }}</li>

                                @endforeach

                            </ul>

                        @endif



                        @if(auth()->user()->role === 'social_worker')

                            <button

                                type="button"

                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition"

                                data-bs-toggle="modal"

                                data-bs-target="#addEducationModal">

                                Add Education Info

                            </button>

                        @endif

                    </div>



                    {{-- Documents --}}

                    <div x-show="tab === 'documents'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        @if($case->documents->isEmpty())

                            <p class="text-gray-500">No documents uploaded.</p>

                        @else

                            <ul class="list-disc pl-5 space-y-1">

                                @foreach($case->documents as $doc)

                                    <li>

                                        <a

                                            href="{{ asset('storage/' . $doc->file_path) }}"

                                            target="_blank"

                                            class="text-indigo-600 hover:underline">

                                            {{ $doc->title ?? $doc->name ?? 'Document' }}

                                        </a>

                                    </li>

                                @endforeach

                            </ul>

                        @endif



                        @if(auth()->user()->role === 'social_worker')

                            <button

                                type="button"

                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition"

                                data-bs-toggle="modal"

                                data-bs-target="#addDocumentModal">

                                Upload Document

                            </button>

                        @endif

                    </div>



                    {{-- Appointments --}}

                    <div x-show="tab === 'appointments'" x-transition class="bg-white p-6 rounded-xl shadow space-y-4">

                        @php

                            $nextAppointment = $case->appointments->where('start_time', '>=', now())->sortBy('start_time')->first();

                        @endphp



                        @if($nextAppointment)

                            <div class="bg-blue-50 p-4 rounded mb-4">

                                <strong>Next Appointment:</strong>

                                {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}

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

                                        <strong>

                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('d M Y H:i') }}

                                            -

                                            {{ $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('H:i') : 'TBC' }}

                                        </strong>

                                        <br>Location: {{ $appointment->location ?? '-' }}



                                        @if($appointment->notes)

                                            <br>Notes: {{ $appointment->notes }}

                                        @endif



                                        <br>Created by: {{ $appointment->creator->name ?? 'System' }}

                                    </li>

                                @endforeach

                            </ul>

                        @endif

                    </div>



                </div>

            </div>

        </div>

    </div>



    @include('socialworker.partials.modals')

</x-app-layout>