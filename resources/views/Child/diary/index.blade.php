<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My diary 📖</h1>
            <p class="text-sm text-gray-500 mt-0.5">Your private space</p>
        </div>
        <a href="{{ route('child.dashboard') }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition">
            ← Back
        </a>
    </div>
</x-slot>

@php
    $moodEmoji = ['happy'=>'😊','calm'=>'😌','okay'=>'😐','worried'=>'😟','sad'=>'😢'];
    $moodColor = [
        'happy'   => ['bg' => 'bg-yellow-50',  'border' => 'border-yellow-200', 'text' => 'text-yellow-700',  'dot' => 'bg-yellow-400'],
        'calm'    => ['bg' => 'bg-green-50',   'border' => 'border-green-200',  'text' => 'text-green-700',   'dot' => 'bg-green-400'],
        'okay'    => ['bg' => 'bg-blue-50',    'border' => 'border-blue-200',   'text' => 'text-blue-700',    'dot' => 'bg-blue-400'],
        'worried' => ['bg' => 'bg-purple-50',  'border' => 'border-purple-200', 'text' => 'text-purple-700',  'dot' => 'bg-purple-400'],
        'sad'     => ['bg' => 'bg-red-50',     'border' => 'border-red-200',    'text' => 'text-red-700',     'dot' => 'bg-red-400'],
    ];
@endphp

{{-- ── Stats strip ─────────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-gray-900">{{ $entries->total() }}</p>
        <p class="text-xs text-gray-400 mt-0.5">Total entries</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
        @php
            $streak = 0;
            $day = now()->startOfDay();
            while ($entries->getCollection()->first(fn($e) => \Carbon\Carbon::parse($e->created_at)->isSameDay($day))) {
                $streak++;
                $day = $day->subDay();
            }
        @endphp
        <p class="text-2xl font-bold text-gray-900">{{ $streak }}</p>
        <p class="text-xs text-gray-400 mt-0.5">Day streak 🔥</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
        @php
            $topMood = $entries->getCollection()->groupBy('mood')->sortByDesc(fn($g) => $g->count())->keys()->first();
        @endphp
        <p class="text-2xl">{{ $moodEmoji[$topMood] ?? '📝' }}</p>
        <p class="text-xs text-gray-400 mt-0.5">Most logged mood</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── LEFT: New entry form ── --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden sticky top-4">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">New entry</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>

            @if(session('success'))
                <div class="mx-5 mt-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-800">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mx-5 mt-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('child.diary.store') }}" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="e.g. Today was a good day!"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">How are you feeling?</label>
                    <div class="flex gap-2 mb-3">
                        @foreach(['happy'=>'😊','calm'=>'😌','okay'=>'😐','worried'=>'😟','sad'=>'😢'] as $m => $e)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="mood" value="{{ $m }}"
                                   {{ old('mood', 'okay') === $m ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="block text-center py-2 rounded-xl border border-gray-100 text-xl
                                         peer-checked:border-indigo-400 peer-checked:bg-indigo-50 hover:bg-gray-50 transition">
                                {{ $e }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Write here</label>
                    <textarea name="content" rows="6"
                              placeholder="Write anything you want — this is just for you..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('content') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl py-2.5 transition">
                    Save entry 
                </button>
            </form>
        </div>
    </div>

    {{-- ── RIGHT: Journal entries ── --}}
    <div class="lg:col-span-2 space-y-5">

        @if($entries->isEmpty())
            <div class="bg-white border border-gray-100 rounded-xl px-6 py-16 text-center">
                <p class="text-4xl mb-3">📖</p>
                <p class="text-base font-semibold text-gray-700">No entries yet</p>
                <p class="text-sm text-gray-400 mt-1">Write your first diary entry using the form.</p>
            </div>
        @else

        {{-- Group entries by month ─────────────────────────────────── --}}
        @php
            $grouped = $entries->getCollection()->groupBy(fn($e) =>
                \Carbon\Carbon::parse($e->created_at)->format('F Y')
            );
        @endphp

        @foreach($grouped as $month => $monthEntries)
        <div>
            {{-- Month heading --}}
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">{{ $month }}</span>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-300">{{ $monthEntries->count() }} {{ Str::plural('entry', $monthEntries->count()) }}</span>
            </div>

            {{-- Entries for this month --}}
            <div class="space-y-3">
                @foreach($monthEntries as $entry)
                @php
                    $mood  = $entry->mood ?? 'okay';
                    $mc    = $moodColor[$mood] ?? $moodColor['okay'];
                    $date  = \Carbon\Carbon::parse($entry->created_at);
                    $isToday     = $date->isToday();
                    $isYesterday = $date->isYesterday();
                    $dateLabel   = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $date->format('l, d M Y'));
                @endphp
                <div x-data="{ expanded: false }"
                     class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:border-indigo-100 transition">

                    {{-- Entry header --}}
                    <button type="button" @click="expanded = !expanded"
                            class="w-full flex items-start gap-4 px-5 py-4 text-left">

                        {{-- Date column --}}
                        <div class="shrink-0 text-center w-12">
                            <p class="text-xl font-bold text-gray-900 leading-none">{{ $date->format('d') }}</p>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase">{{ $date->format('M') }}</p>
                        </div>

                        {{-- Vertical line --}}
                        <div class="shrink-0 flex flex-col items-center pt-1">
                            <span class="w-2.5 h-2.5 rounded-full {{ $mc['dot'] }}"></span>
                            <div class="w-px flex-1 bg-gray-100 mt-1 min-h-[20px]"></div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $entry->title ?: 'Untitled entry' }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs {{ $mc['text'] }}">
                                            {{ $moodEmoji[$mood] ?? '📝' }} {{ ucfirst($mood) }}
                                        </span>
                                        <span class="text-gray-200">·</span>
                                        <span class="text-xs text-gray-400">{{ $dateLabel }}</span>
                                        @if($isToday)
                                            <span class="text-[10px] font-medium bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded-full">Today</span>
                                        @endif
                                    </div>
                                    {{-- Preview line when collapsed --}}
                                    <p x-show="!expanded"
                                       class="text-xs text-gray-400 mt-1.5 line-clamp-1">
                                        {{ $entry->content ? Str::limit(strip_tags($entry->content), 80) : 'No content.' }}
                                    </p>
                                </div>
                                {{-- Expand chevron --}}
                                <svg class="w-4 h-4 text-gray-300 shrink-0 mt-0.5 transition-transform duration-200"
                                     :class="{ 'rotate-180': expanded }"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Expanded content --}}
                    <div x-show="expanded" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-5 pb-5 ml-16">
                        <div class="{{ $mc['bg'] }} {{ $mc['border'] }} border rounded-xl px-4 py-4">
                            @if($entry->content)
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $entry->content }}</p>
                            @else
                                <p class="text-sm text-gray-400 italic">No content written.</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xs text-gray-400">
                                Written at {{ $date->format('g:i A') }}
                            </p>
                            <form method="POST"
                                action="{{ route('child.diary.destroy', $entry) }}"
                                  onsubmit="return confirm('Delete this entry?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-gray-400 hover:text-red-500 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        @if($entries->hasPages())
        <div class="pt-2">
            {{ $entries->links() }}
        </div>
        @endif

        @endif
    </div>
</div>

</x-app-layout>
