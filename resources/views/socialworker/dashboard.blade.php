@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Page Heading --}}
    <h2 class="text-2xl font-semibold text-gray-800">
        Cases and Risk Level
    </h2>

    {{-- Cases Table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opened At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cases as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-medium">
                            <a href="{{ route('socialworker.case.show', $case->id) }}" class="hover:underline">
                                {{ $case->id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $case->risklevel }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $case->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($case->openedat)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection