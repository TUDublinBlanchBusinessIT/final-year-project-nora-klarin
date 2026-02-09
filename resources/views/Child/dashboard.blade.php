<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👋 Hi {{ Auth::user()->name ?? 'there' }}!
            </h2>
            <span class="text-sm text-gray-500">
                {{ now()->format('l, jS F') }}
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- TOP CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Check-in --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100">
                    <h3 class="text-lg font-extrabold text-blue-700">🌟 Today’s Check-in</h3>
                    <p class="text-gray-600 mt-2">How are you feeling today?</p>

                    <div class="mt-4 grid grid-cols-5 gap-2 text-xl">
                        <a href="{{ route('child.mood.save', 'happy') }}" class="text-center rounded-2xl bg-yellow-100 hover:bg-yellow-200 py-3 transition">😊</a>
                        <a href="{{ route('child.mood.save', 'calm') }}" class="text-center rounded-2xl bg-green-100 hover:bg-green-200 py-3 transition">😌</a>
                        <a href="{{ route('child.mood.save', 'okay') }}" class="text-center rounded-2xl bg-blue-100 hover:bg-blue-200 py-3 transition">😐</a>
                        <a href="{{ route('child.mood.save', 'worried') }}" class="text-center rounded-2xl bg-purple-100 hover:bg-purple-200 py-3 transition">😟</a>
                        <a href="{{ route('child.mood.save', 'sad') }}" class="text-center rounded-2xl bg-red-100 hover:bg-red-200 py-3 transition">😢</a>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">Pick one to start your day 🌈</p>
                </div>

                {{-- Quick Links (FIXED WRAPPER) --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-pink-100">
                    <h3 class="text-lg font-extrabold text-pink-700">📌 Quick Links</h3>

                    <div class="mt-4 space-y-3">
                        <a href="{{ route('child.goals') }}"
                           class="block rounded-2xl bg-pink-50 hover:bg-pink-100 px-4 py-3 font-semibold text-pink-700 transition">
                            🧩 My Goals
                        </a>

                        <a href="{{ route('child.trusted') }}"
                           class="block rounded-2xl bg-blue-50 hover:bg-blue-100 px-4 py-3 font-semibold text-blue-700 transition">
                            👨‍👩‍👧 Trusted People
                        </a>

                        <a href="{{ route('child.week') }}"
                           class="block rounded-2xl bg-yellow-50 hover:bg-yellow-100 px-4 py-3 font-semibold text-yellow-700 transition">
                            📅 My Week
                        </a>
                    </div>
                </div>

                {{-- Help --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-yellow-100">
                    <h3 class="text-lg font-extrabold text-yellow-700">🆘 Need Help?</h3>
                    <p class="text-gray-600 mt-2">
                        If you feel unsafe or worried, press the button.
                    </p>

                    <a href="{{ route('child.support') }}"
                     class="mt-4 block text-center w-full rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold py-3 shadow transition">
                    I need support now
                    </a>


                    <p class="text-xs text-gray-500 mt-3">
                        This can alert a trusted adult (later feature).
                    </p>
                </div>

            </div>

            {{-- DIARY + RECENT --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Diary Form --}}
                <div class="lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-white/95 backdrop-blur border border-indigo-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-extrabold text-indigo-700">📖 My Diary</h3>
                            <p class="text-gray-600 mt-1">Write anything you want — this is your space.</p>
                        </div>
                        <span class="text-xs sm:text-sm px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                            New Entry
                        </span>
                    </div>

                    {{-- Success message --}}
                    @if (session('success'))
                        <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                            <div class="font-bold mb-1">Please fix:</div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('child.diary.store') }}" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Title</label>
                            <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="e.g. Today was a good day!"
                                class="w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                            />
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">What happened today?</label>
                            <textarea
                                name="content"
                                rows="6"
                                placeholder="Write here..."
                                class="w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                            >{{ old('content') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Mood</label>
                                <select
                                    name="mood"
                                    class="w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                                >
                                    <option value="happy"  @selected(old('mood') === 'happy')>😊 Happy</option>
                                    <option value="calm"   @selected(old('mood') === 'calm')>😌 Calm</option>
                                    <option value="okay"   @selected(old('mood') === 'okay')>😐 Okay</option>
                                    <option value="worried"@selected(old('mood') === 'worried')>😟 Worried</option>
                                    <option value="sad"    @selected(old('mood') === 'sad')>😢 Sad</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Private?</label>
                                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="private"
                                        value="1"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        @checked(old('private'))
                                    >
                                    <span class="text-gray-600">Keep this entry private (later feature)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full mt-4 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600
                                       hover:opacity-95 text-white font-extrabold py-3 shadow-lg
                                       focus:outline-none focus:ring-4 focus:ring-pink-200 transition">
                            Save Diary Entry ✨
                        </button>
                    </form>
                </div>

                {{-- Recent Entries --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">
                    <h3 class="text-lg font-extrabold text-indigo-700">🗂️ Recent Entries</h3>
                    <p class="text-sm text-gray-600 mt-1">You’ll see your saved diary entries here.</p>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div class="font-semibold text-gray-800">Example: A good day</div>
                            <div class="text-xs text-gray-500 mt-1">Mood: 😊 Happy • Today</div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div class="font-semibold text-gray-800">Example: Felt worried</div>
                            <div class="text-xs text-gray-500 mt-1">Mood: 😟 Worried • Yesterday</div>
                        </div>

                        <div class="text-xs text-gray-500 pt-1">
                            Next step: we’ll load real entries from DB.
                        </div>
                    </div>
                </div>

            </div>

            {{-- Bottom cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-3xl p-6 shadow-lg bg-gradient-to-br from-green-50 to-blue-50 border border-green-100">
                    <h3 class="text-lg font-extrabold text-green-700">🎯 My Goal This Week</h3>
                    <p class="text-gray-700 mt-2">Pick one small thing to work on.</p>
                    <ul class="mt-4 space-y-2 text-gray-700">
                        <li>✅ Sleep on time</li>
                        <li>✅ Talk to someone I trust</li>
                        <li>✅ Do something fun</li>
                    </ul>
                </div>

                <div class="rounded-3xl p-6 shadow-lg bg-gradient-to-br from-yellow-50 to-pink-50 border border-yellow-100">
                    <h3 class="text-lg font-extrabold text-pink-700">🌈 Something Positive</h3>
                    <p class="text-gray-700 mt-2">
                        “You don’t have to do everything. Just one small step.”
                    </p>
                    <div class="mt-4 text-3xl">💛✨</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
