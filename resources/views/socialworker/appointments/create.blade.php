<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Appointment for Case #{{ $case->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-6 rounded-xl shadow">

            @if(!$youngPerson)
                <p class="text-red-600 mb-4">
                    No young person assigned to this case. Appointment cannot be created.
                </p>
            @else
            <form method="POST" action="{{ route('social-worker.appointments.store', $case) }}">
                @csrf
                <input type="hidden" name="case_file_id" value="{{ $case->id }}">
                <input type="hidden" name="young_person_id" value="{{ $youngPerson->id }}">

                {{-- Date & Time --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Date & Time</label>
                    <input type="datetime-local" name="start_time"
                           class="border border-gray-300 rounded px-3 py-2 w-full"
                           value="{{ $availableSlot ? $availableSlot->format('Y-m-d\TH:i') : '' }}"
                           required>
                </div>

                {{-- End Time --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">End Time</label>
                    <input type="datetime-local" name="end_time"
                           class="border border-gray-300 rounded px-3 py-2 w-full"
                           value="{{ $availableSlot ? $availableSlot->copy()->addMinutes(30)->format('Y-m-d\TH:i') : '' }}">
                </div>

                {{-- Location --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Location</label>
                    <input type="text" name="location"
                           class="border border-gray-300 rounded px-3 py-2 w-full">
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Title</label>
                    <input type="text" name="title"
                           class="border border-gray-300 rounded px-3 py-2 w-full"
                           required>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Description</label>
                    <textarea name="description"
                              class="border border-gray-300 rounded px-3 py-2 w-full"></textarea>
                </div>

                {{-- Carers --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Assign Carers</label>
                    <div class="space-y-2">
                        @foreach($carers as $carer)
                            <div class="flex items-center">
                                <input type="checkbox" name="carers[]" value="{{ $carer->id }}"
                                       id="carer_{{ $carer->id }}"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="carer_{{ $carer->id }}" class="ml-2 text-gray-700">
                                    {{ $carer->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-6">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 transition">
                        Create Appointment
                    </button>
                </div>

            </form>
            @endif
        </div>
    </div>
</x-app-layout>