<x-app-layout>
    @php
        $theme = auth()->user()->theme ?? 'calm';
        $layout = auth()->user()->dashboard_layout ?? 'standard';

        $pageBgClass = match ($theme) {
            'bright' => 'bg-gradient-to-br from-yellow-50 via-pink-50 to-orange-50',
            'simple' => 'bg-gray-50',
            'dark' => 'bg-gray-900',
            default => 'bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50',
        };

        $headerTextClass = $theme === 'dark' ? 'text-white' : 'text-gray-800';
        $subtleTextClass = $theme === 'dark' ? 'text-gray-300' : 'text-gray-600';
        $smallTextClass = $theme === 'dark' ? 'text-gray-400' : 'text-gray-500';

        $cardClass = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100';

        $cardClassPink = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-pink-100';

        $cardClassIndigo = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100';

        $cardClassYellow = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-yellow-100';

        $largeCardClass = $theme === 'dark'
            ? 'lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-gray-800 border border-gray-700 text-white'
            : 'lg:col-span-2 rounded-3xl p-7 sm:p-8 shadow-xl bg-white/95 backdrop-blur border border-indigo-100';

        $recentCardClass = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100';

        $bottomLeftCardClass = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-gradient-to-br from-green-50 to-blue-50 border border-green-100';

        $bottomRightCardClass = $theme === 'dark'
            ? 'rounded-3xl p-6 shadow-lg bg-gray-800 border border-gray-700 text-white'
            : 'rounded-3xl p-6 shadow-lg bg-gradient-to-br from-yellow-50 to-pink-50 border border-yellow-100';

        $innerBoxClass = $theme === 'dark'
            ? 'rounded-2xl border border-gray-700 bg-gray-700 px-4 py-3'
            : 'rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3';

        $inputClass = $theme === 'dark'
            ? 'w-full rounded-2xl border-gray-600 bg-gray-700 text-white focus:border-indigo-400 focus:ring-indigo-400 px-4 py-3'
            : 'w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3';

        $selectClass = $theme === 'dark'
            ? 'w-full rounded-2xl border-gray-600 bg-gray-700 text-white focus:border-indigo-400 focus:ring-indigo-400 px-4 py-3'
            : 'w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3';

        $checkboxWrapClass = $theme === 'dark'
            ? 'flex items-center gap-3 rounded-2xl border border-gray-600 px-4 py-3 cursor-pointer bg-gray-700'
            : 'flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 cursor-pointer';

        $topGridClass = $layout === 'minimal'
            ? 'grid grid-cols-1 md:grid-cols-3 gap-6'
            : 'grid grid-cols-1 md:grid-cols-4 gap-6';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl leading-tight {{ $headerTextClass }}">
                👋 Hi {{ Auth::user()->name ?? 'there' }}!
            </h2>

            <div class="flex items-center gap-4">
                @php
                    $totalNotificationCount = ($reminderCount ?? 0) + ($unreadMessageCount ?? 0);
                @endphp

                <div x-data="{ open: false }" class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="relative text-2xl rounded-full px-2 py-1 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        aria-label="Open reminders"
                    >
                        🔔

                        @if($totalNotificationCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                                {{ $totalNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden z-50"
                    >
                        <div class="px-4 py-3 border-b bg-gray-50">
                            <div class="font-extrabold text-gray-800">Notifications</div>
                            <div class="text-xs text-gray-500">Things to check today</div>
                        </div>

                        <div class="px-4 py-4 space-y-3">
                            @if(($unreadMessageCount ?? 0) > 0)
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                                    <div class="font-bold text-indigo-800">💬 New messages</div>
                                    <div class="text-sm text-indigo-900 mt-1">
                                        You have {{ $unreadMessageCount }} unread {{ $unreadMessageCount === 1 ? 'message' : 'messages' }}.
                                    </div>
                                </div>

                                <a
                                    href="{{ route('child.messages.index') }}"
                                    @click="open = false"
                                    class="block text-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 transition"
                                >
                                    Open messages
                                </a>
                            @endif

                            @if(isset($reminderCount) && $reminderCount > 0)
                                <div class="rounded-xl border border-yellow-100 bg-yellow-50 px-4 py-3">
                                    <div class="font-bold text-yellow-800">📖 Diary reminder</div>
                                    <div class="text-sm text-yellow-900 mt-1">
                                        You haven’t written a diary entry today.
                                    </div>
                                </div>

                                <a
                                    href="#diary"
                                    @click="open = false"
                                    class="block text-center rounded-xl bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 transition"
                                >
                                    Write diary now ✨
                                </a>
                            @endif

                            @if(($unreadMessageCount ?? 0) === 0 && ($reminderCount ?? 0) === 0)
                                <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3">
                                    <div class="font-bold text-green-800">🎉 All done!</div>
                                    <div class="text-sm text-green-900 mt-1">
                                        You have no notifications right now.
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

                <span class="text-sm {{ $smallTextClass }}">
                    {{ now()->format('l, jS F') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 {{ $pageBgClass }}">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- TOP CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Wellbeing Check --}}
            <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-indigo-100">
                <h3 class="text-lg font-extrabold text-indigo-700">🧠 Wellbeing Check</h3>
                <p class="text-gray-600 mt-2">
                    Answer a few quick questions about how you're feeling.
                </p>

                <a href="{{ route('child.wellbeing.check') }}"
                class="mt-4 block text-center w-full rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 shadow transition">
                    Start check ✨
                </a>

                <p class="text-xs text-gray-500 mt-3">
                    Takes about 2–3 minutes
                </p>
            </div>
                {{-- Check-in --}}
                <div class="rounded-3xl p-6 shadow-lg bg-white/90 backdrop-blur border border-blue-100">
            <div class="{{ $topGridClass }}">

                <div class="{{ $cardClass }}">
                    <h3 class="text-lg font-extrabold text-blue-700">🌟 Today’s Check-in</h3>
                    <p class="mt-2 {{ $subtleTextClass }}">How are you feeling today?</p>

                    <div class="mt-4 grid grid-cols-5 gap-2 text-xl">
                        <a href="{{ route('child.mood.save', 'happy') }}" class="text-center rounded-2xl bg-yellow-100 hover:bg-yellow-200 py-3 transition">😊</a>
                        <a href="{{ route('child.mood.save', 'calm') }}" class="text-center rounded-2xl bg-green-100 hover:bg-green-200 py-3 transition">😌</a>
                        <a href="{{ route('child.mood.save', 'okay') }}" class="text-center rounded-2xl bg-blue-100 hover:bg-blue-200 py-3 transition">😐</a>
                        <a href="{{ route('child.mood.save', 'worried') }}" class="text-center rounded-2xl bg-purple-100 hover:bg-purple-200 py-3 transition">😟</a>
                        <a href="{{ route('child.mood.save', 'sad') }}" class="text-center rounded-2xl bg-red-100 hover:bg-red-200 py-3 transition">😢</a>
                    </div>

                    <p class="text-xs mt-3 {{ $smallTextClass }}">Pick one to start your day 🌈</p>
                </div>

                @if($layout !== 'minimal')
                    <div class="{{ $cardClassPink }}">
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

                            <a href="{{ route('child.support.map') }}"
                               class="block rounded-2xl bg-green-50 hover:bg-green-100 px-4 py-3 font-semibold text-green-700 transition">
                                🗺️ Find Help Nearby
                            </a>
                        </div>
                    </div>
                @endif

                <div class="{{ $cardClassIndigo }}">
                    <h3 class="text-lg font-extrabold text-indigo-700">💬 Messages</h3>

                    <p class="mt-2 {{ $subtleTextClass }}">
                        Chat with your carer securely inside the app.
                    </p>

                    <a href="{{ route('child.messages.index') }}"
                       class="mt-4 block text-center w-full rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 shadow transition">
                        Open messages
                    </a>

                    <p class="text-xs mt-3 {{ $smallTextClass }}">
                        Safe chat ✨
                    </p>
                </div>

                <div class="{{ $cardClassYellow }}">
                    <h3 class="text-lg font-extrabold text-yellow-700">🆘 Need Help?</h3>
                    <p class="mt-2 {{ $subtleTextClass }}">
                        If you feel unsafe or worried, press the button.
                    </p>

                    <a href="{{ route('child.support') }}"
                       class="mt-4 block text-center w-full rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold py-3 shadow transition">
                        I need support now
                    </a>

                    <p class="text-xs mt-3 {{ $smallTextClass }}">
                        This can alert a trusted adult (later feature).
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div id="diary" class="{{ $largeCardClass }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-extrabold text-indigo-700">📖 My Diary</h3>
                            <p class="mt-1 {{ $subtleTextClass }}">Write anything you want — this is your space.</p>
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
                            <label class="block font-semibold mb-2 {{ $theme === 'dark' ? 'text-gray-200' : 'text-gray-700' }}">Title</label>
                            <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="e.g. Today was a good day!"
                                class="{{ $inputClass }}"
                            />
                        </div>

                        <div>
                            <label class="block font-semibold mb-2 {{ $theme === 'dark' ? 'text-gray-200' : 'text-gray-700' }}">What happened today?</label>
                            <textarea
                                name="content"
                                rows="6"
                                placeholder="Write here..."
                                class="{{ $inputClass }}"
                            >{{ old('content') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold mb-2 {{ $theme === 'dark' ? 'text-gray-200' : 'text-gray-700' }}">Mood</label>
                                <select
                                    name="mood"
                                    class="{{ $selectClass }}"
                                >
                                    <option value="happy" @selected(old('mood') === 'happy')>😊 Happy</option>
                                    <option value="calm" @selected(old('mood') === 'calm')>😌 Calm</option>
                                    <option value="okay" @selected(old('mood') === 'okay')>😐 Okay</option>
                                    <option value="worried" @selected(old('mood') === 'worried')>😟 Worried</option>
                                    <option value="sad" @selected(old('mood') === 'sad')>😢 Sad</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold mb-2 {{ $theme === 'dark' ? 'text-gray-200' : 'text-gray-700' }}">Private?</label>
                                <label class="{{ $checkboxWrapClass }}">
                                    <input
                                        type="checkbox"
                                        name="private"
                                        value="1"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        @checked(old('private'))
                                    >
                                    <span class="{{ $subtleTextClass }}">Keep this entry private (later feature)</span>
                                </label>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            Save Diary Entry ✨
                        </button>
                    </form>
                </div>

                <div class="{{ $recentCardClass }}">
                    <h3 class="text-lg font-extrabold text-indigo-700">🗂️ Recent Entries</h3>
                    <p class="text-sm mt-1 {{ $subtleTextClass }}">Your latest diary entries.</p>

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
                                <div class="{{ $innerBoxClass }}">
                                    <div class="font-semibold {{ $theme === 'dark' ? 'text-white' : 'text-gray-800' }}">
                                        {{ $entry->title ?: 'Untitled entry' }}
                                    </div>

                                    <div class="text-xs mt-1 {{ $smallTextClass }}">
                                        Mood:
                                        <span class="mr-1">{{ $moodEmoji[$entry->mood] ?? '✅' }}</span>
                                        {{ ucfirst($entry->mood ?? 'unknown') }}
                                        •
                                        {{ \Carbon\Carbon::parse($entry->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="{{ $innerBoxClass }} {{ $subtleTextClass }}">
                                No entries yet — write your first one on the left ✨
                            </div>
                        @endif
                    </div>

                    <div class="text-xs pt-3 {{ $smallTextClass }}">
                        Tip: Your newest entries will show here automatically.
                    </div>
                </div>
            </div>

            @if($layout !== 'minimal')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="{{ $bottomLeftCardClass }}">
                        <h3 class="text-lg font-extrabold text-green-700">🎯 My Goal This Week</h3>
                        <p class="mt-2 {{ $subtleTextClass }}">Pick one small thing to work on.</p>
                        <ul class="mt-4 space-y-2 {{ $theme === 'dark' ? 'text-gray-200' : 'text-gray-700' }}">
                            <li>✅ Sleep on time</li>
                            <li>✅ Talk to someone I trust</li>
                            <li>✅ Do something fun</li>
                        </ul>
                    </div>

                    <div class="{{ $bottomRightCardClass }}">
                        <h3 class="text-lg font-extrabold text-pink-700">🌈 Something Positive</h3>
                        <p class="mt-2 {{ $subtleTextClass }}">
                            “You don’t have to do everything. Just one small step.”
                        </p>
                        <div class="mt-4 text-3xl">💛✨</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating Chatbot Button --}}
    <a href="{{ route('chatbot.index') }}"
       class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-16 h-16 rounded-full bg-blue-700 hover:bg-blue-800 text-white shadow-2xl transition transform hover:scale-105"
       aria-label="Open {{ auth()->user()->chatbot_name ?? 'CareHub Assistant' }}">
        <span class="text-2xl">💬</span>
    </a>
</x-app-layout>