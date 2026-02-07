<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Message</h2>
                <p class="text-sm text-gray-500">Send a message to another user</p>
            </div>

            <a href="{{ route('carer.messages.index') }}"
               class="px-4 py-2 rounded bg-gray-100 text-sm hover:bg-gray-200">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('carer.messages.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Recipient</label>
                        <select name="recipient_id" class="mt-1 block w-full rounded border-gray-300">
                            @foreach($recipients as $r)
                                <option value="{{ $r->id }}" @selected(old('recipient_id') == $r->id)>
                                    {{ $r->name }} ({{ $r->role }}) — {{ $r->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="body" rows="6"
                                  class="mt-1 block w-full rounded border-gray-300"
                                  placeholder="Type your message...">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                            Send
                        </button>

                        <a href="{{ route('carer.messages.index') }}"
                           class="px-4 py-2 rounded bg-gray-100 text-sm hover:bg-gray-200">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
