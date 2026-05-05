<x-app-layout>
    @php
        $chatbotName = auth()->user()->chatbot_name ?? 'CareHub Assistant';
        $userName    = explode(' ', auth()->user()->name ?? 'there')[0];
    @endphp

    <div class="theme-page min-h-screen flex flex-col">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 via-fuchsia-600 to-sky-500 text-white px-5 py-4 flex items-center gap-3 shadow-lg">
            <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center text-xl flex-shrink-0">
                💬
            </div>
            <div class="flex-1">
                <div class="font-bold text-base">{{ $chatbotName }}</div>
                <div class="text-xs text-white/70 flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 inline-block"></span>
                    Always here for you
                </div>
            </div>
            <a href="{{ route('child.dashboard') }}"
               class="text-white/70 hover:text-white text-sm transition">
                ✕ Close
            </a>
        </div>

        {{-- Emergency banner --}}
        <div id="emergencyBanner" class="hidden bg-red-600 text-white px-5 py-3 text-sm font-semibold flex items-center gap-2">
            🚨 If you're in danger right now, call <a href="tel:999" class="underline font-bold">999</a>
            or Childline <a href="tel:116111" class="underline font-bold">116 111</a> (free, 24/7)
        </div>

        {{-- Chat area --}}
        <div id="chatBox"
             class="flex-1 overflow-y-auto px-4 py-5 space-y-4"
             style="min-height: 0; max-height: calc(100vh - 280px)">

            {{-- Welcome message --}}
            <div class="flex items-end gap-2 bot-message">
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0 mb-1">
                    💬
                </div>
                <div class="max-w-[80%]">
                    <div class="theme-card rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm">
                        Hey {{ $userName }}! 👋 I'm {{ $chatbotName }}. I'm here to help with anything on your mind — whether that's school, how you're feeling, your rights, or just having someone to talk to.
                    </div>
                    <div class="text-xs opacity-40 mt-1 ml-1">Just now</div>
                </div>
            </div>

            {{-- Restore history --}}
            @foreach($history as $msg)
                <div class="flex justify-end user-message">
                    <div class="max-w-[80%]">
                        <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 text-sm shadow-sm">
                            {{ $msg->user_message }}
                        </div>
                        <div class="text-xs opacity-40 mt-1 text-right mr-1">
                            {{ $msg->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                <div class="flex items-end gap-2 bot-message">
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0 mb-1">
                        💬
                    </div>
                    <div class="max-w-[80%]">
                        <div class="theme-card rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm
                                    {{ $msg->is_emergency ? 'bg-red-50 border border-red-200 text-red-800' : '' }}">
                            {{ $msg->bot_reply }}
                        </div>
                        <div class="text-xs opacity-40 mt-1 ml-1">
                            {{ $msg->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Typing indicator (hidden by default) --}}
            <div id="typingIndicator" class="hidden flex items-end gap-2">
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0">
                    💬
                </div>
                <div class="theme-card rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1 items-center">
                        <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0ms"></span>
                        <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:150ms"></span>
                        <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick suggestions --}}
        <div id="suggestions"
             class="px-4 py-2 flex gap-2 overflow-x-auto border-t theme-card"
             style="scrollbar-width:none">
            @foreach(['How are you feeling?','School support','My rights in care','Talk to someone','Money help','Housing'] as $s)
                <button type="button"
                        onclick="sendQuickMessage('{{ $s }}')"
                        class="flex-shrink-0 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition border border-indigo-100">
                    {{ $s }}
                </button>
            @endforeach
        </div>

        {{-- Input bar --}}
        <div class="px-4 py-3 border-t theme-card">
            <div class="flex gap-2 items-end">
                <div class="flex-1 theme-card border rounded-2xl px-4 py-2.5 flex items-center">
                    <textarea
                        id="messageInput"
                        placeholder="Type a message…"
                        rows="1"
                        class="flex-1 bg-transparent border-none outline-none resize-none text-sm leading-relaxed max-h-24"
                        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                        oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                    ></textarea>
                </div>
                <button
                    id="sendBtn"
                    type="button"
                    onclick="sendMessage()"
                    class="h-11 w-11 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center transition active:scale-95 flex-shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
            <div class="text-center mt-2">
                <span class="text-xs opacity-40">Need urgent help? Call Childline free on </span>
                <a href="tel:116111" class="text-xs text-indigo-500 font-semibold">116 111</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Conversation history kept in memory for Gemini context
    const conversationHistory = @json(
        $history->map(fn($m) => ['user' => $m->user_message, 'bot' => $m->bot_reply])->values()
    );

    const chatBox     = document.getElementById('chatBox');
    const input       = document.getElementById('messageInput');
    const sendBtn     = document.getElementById('sendBtn');
    const typing      = document.getElementById('typingIndicator');
    const suggestions = document.getElementById('suggestions');
    const emergency   = document.getElementById('emergencyBanner');

    chatBox.scrollTop = chatBox.scrollHeight;

    function timeAgo() {
        return 'Just now';
    }

    function appendUser(message) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex justify-end user-message';
        wrapper.innerHTML = `
            <div class="max-w-[80%]">
                <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 text-sm shadow-sm">
                    ${escapeHtml(message)}
                </div>
                <div class="text-xs opacity-40 mt-1 text-right mr-1">Just now</div>
            </div>`;
        chatBox.insertBefore(wrapper, typing);
        scrollBottom();
    }

    function appendBot(reply, isEmergency, linkLabel, linkUrl) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-end gap-2 bot-message';

        const bubbleCls = isEmergency
            ? 'bg-red-50 border border-red-200 text-red-800 rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm'
            : 'theme-card rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm';

        let linkHtml = '';
        if (linkLabel && linkUrl) {
            linkHtml = `<a href="${linkUrl}" target="_blank" rel="noopener"
                class="inline-block mt-2 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100">
                ${escapeHtml(linkLabel)} →
            </a>`;
        }

        wrapper.innerHTML = `
            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0 mb-1">💬</div>
            <div class="max-w-[80%]">
                <div class="${bubbleCls}">
                    ${escapeHtml(reply)}
                    ${linkHtml}
                </div>
                <div class="text-xs opacity-40 mt-1 ml-1">Just now</div>
            </div>`;

        chatBox.insertBefore(wrapper, typing);
        scrollBottom();

        // Animate in
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(8px)';
        requestAnimationFrame(() => {
            wrapper.style.transition = 'opacity 0.3s, transform 0.3s';
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });
    }

    function renderSuggestions(items = []) {
        suggestions.innerHTML = '';
        items.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isEmerg = item.toLowerCase().includes('emergency') || item.toLowerCase().includes('999');
            btn.className = isEmerg
                ? 'flex-shrink-0 px-3 py-1.5 rounded-full bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition border border-red-100'
                : 'flex-shrink-0 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition border border-indigo-100';
            btn.textContent = item;
            btn.onclick = () => sendQuickMessage(item);
            suggestions.appendChild(btn);
        });
    }

    function scrollBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/\n/g, '<br>');
    }

    function sendQuickMessage(text) {
        input.value = text;
        sendMessage();
    }

    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        appendUser(message);

        // Show typing indicator
        typing.classList.remove('hidden');
        scrollBottom();

        try {
            const response = await fetch("{{ route('chatbot.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message,
                    history: conversationHistory.slice(-6),
                }),
            });

            const data = await response.json();

            typing.classList.add('hidden');

            if (!response.ok) {
                appendBot("Something went wrong — please try again.", false, null, null);
                return;
            }

            conversationHistory.push({ user: message, bot: data.reply });

            appendBot(data.reply, data.is_emergency ?? false, data.link_label ?? null, data.link_url ?? null);
            renderSuggestions(data.suggestions ?? []);

            if (data.is_emergency) {
                emergency.classList.remove('hidden');
            }

        } catch (e) {
            typing.classList.add('hidden');
            appendBot("I couldn't connect just now — please try again.", false, null, null);
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }
    </script>
    @endpush
</x-app-layout>