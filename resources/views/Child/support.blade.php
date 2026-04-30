<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🆘</span>
            <h2 class="font-semibold text-xl leading-tight">Need Help?</h2>
        </div>
    </x-slot>

    <div class="theme-page min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="theme-card rounded-3xl p-8 shadow-xl">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🆘</span>
                    <h3 class="text-2xl font-extrabold text-yellow-600">Need Help?</h3>
                </div>

                <p class="mt-3 text-lg opacity-80">
                    If you feel unsafe or worried, choose one of these options.
                </p>

                <form method="POST" action="{{ route('child.support.store') }}" class="mt-6">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold py-4 shadow-lg transition"
                    >
                        I need support now
                    </button>
                </form>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($carer && !empty($carer->phone))
                        <a
                            href="tel:{{ $carer->phone }}"
                            class="block text-center rounded-2xl px-4 py-3 font-semibold bg-indigo-600 text-white shadow-sm
                                   hover:bg-indigo-700 active:scale-[0.98] transition
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                            📞 Call carer
                        </a>
                    @else
                        <div class="block text-center rounded-2xl px-4 py-3 font-semibold bg-slate-100 text-slate-400">
                            📞 No phone available
                        </div>
                    @endif

                    @if($carer && !empty($carer->email))
                        <a
                            href="mailto:{{ $carer->email }}"
                            class="block text-center rounded-2xl px-4 py-3 font-semibold bg-indigo-600 text-white shadow-sm
                                   hover:bg-indigo-700 active:scale-[0.98] transition
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >
                            ✉️ Email carer
                        </a>
                    @else
                        <div class="block text-center rounded-2xl px-4 py-3 font-semibold bg-slate-100 text-slate-400">
                            ✉️ No email available
                        </div>
                    @endif
                </div>

                <p class="text-sm mt-4 opacity-60">
                    This sends a support alert. You can also call or email your carer directly.
                </p>

                <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-blue-900">
                    <div class="font-bold text-blue-800">If it’s urgent</div>
                    <p class="mt-1 text-sm">
                        If you are in immediate danger, contact emergency services or a trusted adult nearby.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('child.dashboard') }}" class="underline text-sm opacity-70 hover:opacity-100">
                        ← Back to Child Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>