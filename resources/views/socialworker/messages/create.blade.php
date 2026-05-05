<x-app-layout>

@php
    $isSW       = request()->routeIs('socialworker.*');
    $indexRoute = $isSW ? 'socialworker.messages.index' : 'carer.messages.index';
    $storeRoute = $isSW ? 'socialworker.messages.store' : 'carer.messages.store';
@endphp

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">New message</h1>
            <p class="text-sm text-gray-500 mt-0.5">Start a secure conversation</p>
        </div>
        <a href="{{ route($indexRoute) }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition">
            ← Back
        </a>
    </div>
</x-slot>

<div class="max-w-2xl">
    <div class="bg-white border border-gray-200 rounded-[14px] p-6">

        <form method="POST"
              action="{{ route($storeRoute) }}"
              class="space-y-5">
            @csrf

            {{-- Recipient --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Recipient <span class="text-red-500">*</span></label>
                <select name="recipient_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Select recipient…</option>
                    @foreach($recipients as $r)
                        <option value="{{ $r->id }}" @selected(old('recipient_id') == $r->id)>
                            {{ $r->name }} · {{ ucfirst(str_replace('_', ' ', $r->role)) }}
                        </option>
                    @endforeach
                </select>
                @error('recipient_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quick chips --}}
            <div>
                <p class="text-xs font-medium text-gray-400 mb-2">Quick starters</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach([
                        'Hi, just checking in',
                        'Can we schedule a call?',
                        'Please update me on this case',
                        'Thanks for your help',
                    ] as $chip)
                        <button type="button"
                            onclick="document.querySelector('textarea[name=body]').value='{{ $chip }}'; document.querySelector('textarea[name=body]').focus();"
                            class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium hover:bg-slate-200 transition">
                            {{ $chip }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Message body --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                <textarea name="body" rows="6"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-300 placeholder-gray-400"
                          placeholder="Type your message…">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                <a href="{{ route($indexRoute) }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Send message
                </button>
            </div>
        </form>

    </div>
</div>

</x-app-layout>
