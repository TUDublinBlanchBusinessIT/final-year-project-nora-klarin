<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👋 Hi {{ Auth::user()->name ?? 'there' }}!
            </h2>
            <span class="text-sm text-gray-500">
                {{ now()->format('l, jS F') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-8">

            {{-- Top Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl p-6 shadow bg-white border border-blue-100">
                    <h3 class="text-lg font-bold text-blue-700">🌟 Today’s Check-in</h3>
                    <p class="text-gray-600 mt-2">How are you feeling today?</p>

                    <div class="mt-4 grid grid-cols-5 gap-2 text-xl">
                        <button class="rounded-xl bg-yellow-100 hover:bg-yellow-200 py-2">😊</button>
                        <button class="rounded-xl bg-green-100 hover:bg-green-200 py-2">😌</button>
                        <button class="rounded-xl bg-blue-100 hover:bg-blue-200 py-2">😐</button>
                        <button class="rounded-xl bg-purple-100 hover:bg-purple-200 py-2">😟</button>
                        <button class="rounded-xl bg-red-100 hover:bg-red-200 py-2">😢</button>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">Pick one to start your day 🌈</p>
                </div>

                <div class="rounded-2xl p-6 shadow bg-white border border-pink-100">
                    <h3 class="text-lg font-bold text-pink-700">📌 Quick Links</h3>
                    <div class="mt-4 space-y-3">
                        <a href="#" class="block rounded-xl bg-pink-50 hover:bg-pink-100 px-4 py-3 font-semibold text-pink-700">
                            🧩 My Goals
                        </a>
                        <a href="#" class="block rounded-xl bg-blue-50 hover:bg-blue-100 px-4 py-3 font-semibold text-blue-700">
                            👨‍👩‍👧 Trusted People
                        </a>
                        <a href="#" class="block rounded-xl bg-yellow-50 hover:bg-yellow-100 px-4 py-3 font-semibold text-yellow-700">
                            📅 My Week
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl p-6 shadow bg-white border border-yellow-100">
                    <h3 class="text-lg font-bold text-yellow-700">🆘 Need Help?</h3>
                    <p class="text-gray-600 mt-2">
                        If you feel unsafe or worried, press the button.
                    </p>

                    <button class="mt-4 w-full rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold py-3 shadow">
                        I need support now
                    </button>

                    <p class="text-xs text-gray-500 mt-3">
                        This can alert a trusted adult (later feature).
                    </p>
                </div>
            </div>

            {{-- Diary Section --}}
            <div class="rounded-3xl p-8 shadow-lg bg-white border border-indigo-100">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-2xl font-extrabold text-indigo-700">📖 My Diary</h3>
                        <p class="text-gray-600 mt-1">Write anything you want — this is your space.</p>
                    </div>
                    <span class="text-sm px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                        New Entry
                    </span>
                </div>

                <form method="POST" action="#" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block font-semibold text-gray-700 mb-2">Title</label>
                        <input type="text"
                               name="title"
                               placeholder="e.g. Today was a good day!"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-2">What happened today?</label>
                        <textarea name="content"
                                  rows="5"
                                  placeholder="Write here..."
                                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Mood</label>
                            <select name="mood"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="happy">😊 Happy</option>
                                <option value="calm">😌 Calm</option>
                                <option value="okay">😐 Okay</option>
                                <option value="worried">😟 Worried</option>
                                <option value="sad">😢 Sad</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Private?</label>
                            <div class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3">
                                <input type="checkbox" name="private" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-gray-600">Keep this entry private (later feature)</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 shadow">
                        Save Diary Entry ✨
                    </button>
                </form>
            </div>

            {{-- Fun Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl p-6 shadow bg-gradient-to-br from-green-50 to-blue-50 border border-green-100">
                    <h3 class="text-lg font-bold text-green-700">🎯 My Goal This Week</h3>
                    <p class="text-gray-700 mt-2">Pick one small thing to work on.</p>
                    <ul class="mt-4 space-y-2 text-gray-700">
                        <li>✅ Sleep on time</li>
                        <li>✅ Talk to someone I trust</li>
                        <li>✅ Do something fun</li>
                    </ul>
                </div>

                <div class="rounded-2xl p-6 shadow bg-gradient-to-br from-yellow-50 to-pink-50 border border-yellow-100">
                    <h3 class="text-lg font-bold text-pink-700">🌈 Something Positive</h3>
                    <p class="text-gray-700 mt-2">
                        “You don’t have to do everything. Just one small step.”
                    </p>
                    <div class="mt-4 text-3xl">💛✨</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
