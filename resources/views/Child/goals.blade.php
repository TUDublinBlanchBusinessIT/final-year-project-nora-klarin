<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🧩</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Goals</h2>
        </div>
    </x-slot>

    @php
        // Fallbacks so page never crashes
        $goals = $goals ?? [
            'sleep' => 'Sleep on time',
            'talk'  => 'Talk to someone I trust',
            'fun'   => 'Do something fun',
            'water' => 'Drink water',
        ];

        $currentGoalKey = $currentGoalKey ?? null;
    @endphp

    <div class="min-h-screen py-10 bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="rounded-3xl p-8 shadow-xl bg-white/95 backdrop-blur border border-pink-100">
                <h3 class="text-2xl font-extrabold text-gray-800">
                    Pick one small goal for this week ✨
                </h3>

                @if (session('success'))
                    <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('child.goals.store') }}" class="mt-8">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @php
                            $styles = [
                                'sleep' => 'bg-pink-50 border-pink-100',
                                'talk'  => 'bg-blue-50 border-blue-100',
                                'fun'   => 'bg-yellow-50 border-yellow-100',
                                'water' => 'bg-green-50 border-green-100',
                            ];

                            $icons = [
                                'sleep' => '🛏️',
                                'talk'  => '🧑‍🤝‍🧑',
                                'fun'   => '🎈',
                                'water' => '💧',
                            ];
                        @endphp

                        @foreach ($goals as $key => $label)
                            @php
                                $selected = ($currentGoalKey === $key);
                            @endphp

                            <label
                                class="cursor-pointer rounded-3xl p-5 border shadow-sm transition
                                {{ $styles[$key] ?? 'bg-gray-50 border-gray-100' }}
                                {{ $selected ? 'ring-4 ring-purple-200 scale-[1.01]' : 'hover:scale-[1.01]' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">{{ $icons[$key] ?? '✅' }}</span>

                                    <div class="flex-1">
                                        <div class="font-extrabold text-gray-800 text-lg">
                                            {{ $label }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Choose this goal for the week
                                        </div>
                                    </div>

                                    <input
                                        type="radio"
                                        name="goal_key"
                                        value="{{ $key }}"
                                        class="h-5 w-5 text-purple-600"
                                        @checked(old('goal_key', $currentGoalKey) === $key)
                                    />
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <button
                        type="submit"
                        class="mt-7 w-full rounded-2xl bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600
                               hover:opacity-95 text-white font-extrabold py-4 shadow-lg
                               focus:outline-none focus:ring-4 focus:ring-purple-200 transition"
                    >
                        Save My Weekly Goal ✨
                    </button>

                    <div class="mt-6">
                        <a href="{{ route('child.dashboard') }}" class="underline text-gray-600 hover:text-gray-900">
                            ← Back to Child Dashboard
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
