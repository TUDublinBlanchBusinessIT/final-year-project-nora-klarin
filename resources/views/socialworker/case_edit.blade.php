@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Edit Case #{{ $case->id }}</h3>

    <form method="POST" action="{{ route('socialworker.case.update', $case) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            {{-- Assign Child --}}
            <div class="col-md-6">
                <label class="form-label">Child</label>
                <select name="young_person_id" class="form-select">
                    <option value="">-- Select Child --</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}"
                            {{ $case->young_person_id == $child->id ? 'selected' : '' }}>
                            {{ $child->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Open" {{ $case->status == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="Closed" {{ $case->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                    <option value="Review" {{ $case->status == 'Review' ? 'selected' : '' }}>Review</option>
                </select>
            </div>

            {{-- Risk Level --}}
            <div class="col-md-3">
                <label class="form-label">Risk Level</label>
                <select name="risk_level" class="form-select">
                    <option value="Low" {{ $case->risk_level == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" {{ $case->risk_level == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" {{ $case->risk_level == 'High' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        {{-- Assign Carers --}}
        <div class="mt-4">
            <label class="form-label">Carers</label>
            <select name="carers[]" class="form-select" multiple>
                @foreach($carers as $carer)
                    <option value="{{ $carer->id }}"
                        {{ $case->carers->contains($carer->id) ? 'selected' : '' }}>
                        {{ $carer->name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple carers.</small>
        </div>

        {{-- Update Button --}}
        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Update Case
            </button>
        </div>

    </form>

</div>
@endsection
