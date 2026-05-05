<x-app-layout>

@php
    $theme  = auth()->user()->theme ?? 'calm';
    $layout = auth()->user()->dashboard_layout ?? 'standard';
@endphp

{{-- ── Notification bell (matches SW + carer pattern) ── --}}
<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            @php
                $hour = now()->hour;
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
            @endphp
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $greeting }}, {{ Auth::user()->name ?? 'there' }} 👋
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, jS F Y') }}</p>
        </div>

        {{-- Bell — same structure as SW/carer --}}
        <div x-data="{ open: false }" class="relative shrink-0">
            <button @click="open = !open" @click.outside="open = false"
                    class="relative p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @php $totalBell = ($unreadMessageCount ?? 0) + ($reminderCount ?? 0); @endphp
                @if($totalBell > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-indigo-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none">
                        {{ $totalBell > 9 ? '9+' : $totalBell }}
                    </span>
                @endif
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

                <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Notifications</p>
                </div>

                <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                    @if(($unreadMessageCount ?? 0) > 0)
                        <a href="{{ route('child.messages.index') }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="shrink-0 mt-0.5" style="font-size:15px">💬</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800">{{ $unreadMessageCount }} unread {{ $unreadMessageCount === 1 ? 'message' : 'messages' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Tap to open messages</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0 mt-2"></span>
                        </a>
                    @endif
                    @if(($reminderCount ?? 0) > 0)
                        <a href="#diary"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            <span class="shrink-0 mt-0.5" style="font-size:15px">📖</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800">Diary reminder</p>
                                <p class="text-xs text-gray-400 mt-0.5">You haven't written today</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0 mt-2"></span>
                        </a>
                    @endif
                    @if($totalBell === 0)
                        <div class="px-4 py-8 text-center text-sm text-gray-400">All caught up! 🎉</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-slot>

{{-- ── Today's mood strip ──────────────────────────────────────── --}}
<div class="bg-white border border-gray-100 rounded-xl px-5 py-4 mb-5 flex items-center gap-4 flex-wrap">
    <div class="shrink-0">
        <p class="text-sm font-semibold text-gray-900">Today I feel...</p>
        <p class="text-xs text-gray-400 mt-0.5">Tap a mood</p>
    </div>
    <div class="flex gap-2 flex-1 justify-end flex-wrap">
        @foreach(['happy' => '😊', 'calm' => '😌', 'okay' => '😐', 'worried' => '😟', 'sad' => '😢'] as $mood => $emoji)
            <a href="{{ route('child.mood.save', $mood) }}"
               class="w-11 h-11 flex items-center justify-center rounded-xl text-xl hover:scale-110 transition-transform
                      {{ isset($todayMood) && $todayMood === $mood ? 'ring-2 ring-indigo-400 bg-indigo-50' : 'bg-gray-50 hover:bg-gray-100' }}">
                {{ $emoji }}
            </a>
        @endforeach
    </div>
</div>

{{-- ── Main grid ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── LEFT: Wellbeing check + Quick links ── --}}
    <div class="space-y-5">

        {{-- Wellbeing check card --}}
        <div class="bg-white border border-indigo-100 rounded-xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Check in</p>
                    <p class="text-xs text-gray-400 mt-0.5">How are you feeling this week?</p>
                </div>
                <span class="text-2xl">🩷</span>
            </div>
            <a href="{{ route('child.wellbeing.form') }}"
               class="block text-center w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 transition">
                Start ✨
            </a>
        </div>

        {{-- Quick links --}}
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-sm font-semibold text-gray-900 mb-3">Quick links</p>
            <div class="space-y-2">
                <a href="{{ route('child.trusted') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium transition">
                    <span>👨‍👩‍👧</span> Trusted people
                </a>
                <a href="{{ route('child.week') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium transition">
                    <span>📅</span> My week
                </a>
                <a href="{{ route('child.support.map') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium transition">
                    <span>🗺️</span> Find help nearby
                </a>
                <a href="{{ route('child.messages.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition">
                    <span>💬</span> Messages
                    @if(($unreadMessageCount ?? 0) > 0)
                        <span class="ml-auto bg-indigo-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            {{ $unreadMessageCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

    </div>

    {{-- ── MIDDLE: My Journey (Goals) ── --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-900">My journey 🌱</p>
            </div>
            <a href="{{ route('child.goals') }}"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                View all →
            </a>
        </div>

        <div class="flex-1 divide-y divide-gray-50 overflow-y-auto max-h-96">
            @forelse(isset($activeGoals) ? $activeGoals->take(4) : [] as $goal)
            @php
                $tasks = isset($tasksByGoal) ? ($tasksByGoal[$goal->case_goal_id] ?? collect()) : collect();
                $done  = $tasks->filter(fn($t) => $t->completed_at !== null)->count();
                $total = $tasks->count();
                $pct   = $total > 0 ? round(($done / $total) * 100) : 0;
            @endphp
            <div class="px-5 py-3.5 flex items-center gap-3">
                {{-- Mini ring --}}
                <div class="shrink-0 w-9 h-9 relative flex items-center justify-center">
                    <svg class="w-9 h-9 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="4"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                            stroke="{{ $pct === 100 ? '#22c55e' : '#818cf8' }}" stroke-width="4"
                            stroke-dasharray="{{ $pct }},100" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-[8px] font-bold {{ $pct === 100 ? 'text-green-600' : 'text-indigo-500' }}">
                        {{ $pct }}%
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $goal->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $done }}/{{ $total }} tasks done</p>
                </div>
                @if($pct === 100)
                    <span class="text-green-500 text-lg shrink-0">✓</span>
                @endif
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-2xl mb-2">🌱</p>
                <p class="text-sm text-gray-500">No goals yet.</p>
                <p class="text-xs text-gray-400 mt-1">Your social worker will add some soon.</p>
            </div>
            @endforelse
        </div>

        @if(isset($activeGoals) && $activeGoals->count() > 4)
        <div class="px-5 py-3 border-t border-gray-100 text-center">
            <a href="{{ route('child.goals') }}" class="text-xs text-indigo-600 font-medium hover:text-indigo-800">
                + {{ $activeGoals->count() - 4 }} more goals
            </a>
        </div>
        @endif
    </div>

    {{-- ── RIGHT: Diary ── --}}
    <div id="diary" class="bg-white border border-gray-100 rounded-xl overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-900">📖 My diary</p>
                <p class="text-xs text-gray-400 mt-0.5">Your private space</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-5 mt-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-800">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="flex-1 px-5 py-4">
            <form method="POST" action="{{ route('child.diary.store') }}" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="Title — e.g. Today was good!"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <textarea name="content" rows="4"
                              placeholder="Write anything you want..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('content') }}</textarea>
                </div>
                <div>
                    <select name="mood"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="happy"   @selected(old('mood') === 'happy')>😊 Happy</option>
                        <option value="calm"    @selected(old('mood') === 'calm')>😌 Calm</option>
                        <option value="okay"    @selected(old('mood') === 'okay')>😐 Okay</option>
                        <option value="worried" @selected(old('mood') === 'worried')>😟 Worried</option>
                        <option value="sad"     @selected(old('mood') === 'sad')>😢 Sad</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl py-2.5 transition">
                    Save entry
                </button>
            </form>
        </div>

        {{-- Recent entries --}}
        @if(isset($recentEntries) && $recentEntries->count())
        <div class="border-t border-gray-100 px-5 py-3">
            <p class="text-xs font-medium text-gray-500 mb-2">Recent entries</p>
            <div class="space-y-2">
                @php $moodEmoji = ['happy'=>'😊','calm'=>'😌','okay'=>'😐','worried'=>'😟','sad'=>'😢']; @endphp
                @foreach($recentEntries->take(3) as $entry)
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span>{{ $moodEmoji[$entry->mood] ?? '📝' }}</span>
                    <span class="flex-1 truncate font-medium">{{ $entry->title ?: 'Untitled' }}</span>
                    <span class="text-gray-400 shrink-0">{{ \Carbon\Carbon::parse($entry->created_at)->format('d M') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Floating chatbot button --}}
<a href="{{ route('chatbot.index') }}"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-full
          bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white shadow-2xl
          hover:scale-105 transition-transform"
   aria-label="Open {{ auth()->user()->chatbot_name ?? 'CareHub Assistant' }}">
    <span class="text-xl">💬</span>
    <span class="text-sm font-bold hidden sm:inline">
        {{ auth()->user()->chatbot_name ?? 'Chat' }}
    </span>
</a>

</x-app-layout>
