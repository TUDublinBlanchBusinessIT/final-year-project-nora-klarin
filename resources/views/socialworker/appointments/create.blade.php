@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Create Appointment for Case #{{ $case->id }}</h3>

    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf

        <input type="hidden" name="case_file_id" value="{{ $case->id }}">

        @if($youngPerson)
    <input type="hidden" name="young_person_id" value="{{ $youngPerson->id }}">
@else
    <p class="text-danger">
        No young person assigned to this case. Appointment cannot be created.
    </p>
@endif

        <div class="mb-3">
            <label class="form-label">Date & Time</label>
            <input type="datetime-local" name="start_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">End Time</label>
            <input type="datetime-local" name="end_time" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Carers</label>
            @foreach($carers as $carer)
                <div class="form-check">
                    <input
                        type="checkbox"
                        name="carers[]"
                        value="{{ $carer->id }}"
                        class="form-check-input"
                    >
                    <label class="form-check-label">
                        {{ $carer->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <button class="btn btn-primary">Create Appointment</button>

    </form>


</div>
@endsection
