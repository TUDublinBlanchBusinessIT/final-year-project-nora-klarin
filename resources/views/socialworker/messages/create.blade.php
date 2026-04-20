<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">New Message</h2>
            <p class="text-sm text-gray-500 mt-1">Start a secure conversation</p>
        </div>

        <a href="{{ route('socialworker.messages.index') }}"
           class="px-4 py-2 rounded-2xl bg-white border border-gray-200 text-sm font-medium hover:bg-gray-50 shadow-sm">
            Back
        </a>
    </div>
</x-slot>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-pink-50 to-yellow-50 py-10">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="rounded-[28px] bg-white/90 backdrop-blur shadow-xl border border-white/70 p-8">

            <form method="POST" action="{{ route('socialworker.messages.store') }}" class="space-y-6">
                @csrf

                {{-- RECIPIENT --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Choose recipient
                    </label>

                    <select name="recipient_id"
                        class="w-full rounded-2xl border-gray-200 text-sm focus:border-indigo-400 focus:ring-indigo-400">

                        <option value="">Select user...</option>

                        @foreach($recipients as $r)
                            <option value="{{ $r->id }}" @selected(old('recipient_id') == $r->id)>
                                {{ $r->name }} • {{ ucfirst($r->role) }}
                            </option>
                        @endforeach

                    </select>

                    @error('recipient_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- MESSAGE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Message
                    </label>

                    <textarea name="body" rows="5"
                        class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 resize-none"
                        placeholder="Type your message...">{{ old('body') }}</textarea>

                    @error('body')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- QUICK ACTION CHIPS --}}
                <div class="flex flex-wrap gap-2">
                    @foreach(['Hi, just checking in', 'Can we schedule a call?', 'Please update me on this case', 'Thanks for your help'] as $chip)
                        <button type="button"
                            onclick="document.querySelector('textarea[name=body]').value='{{ $chip }}'"
                            class="px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100 hover:bg-indigo-100">
                            {{ $chip }}
                        </button>
                    @endforeach
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-between items-center pt-4">

                    <a href="{{ route('socialworker.messages.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                        Send Message
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</x-app-layout>