<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
                <p class="text-sm text-gray-500">Chat with your team</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('carer.dashboard') }}"
                   class="px-4 py-2 rounded-lg bg-gray-100 text-sm hover:bg-gray-200">
                    Back
                </a>

                <a href="{{ route('carer.messages.create') }}"
                   class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                    New Message
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border">
                <div class="grid grid-cols-1 md:grid-cols-3 min-h-[640px]">

                    <!-- LEFT: Conversations -->
                    <div class="border-b md:border-b-0 md:border-r">
                        <div class="p-4">
                            <div class="text-sm font-semibold text-gray-800">Conversations</div>
                            <div class="text-xs text-gray-500 mt-1">Select a person to view the chat</div>
                        </div>

                        @if($conversations->isEmpty())
                            <div class="p-6 text-sm text-gray-600">
                                No conversations yet.<br>
                                Click <span class="font-semibold">New Message</span> to start one.
                            </div>
                        @else
                            <div class="max-h-[560px] overflow-auto">
                                @foreach($conversations as $c)
                                    @php
                                        $isActive = $selectedUser && $selectedUser->id === $c->id;
                                        $initials = collect(explode(' ', trim($c->name)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                            ->join('');
                                        $preview = $c->last_body ? \Illuminate\Support\Str::limit($c->last_body, 42) : 'No messages yet';
                                        $time = $c->last_at ? \Carbon\Carbon::parse($c->last_at)->format('D H:i') : '';
                                    @endphp

                                    <a href="{{ route('carer.messages.index', ['with' => $c->id]) }}"
                                       class="block px-4 py-3 border-t hover:bg-gray-50 {{ $isActive ? 'bg-gray-50' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <!-- Avatar -->
                                            <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-semibold shrink-0">
                                                {{ $initials ?: 'U' }}
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="font-medium text-gray-900 truncate">{{ $c->name }}</div>

                                                    <div class="flex items-center gap-2 shrink-0">
                                                        @if($time)
                                                            <span class="text-[11px] text-gray-500">{{ $time }}</span>
                                                        @endif

                                                        @if(($c->unread_count ?? 0) > 0)
                                                            <span class="text-[11px] bg-gray-900 text-white rounded-full px-2 py-0.5">
                                                                {{ $c->unread_count }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="text-xs text-gray-500 truncate">
                                                    {{ $c->role }} • {{ $preview }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- RIGHT: Chat -->
                    <div class="md:col-span-2 flex flex-col">
                        @if(session('status'))
                            <div class="m-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(!$selectedUser)
                            <div class="flex-1 flex items-center justify-center p-10 text-center">
                                <div>
                                    <div class="text-lg font-semibold text-gray-800">Pick a conversation</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Choose someone on the left to start chatting.
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

                            <!-- Chat header -->
                            <div class="border-b p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-semibold">
                                        {{ $initials ?: 'U' }}
                                    </div>

                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $selectedUser->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $selectedUser->role }} • {{ $selectedUser->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages area -->
                            <div id="chatScroll"
                                 class="flex-1 p-4 overflow-auto bg-gray-50 space-y-3">
                                @forelse($messages as $m)
                                    @php $isMe = $m->sender_id === auth()->id(); @endphp

                                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[78%] rounded-2xl px-4 py-2 shadow-sm
                                            {{ $isMe ? 'bg-gray-900 text-white' : 'bg-white text-gray-900 border' }}">
                                            <div class="text-sm whitespace-pre-wrap">{{ $m->body }}</div>

                                            <div class="text-[11px] mt-1 opacity-70 {{ $isMe ? 'text-gray-200' : 'text-gray-500' }}">
                                                {{ $m->created_at->format('D H:i') }}
                                                @if($isMe && $m->read_at)
                                                    • Seen
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-600">
                                        No messages in this chat yet — send one below.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Composer -->
                            <div class="border-t p-4 bg-white">
                                <form method="POST" action="{{ route('carer.messages.store') }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">

                                    <input
                                        name="body"
                                        class="flex-1 rounded-lg border-gray-300 focus:border-gray-400 focus:ring-gray-400"
                                        placeholder="Type a message…"
                                        autocomplete="off"
                                    />

                                    <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                        Send
                                    </button>
                                </form>

                                @error('body')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Auto-scroll to bottom -->
                            <script>
                                window.addEventListener('load', () => {
                                    const el = document.getElementById('chatScroll');
                                    if (el) el.scrollTop = el.scrollHeight;
                                });
                            </script>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
