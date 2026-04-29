<x-app-layout>
    @php
        $chatbotName = auth()->user()->chatbot_name ?? 'CareHub Assistant';
    @endphp

    <div class="theme-page min-h-screen py-8">
        <div class="max-w-md mx-auto px-4">
            <div class="theme-card rounded-3xl shadow-xl overflow-hidden">

                <div class="bg-blue-700 text-white px-6 py-5 text-center">
                    <div class="text-2xl font-bold">{{ $chatbotName }}</div>
                    <div class="text-sm text-blue-100 mt-1">How can we help?</div>
                </div>

                <div id="chatBox" class="h-[420px] overflow-y-auto p-4 space-y-4 theme-page">
                    <div class="flex">
                        <div class="theme-card max-w-[85%] rounded-2xl rounded-bl-md px-4 py-3 text-sm shadow-sm">
                            Hi, I’m {{ $chatbotName }}. I can help with housing, money, wellbeing, education, and support.
                        </div>
                    </div>
                </div>

                <div id="suggestions" class="px-4 pt-3 pb-2 theme-card border-t flex flex-wrap gap-2">
                    <button type="button" onclick="sendQuickMessage('Housing help')" class="px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100">Housing</button>
                    <button type="button" onclick="sendQuickMessage('Money advice')" class="px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100">Money</button>
                    <button type="button" onclick="sendQuickMessage('Wellbeing support')" class="px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100">Wellbeing</button>
                    <button type="button" onclick="sendQuickMessage('Education support')" class="px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100">Education</button>
                    <button type="button" onclick="sendQuickMessage('Emergency help')" class="px-3 py-2 rounded-full bg-red-50 text-red-700 text-sm font-medium hover:bg-red-100">Emergency</button>
                </div>

                <div class="p-4 border-t theme-card">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="messageInput"
                            placeholder="Type a message"
                            class="theme-input flex-1 rounded-2xl focus:border-blue-500 focus:ring-blue-500 px-4 py-3"
                            onkeydown="if(event.key === 'Enter') sendMessage()"
                        >
                        <button
                            type="button"
                            onclick="sendMessage()"
                            class="px-5 py-3 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-semibold"
                        >
                            Send
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('child.dashboard') }}"
                           class="inline-block px-6 py-3 rounded-2xl border border-blue-700 text-blue-700 font-semibold hover:bg-blue-50">
                            End chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function appendMessage(message, sender, isEmergency = false, linkLabel = null, linkUrl = null) {
            const chatBox = document.getElementById('chatBox');
            const wrapper = document.createElement('div');
            wrapper.className = sender === 'user' ? 'flex justify-end' : 'flex';

            const container = document.createElement('div');
            container.className = 'max-w-[85%]';

            const bubble = document.createElement('div');
            bubble.className = sender === 'user'
                ? 'bg-blue-700 text-white rounded-2xl rounded-br-md px-4 py-3 text-sm shadow-sm'
                : 'theme-card rounded-2xl rounded-bl-md px-4 py-3 text-sm shadow-sm';

            if (isEmergency && sender === 'bot') {
                bubble.className = 'bg-red-50 border border-red-200 rounded-2xl rounded-bl-md px-4 py-3 text-sm text-red-800 shadow-sm';
            }

            bubble.textContent = message;
            container.appendChild(bubble);

            if (sender === 'bot' && linkLabel && linkUrl) {
                const link = document.createElement('a');
                link.href = linkUrl;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.className = 'inline-block mt-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-sm font-semibold hover:bg-blue-100';
                link.textContent = linkLabel;
                container.appendChild(link);
            }

            wrapper.appendChild(container);
            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function renderSuggestions(items = []) {
            const suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';

            items.forEach(item => {
                const button = document.createElement('button');
                button.type = 'button';

                const isEmergency = item.toLowerCase().includes('emergency');

                button.className = isEmergency
                    ? 'px-3 py-2 rounded-full bg-red-50 text-red-700 text-sm font-medium hover:bg-red-100'
                    : 'px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100';

                button.textContent = item;
                button.onclick = () => sendQuickMessage(item);
                suggestions.appendChild(button);
            });
        }

        function sendQuickMessage(text) {
            document.getElementById('messageInput').value = text;
            sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();

            if (!message) return;

            appendMessage(message, 'user');
            input.value = '';

            try {
                const response = await fetch("{{ route('chatbot.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();

                if (!response.ok) {
                    appendMessage('Something went wrong. Please try again.', 'bot', false);
                    return;
                }

                appendMessage(
                    data.reply,
                    'bot',
                    data.is_emergency ?? false,
                    data.link_label ?? null,
                    data.link_url ?? null
                );

                renderSuggestions(data.suggestions ?? []);
            } catch (error) {
                appendMessage('I could not connect right now. Please try again.', 'bot', false);
            }
        }
    </script>
</x-app-layout>