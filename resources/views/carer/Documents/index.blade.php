<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Documents</h2>
                <p class="text-sm text-gray-500">Upload and download PDFs securely</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('carer.dashboard') }}"
                   class="px-4 py-2 rounded-xl bg-gray-100 text-sm hover:bg-gray-200">
                    Back
                </a>

                <a href="{{ route('carer.messages.index') }}"
                   class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">
                    Messages
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Top hero --}}
            <div class="rounded-2xl p-6 bg-indigo-700 bg-gradient-to-r from-indigo-600 to-sky-500 shadow-sm text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="text-sm opacity-90">Your secure folder</div>
                        <div class="text-2xl font-bold mt-1">Carer Documents</div>
                        <div class="text-sm opacity-90 mt-2">
                            Keep care plans, consent forms and reports in one place.
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur px-4 py-3">
                            <div class="text-xs opacity-90">Total files</div>
                            <div class="text-lg font-semibold">{{ $docs->count() }}</div>
                        </div>

                        <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur px-4 py-3">
                            <div class="text-xs opacity-90">Allowed</div>
                            <div class="text-lg font-semibold">PDF</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Upload card --}}
                <div class="lg:col-span-1">
                    <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                        <div class="p-5 border-b bg-gray-50">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">📤</span>
                                <h3 class="font-semibold text-gray-900">Upload a PDF</h3>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Max 10MB • Stored privately</p>
                        </div>

                        <form method="POST" action="{{ route('carer.documents.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                            @csrf

                            <div>
                                <label class="text-sm text-gray-700 font-medium">Title</label>
                                <input name="title"
                                       class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g. Consent Form, Care Plan"
                                       value="{{ old('title') }}">
                                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm text-gray-700 font-medium">PDF file</label>

                                <div class="mt-2 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
                                    <div class="text-center">
                                        <div class="text-2xl">📄</div>
                                        <div class="text-sm text-gray-700 font-medium mt-2">Choose a PDF to upload</div>
                                        <div class="text-xs text-gray-500 mt-1">Drag & drop isn’t enabled yet — choose file below</div>

                                        <input type="file" name="file" accept="application/pdf"
                                               class="mt-4 block w-full text-sm">
                                    </div>
                                </div>

                                @error('file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            <button
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                                <span>Upload</span>
                                <span>→</span>
                            </button>

                            <p class="text-[11px] text-gray-500 text-center">
                                Tip: Use clear titles so you can find files quickly.
                            </p>
                        </form>
                    </div>
                </div>

                {{-- List card --}}
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                        <div class="p-5 border-b">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🗂️</span>
                                        <h3 class="font-semibold text-gray-900">Your files</h3>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only you can access these documents.</p>
                                </div>

                                <div class="relative">
                                    <input id="docSearch" type="text"
                                           class="w-full sm:w-64 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Search documents…">
                                </div>
                            </div>
                        </div>

                        @if($docs->isEmpty())
                            <div class="p-10 text-center bg-gray-50">
                                <div class="text-3xl">📁</div>
                                <div class="text-lg font-semibold text-gray-900 mt-3">No documents yet</div>
                                <div class="text-sm text-gray-600 mt-1">Upload your first PDF on the left.</div>
                            </div>
                        @else
                            <div id="docList" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50">
                                @foreach($docs as $d)
                                    @php
                                        $display = $d->title ?: $d->original_name;
                                        $kb = $d->size ? round($d->size / 1024, 1) : 0;
                                        $when = $d->created_at->format('D d M, H:i');
                                    @endphp

                                    <div class="rounded-2xl border bg-white p-4 hover:shadow-sm transition"
                                         data-name="{{ strtolower($display . ' ' . $d->original_name) }}">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="h-11 w-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-xl shrink-0">
                                                    📄
                                                </div>

                                                <div class="min-w-0">
                                                    <div class="font-semibold text-gray-900 truncate">
                                                        {{ $display }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 truncate mt-1">
                                                        {{ $d->original_name }}
                                                    </div>

                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                            {{ $kb }} KB
                                                        </span>
                                                        <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                            {{ $when }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-2 shrink-0">
                                                <a href="{{ route('carer.documents.download', $d) }}"
                                                   class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-gray-900 text-white text-sm hover:bg-gray-800">
                                                    Download
                                                </a>

                                                <form method="POST" action="{{ route('carer.documents.destroy', $d) }}"
                                                      onsubmit="return confirm('Delete this document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-white text-gray-700 text-sm border hover:bg-gray-50">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <script>
                                const search = document.getElementById('docSearch');
                                const list = document.getElementById('docList');

                                if (search && list) {
                                    search.addEventListener('input', () => {
                                        const q = search.value.toLowerCase();
                                        list.querySelectorAll('[data-name]').forEach(card => {
                                            card.style.display = card.getAttribute('data-name').includes(q) ? '' : 'none';
                                        });
                                    });
                                }
                            </script>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
