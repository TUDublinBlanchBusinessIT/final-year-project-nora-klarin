<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Message</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('carer.messages.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Send to</label>
                        <select name="recipient_id" class="mt-1 block w-full rounded border-gray-300">
                            @foreach($recipients as $r)
                                <option value="{{ $r->id }}">
                                    {{ $r->name }} ({{ $r->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="body" rows="5" class="mt-1 block w-full rounded border-gray-300">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 rounded bg-gray-900 text-white text-sm">Send</button>
                        <a href="{{ route('carer.messages.index') }}" class="px-4 py-2 rounded bg-gray-100 text-sm">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
