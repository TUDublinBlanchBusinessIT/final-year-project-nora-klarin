<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    💬 Chat with {{ $carer->name ?? 'Your Carer' }}
                </h2>
                <p class="text-xs text-gray-500">Messages stay saved here.</p>
            </div>

            <a href="{{ route('child.dashboard') }}" class="text-sm text-indigo-600 underline">Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4">

            <div class="rounded-3xl bg-white shadow border p-6">
                <div id="chatBox" class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
                    @forelse($messages as $msg)
                        <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] rounded-2xl px-4 py-3
                                {{ $msg->sender_id === auth()->id()
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-100 text-gray-900' }}">
                                <div class="text-sm whitespace-pre-line">{{ $msg->body }}</div>
                                <div class="text-[11px] opacity-70 mt-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-600">No messages yet. Say hello 👋</div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('child.messages.store', $thread) }}" class="mt-6 flex gap-3">
                    @csrf
                    <input
                        type="text"
                        name="body"
                        value="{{ old('body') }}"
                        placeholder="Type a message..."
                        class="flex-1 rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                        required
                    />
                    <button class="rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3">
                        Send
                    </button>
                </form>

                @if ($errors->any())
                    <div class="mt-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        // ✅ Auto-scroll to bottom on load
        const chatBox = document.getElementById('chatBox');
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</x-app-layout>
