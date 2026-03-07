<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Messages</h2>

                <p class="text-sm text-gray-500 mt-1">Chat securely with your social worker</p>

            </div>



            <div class="flex gap-2">

                <a href="{{ route('carer.dashboard') }}"

                   class="px-4 py-2 rounded-2xl bg-white border border-gray-200 text-sm font-medium hover:bg-gray-50 shadow-sm">

                    Back

                </a>



                <a href="{{ route('carer.messages.create') }}"

                   class="px-4 py-2 rounded-2xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">

                    New Message

                </a>

            </div>

        </div>

    </x-slot>



    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-pink-50 to-yellow-50 py-10">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">



            {{-- top mini cards --}}

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

                <div class="rounded-3xl bg-white/90 backdrop-blur shadow-sm border border-indigo-100 p-5">

                    <div class="text-sm text-gray-500">Conversations</div>

                    <div class="text-2xl font-bold text-indigo-700 mt-2">{{ $conversations->count() }}</div>

                </div>



                <div class="rounded-3xl bg-white/90 backdrop-blur shadow-sm border border-pink-100 p-5">

                    <div class="text-sm text-gray-500">Unread</div>

                    <div class="text-2xl font-bold text-pink-600 mt-2">

                        {{ $conversations->sum(fn($c) => $c->unread_count ?? 0) }}

                    </div>

                </div>



                <div class="rounded-3xl bg-white/90 backdrop-blur shadow-sm border border-sky-100 p-5">

                    <div class="text-sm text-gray-500">Status</div>

                    <div class="text-base font-semibold text-sky-700 mt-2">Secure messaging enabled</div>

                </div>

            </div>



            {{-- main chat area --}}

            <div class="rounded-[28px] overflow-hidden shadow-xl border border-white/70 bg-white/80 backdrop-blur">

                <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[720px]">



                    {{-- left panel --}}

                    <aside class="lg:col-span-4 border-r border-gray-100 bg-white/80">

                        <div class="p-5 border-b border-gray-100">

                            <div class="flex items-center justify-between">

                                <div>

                                    <h3 class="font-bold text-gray-900">Conversations</h3>

                                    <p class="text-xs text-gray-500 mt-1">Pick a chat to open</p>

                                </div>



                                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">

                                    Inbox

                                </span>

                            </div>



                            <div class="mt-4">

                                <input id="convSearch" type="text"

                                       class="w-full rounded-2xl border-gray-200 text-sm focus:border-indigo-400 focus:ring-indigo-400"

                                       placeholder="Search conversations...">

                            </div>

                        </div>



                        @if($conversations->isEmpty())

                            <div class="p-6">

                                <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">

                                    <div class="text-4xl mb-3">💬</div>

                                    <div class="font-semibold text-gray-900">No conversations yet</div>

                                    <div class="text-sm text-gray-500 mt-1">Start a chat with a social worker.</div>



                                    <div class="mt-5">

                                        <a href="{{ route('carer.messages.create') }}"

                                           class="inline-flex px-4 py-2 rounded-2xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">

                                            Start chat

                                        </a>

                                    </div>

                                </div>

                            </div>

                        @else

                            <div id="convList" class="max-h-[620px] overflow-y-auto">

                                @foreach($conversations as $c)

                                    @php

                                        $isActive = $selectedUser && $selectedUser->id === $c->id;



                                        $initials = collect(explode(' ', trim($c->name)))

                                            ->filter()->take(2)

                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))

                                            ->join('');



                                        $preview = $c->last_body

                                            ? \Illuminate\Support\Str::limit($c->last_body, 40)

                                            : 'No messages yet';



                                        $time = $c->last_at

                                            ? \Carbon\Carbon::parse($c->last_at)->format('D H:i')

                                            : '';

                                    @endphp



                                    <a href="{{ route('carer.messages.index', ['with' => $c->id]) }}"

                                       data-name="{{ strtolower($c->name . ' ' . $c->email) }}"

                                       class="block px-4 py-4 border-b border-gray-100 transition hover:bg-indigo-50/50 {{ $isActive ? 'bg-indigo-50' : '' }}">

                                        <div class="flex items-start gap-3">

                                            <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-500 text-white flex items-center justify-center font-bold shadow-sm shrink-0">

                                                {{ $initials ?: 'U' }}

                                                <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>

                                            </div>



                                            <div class="min-w-0 flex-1">

                                                <div class="flex items-start justify-between gap-2">

                                                    <div class="min-w-0">

                                                        <div class="font-semibold text-gray-900 truncate">{{ $c->name }}</div>

                                                        <div class="text-xs text-gray-500 truncate mt-1">{{ $preview }}</div>

                                                    </div>



                                                    <div class="text-right shrink-0">

                                                        <div class="text-[11px] text-gray-400">{{ $time }}</div>



                                                        @if(($c->unread_count ?? 0) > 0)

                                                            <div class="mt-1 inline-flex min-w-6 h-6 px-2 items-center justify-center rounded-full bg-pink-500 text-white text-[11px] font-bold">

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



                    {{-- right panel --}}

                    <main class="lg:col-span-8 flex flex-col bg-gradient-to-b from-white to-indigo-50/30">

                        @if(!$selectedUser)

                            <div class="flex-1 flex items-center justify-center p-10">

                                <div class="text-center max-w-md">

                                    <div class="text-5xl mb-4">💭</div>

                                    <h3 class="text-xl font-bold text-gray-900">Choose a conversation</h3>

                                    <p class="text-sm text-gray-500 mt-2">

                                        Select a social worker on the left to view your messages.

                                    </p>

                                </div>

                            </div>

                        @else

                            @php

                                $initials = collect(explode(' ', trim($selectedUser->name)))

                                    ->filter()->take(2)

                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))

                                    ->join('');

                            @endphp



                            {{-- chat header --}}

                            <div class="p-5 border-b border-gray-100 bg-white/90">

                                <div class="flex items-center justify-between">

                                    <div class="flex items-center gap-3">

                                        <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-pink-500 text-white flex items-center justify-center font-bold shadow-sm">

                                            {{ $initials ?: 'U' }}

                                            <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>

                                        </div>



                                        <div>

                                            <div class="font-bold text-gray-900">{{ $selectedUser->name }}</div>

                                            <div class="text-xs text-gray-500">{{ $selectedUser->role }} • Available</div>

                                        </div>

                                    </div>



                                    <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">

                                        Secure chat

                                    </span>

                                </div>

                            </div>



                            {{-- messages --}}

                            <div id="chatScroll" class="flex-1 overflow-y-auto px-5 py-6 space-y-4">

                                @php $lastLabel = null; @endphp



                                @forelse($messages as $m)

                                    @php

                                        $isMe = $m->sender_id === auth()->id();

                                        $label = $m->created_at->isToday() ? 'Today' : 'Earlier';

                                    @endphp



                                    @if($label !== $lastLabel)

                                        <div class="flex justify-center py-2">

                                            <span class="px-4 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-500 shadow-sm">

                                                {{ $label }}

                                            </span>

                                        </div>

                                        @php $lastLabel = $label; @endphp

                                    @endif



                                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">

                                        <div class="max-w-[75%] rounded-[22px] px-4 py-3 shadow-sm

                                            {{ $isMe

                                                ? 'bg-gradient-to-r from-indigo-600 to-sky-500 text-white'

                                                : 'bg-white text-gray-900 border border-gray-100' }}">

                                            <div class="text-sm leading-relaxed whitespace-pre-wrap">{{ $m->body }}</div>



                                            <div class="mt-2 text-[11px] {{ $isMe ? 'text-indigo-100' : 'text-gray-400' }}">

                                                {{ $m->created_at->format($m->created_at->isToday() ? 'H:i' : 'D H:i') }}

                                                @if($isMe && $m->read_at)

                                                    • Seen

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="text-center py-16">

                                        <div class="text-4xl mb-3">✨</div>

                                        <div class="font-semibold text-gray-900">No messages yet</div>

                                        <div class="text-sm text-gray-500 mt-1">Start the conversation below.</div>

                                    </div>

                                @endforelse

                            </div>



                            {{-- composer --}}

                            <div class="border-t border-gray-100 bg-white/90 p-4">

                                <form id="composerForm" method="POST" action="{{ route('carer.messages.store') }}" class="flex items-end gap-3">

                                    @csrf

                                    <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">



                                    <textarea id="composerBody" name="body" rows="1"

                                              class="flex-1 resize-none rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400"

                                              placeholder="Write a message..."></textarea>



                                    <button type="submit"

                                            class="px-5 py-3 rounded-2xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">

                                        Send

                                    </button>

                                </form>



                                <div class="mt-3 flex flex-wrap gap-2">

                                    @foreach(['Thanks!', 'Noted ✅', 'Can we reschedule?', 'I’ll confirm soon'] as $chip)

                                        <button type="button"

                                                class="px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100 hover:bg-indigo-100"

                                                onclick="document.getElementById('composerBody').value='{{ $chip }}'; document.getElementById('composerBody').focus();">

                                            {{ $chip }}

                                        </button>

                                    @endforeach

                                </div>



                                @error('body')

                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>

                                @enderror

                            </div>

                        @endif

                    </main>

                </div>

            </div>

        </div>

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

            body.addEventListener('keydown', (e) => {

                if (e.key === 'Enter' && !e.shiftKey) {

                    e.preventDefault();

                    if (body.value.trim().length > 0) form.submit();

                }

            });

        }

    </script>

</x-app-layout>