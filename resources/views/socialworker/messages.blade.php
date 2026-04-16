<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

            <div>

                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">

                    Messages

                </h2>

                <p class="text-sm text-gray-500 mt-1">

                    Chat with young people and carers in one place.

                </p>

            </div>



            <a href="{{ route('socialworker.dashboard') }}"

               class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">

                ← Back to dashboard

            </a>

        </div>

    </x-slot>



    <div class="min-h-screen py-8 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">



            @if(session('status'))

                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">

                    {{ session('status') }}

                </div>

            @endif



            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">



                {{-- LEFT PANEL --}}

                <div class="lg:col-span-4 space-y-6">



                    <div class="rounded-3xl bg-white/90 backdrop-blur shadow-sm border border-indigo-100 p-5">

                        <div class="text-sm text-gray-500">Conversations</div>

                        <div class="text-2xl font-bold text-indigo-700 mt-2">

                            {{ $threads->count() }}

                        </div>

                    </div>



                    <div class="rounded-3xl bg-white/90 backdrop-blur shadow-sm border border-indigo-100 overflow-hidden">

                        <div class="px-5 py-4 border-b border-gray-100">

                            <h3 class="font-bold text-gray-900">Inbox</h3>

                            <p class="text-sm text-gray-500 mt-1">

                                Select a conversation to open it.

                            </p>

                        </div>



                        <div class="max-h-[650px] overflow-y-auto">

                            @forelse($threads as $thread)

                                @php

                                    $isActive = $selectedThread && $selectedThread->id === $thread->id;

                                    $badgeClass = $thread->partner_role_label === 'Carer'

                                        ? 'bg-pink-100 text-pink-700'

                                        : 'bg-blue-100 text-blue-700';

                                @endphp



                                <a href="{{ route('socialworker.messages.index', ['thread' => $thread->id]) }}"

                                   class="block px-5 py-4 border-b border-gray-100 transition hover:bg-indigo-50 {{ $isActive ? 'bg-indigo-50' : 'bg-white' }}">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="min-w-0 flex-1">

                                            <div class="flex items-center gap-3">

                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center text-white font-bold shrink-0">

                                                    {{ strtoupper(substr($thread->partner_name ?? 'U', 0, 1)) }}

                                                </div>



                                                <div class="min-w-0">

                                                    <div class="font-semibold text-gray-900 truncate">

                                                        {{ $thread->partner_name }}

                                                    </div>



                                                    <div class="mt-1 flex items-center gap-2">

                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">

                                                            {{ $thread->partner_role_label }}

                                                        </span>



                                                        @if($thread->partner_email)

                                                            <span class="text-xs text-gray-500 truncate">

                                                                {{ $thread->partner_email }}

                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>



                                            <div class="mt-3 text-sm text-gray-600 truncate">

                                                {{ $thread->last_body ?? 'No messages yet.' }}

                                            </div>

                                        </div>



                                        <div class="text-right shrink-0">

                                            @if($thread->last_at)

                                                <div class="text-xs text-gray-400">

                                                    {{ \Carbon\Carbon::parse($thread->last_at)->diffForHumans() }}

                                                </div>

                                            @endif



                                            @if(($thread->unread_count ?? 0) > 0)

                                                <div class="mt-2 inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 rounded-full bg-red-600 text-white text-xs font-bold">

                                                    {{ $thread->unread_count }}

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </a>

                            @empty

                                <div class="px-5 py-10 text-center text-gray-500">

                                    No conversations yet.

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>



                {{-- RIGHT PANEL --}}

                <div class="lg:col-span-8">

                    <div class="relative overflow-hidden rounded-[28px] border border-white/60 shadow-2xl bg-white/75 backdrop-blur">

                        <div class="h-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600"></div>



                        @if($selectedThread)

                            @php

                                $partner = $selectedThread->conversation_type === 'carer' 
                                    ? $selectedThread->carer
                                    : $selectedThread->child;

                                $partnerName = $partner->name ?? 'Unknown User';

                                $partnerRole = $selectedThread->conversation_type === 'carer'
                                    ? 'Carer'
                                    : 'Young Person';

                            @endphp



                            <div class="px-6 py-5 border-b border-gray-100 bg-white">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center text-white font-extrabold shadow">

                                            {{ strtoupper(substr($partnerName, 0, 1)) }}

                                        </div>



                                        <div>

                                            <div class="font-bold text-lg text-gray-900">

                                                Chat with {{ $partnerName }}

                                            </div>

                                            <div class="text-xs text-gray-500 flex items-center gap-2">

                                                <span class="px-2 py-0.5 rounded-full {{ $partnerRole === 'Carer' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">

                                                    {{ $partnerRole }}

                                                </span>

                                                <span>Secure chat</span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>



                            <div id="chatBox" class="h-[540px] overflow-y-auto px-5 sm:px-8 py-6 space-y-4 bg-gradient-to-br from-indigo-50/40 via-white to-pink-50/40">

                                @if($messages->isEmpty())

                                    <div class="text-center py-14">

                                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-pink-600 text-white text-3xl shadow">

                                            💬

                                        </div>

                                        <div class="mt-4 text-xl font-extrabold text-gray-900">

                                            Start your chat

                                        </div>

                                        <div class="mt-1 text-sm text-gray-600">

                                            Send a message to {{ $partnerName }} 👋

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

                                            <div class="relative px-3.5 py-2.5 rounded-[18px] shadow

                                                {{ $mine

                                                    ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white'

                                                    : 'bg-white text-gray-900 border border-gray-100' }}">

                                                <div class="whitespace-pre-line text-[14px] leading-relaxed">

                                                    {{ $msg->body }}

                                                </div>



                                                <div class="mt-1.5 flex items-center justify-end gap-2 text-[10px]

                                                    {{ $mine ? 'text-white/70' : 'text-gray-500' }}">

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

                                                    {{ $mine ? '-right-1.5 bg-purple-600' : '-left-1.5 bg-white border border-gray-100' }}">

                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>



                            <div class="px-6 sm:px-8 pb-2">

                                <div class="text-xs text-gray-500">

                                    Replies may not be instant

                                </div>

                            </div>



                            <div class="border-t bg-white/85 backdrop-blur px-4 sm:px-8 py-4">

                                <form method="POST" action="{{ route('socialworker.messages.store', $selectedThread) }}" class="flex items-center gap-3">

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

                                        class="rounded-2xl px-6 py-3 font-semibold text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition focus:outline-none focus:ring-2 focus:ring-indigo-200">

                                        Send ➤

                                    </button>

                                </form>



                                @if ($errors->any())

                                    <div class="mt-3 text-sm text-red-600 font-semibold">

                                        {{ $errors->first() }}

                                    </div>

                                @endif

                            </div>

                        @else

                            <div class="h-[650px] flex items-center justify-center bg-white">

                                <div class="text-center px-6">

                                    <div class="text-5xl mb-4">📭</div>

                                    <h3 class="text-xl font-bold text-gray-900">No conversation selected</h3>

                                    <p class="text-sm text-gray-500 mt-2">

                                        Choose a thread from the left to view messages.

                                    </p>

                                </div>

                            </div>

                        @endif

                    </div>



                    <div class="text-center text-xs text-gray-500 mt-4">

                        Messages are private and stored securely in CareHub.

                    </div>

                </div>

            </div>

        </div>

    </div>



    <script>

        const chatBox = document.getElementById('chatBox');

        const msgInput = document.getElementById('msgInput');



        if (chatBox) {

            chatBox.scrollTop = chatBox.scrollHeight;

        }



        if (msgInput) {

            msgInput.focus();

        }

    </script>

</x-app-layout>