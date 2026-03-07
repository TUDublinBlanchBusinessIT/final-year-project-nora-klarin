<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            Social Worker Dashboard

        </h2>

    </x-slot>



    <div class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



            <div class="bg-white shadow rounded-xl p-6">



                <h4 class="text-lg font-semibold mb-4">Cases and Risk Level</h4>



                <table class="min-w-full border">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="px-4 py-2 text-left">Case ID</th>

                            <th class="px-4 py-2 text-left">Risk Level</th>

                            <th class="px-4 py-2 text-left">Status</th>

                            <th class="px-4 py-2 text-left">Opened At</th>

                        </tr>

                    </thead>



                    <tbody>

                        @foreach($cases as $case)

                            <tr class="border-t">

                                <td class="px-4 py-2">

                                    <a href="{{ route('socialworker.case.show', $case->id) }}" class="text-indigo-600 hover:underline">

                                        {{ $case->id }}

                                    </a>

                                </td>



                                <td class="px-4 py-2">{{ $case->risklevel }}</td>

                                <td class="px-4 py-2">{{ $case->status }}</td>



                                <td class="px-4 py-2">

                                    {{ \Carbon\Carbon::parse($case->openedat)->format('d M Y') }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>



                </table>



            </div>



        </div>

    </div>

</x-app-layout>
