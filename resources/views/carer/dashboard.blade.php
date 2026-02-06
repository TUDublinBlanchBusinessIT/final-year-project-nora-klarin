<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Carer Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700 mb-6">
                    Welcome back, {{ $user->name }}.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Appointments -->
                    <div class="border rounded p-4">
                        <h3 class="font-semibold mb-2">Upcoming Appointments</h3>

                        @if(!$carer)
                            <p class="text-sm text-red-600">
                                No foster carer profile found for {{ $user->email }}.
                                Your email must match a record in the <strong>fostercarer</strong> table.
                            </p>
                        @endif

                        @if($appointments->isEmpty())
                            <p class="text-sm text-gray-600 mt-2">
                                No upcoming appointments.
                            </p>
                        @else
                            <ul class="space-y-2 text-sm mt-2">
                                @foreach($appointments as $appt)
                                    <li class="border-b pb-2">
                                        <div class="font-medium">
                                            {{ \Carbon\Carbon::parse($appt->starttime)->format('D d M Y, H:i') }}
                                        </div>
                                        <div class="text-gray-600">
                                            {{ $appt->notes ?? 'No notes provided' }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-3">
                                <a href="{{ route('carer.calendar') }}"
                                   class="text-blue-600 text-sm hover:underline">
                                    View full calendar →
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Messages -->
                    <!-- Messages -->
                    <div class="border rounded p-4">
                        <h3 class="font-semibold mb-2">Messages</h3>

                        <a href="{{ route('carer.messages.index') }}"
                        class="text-sm text-blue-600 hover:underline">
                            Open messages →
                        </a>
                    </div>


                    <!-- Notifications -->
                    <div class="border rounded p-4">
                        <h3 class="font-semibold">Notifications</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            You're all caught up.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
