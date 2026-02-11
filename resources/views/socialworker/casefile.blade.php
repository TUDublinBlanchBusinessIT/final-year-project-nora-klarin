@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Case #{{ $case->id }}</h2>
        @if(auth()->user()->role === 'social_worker')
            <a href="{{ route('socialworker.case.edit', $case) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Edit Case
            </a>
        @endif
    </div>

    {{-- Child --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Child</h5>
            <p class="card-text">
                @if($case->youngPerson)
                    {{ $case->youngPerson->name ?? 'Name not set' }}
                    @if($case->youngPerson->dob)
                        <span class="text-muted">
                            ({{ \Carbon\Carbon::parse($case->youngPerson->dob)->age }} years old)
                        </span>
                    @endif
                @else
                    <span class="text-muted">Not assigned</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Case Meta --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Case Details</h5>
            <p><strong>Status:</strong> {{ $case->status }}</p>
            <p><strong>Risk Level:</strong> {{ $case->risk_level }}</p>
        </div>
    </div>

    {{-- Carers --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Carer(s)</h5>
            @if($case->carers->isEmpty())
                <p class="text-muted">No carers assigned</p>
            @else
                <ul class="list-group">
                    @foreach($case->carers as $carer)
                        <li class="list-group-item">{{ $carer->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Next Appointment --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Next Appointment</h5>
            @php
                $nextAppointment = $case->appointments()->orderBy('start_time')->first();
            @endphp
            @if($nextAppointment)
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}</p>
                <p><strong>Location:</strong> {{ $nextAppointment->location ?? 'TBC' }}</p>
            @else
                <p class="text-muted">No upcoming appointments</p>
            @endif

            {{-- Create Appointment Button --}}
            @if(auth()->user()->role === 'social_worker')
                <a href="{{ route('social-worker.appointments.create', $case) }}" 
                   class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Create Appointment
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
