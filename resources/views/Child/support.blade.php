<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🆘</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Need Help?</h2>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-yellow-50 via-pink-50 to-orange-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="rounded-3xl p-8 shadow-xl bg-white/95 backdrop-blur border border-yellow-100">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🆘</span>
                    <h3 class="text-2xl font-extrabold text-yellow-800">Need Help?</h3>
                </div>

                <p class="text-gray-700 mt-3 text-lg">
                    If you feel unsafe or worried, press the button.
                </p>

                {{-- SUPPORT REQUEST FORM --}}
                <form method="POST" action="{{ route('child.support.store') }}" class="mt-6">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold py-4 shadow-lg transition"
                    >
                        I need support now
                    </button>
                </form>

                <p class="text-sm text-gray-500 mt-4">
                    This will notify a trusted adult in future versions.
                </p>

                <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">
                    <div class="font-bold text-blue-800">If it’s urgent</div>
                    <p class="text-blue-900 mt-1 text-sm">
                        If you are in immediate danger, contact emergency services or a trusted adult nearby.
                    </p>
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
