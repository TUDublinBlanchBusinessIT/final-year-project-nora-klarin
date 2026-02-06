<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
            <a href="{{ route('carer.messages.create') }}" class="px-4 py-2 rounded bg-gray-900 text-white text-sm">
                New Message
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if(session('status'))
                    <div class="mb-4 text-sm text-green-700">{{ session('status') }}</div>
                @endif

                @if($messages->count() === 0)
                    <p class="text-gray-600">No messages yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($messages as $msg)
                            <div class="border rounded p-4">
                                <div class="text-sm text-gray-600">
                                    From user #{{ $msg->sender_id }} • {{ $msg->created_at->format('D d M Y, H:i') }}
                                </div>
                                <div class="mt-1">
                                    {{ $msg->body }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
