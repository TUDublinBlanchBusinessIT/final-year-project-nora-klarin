<x-app-layout>

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Wellbeing &amp; Documents</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage shared documents and send wellbeing updates</p>
        </div>
        <a href="{{ route('carer.dashboard') }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition">
            ← Back
        </a>
    </div>
</x-slot>

@if(session('status'))
    <div class="bg-green-50 border border-green-200 rounded-[14px] px-4 py-3 text-sm text-green-800 mb-4">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-[14px] px-4 py-3 text-sm text-red-800 mb-4">
        <p class="font-medium mb-1">Please fix the following:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white border border-gray-200 rounded-[14px] p-4">
        <p class="text-xs font-medium text-gray-400 mb-1">Sent by you</p>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $sentDocs->count() }}</p>
    </div>
    <div class="bg-white border border-indigo-200 rounded-[14px] p-4">
        <p class="text-xs font-medium text-indigo-400 mb-1">Shared with you</p>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $receivedDocs->count() }}</p>
    </div>
    <div class="bg-white border border-amber-200 rounded-[14px] p-4">
        <p class="text-xs font-medium text-amber-500 mb-1">Case files</p>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $caseFiles->count() }}</p>
    </div>
</div>

<div x-data="{ tab: 'upload' }" x-cloak class="space-y-4">

    {{-- Tab bar --}}
    <div class="bg-white border border-gray-200 rounded-[14px] px-1 py-1 flex flex-wrap gap-0.5">
        @foreach([
            'upload'   => 'Upload document',
            'received' => 'Shared with me',
            'sent'     => 'Sent by me',
        ] as $key => $label)
            <button @click="tab='{{ $key }}'"
                    :class="tab==='{{ $key }}' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-slate-50'"
                    class="py-1.5 px-4 text-sm font-medium rounded-lg transition">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Upload tab --}}
    <div x-show="tab==='upload'" x-transition class="bg-white border border-gray-200 rounded-[14px] p-6 space-y-5">
        <div>
            <p class="text-sm font-semibold text-gray-700">Upload document</p>
            <p class="text-xs text-gray-400 mt-0.5">Send a PDF document linked to one of your case files</p>
        </div>

        <form method="POST" action="{{ route('carer.documents.store') }}" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Case file <span class="text-red-500">*</span></label>
                <select name="case_file_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Select case file</option>
                    @foreach($caseFiles as $caseFile)
                        <option value="{{ $caseFile->id }}" @selected(old('case_file_id') == $caseFile->id)>
                            {{ $caseFile->case_reference ?? ('Case #' . $caseFile->id) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Consent Form, Care Plan"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">PDF file <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center bg-slate-50 hover:bg-slate-100 transition">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <p class="text-xs text-gray-500 mb-2">PDF files only</p>
                    <input type="file" name="file" accept="application/pdf" required class="text-xs text-gray-600 mx-auto">
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end pt-1 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Send to social worker
                </button>
            </div>
        </form>
    </div>

    {{-- Received tab --}}
    <div x-show="tab==='received'" x-transition class="bg-white border border-gray-200 rounded-[14px]">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-700">Shared with me</p>
                <p class="text-xs text-gray-400 mt-0.5">Documents shared by your social worker</p>
            </div>
            <input id="receivedSearch" type="text"
                   class="w-48 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300 placeholder-gray-400"
                   placeholder="Search…">
        </div>

        @if($receivedDocs->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No shared documents yet.</p>
            </div>
        @else
            <div id="receivedList" class="divide-y divide-gray-100">
                @foreach($receivedDocs as $d)
                    @php
                        $ext = strtoupper(pathinfo($d->file_path, PATHINFO_EXTENSION));
                    @endphp
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition"
                         data-name="{{ strtolower($d->title . ' ' . basename($d->file_path)) }}">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $d->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $ext }} &middot; {{ $d->created_at->format('d M Y') }}
                                &middot; {{ $d->caseFile->case_reference ?? ('Case #' . $d->case_file_id) }}
                            </p>
                        </div>
                        <a href="{{ route('carer.documents.download', $d) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition shrink-0">
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Sent tab --}}
    <div x-show="tab==='sent'" x-transition class="bg-white border border-gray-200 rounded-[14px]">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-700">Sent by me</p>
                <p class="text-xs text-gray-400 mt-0.5">Documents you uploaded for your case files</p>
            </div>
            <input id="sentSearch" type="text"
                   class="w-48 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300 placeholder-gray-400"
                   placeholder="Search…">
        </div>

        @if($sentDocs->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No uploaded documents yet.</p>
            </div>
        @else
            <div id="sentList" class="divide-y divide-gray-100">
                @foreach($sentDocs as $d)
                    @php $ext = strtoupper(pathinfo($d->file_path, PATHINFO_EXTENSION)); @endphp
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition"
                         data-name="{{ strtolower($d->title . ' ' . basename($d->file_path)) }}">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $d->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $ext }} &middot; {{ $d->created_at->format('d M Y') }}
                                &middot; {{ $d->caseFile->case_reference ?? ('Case #' . $d->case_file_id) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('carer.documents.download', $d) }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">
                                Download
                            </a>
                            <form method="POST" action="{{ route('carer.documents.destroy', $d) }}"
                                  onsubmit="return confirm('Delete this document?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-gray-500 hover:text-red-600 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:border-red-200 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>


<script>
    function initSearch(inputId, listId) {
        const input = document.getElementById(inputId);
        const list  = document.getElementById(listId);
        if (!input || !list) return;
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            list.querySelectorAll('[data-name]').forEach(el => {
                el.style.display = el.getAttribute('data-name').includes(q) ? '' : 'none';
            });
        });
    }
    initSearch('receivedSearch', 'receivedList');
    initSearch('sentSearch', 'sentList');
</script>

</x-app-layout>
