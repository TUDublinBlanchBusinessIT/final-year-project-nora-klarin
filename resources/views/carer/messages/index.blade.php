<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
                <p class="text-sm text-gray-500">Secure chat with your social worker</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('carer.dashboard') }}"
                   class="px-4 py-2 rounded-xl bg-gray-100 text-sm hover:bg-gray-200">
                    Back
                </a>

                <a href="{{ route('carer.messages.create') }}"
                   class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">
                    New Message
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 min-h-[680px]">

                    <!-- LEFT: Conversations -->
                    <aside class="md:col-span-4 border-b md:border-b-0 md:border-r">
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Conversations</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Select to open chat</div>
                                </div>

                                <span class="text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    Inbox
                                </span>
                            </div>

                            <div class="mt-3">
                                <input id="convSearch" type="text"
                                       class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-400 focus:ring-indigo-400"
                                       placeholder="Search…">
                            </div>
                        </div>

                        @if($conversations->isEmpty())
                            <div class="p-6">
                                <div class="rounded-2xl border border-dashed p-6 text-center">
                                    <div class="text-sm font-semibold text-gray-900">No conversations yet</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Start a chat with a social worker.
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('carer.messages.create') }}"
                                           class="inline-block px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">
                                            Start chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div id="convList" class="max-h-[590px] overflow-auto">
                                @foreach($conversations as $c)
                                    @php
                                        $isActive = $selectedUser && $selectedUser->id === $c->id;

                                        $initials = collect(explode(' ', trim($c->name)))
                                            ->filter()->take(2)
                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                            ->join('');

                                        $preview = $c->last_body
                                            ? \Illuminate\Support\Str::limit($c->last_body, 44)
                                            : 'No messages yet';

                                        $time = $c->last_at
                                            ? \Carbon\Carbon::parse($c->last_at)->format('D H:i')
                                            : '';

                                        $isOnline = true;
                                    @endphp

                                    <a href="{{ route('carer.messages.index', ['with' => $c->id]) }}"
                                       data-name="{{ strtolower($c->name . ' ' . $c->email) }}"
                                       class="block px-4 py-3 border-t hover:bg-gray-50 transition
                                       {{ $isActive ? 'bg-indigo-50/60' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <!-- Avatar -->
                                            <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-500 text-white flex items-center justify-center text-sm font-semibold shrink-0 shadow-sm">
                                                {{ $initials ?: 'U' }}
                                                @if($isOnline)
                                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $c->name }}</div>
                                                        <div class="text-xs text-gray-600 truncate mt-0.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                {{ $c->role }}
                                                            </span>
                                                            <span class="ml-1">•</span>
                                                            <span class="ml-1 text-gray-500">{{ $preview }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="shrink-0 text-right">
                                                        <div class="text-[11px] text-gray-500 whitespace-nowrap">{{ $time }}</div>

                                                        @if(($c->unread_count ?? 0) > 0)
                                                            <div class="mt-1 inline-flex items-center justify-center min-w-6 h-6 px-2 rounded-full bg-indigo-600 text-white text-[11px] shadow-sm">
                                                                {{ $c->unread_count }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </aside>

                    <!-- RIGHT: Chat -->
                    <main class="md:col-span-8 flex flex-col">
                        @if(session('status'))
                            <div class="m-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(!$selectedUser)
                            <div class="flex-1 flex items-center justify-center p-10 text-center">
                                <div class="max-w-md">
                                    <div class="text-lg font-semibold text-gray-900">Choose a conversation</div>
                                    <div class="text-sm text-gray-600 mt-2">
                                        Pick a social worker on the left to view the chat.
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $initials = collect(explode(' ', trim($selectedUser->name)))
                                    ->filter()->take(2)
                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                    ->join('');
                            @endphp

                            <!-- Chat header (colour) -->
                            <div class="border-b p-4 flex items-center justify-between bg-gradient-to-r from-indigo-600 to-sky-500 text-white">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-11 h-11 rounded-2xl bg-white/15 text-white flex items-center justify-center text-sm font-semibold">
                                        {{ $initials ?: 'U' }}
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div>
                                        <div class="font-semibold">{{ $selectedUser->name }}</div>
                                        <div class="text-xs text-white/80">
                                            {{ $selectedUser->role }} • {{ $selectedUser->email }}
                                        </div>
                                    </div>
                                </div>

                                <span class="text-xs bg-white/15 px-3 py-1 rounded-full">
                                    Encrypted
                                </span>
                            </div>

                            <!-- Messages -->
                            <div id="chatScroll" class="flex-1 p-5 overflow-auto bg-gradient-to-b from-indigo-50/40 to-white space-y-3">
                                @php $lastLabel = null; @endphp

                                @forelse($messages as $m)
                                    @php
                                        $isMe = $m->sender_id === auth()->id();
                                        $label = $m->created_at->isToday() ? 'Today' : 'Earlier';
                                    @endphp

                                    @if($label !== $lastLabel)
                                        <div class="flex justify-center py-2">
                                            <span class="text-xs px-3 py-1 rounded-full bg-white/80 border text-gray-600 shadow-sm">
                                                {{ $label }}
                                            </span>
                                        </div>
                                        @php $lastLabel = $label; @endphp
                                    @endif

                                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[72%] rounded-2xl px-4 py-3 shadow-sm
                                            {{ $isMe
                                                ? 'bg-gradient-to-r from-indigo-600 to-sky-500 text-white'
                                                : 'bg-white text-gray-900 border border-indigo-100' }}">
                                            <div class="text-sm leading-relaxed whitespace-pre-wrap">{{ $m->body }}</div>

                                            <div class="text-[11px] mt-2 {{ $isMe ? 'text-indigo-100' : 'text-gray-500' }}">
                                                {{ $m->created_at->format($m->created_at->isToday() ? 'H:i' : 'D H:i') }}
                                                @if($isMe && $m->read_at)
                                                    • Seen
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-600">
                                        No messages here yet — send one below.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Composer -->
                            <div class="border-t bg-white p-4">
                                <form id="composerForm" method="POST" action="{{ route('carer.messages.store') }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">

                                    <textarea id="composerBody" name="body" rows="1"
                                              class="flex-1 resize-none rounded-xl border-gray-300 focus:border-indigo-400 focus:ring-indigo-400"
                                              placeholder="Type a message… (Enter to send, Shift+Enter new line)"></textarea>

                                    <button type="submit"
                                            class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">
                                        Send
                                    </button>
                                </form>

                                <!-- Quick replies -->
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach(['Thanks!', 'Noted ✅', 'Can we reschedule?', 'I’ll confirm soon'] as $chip)
                                        <button type="button"
                                                class="text-xs px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100"
                                                onclick="document.getElementById('composerBody').value = '{{ $chip }}'; document.getElementById('composerBody').focus();">
                                            {{ $chip }}
                                        </button>
                                    @endforeach
                                </div>

                                @error('body')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Scripts: search + autosend + autoscroll -->
                            <script>
                                // Auto-scroll to bottom
                                window.addEventListener('load', () => {
                                    const el = document.getElementById('chatScroll');
                                    if (el) el.scrollTop = el.scrollHeight;
                                });

                                // Filter conversations
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

                                // Enter-to-send (Shift+Enter for newline)
                                const body = document.getElementById('composerBody');
                                const form = document.getElementById('composerForm');
                                if (body && form) {
                                    body.addEventListener('keydown', (e) => {
                                        if (e.key === 'Enter' && !e.shiftKey) {
                                            e.preventDefault();
                                            if (body.value.trim().length > 0) form.submit();
                                        }
                                    });
                                }
                            </script>
                        @endif
                    </main>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
