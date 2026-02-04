<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Carer Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">Welcome back, {{ $user->name }}.</p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded p-4">
                        <h3 class="font-semibold">Appointments</h3>
                        <p class="text-sm text-gray-600 mt-2">No appointments yet.</p>
                    </div>

                    <div class="border rounded p-4">
                        <h3 class="font-semibold">Messages</h3>
                        <p class="text-sm text-gray-600 mt-2">No messages yet.</p>
                    </div>

                    <div class="border rounded p-4">
                        <h3 class="font-semibold">Notifications</h3>
                        <p class="text-sm text-gray-600 mt-2">You're all caught up.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
