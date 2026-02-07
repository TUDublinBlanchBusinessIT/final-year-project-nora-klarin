<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
                <p class="text-sm text-gray-500">Your inbox</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('carer.dashboard') }}"
                   class="px-4 py-2 rounded bg-gray-100 text-sm hover:bg-gray-200">
                    Back
                </a>

                <a href="{{ route('carer.messages.create') }}"
                   class="px-4 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                    New Message
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if(session('status'))
                    <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if($messages->isEmpty())
                    <div class="rounded border border-dashed p-8 text-center">
                        <div class="text-lg font-semibold text-gray-800">No messages yet</div>
                        <div class="text-sm text-gray-600 mt-1">
                            When someone messages you, it will appear here.
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('carer.messages.create') }}"
                               class="inline-block px-4 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                                Send your first message
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($messages as $msg)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium text-gray-800">From:</span>
                                            User #{{ $msg->sender_id }}
                                        </div>

                                        <div class="mt-2 text-gray-900">
                                            {{ $msg->body }}
                                        </div>
                                    </div>

                                    <div class="text-xs text-gray-500 whitespace-nowrap">
                                        {{ $msg->created_at->format('D d M, H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        {{ $messages->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
