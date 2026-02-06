@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Social Worker Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Total Cases</h5>
                    <h2>{{ $totalCases }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5>Open Cases</h5>
                    <h2>{{ $openCases }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5>High Risk Cases</h5>
                    <h2 class="text-danger">{{ $highRiskCases }}</h2>
                </div>
            </div>
        </div>
    </div>

    <h4>Recent Cases</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Case ID</th>
                <th>Status</th>
                <th>Risk Level</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentCases as $case)
                <tr>
                    <td>{{ $case->id }}</td>
                    <td>{{ $case->status }}</td>
                    <td>{{ $case->risk_level }}</td>
                    <td>{{ $case->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('cases.create') }}" class="btn btn-primary mt-3">
        Create New Case
    </a>
</div>
@endsection
