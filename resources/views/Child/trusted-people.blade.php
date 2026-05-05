<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🧩</span>
            <h2 class="font-semibold text-xl leading-tight">Trusted People</h2>
        </div>
    </x-slot>

    <div class="theme-page min-h-[calc(100vh-6rem)]">
        <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10 space-y-6">

            {{-- ── SUPPORT REQUEST CARD ─────────────────────────────── --}}
            <div class="theme-card rounded-3xl shadow-xl overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-red-500 via-rose-500 to-pink-500"></div>
                <div class="p-6 sm:p-8">

                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-3xl">🆘</span>
                        <h1 class="text-2xl font-extrabold text-rose-600">Need Help?</h1>
                    </div>

                    <p class="text-base opacity-75 mb-6">
                        If you feel unsafe or worried, choose one of these options.
                    </p>

                    @if(session('support_sent'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900 font-semibold">
                             Support alert sent. Your social worker has been notified.
                        </div>
                    @endif

                    {{-- Big support button --}}
                    <form method="POST" action="{{ route('child.support.request') }}" class="mb-4">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-2xl bg-red-600 hover:bg-red-700 active:scale-[0.98] transition
                                       text-white font-bold text-lg py-4 shadow-md focus:outline-none focus:ring-4 focus:ring-red-200">
                            I need support now
                        </button>
                    </form>

                    @if(!empty($socialWorker))
                        <p class="text-xs text-gray-500 mb-4">
                            Your social worker is <strong>{{ $socialWorker->name }}</strong>
                            — they will also be notified when you tap the button above.
                        </p>
                    @endif


                    {{-- Urgent box --}}
                    <div class="rounded-2xl bg-indigo-50 border border-indigo-100 px-5 py-4">
                        <div class="font-extrabold text-indigo-700 mb-1">If it's urgent</div>
                        <div class="text-sm text-indigo-600">
                            If you are in immediate danger, contact emergency services or a trusted adult nearby.
                            <strong>Call 999</strong> in an emergency.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TRUSTED PEOPLE CARD ──────────────────────────────── --}}
            <div class="theme-card rounded-3xl shadow-xl overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-indigo-600 via-fuchsia-600 to-sky-600"></div>
                <div class="p-6 sm:p-10">

                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                        Trusted people you can reach out to <span>💙</span>
                    </h1>
                    <p class="mt-2 text-base sm:text-lg opacity-75">
                        Add adults you trust (family, carers, teachers). You can contact them when you need support.
                    </p>

                    @if(session('success'))
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900">
                            <div class="font-semibold">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900">
                            <div class="font-semibold mb-1">Please fix:</div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- LIST --}}
                    <div class="mt-8">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-lg font-extrabold">Saved trusted people</h3>
                            <span class="text-xs font-semibold opacity-60">{{ $people->count() }} saved</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse($people as $p)
                                <div class="theme-card rounded-2xl px-5 py-4 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-2xl bg-slate-100 flex items-center justify-center text-lg">👤</div>
                                                <div class="min-w-0">
                                                    <div class="font-extrabold truncate">{{ $p->name }}</div>
                                                    <div class="text-sm opacity-75">{{ $p->relationship ?: 'Trusted person' }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                                                @if(!empty($p->phone))
                                                    <a href="tel:{{ $p->phone }}"
                                                       class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-800 px-3 py-1 hover:bg-slate-200 transition">
                                                        📞 <span class="font-semibold">{{ $p->phone }}</span>
                                                    </a>
                                                @endif
                                                @if(!empty($p->email))
                                                    <a href="mailto:{{ $p->email }}"
                                                       class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-800 px-3 py-1 hover:bg-slate-200 transition">
                                                        ✉️ <span class="font-semibold">{{ $p->email }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold shrink-0">
                                            Saved
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="theme-card rounded-2xl px-6 py-8">
                                    <div class="flex items-start gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl shadow-sm">✨</div>
                                        <div>
                                            <div class="font-extrabold">No trusted people yet</div>
                                            <div class="mt-1 text-sm opacity-75">Add someone below so they appear here.</div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ADD FORM --}}
                    <div class="mt-10 border-t border-slate-200 pt-8">
                        <h3 class="text-lg font-extrabold">Add a trusted person</h3>
                        <p class="mt-1 text-sm opacity-75">Add a name + relationship, and optionally a phone or email.</p>

                        <form method="POST" action="{{ route('child.trusted.store') }}" class="mt-6 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold mb-1">Name</label>
                                    <input name="name" value="{{ old('name') }}" required
                                           class="theme-input w-full rounded-2xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g. Ms. Kelly" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-1">Relationship</label>
                                    <input name="relationship" value="{{ old('relationship') }}" required
                                           class="theme-input w-full rounded-2xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g. Social Worker" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold mb-1">Phone (optional)</label>
                                    <input name="phone" value="{{ old('phone') }}"
                                           class="theme-input w-full rounded-2xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g. 0871234567" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-1">Email (optional)</label>
                                    <input name="email" type="email" value="{{ old('email') }}"
                                           class="theme-input w-full rounded-2xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g. support@email.com" />
                                </div>
                            </div>
                            <div class="mt-2 flex flex-col sm:flex-row gap-3">
                                <button type="submit"
                                        class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl px-6 py-3
                                               font-semibold bg-indigo-600 text-white shadow-sm hover:bg-indigo-700
                                               active:scale-[0.98] transition focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    Save
                                </button>
                                <a href="{{ route('child.dashboard') }}"
                                   class="inline-flex w-full sm:w-auto items-center justify-center rounded-2xl px-6 py-3.5
                                          font-semibold bg-slate-100 text-slate-800 hover:bg-slate-200 transition">
                                    ← Back
                                </a>
                            </div>
                        </form>

                        <p class="mt-4 text-xs opacity-60">Tip: Add at least one person you can contact quickly.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
