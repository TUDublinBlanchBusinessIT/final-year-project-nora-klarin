@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Case #{{ $case->id }} - {{ $case->youngPerson->name ?? 'Unassigned' }}</h2>

    <ul class="nav nav-tabs mt-4" id="caseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="child-tab" data-bs-toggle="tab" href="#child" role="tab">Child</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="case-tab" data-bs-toggle="tab" href="#caseDetails" role="tab">Case Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="placements-tab" data-bs-toggle="tab" href="#placements" role="tab">Placements</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="medical-tab" data-bs-toggle="tab" href="#medical" role="tab">Medical</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="education-tab" data-bs-toggle="tab" href="#education" role="tab">Education</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab">Documents</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="appointments-tab" data-bs-toggle="tab" href="#appointments" role="tab">Appointments</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        {{-- Child --}}
        <div class="tab-pane fade show active" id="child" role="tabpanel">
            <p><strong>Name:</strong> {{ $case->youngPerson->name ?? 'N/A' }}</p>
            <p><strong>DOB:</strong> {{ $case->youngPerson->dob ?? 'N/A' }} ({{ \Carbon\Carbon::parse($case->youngPerson->dob)->age ?? '-' }} yrs)</p>
            <p><strong>Email:</strong> {{ $case->youngPerson->email ?? '-' }}</p>
        </div>

        {{-- Case Details --}}
        <div class="tab-pane fade" id="caseDetails" role="tabpanel">
            <p><strong>Status:</strong> {{ $case->status }}</p>
            <p><strong>Risk Level:</strong> {{ $case->risk_level }}</p>
            <p><strong>Summary:</strong> {{ $case->summary ?? '-' }}</p>
            <p><strong>Last Reviewed:</strong> {{ $case->last_reviewed_at ?? '-' }}</p>
        </div>

        {{-- Placements --}}
        <div class="tab-pane fade" id="placements" role="tabpanel">
            <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addPlacementModal">Add Placement</button>
            @foreach($case->placements->sortByDesc('start_date') as $placement)
                <div class="border p-2 mb-2">
                    <strong>{{ $placement->type }}</strong> ({{ $placement->start_date }} - {{ $placement->end_date ?? 'Current' }})
                    <p><strong>Location:</strong> {{ $placement->location }}</p>
                    <p>{{ $placement->notes ?? '' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Medical --}}
        <div class="tab-pane fade" id="medical" role="tabpanel">
            <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addMedicalModal">Add Medical Info</button>
            @if($case->medicalInfos->isEmpty())
                <p class="text-muted">No medical information recorded</p>
            @else
                <ul>
                    @foreach($case->medicalInfos as $info)
                        <li>{{ $info->condition }} - {{ $info->notes }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Education --}}
        <div class="tab-pane fade" id="education" role="tabpanel">
            <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addEducationModal">Add Education Info</button>
            @if($case->educationInfos->isEmpty())
                <p class="text-muted">No education information recorded</p>
            @else
                <ul>
                    @foreach($case->educationInfos as $edu)
                        <li>{{ $edu->school_name }} ({{ $edu->grade }}): {{ $edu->notes }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Documents --}}
        <div class="tab-pane fade" id="documents" role="tabpanel">
            <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDocumentModal">Upload Document</button>
            @if($case->documents->isEmpty())
                <p class="text-muted">No documents uploaded</p>
            @else
                <ul>
                    @foreach($case->documents as $doc)
                        <li><a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">{{ $doc->name }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Appointments --}}
        <div class="tab-pane fade" id="appointments" role="tabpanel">
            @php $nextAppointment = $case->appointments()->orderBy('start_time')->first(); @endphp
            @if($nextAppointment)
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}</p>
                <p><strong>Location:</strong> {{ $nextAppointment->location ?? 'TBC' }}</p>
            @else
                <p class="text-muted">No upcoming appointments</p>
            @endif
            <a href="{{ route('social-worker.appointments.create', $case) }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle"></i> Create Appointment
            </a>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('socialworker.partials.modals')
@endsection