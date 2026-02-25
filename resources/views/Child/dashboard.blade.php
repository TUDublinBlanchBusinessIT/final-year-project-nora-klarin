<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👋 Hi {{ Auth::user()->name ?? 'there' }}!
            </h2>

            <div class="flex items-center gap-4">
                {{-- ✅ Bell always visible --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="relative text-2xl rounded-full px-2 py-1 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        aria-label="Open reminders"
                    >
                        🔔

                        {{-- ✅ Badge only if reminders exist --}}
                        @if(isset($reminderCount) && $reminderCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                                {{ $reminderCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden z-50"
                    >
                        <div class="px-4 py-3 border-b bg-gray-50">
                            <div class="font-extrabold text-gray-800">Reminders</div>
                            <div class="text-xs text-gray-500">Things to do today</div>
                        </div>

                        <div class="px-4 py-4 space-y-3">
                            @if(isset($reminderCount) && $reminderCount > 0)
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                                    <div class="font-bold text-indigo-800">📖 Diary reminder</div>
                                    <div class="text-sm text-indigo-900 mt-1">
                                        You haven’t written a diary entry today.
                                    </div>
                                </div>

                                <a
                                    href="#diary"
                                    @click="open = false"
                                    class="block text-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 transition"
                                >
                                    Write diary now ✨
                                </a>
                            @else
                                <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3">
                                    <div class="font-bold text-green-800">🎉 All done!</div>
                                    <div class="text-sm text-green-900 mt-1">
                                        You have no reminders right now.
                                    </div>
                                </div>
                            @endif

                            <button
                                type="button"
                                @click="open = false"
                                class="w-full text-sm text-gray-600 hover:text-gray-900 underline"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <span class="text-sm text-gray-500">
                    {{ now()->format('l, jS F') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- TOP CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

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

                {{-- Quick Links --}}
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

                {{-- ✅ Messages (NEW) --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">
                    <h3 class="text-lg font-extrabold text-indigo-700">💬 Messages</h3>
                    <p class="text-gray-600 mt-2">
                        Chat with your carer securely inside the app.
                    </p>

                    <a href="{{ route('child.messages.index') }}"
                       class="mt-4 block text-center w-full rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 shadow transition">
                        Open messages
                    </a>

                    <p class="text-xs text-gray-500 mt-3">
                        Safe chat ✨
                    </p>
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
                <div id="diary" class="lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-white/95 backdrop-blur border border-indigo-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-extrabold text-indigo-700">📖 My Diary</h3>
                            <p class="text-gray-600 mt-1">Write anything you want — this is your space.</p>
                        </div>
                        <span class="text-xs sm:text-sm px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                            New Entry
                        </span>
                    </div>

                    @if (session('success'))
                        <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

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
                    <p class="text-sm text-gray-600 mt-1">Your latest diary entries.</p>

                    @php
                        $moodEmoji = [
                            'happy' => '😊',
                            'calm' => '😌',
                            'okay' => '😐',
                            'worried' => '😟',
                            'sad' => '😢',
                        ];
                    @endphp

                    <div class="mt-4 space-y-3">
                        @if(isset($recentEntries) && $recentEntries->count())
                            @foreach($recentEntries as $entry)
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <div class="font-semibold text-gray-800">
                                        {{ $entry->title ?: 'Untitled entry' }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        Mood:
                                        <span class="mr-1">{{ $moodEmoji[$entry->mood] ?? '✅' }}</span>
                                        {{ ucfirst($entry->mood ?? 'unknown') }}
                                        •
                                        {{ \Carbon\Carbon::parse($entry->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-gray-600">
                                No entries yet — write your first one on the left ✨
                            </div>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500 pt-3">
                        Tip: Your newest entries will show here automatically.
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
