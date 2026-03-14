<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            {{ $case->case_reference ?? 'Case File' }} - {{ $case->youngPerson->name ?? 'Child' }}

        </h2>

    </x-slot>



    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">



            <div class="mb-6 flex items-center justify-between">

                <div>

                    <h1 class="text-2xl font-bold text-gray-900">

                        Case File Overview

                    </h1>

                    <p class="text-sm text-gray-500 mt-1">

                        View important details for the child in your care

                    </p>

                </div>



                <a href="{{ route('carer.dashboard') }}"

                   class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow border border-gray-200">

                    ← Back to dashboard

                </a>

            </div>



            <div x-data="{ tab: 'child' }" x-cloak class="space-y-6">



                {{-- Tabs --}}

                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-2">

                    <nav class="flex flex-wrap gap-2">

                        <template x-for="t in ['child','caseDetails','placements','medical','education','documents','appointments','wellbeing']" :key="t">

                            <button

                                @click="tab = t"

                                :class="tab === t

                                    ? 'bg-indigo-600 text-white shadow'

                                    : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"

                                class="px-4 py-2 rounded-xl text-sm font-medium capitalize transition"

                                x-text="t.replace(/([A-Z])/g, ' $1')"

                            ></button>

                        </template>

                    </nav>

                </div>



                {{-- Child --}}

                <div x-show="tab === 'child'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100 space-y-3">

                    <h3 class="text-lg font-bold text-blue-700">Child Information</h3>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="rounded-xl bg-blue-50 p-4">

                            <p class="text-sm text-gray-500">Name</p>

                            <p class="font-semibold text-gray-900">{{ $case->youngPerson->name ?? 'N/A' }}</p>

                        </div>



                        <div class="rounded-xl bg-blue-50 p-4">

                            <p class="text-sm text-gray-500">Date of Birth</p>

                            <p class="font-semibold text-gray-900">

                                {{ $case->youngPerson->dob ?? 'N/A' }}

                            </p>

                        </div>



                        <div class="rounded-xl bg-blue-50 p-4">

                            <p class="text-sm text-gray-500">Age</p>

                            <p class="font-semibold text-gray-900">

                                {{ !empty($case->youngPerson?->dob) ? \Carbon\Carbon::parse($case->youngPerson->dob)->age . ' years' : 'N/A' }}

                            </p>

                        </div>



                        <div class="rounded-xl bg-blue-50 p-4">

                            <p class="text-sm text-gray-500">Email</p>

                            <p class="font-semibold text-gray-900">{{ $case->youngPerson->email ?? '-' }}</p>

                        </div>

                    </div>

                </div>



                {{-- Case Details --}}

                <div x-show="tab === 'caseDetails'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 space-y-4">

                    <h3 class="text-lg font-bold text-indigo-700">Case Details</h3>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="rounded-xl bg-indigo-50 p-4">

                            <p class="text-sm text-gray-500">Status</p>

                            <p class="font-semibold text-gray-900">{{ $case->status ?? '-' }}</p>

                        </div>



                        <div class="rounded-xl bg-indigo-50 p-4">

                            <p class="text-sm text-gray-500">Risk Level</p>

                            <p class="font-semibold text-gray-900">{{ $case->risk_level ?? '-' }}</p>

                        </div>



                        <div class="rounded-xl bg-indigo-50 p-4 md:col-span-2">

                            <p class="text-sm text-gray-500">Summary</p>

                            <p class="font-semibold text-gray-900">{{ $case->summary ?? '-' }}</p>

                        </div>



                        <div class="rounded-xl bg-indigo-50 p-4">

                            <p class="text-sm text-gray-500">Last Reviewed</p>

                            <p class="font-semibold text-gray-900">{{ $case->last_reviewed_at ?? '-' }}</p>

                        </div>

                    </div>



                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">

                        <p class="font-semibold text-gray-900 mb-2">Assigned Carers</p>



                        @if($case->carers->isEmpty())

                            <p class="text-gray-500">No carers assigned.</p>

                        @else

                            <ul class="list-disc pl-5 space-y-1 text-gray-700">

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

                </div>



                {{-- Placements --}}

                <div x-show="tab === 'placements'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-pink-100 space-y-4">

                    <h3 class="text-lg font-bold text-pink-700">Placement</h3>



                    @php

                        $currentPlacement = $case->placements->whereNull('end_date')->first();

                    @endphp



                    @if($currentPlacement)

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div class="rounded-xl bg-pink-50 p-4">

                                <p class="text-sm text-gray-500">Type</p>

                                <p class="font-semibold text-gray-900">{{ $currentPlacement->placement->type ?? '-' }}</p>

                            </div>



                            <div class="rounded-xl bg-pink-50 p-4">

                                <p class="text-sm text-gray-500">Location</p>

                                <p class="font-semibold text-gray-900">{{ $currentPlacement->placement->address ?? '-' }}</p>

                            </div>



                            @if($currentPlacement->placement->carer)

                                <div class="rounded-xl bg-pink-50 p-4">

                                    <p class="text-sm text-gray-500">Carer</p>

                                    <p class="font-semibold text-gray-900">{{ $currentPlacement->placement->carer->name }}</p>

                                </div>

                            @endif



                            @if($currentPlacement->notes)

                                <div class="rounded-xl bg-pink-50 p-4 md:col-span-2">

                                    <p class="text-sm text-gray-500">Notes</p>

                                    <p class="font-semibold text-gray-900">{{ $currentPlacement->notes }}</p>

                                </div>

                            @endif

                        </div>

                    @else

                        <p class="text-gray-500">No current placement assigned.</p>

                    @endif

                </div>



                {{-- Medical --}}

                <div x-show="tab === 'medical'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-green-100 space-y-3">

                    <h3 class="text-lg font-bold text-green-700">Medical Information</h3>



                    @if($case->medicalInfos->isEmpty())

                        <p class="text-gray-500">No medical information recorded.</p>

                    @else

                        <ul class="space-y-2">

                            @foreach($case->medicalInfos as $info)

                                <li class="rounded-xl bg-green-50 p-4 border border-green-100">

                                    <p class="font-semibold text-gray-900">{{ $info->condition }}</p>

                                    <p class="text-sm text-gray-600 mt-1">{{ $info->notes }}</p>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>



                {{-- Education --}}

                <div x-show="tab === 'education'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 space-y-3">

                    <h3 class="text-lg font-bold text-yellow-700">Education</h3>



                    @if($case->educationInfos->isEmpty())

                        <p class="text-gray-500">No education information recorded.</p>

                    @else

                        <ul class="space-y-2">

                            @foreach($case->educationInfos as $edu)

                                <li class="rounded-xl bg-yellow-50 p-4 border border-yellow-100">

                                    <p class="font-semibold text-gray-900">

                                        {{ $edu->school_name }} @if($edu->grade) ({{ $edu->grade }}) @endif

                                    </p>

                                    <p class="text-sm text-gray-600 mt-1">{{ $edu->notes }}</p>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>



                {{-- Documents --}}

                <div x-show="tab === 'documents'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-sky-100 space-y-3">

                    <div class="flex items-center justify-between">

                        <h3 class="text-lg font-bold text-sky-700">Documents</h3>



                        <a href="{{ route('carer.documents.index') }}"

                           class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm hover:bg-sky-700 shadow-sm">

                            Open My Documents

                        </a>

                    </div>



                    @if($case->documents->isEmpty())

                        <p class="text-gray-500">No documents uploaded.</p>

                    @else

                        <ul class="space-y-2">

                            @foreach($case->documents as $doc)

                                <li class="rounded-xl bg-sky-50 p-4 border border-sky-100">

                                    <a href="{{ asset('storage/' . $doc->file_path) }}"

                                       target="_blank"

                                       class="text-sky-700 font-medium hover:underline">

                                        {{ $doc->name ?? $doc->title ?? 'Document' }}

                                    </a>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>



                {{-- Appointments --}}

                <div x-show="tab === 'appointments'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 space-y-4">

                    <div class="flex items-center justify-between">

                        <h3 class="text-lg font-bold text-indigo-700">Appointments</h3>



                        <a href="{{ route('carer.calendar') }}"

                           class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">

                            Open Calendar

                        </a>

                    </div>



                    @php

                        $nextAppointment = $case->appointments->where('start_time', '>=', now())->sortBy('start_time')->first();

                    @endphp



                    @if($nextAppointment)

                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">

                            <p class="font-semibold text-indigo-900">Next Appointment</p>

                            <p class="text-sm text-gray-700 mt-1">

                                {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}

                            </p>

                            <p class="text-sm text-gray-700">

                                Location: {{ $nextAppointment->location ?? 'TBC' }}

                            </p>

                        </div>

                    @endif



                    @if($case->appointments->isEmpty())

                        <p class="text-gray-500">No appointments scheduled.</p>

                    @else

                        <ul class="space-y-3">

                            @foreach($case->appointments->sortByDesc('start_time') as $appointment)

                                <li class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">

                                    <p class="font-semibold text-gray-900">

                                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('d M Y H:i') }}

                                        -

                                        {{ $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('H:i') : 'TBC' }}

                                    </p>



                                    <p class="text-sm text-gray-600 mt-1">

                                        Location: {{ $appointment->location ?? '-' }}

                                    </p>



                                    @if($appointment->notes)

                                        <p class="text-sm text-gray-600 mt-1">

                                            Notes: {{ $appointment->notes }}

                                        </p>

                                    @endif



                                    <p class="text-sm text-gray-500 mt-1">

                                        Created by: {{ $appointment->creator->name ?? 'System' }}

                                    </p>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>



                {{-- Wellbeing --}}

                <div x-show="tab === 'wellbeing'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 space-y-6">

                    <h3 class="text-lg font-bold text-emerald-700">Wellbeing Checks</h3>



                    @if($case->wellbeingChecks->isEmpty())

                        <p class="text-gray-500">No wellbeing checks submitted yet.</p>

                    @else

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

                                        <td class="p-3">{{ $check->created_at->format('d M Y') }}</td>

                                        <td class="text-center font-semibold">{{ round($check->overall_score, 1) }}</td>

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



                        <div class="flex justify-between items-center mt-6">

                            <h3 class="text-lg font-semibold">Wellbeing Trend</h3>



                            <select id="datasetSelector" class="border rounded px-3 py-1">

                                <option value="overall">Overall Score</option>

                                <option value="domains">Domain Breakdown</option>

                            </select>

                        </div>



                        <div class="mt-6">

                            <canvas id="wellbeingTrend" height="100"></canvas>

                        </div>

                    @endif

                </div>



            </div>

        </div>

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



        if (ctx) {

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



            document.getElementById('datasetSelector')?.addEventListener('change', function () {

                if (this.value === "overall") {

                    chart.data.datasets = [

                        {

                            label: 'Overall Wellbeing',

                            data: overallData,

                            borderWidth: 3,

                            tension: 0.3

                        }

                    ];

                } else {

                    chart.data.datasets = [

                        { label: 'Emotional', data: domains.emotional, tension: 0.3 },

                        { label: 'Behavioural', data: domains.behavioural, tension: 0.3 },

                        { label: 'Physical', data: domains.physical, tension: 0.3 },

                        { label: 'Safety', data: domains.safety, tension: 0.3 },

                        { label: 'School', data: domains.school, tension: 0.3 },

                        { label: 'Relationships', data: domains.relationships, tension: 0.3 }

                    ];

                }



                chart.update();

            });

        }

    </script>

</x-app-layout>