{{-- resources/views/Child/goals.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🧩</span>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">My Goals</h2>
        </div>
    </x-slot>

    @php
        $rawGoals = $goals ?? [
            'sleep' => 'Sleep on time',
            'talk'  => 'Talk to someone I trust',
            'fun'   => 'Do something fun',
            'water' => 'Drink water',
        ];

        $normalizedGoals = [];
        foreach ($rawGoals as $key => $val) {
            if (is_array($val)) {
                $normalizedGoals[$key] = $val + [
                    'title' => ucfirst($key),
                    'desc'  => 'Tap to select',
                    'icon'  => '✅',
                ];
            } else {
                $fallbackIcon = [
                    'sleep' => '🛏️',
                    'talk'  => '🧑‍🤝‍🧑',
                    'fun'   => '🎈',
                    'water' => '💧',
                ][$key] ?? '✅';

                $fallbackDesc = [
                    'sleep' => 'Build a steady bedtime routine.',
                    'talk'  => 'Reach out when you need support.',
                    'fun'   => 'Make time for something you enjoy.',
                    'water' => 'Stay hydrated throughout the day.',
                ][$key] ?? 'Tap to select';

                $normalizedGoals[$key] = [
                    'title' => $val,
                    'desc'  => $fallbackDesc,
                    'icon'  => $fallbackIcon,
                ];
            }
        }

        // IMPORTANT UX:
        // Start with nothing selected (best UX for “pick one” screens)
        // If validation fails, old('goal_key') will reselect.
        $selectedKey = old('goal_key');
    @endphp

    <div class="min-h-[calc(100vh-6rem)] bg-slate-50">
        <div class="relative overflow-hidden">
            {{-- soft background --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-indigo-200/35 blur-3xl"></div>
                <div class="absolute -top-28 right-0 h-72 w-72 rounded-full bg-fuchsia-200/30 blur-3xl"></div>
                <div class="absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-sky-200/30 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10">
                <div class="rounded-3xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-indigo-600 via-fuchsia-600 to-sky-600"></div>

                    <div class="p-6 sm:p-10">
                        <div class="flex flex-col gap-2">
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                                Pick one small goal for this week <span class="align-middle">✨</span>
                            </h1>
                            <p class="text-slate-600 text-base sm:text-lg">
                                Choose something realistic. You can change it again next week.
                            </p>
                        </div>

                        @if (session('success'))
                            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900">
                                <div class="font-semibold">✅ {{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900">
                                <div class="font-semibold mb-1">Please fix:</div>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- FORM --}}
                        <form method="POST" action="{{ route('child.goals.store') }}" class="mt-8" id="goalForm">
                            @csrf

                            <fieldset>
                                <legend class="sr-only">Weekly goal</legend>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                    @foreach ($normalizedGoals as $key => $g)
                                        @php
                                            $id = 'goal_' . $key;
                                            $isSelected = ($selectedKey === $key);
                                        @endphp

                                        <label for="{{ $id }}" class="relative block">
                                            {{-- input (covers the whole card so clicking anywhere works) --}}
                                            <input
                                                id="{{ $id }}"
                                                type="radio"
                                                name="goal_key"
                                                value="{{ $key }}"
                                                class="goal-radio peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                                                @checked($isSelected)
                                                required
                                            />

                                            {{-- card --}}
                                            <div
                                                class="rounded-2xl border bg-white p-5 sm:p-6 shadow-sm transition
                                                       border-slate-200 hover:shadow-md
                                                       peer-checked:border-indigo-400 peer-checked:ring-4 peer-checked:ring-indigo-100"
                                            >
                                                <div class="flex items-start gap-4">
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                                                        {{ $g['icon'] ?? '✅' }}
                                                    </div>

                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-start justify-between gap-4">
                                                            <div class="min-w-0">
                                                                <div class="text-lg font-bold text-slate-900">
                                                                    {{ $g['title'] ?? 'Goal' }}
                                                                </div>
                                                                <div class="mt-1 text-sm text-slate-600">
                                                                    {{ $g['desc'] ?? 'Tap to select' }}
                                                                </div>
                                                            </div>

                                                            {{-- selection circle + tick --}}
                                                            <div class="flex items-center">
                                                                <span
                                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border bg-white
                                                                           border-slate-200
                                                                           peer-checked:border-indigo-400 peer-checked:bg-indigo-50"
                                                                    aria-hidden="true"
                                                                >
                                                                    {{-- SVG tick: this ALWAYS shows when selected --}}
                                                                    <svg
                                                                        class="tick-icon h-5 w-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition"
                                                                        viewBox="0 0 20 20"
                                                                        fill="currentColor"
                                                                        aria-hidden="true"
                                                                    >
                                                                        <path fill-rule="evenodd"
                                                                              d="M16.704 5.29a1 1 0 01.006 1.414l-7.5 7.57a1 1 0 01-1.42 0L3.29 9.78a1 1 0 011.42-1.4l3.08 3.12 6.79-6.86a1 1 0 011.414-.01z"
                                                                              clip-rule="evenodd" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="mt-4 text-xs text-slate-500">
                                                            Tap to select
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- keyboard focus --}}
                                                <span class="pointer-events-none absolute inset-0 rounded-2xl ring-0 ring-indigo-300 peer-focus-visible:ring-4"></span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            {{-- Actions: HIDDEN until a goal is picked --}}
                            <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                                <button
                                    id="saveBtn"
                                    type="submit"
                                    class="hidden inline-flex items-center justify-center rounded-2xl px-6 py-3.5
                                           font-semibold border border-slate-200 bg-slate-100 text-slate-800
                                           hover:bg-slate-200 active:scale-[0.99] transition
                                           focus:outline-none focus:ring-4 focus:ring-slate-200"
                                >
                                    Save My Weekly Goal ✨
                                </button>

                                <a
                                    href="{{ route('child.dashboard') }}"
                                    class="inline-flex items-center justify-center rounded-2xl px-6 py-3.5
                                           font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition"
                                >
                                    ← Back to Child Dashboard
                                </a>
                            </div>

                            <p class="mt-4 text-sm text-slate-500">
                                Tip: Picking one goal makes it easier to stick with.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const radios = document.querySelectorAll('.goal-radio');
        const saveBtn = document.getElementById('saveBtn');

        function updateUI() {
            const checked = Array.from(radios).some(r => r.checked);

            // Show Save only after selecting something
            if (checked) saveBtn.classList.remove('hidden');
            else saveBtn.classList.add('hidden');

            // Safety fallback: force tick visibility even if peer CSS ever glitches
            radios.forEach(r => {
                const label = r.closest('label');
                if (!label) return;
                const tick = label.querySelector('.tick-icon');
                if (!tick) return;
                tick.style.opacity = r.checked ? '1' : '0';
            });
        }

        // run now + on change
        updateUI();
        radios.forEach(r => r.addEventListener('change', updateUI));
    </script>
</x-app-layout>