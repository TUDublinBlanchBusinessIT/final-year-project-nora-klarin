<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Documents</h2>
                <p class="text-sm text-gray-500">Upload and download PDFs securely</p>
            </div>

            <a href="{{ route('carer.dashboard') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 text-sm hover:bg-gray-200">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-gray-900">Upload a PDF</h3>

                <form method="POST" action="{{ route('carer.documents.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm text-gray-700">Title (optional)</label>
                        <input name="title" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-400 focus:ring-gray-400"
                               placeholder="e.g. Consent form, Care plan..." value="{{ old('title') }}">
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-700">PDF file</label>
                        <input type="file" name="file" accept="application/pdf"
                               class="mt-1 block w-full text-sm">
                        @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700">
                        Upload
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Your documents</h3>
                    <span class="text-xs text-gray-500">{{ $docs->count() }} total</span>
                </div>

                @if($docs->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed p-8 text-center text-gray-600">
                        No documents yet.
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($docs as $d)
                            <div class="rounded-xl border p-4 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $d->title ?: $d->original_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $d->original_name }} • {{ round($d->size / 1024, 1) }} KB • {{ $d->created_at->format('D d M, H:i') }}
                                    </div>
                                </div>

                                <div class="flex gap-2 shrink-0">
                                    <a href="{{ route('carer.documents.download', $d) }}"
                                       class="px-3 py-2 rounded-xl bg-gray-900 text-white text-sm hover:bg-gray-800">
                                        Download
                                    </a>

                                    <form method="POST" action="{{ route('carer.documents.destroy', $d) }}"
                                          onsubmit="return confirm('Delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-xl bg-gray-100 text-sm hover:bg-gray-200">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
