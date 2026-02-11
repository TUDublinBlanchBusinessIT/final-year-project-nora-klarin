@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Case #{{ $case->id }}</h2>

    {{-- Child --}}
    <div class="mb-3">
        <strong>Child:</strong>
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
    </div>

    {{-- Case meta --}}
    <div class="mb-4">
        <p class="mb-1"><strong>Status:</strong> {{ $case->status }}</p>
        <p class="mb-1"><strong>Risk Level:</strong> {{ $case->risklevel }}</p>
    </div>

    <hr>

    {{-- Carers --}}
    <h4>Carer(s)</h4>
    @if($case->carers->isEmpty())
        <p class="text-muted">No carers assigned</p>
    @else
        <ul>
            @foreach($case->carers as $carer)
                <li>{{ $carer->name }}</li>
            @endforeach
        </ul>
    @endif

    <hr>

    <h4>Next Appointment</h4>
    @php
        $nextAppointment = $case->appointments()
            ->orderBy('start_time')
            ->first();
    @endphp

    @if($nextAppointment)
        <p class="mb-1">
            <strong>Date:</strong>
            {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('d M Y H:i') }}
        </p>
        <p class="mb-1">
            <strong>Location:</strong>
            {{ $nextAppointment->location ?? 'TBC' }}
        </p>
    @else
        <p class="text-muted">No upcoming appointments</p>
    @endif

    @can('create', App\Models\Appointment::class)
    <a href="{{ route('social-worker.appointments.create', $case) }}" 
       class="btn btn-primary mb-3">
        + Create Appointment
    </a>
@endcan
@if(auth()->user()->role === 'social_worker')
    <a href="{{ route('social-worker.appointments.create', $case) }}" 
       class="btn btn-primary mb-3">
        + Create Appointment
    </a>
@endif

</div>
@endsection

@if(auth()->user()->role === 'social_worker')
    <a href="{{ route('socialworker.case.edit', $case) }}"
       class="btn btn-warning">
        Edit Case
    </a>
@endif
