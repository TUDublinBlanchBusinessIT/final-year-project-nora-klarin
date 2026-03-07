<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Child Case File</h2>

                <p class="text-sm text-gray-500 mt-1">Case information linked to your account</p>

            </div>



            <a href="{{ route('carer.dashboard') }}"

               class="px-4 py-2 rounded-xl bg-gray-100 text-sm hover:bg-gray-200">

                Back

            </a>

        </div>

    </x-slot>



    <div class="py-10 bg-gradient-to-br from-indigo-50 via-pink-50 to-yellow-50 min-h-screen">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">



            @if($cases->isEmpty())

                <div class="bg-white rounded-3xl shadow-sm border p-10 text-center">

                    <div class="text-4xl mb-3">📁</div>

                    <h3 class="text-lg font-semibold text-gray-900">No case file found</h3>

                    <p class="text-sm text-gray-500 mt-2">There are no case files linked to your account yet.</p>

                </div>

            @else

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    @foreach($cases as $case)

                        <div class="rounded-3xl bg-white shadow-sm border overflow-hidden">

                            <div class="bg-gradient-to-r from-indigo-600 to-sky-500 text-white p-6">

                                <div class="text-sm opacity-90">Case Overview</div>

                                <div class="text-2xl font-bold mt-1">Case #{{ $case->id }}</div>

                            </div>



                            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div class="rounded-2xl bg-indigo-50 p-4">

                                    <div class="text-xs text-gray-500">Case ID</div>

                                    <div class="text-lg font-semibold text-gray-900">{{ $case->id }}</div>

                                </div>



                                <div class="rounded-2xl bg-pink-50 p-4">

                                    <div class="text-xs text-gray-500">Risk Level</div>

                                    <div class="text-lg font-semibold text-gray-900">{{ $case->risklevel ?? 'N/A' }}</div>

                                </div>



                                <div class="rounded-2xl bg-yellow-50 p-4">

                                    <div class="text-xs text-gray-500">Status</div>

                                    <div class="text-lg font-semibold text-gray-900">{{ $case->status ?? 'N/A' }}</div>

                                </div>



                                <div class="rounded-2xl bg-sky-50 p-4">

                                    <div class="text-xs text-gray-500">Opened Date</div>

                                    <div class="text-lg font-semibold text-gray-900">

                                        {{ !empty($case->openedat) ? \Carbon\Carbon::parse($case->openedat)->format('d M Y') : 'N/A' }}

                                    </div>

                                </div>

                            </div>



                            <div class="px-6 pb-6">

                                <div class="rounded-2xl border bg-gray-50 p-5">

                                    <h3 class="font-semibold text-gray-900 mb-2">Summary</h3>

                                    <p class="text-sm text-gray-600">

                                        This case file is linked to your carer account through the case_user table.

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif



        </div>

    </div>

</x-app-layout>