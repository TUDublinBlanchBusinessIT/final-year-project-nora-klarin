<x-app-layout>

@php
    $isSW       = request()->routeIs('socialworker.*');
    $indexRoute = $isSW ? 'socialworker.messages.index'  : 'carer.messages.index';
    $createRoute= $isSW ? 'socialworker.messages.create' : 'carer.messages.create';
    $storeRoute = $isSW ? 'socialworker.messages.store'  : 'carer.messages.store';
    $backRoute  = $isSW ? 'socialworker.dashboard'       : 'carer.dashboard';
@endphp

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Messages</h1>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route($backRoute) }}"
               class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition">
                ← Back
            </a>
            <a href="{{ route($createRoute) }}"
               class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                + New message
            </a>
        </div>
    </div>
</x-slot>

{{-- Stat bar --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
    <div class="bg-white border border-gray-200 rounded-[14px] p-4">
        <p class="text-xs font-medium text-gray-400 mb-1">Conversations</p>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $conversations->count() }}</p>
    </div>
    <div class="bg-white border border-indigo-200 rounded-[14px] p-4">
        <p class="text-xs font-medium text-indigo-400 mb-1">Unread</p>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">
            {{ $conversations->sum(fn($c) => $c->unread_count ?? 0) }}
        </p>
    </div>
</div>

{{-- Main chat layout --}}
<div class="bg-white border border-gray-200 rounded-[14px] overflow-hidden flex" style="min-height: 600px;">

    {{-- Left: conversation list --}}
    <aside class="w-80 shrink-0 border-r border-gray-100 flex flex-col">
        <div class="px-4 py-3.5 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-700 mb-2.5">Conversations</p>
            <input id="convSearch" type="text"
                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 placeholder-gray-400"
                   placeholder="Search…">
        </div>

        @if($conversations->isEmpty())
            <div class="flex-1 flex items-center justify-center p-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">No conversations yet</p>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Start a new chat to get going</p>
                    <a href="{{ route($createRoute) }}"
                       class="inline-flex bg-indigo-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                        Start chat
                    </a>
                </div>
            </div>
        @else
            <div id="convList" class="flex-1 overflow-y-auto divide-y divide-gray-100">
                @foreach($conversations as $c)
                    @php
                        $isActive = $selectedUser && $selectedUser->id === $c->id;
                        $initials = collect(explode(' ', trim($c->name)))->filter()->take(2)
                            ->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
                        $preview = $c->last_body ? \Illuminate\Support\Str::limit($c->last_body, 45) : 'No messages yet';
                        $time = $c->last_at ? \Carbon\Carbon::parse($c->last_at)->format('D H:i') : '';
                    @endphp
                    <a href="{{ route($indexRoute, ['with' => $c->id]) }}"
                       data-name="{{ strtolower($c->name . ' ' . $c->email) }}"
                       class="flex items-start gap-3 px-4 py-3.5 hover:bg-slate-50 transition {{ $isActive ? 'bg-indigo-50 border-l-2 border-indigo-500' : '' }}">

                        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-white flex items-center justify-center text-sm font-bold shrink-0">
                            {{ $initials ?: 'U' }}
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $c->name }}</p>
                                <p class="text-[11px] text-gray-400 shrink-0">{{ $time }}</p>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs text-gray-500 truncate">{{ $preview }}</p>
                                @if(($c->unread_count ?? 0) > 0)
                                    <span class="inline-flex min-w-5 h-5 px-1.5 items-center justify-center rounded-full bg-indigo-600 text-white text-[10px] font-bold shrink-0">
                                        {{ $c->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </aside>

    {{-- Right: message thread --}}
    <main class="flex-1 flex flex-col min-w-0">
        @if(!$selectedUser)
            <div class="flex-1 flex items-center justify-center p-10">
                <div class="text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                        </svg>
                    </div>
                    <p class="text-base font-semibold text-gray-700">Select a conversation</p>
                    <p class="text-sm text-gray-400 mt-1">Choose a chat from the list on the left.</p>
                </div>
            </div>
        @else
            @php
                $initials = collect(explode(' ', trim($selectedUser->name)))->filter()->take(2)
                    ->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
            @endphp

            {{-- Chat header --}}
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-white flex items-center justify-center text-sm font-bold">
                        {{ $initials ?: 'U' }}
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $selectedUser->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $selectedUser->role)) }} · Available</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium ring-1 ring-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Secure
                </span>
            </div>

            {{-- Messages --}}
            <div id="chatScroll" class="flex-1 overflow-y-auto px-5 py-5 space-y-3">
                @php $lastLabel = null; @endphp
                @forelse($messages as $m)
                    @php
                        $isMe = $m->sender_id === auth()->id();
                        $label = $m->created_at->isToday() ? 'Today' : ($m->created_at->isYesterday() ? 'Yesterday' : $m->created_at->format('d M Y'));
                    @endphp

                    @if($label !== $lastLabel)
                        <div class="flex items-center gap-3 py-1">
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <span class="text-[11px] text-gray-400 shrink-0">{{ $label }}</span>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        @php $lastLabel = $label; @endphp
                    @endif

                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[72%] {{ $isMe
                            ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm'
                            : 'bg-slate-100 text-gray-900 rounded-2xl rounded-bl-sm' }} px-4 py-2.5">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $m->body }}</p>
                            <p class="text-[11px] mt-1.5 {{ $isMe ? 'text-indigo-200' : 'text-gray-400' }}">
                                {{ $m->created_at->format($m->created_at->isToday() ? 'H:i' : 'D H:i') }}
                                @if($isMe && $m->read_at) · Seen @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full py-16">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600">No messages yet</p>
                            <p class="text-xs text-gray-400 mt-1">Start the conversation below.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Composer --}}
            <div class="px-4 py-3.5 border-t border-gray-100 shrink-0">
                {{-- Quick chips --}}
                <div class="flex flex-wrap gap-1.5 mb-2.5">
                    @foreach(['Thanks!', 'Noted ✅', 'Can we reschedule?', 'I\'ll confirm soon'] as $chip)
                        <button type="button"
                                class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium hover:bg-slate-200 transition"
                                onclick="document.getElementById('composerBody').value='{{ $chip }}'; document.getElementById('composerBody').focus();">
                            {{ $chip }}
                        </button>
                    @endforeach
                </div>

                <form id="composerForm" method="POST" action="{{ route($storeRoute) }}" class="flex items-end gap-2">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">
                    <textarea id="composerBody" name="body" rows="2"
                              class="flex-1 border border-gray-200 rounded-xl px-3.5 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-300 placeholder-gray-400"
                              placeholder="Write a message…"></textarea>
                    <button type="submit"
                            class="bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition shrink-0">
                        Send
                    </button>
                </form>

                @error('body')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </main>
</div>

<script>
    window.addEventListener('load', () => {
        const el = document.getElementById('chatScroll');
        if (el) el.scrollTop = el.scrollHeight;
    });

    const search = document.getElementById('convSearch');
    const list = document.getElementById('convList');
    if (search && list) {
        search.addEventListener('input', () => {
            const q = search.value.toLowerCase();
            list.querySelectorAll('[data-name]').forEach(a => {
                a.style.display = a.getAttribute('data-name').includes(q) ? '' : 'none';
            });
        });
    }

    const body = document.getElementById('composerBody');
    const form = document.getElementById('composerForm');
    if (body && form) {
        body.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (body.value.trim()) form.submit();
            }
        });
        // Auto-resize
        body.addEventListener('input', () => {
            body.style.height = 'auto';
            body.style.height = Math.min(body.scrollHeight, 120) + 'px';
        });
    }
</script>

</x-app-layout>
