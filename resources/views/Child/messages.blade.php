<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                {{-- Avatar --}}
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center text-white font-extrabold shadow">
                    {{ strtoupper(substr($carer->name ?? 'C', 0, 1)) }}
                </div>

                <div class="leading-tight">
                    <div class="font-extrabold text-gray-900">
                        Chat with {{ $carer->name ?? 'Your Carer' }}
                    </div>
                    <div class="text-xs text-gray-500 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                            Online
                        </span>
                        <span class="opacity-50">•</span>
                        <span class="opacity-80">Messages stay saved here</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('child.dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                ← Back
            </a>
        </div>
    </x-slot>

    {{-- FULL SCREEN CHAT AREA --}}
    <div class="min-h-[85vh] bg-gradient-to-br from-indigo-50 via-white to-pink-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            <div class="relative overflow-hidden rounded-[28px] border border-white/60 shadow-2xl bg-white/75 backdrop-blur">

                {{-- Top strip --}}
                <div class="h-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600"></div>

                {{-- Subtle background pattern --}}
                <div class="absolute inset-0 opacity-[0.08] pointer-events-none"
                     style="background-image: radial-gradient(circle at 1px 1px, #6366f1 1px, transparent 0);
                            background-size: 18px 18px;">
                </div>

                {{-- MAIN CHAT WRAPPER --}}
                <div class="relative flex flex-col h-[70vh]">

                    {{-- Messages area --}}
                    <div id="chatBox" class="flex-1 overflow-y-auto px-5 sm:px-8 py-6 space-y-4">
                        @if($messages->isEmpty())
                            <div class="text-center py-14">
                                <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-pink-600 text-white text-3xl shadow">
                                    💬
                                </div>
                                <div class="mt-4 text-xl font-extrabold text-gray-900">
                                    Start your chat
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    Send a message to {{ $carer->name ?? 'your carer' }} 👋
                                </div>
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

                            {{-- Date separator --}}
                            @if($lastDate !== $date)
                                <div class="flex items-center justify-center py-2">
                                    <span class="text-xs font-bold text-gray-500 bg-white/80 px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                                        {{ $prettyDate }}
                                    </span>
                                </div>
                                @php $lastDate = $date; @endphp
                            @endif

                            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[80%] sm:max-w-[60%]">
                                    {{-- Bubble (smaller) --}}
                                    <div class="relative px-3.5 py-2.5 rounded-[18px] shadow
                                        {{ $mine
                                            ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white'
                                            : 'bg-white text-gray-900 border border-gray-100' }}"
                                    >
                                        <div class="whitespace-pre-line text-[14px] leading-relaxed">
                                            {{ $msg->body }}
                                        </div>

                                        <div class="mt-1.5 flex items-center justify-end gap-2 text-[10px]
                                            {{ $mine ? 'text-white/70' : 'text-gray-500' }}"
                                        >
                                            <span>{{ $msg->created_at->format('H:i') }}</span>

                                            @if($mine)
                                                <span class="text-white/70">✓✓</span>
                                            @endif
                                        </div>

                                        {{-- Bubble tail (smaller) --}}
                                        <span class="absolute bottom-2 h-3 w-3 rotate-45
                                            {{ $mine ? '-right-1.5 bg-purple-600' : '-left-1.5 bg-white border border-gray-100' }}">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Typing bar (fake visual only) --}}
                    <div class="px-6 sm:px-8 pb-2">
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                {{ $carer->name ?? 'Carer' }} is available
                            </span>
                        </div>
                    </div>

                    {{-- Input pinned bottom --}}
                    <div class="border-t bg-white/85 backdrop-blur px-4 sm:px-8 py-4">
                        <form method="POST" action="{{ route('child.messages.store', $thread) }}" class="flex items-center gap-3">
                            @csrf

                            <div class="flex-1 relative">
                                <input
                                    id="msgInput"
                                    type="text"
                                    name="body"
                                    placeholder="Type a message…"
                                    class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 pr-12 shadow-sm"
                                    required
                                    autocomplete="off"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 select-none">😊</span>
                            </div>

                            <button
                                type="submit"
                                class="rounded-2xl px-5 py-3 font-extrabold text-white shadow-lg
                                       bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600
                                       hover:opacity-95 active:scale-[0.98] transition"
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

                </div>
            </div>

            <div class="text-center text-xs text-gray-500 mt-4">
                Messages are private and stored securely in CareHub.
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
