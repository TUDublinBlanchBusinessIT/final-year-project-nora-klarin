<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📅 My Week</h2>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-yellow-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4">
            <div class="rounded-3xl bg-white/90 backdrop-blur shadow-xl border border-yellow-100 p-8">
                <p class="text-gray-700 text-lg font-semibold">A simple week view (demo for now) 🌈</p>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                        <div class="rounded-2xl border bg-yellow-50 px-5 py-4">
                            <div class="font-bold text-gray-800">{{ $day }}</div>
                            <div class="text-sm text-gray-600 mt-1">No plans yet</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <a href="{{ route('child.dashboard') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                        ← Back to Child Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
