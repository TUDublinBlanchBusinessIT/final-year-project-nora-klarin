<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center text-white font-extrabold shadow">
                    {{ strtoupper(substr($selectedUser->name ?? 'M', 0, 1)) }}
                </div>

                <div class="leading-tight">
                    <div class="font-extrabold">
                        {{ $selectedUser ? 'Chat with ' . $selectedUser->name : 'Messages' }}
                    </div>
                </div>
            </div>

            <a href="{{ route('child.dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="theme-page min-h-[85vh]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex h-[80vh] gap-4">
                <!-- Conversations Sidebar -->
                <div class="w-1/3">
                    <div class="theme-card rounded-[28px] shadow-2xl p-4 h-full overflow-y-auto">
                        <h3 class="font-bold mb-4 text-lg">Conversations</h3>
                        @forelse($conversations as $conv)
                            <a href="{{ route('child.messages.index', ['with' => $conv->id]) }}" 
                               class="block p-3 mb-2 rounded-lg transition {{ $selectedUser && $selectedUser->id == $conv->id ? 'bg-indigo-100 border border-indigo-200' : 'hover:bg-gray-100' }}">
                                <div class="font-semibold">{{ $conv->name }}</div>
                                <div class="text-sm text-gray-600 truncate">{{ $conv->last_body }}</div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500">{{ $conv->last_at ? $conv->last_at->diffForHumans() : '' }}</span>
                                    @if($conv->unread_count > 0)
                                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $conv->unread_count }}</span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                No conversations yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="w-2/3">
                    <div class="theme-card relative overflow-hidden rounded-[28px] shadow-2xl h-full flex flex-col">

                        <div class="h-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600"></div>

                        <div class="absolute inset-0 opacity-[0.08] pointer-events-none"
                             style="background-image: radial-gradient(circle at 1px 1px, #6366f1 1px, transparent 0);
                                    background-size: 18px 18px;">
                        </div>

                        <div class="relative flex flex-col h-full">

                    <div id="chatBox" class="flex-1 overflow-y-auto px-5 sm:px-8 py-6 space-y-4">
                        @if($messages->isEmpty())
                            <div class="text-center py-14">
                                @if($selectedUser)
                                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-pink-600 text-white text-3xl shadow">
                                        💬
                                    </div>
                                    <div class="mt-4 text-xl font-extrabold">
                                        Start your chat
                                    </div>
                                    <div class="mt-1 text-sm opacity-70">
                                        Send a message to {{ $selectedUser->name }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-gray-400 to-gray-500 text-white text-3xl shadow">
                                        📭
                                    </div>
                                    <div class="mt-4 text-xl font-extrabold">
                                        No conversation selected
                                    </div>
                                    <div class="mt-1 text-sm opacity-70">
                                        Select a conversation from the sidebar
                                    </div>
                                @endif
                            </div>
                        @endif

                        @php $lastDate = null; @endphp

                        @foreach($messages as $msg)
                            @php
                                $mine = $msg->sender_id === auth()->id();
                                $date = $msg->created_at->format('Y-m-d');
                                $prettyDate = $msg->created_at->isToday()
                                    ? 'Today'
                                    : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('D, j M'));
                            @endphp

                            @if($lastDate !== $date)
                                <div class="flex items-center justify-center py-2">
                                    <span class="theme-card text-xs font-bold px-4 py-2 rounded-full shadow-sm">
                                        {{ $prettyDate }}
                                    </span>
                                </div>
                                @php $lastDate = $date; @endphp
                            @endif

                            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[80%] sm:max-w-[60%]">
                                    <div class="relative px-3.5 py-2.5 rounded-[18px] shadow
                                        {{ $mine
                                            ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white'
                                            : 'theme-card' }}"
                                    >
                                        <div class="whitespace-pre-line text-[14px] leading-relaxed">
                                            {{ $msg->body }}
                                        </div>

                                        <div class="mt-1.5 flex items-center justify-end gap-2 text-[10px]
                                            {{ $mine ? 'text-white/70' : 'opacity-60' }}"
                                        >
                                            <span>{{ $msg->created_at->format('H:i') }}</span>

                                            @if($mine)
                                                @if(is_null($msg->read_at))
                                                    <span class="text-white/70">✓</span>
                                                @else
                                                    <span class="text-white/90">✓✓</span>
                                                @endif
                                            @endif
                                        </div>

                                        <span class="absolute bottom-2 h-3 w-3 rotate-45
                                            {{ $mine ? '-right-1.5 bg-purple-600' : '-left-1.5 theme-card' }}">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($selectedUser)
                        <div class="border-t bg-white/85 backdrop-blur px-4 sm:px-8 py-4">
                            <div class="px-6 sm:px-8 pb-2">
                                <div class="text-xs opacity-60">
                                    Replies may not be instant
                                </div>
                            </div>

                            <form method="POST" action="{{ route('child.messages.store') }}" class="flex items-center gap-3">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">

                                <div class="flex-1 relative">
                                    <input
                                        id="msgInput"
                                        type="text"
                                        name="body"
                                        placeholder="Type a message…"
                                        class="theme-input w-full rounded-2xl focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 pr-12 shadow-sm"
                                        required
                                        autocomplete="off"
                                    />
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 opacity-50 select-none">😊</span>
                                </div>

                                <button
                                    type="submit"
                                    class="rounded-2xl px-6 py-3 font-semibold bg-indigo-600 text-white shadow-sm
                                           hover:bg-indigo-700 active:scale-[0.98] transition
                                           focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                >
                                    Send ➤
                                </button>
                            </form>

                            @if ($errors->any())
                                <div class="mt-3 text-sm text-red-600 font-semibold">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const msgInput = document.getElementById('msgInput');

        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        if (msgInput) msgInput.focus();
    </script>
</x-app-layout>