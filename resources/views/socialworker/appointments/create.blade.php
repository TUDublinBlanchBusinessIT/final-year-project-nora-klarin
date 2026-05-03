<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">
                    Schedule Appointment
                </h1>
                <p class="text-sm text-gray-500">
                    For {{ $youngPerson->name }} (Case {{ $case->case_reference }})
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-6 rounded-xl shadow">

            @if(!$youngPerson)
                <p class="text-red-600 mb-4">
                    No young person assigned to this case. Appointment cannot be created.
                </p>
            @else
            <form method="POST" action="{{ route('socialworker.appointments.store') }}">
                @csrf
                <input type="hidden" name="case_file_id" value="{{ $case->id }}">

                {{-- Date & Time --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Date</label>
                        <input type="date" name="date"
                               class="border border-gray-300 rounded px-3 py-2 w-full"
                               value="{{ $availableSlot ? $availableSlot->format('Y-m-d') : '' }}"
                               required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Time</label>
                        <input type="time" name="time"
                               class="border border-gray-300 rounded px-3 py-2 w-full"
                               value="{{ $availableSlot ? $availableSlot->format('H:i') : '' }}"
                               required>
                    </div>
                </div>

                {{-- End Time --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">End Time (optional)</label>
                    <input type="time" name="end_time"
                           class="border border-gray-300 rounded px-3 py-2 w-full">
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

                {{-- Attendees --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Attendees</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="invite_child" value="1" checked
                                   id="invite_child"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="invite_child" class="ml-2 text-gray-700">
                                Invite child ({{ $youngPerson->name }})
                            </label>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Invite carers:</p>
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