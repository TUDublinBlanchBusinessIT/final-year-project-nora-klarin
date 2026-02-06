<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">👨‍👩‍👧 Trusted People</h2>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 space-y-6">

            {{-- Success message --}}
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <div class="font-bold mb-1">Please fix:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-3xl bg-white/90 backdrop-blur shadow-xl border border-indigo-100 p-8">
                <p class="text-gray-700 text-lg font-semibold">
                    These are the people you can reach out to 💙
                </p>

                {{-- LIST FROM DB --}}
                <div class="mt-6 space-y-3">
                    @forelse ($people as $p)
                        <div class="rounded-2xl border bg-white px-5 py-4 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-gray-800">
                                    {{ $p->name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $p->relationship }}
                                    @if (!empty($p->phone)) • 📞 {{ $p->phone }} @endif
                                    @if (!empty($p->email)) • ✉️ {{ $p->email }} @endif
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">Saved</span>
                        </div>
                    @empty
                        <div class="rounded-2xl border bg-indigo-50 px-5 py-4 text-gray-700">
                            No trusted people added yet — add one below ✨
                        </div>
                    @endforelse
                </div>

                {{-- ADD FORM --}}
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-extrabold text-indigo-700">➕ Add a trusted person</h3>

                    <form method="POST" action="{{ route('child.trusted.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Name</label>
                                <input name="name" value="{{ old('name') }}"
                                    class="w-full rounded-2xl border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Ms. Kelly" />
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Relationship</label>
                                <input name="relationship" value="{{ old('relationship') }}"
                                    class="w-full rounded-2xl border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Social Worker" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Phone (optional)</label>
                                <input name="phone" value="{{ old('phone') }}"
                                    class="w-full rounded-2xl border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. 0871234567" />
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Email (optional)</label>
                                <input name="email" value="{{ old('email') }}"
                                    class="w-full rounded-2xl border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. support@email.com" />
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-95 text-white font-extrabold py-3 shadow-lg transition">
                            Save Trusted Person 💙
                        </button>
                    </form>
                </div>

                <div class="mt-6">
                    <a href="{{ route('child.dashboard') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                        ← Back to Child Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
