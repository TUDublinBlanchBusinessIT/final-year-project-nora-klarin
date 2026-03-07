<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">

                Calendar

            </h2>



            <a href="{{ route('carer.dashboard') }}" class="text-sm text-blue-600 hover:underline">

                ← Back to dashboard

            </a>

        </div>

    </x-slot>



    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($appointments->isEmpty())

                    <p class="text-gray-600">No upcoming appointments.</p>

                @else

                    <div class="space-y-4">

                        @foreach($appointments as $appt)

                            <div class="border rounded p-4">

                                <div class="font-semibold">

                                    {{ \Carbon\Carbon::parse($appt->start_time)->format('D d M Y') }}

                                </div>



                                <div class="text-sm text-gray-600">

                                    {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}

                                    –

                                    {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}

                                </div>



                                @if(!empty($appt->title))

                                    <div class="mt-2 text-sm font-medium text-gray-900">

                                        {{ $appt->title }}

                                    </div>

                                @endif



                                @if(!empty($appt->location))

                                    <div class="mt-1 text-sm text-gray-600">

                                        📍 {{ $appt->location }}

                                    </div>

                                @endif



                                <div class="mt-1 text-sm text-gray-700">

                                    {{ $appt->notes ?? 'No notes' }}

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>