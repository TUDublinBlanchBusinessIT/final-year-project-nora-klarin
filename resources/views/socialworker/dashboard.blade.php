@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Social Worker Dashboard</h2>

    <h4>Cases and Risk Level</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Case ID</th>
                <th>Risk Level</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cases as $case)
                <tr>
                    <td>{{ $case->risklevel }}</td>
                    <td>{{ $case->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($case->openedat)->format('d M Y') }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection


