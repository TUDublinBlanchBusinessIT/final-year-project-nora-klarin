<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Wellbeing and Documents
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage shared documents and send wellbeing updates
                </p>
            </div>

            <a href="{{ route('carer.dashboard') }}"
               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow border border-gray-200">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div x-data="{ tab: 'upload' }" x-cloak class="space-y-6">

                {{-- Tabs --}}
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-2">
                    <nav class="flex flex-wrap gap-2">
                        <button
                            @click="tab = 'upload'"
                            :class="tab === 'upload' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                            Upload
                        </button>

                        <button
                            @click="tab = 'received'"
                            :class="tab === 'received' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                            Shared With Me
                        </button>

                        <button
                            @click="tab = 'sent'"
                            :class="tab === 'sent' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                            Sent By Me
                        </button>

                        <button
                            @click="tab = 'wellbeing'"
                            :class="tab === 'wellbeing' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                            Wellbeing Update
                        </button>
                    </nav>
                </div>

                {{-- Upload --}}
                <div x-show="tab === 'upload'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-xl bg-indigo-50 p-4">
                            <p class="text-sm text-gray-500">Sent by you</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $sentDocs->count() }}</p>
                        </div>

                        <div class="rounded-xl bg-sky-50 p-4">
                            <p class="text-sm text-gray-500">Shared with you</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $receivedDocs->count() }}</p>
                        </div>

                        <div class="rounded-xl bg-green-50 p-4">
                            <p class="text-sm text-gray-500">Case files</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $caseFiles->count() }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-indigo-700">Upload Document</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Send a PDF document linked to one of your case files
                        </p>
                    </div>

                    <form method="POST" action="{{ route('carer.documents.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Case File</label>
                            <select name="case_file_id"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Select case file</option>
                                @foreach($caseFiles as $caseFile)
                                    <option value="{{ $caseFile->id }}" @selected(old('case_file_id') == $caseFile->id)>
                                        {{ $caseFile->case_reference ?? ('Case File #' . $caseFile->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="e.g. Consent Form, Care Plan"
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">PDF File</label>
                            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center">
                                <div class="text-3xl">📄</div>
                                <p class="text-sm font-medium text-gray-800 mt-2">Choose a PDF to upload</p>
                                <p class="text-xs text-gray-500 mt-1">This will be linked to the selected case file.</p>
                                <input type="file"
                                       name="file"
                                       accept="application/pdf"
                                       class="mt-4 block w-full text-sm"
                                       required>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Only PDF files are allowed.</p>
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 shadow-sm">
                                Send to Social Worker
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Received --}}
                <div x-show="tab === 'received'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-sky-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-sky-700">Shared With Me</h3>
                            <p class="text-sm text-gray-500 mt-1">Documents shared by the social worker</p>
                        </div>

                        <div>
                            <input id="receivedSearch" type="text"
                                   class="w-full sm:w-64 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Search received files…">
                        </div>
                    </div>

                    @if($receivedDocs->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <p class="text-gray-500">No shared documents yet.</p>
                        </div>
                    @else
                        <div id="receivedList" class="space-y-3">
                            @foreach($receivedDocs as $d)
                                @php
                                    $display = $d->title;
                                    $when = $d->created_at->format('D d M, H:i');
                                    $type = strtoupper($d->file_type ?? 'PDF');
                                    $filename = basename($d->file_path);
                                @endphp

                                <div class="rounded-2xl border border-sky-100 bg-sky-50 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                                     data-name="{{ strtolower($display . ' ' . $filename) }}">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $display }}</p>
                                        <p class="text-xs text-gray-500 truncate mt-1">{{ $filename }}</p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $type }}
                                            </span>
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $when }}
                                            </span>
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $d->caseFile->case_reference ?? ('Case #' . $d->case_file_id) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <a href="{{ route('carer.documents.download', $d) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-sky-600 text-white text-sm hover:bg-sky-700">
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Sent --}}
                <div x-show="tab === 'sent'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-indigo-700">Sent By Me</h3>
                            <p class="text-sm text-gray-500 mt-1">Documents you uploaded for your case files</p>
                        </div>

                        <div>
                            <input id="sentSearch" type="text"
                                   class="w-full sm:w-64 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Search your files…">
                        </div>
                    </div>

                    @if($sentDocs->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <p class="text-gray-500">No uploaded documents yet.</p>
                        </div>
                    @else
                        <div id="sentList" class="space-y-3">
                            @foreach($sentDocs as $d)
                                @php
                                    $display = $d->title;
                                    $when = $d->created_at->format('D d M, H:i');
                                    $type = strtoupper($d->file_type ?? 'PDF');
                                    $filename = basename($d->file_path);
                                @endphp

                                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                                     data-name="{{ strtolower($display . ' ' . $filename) }}">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $display }}</p>
                                        <p class="text-xs text-gray-500 truncate mt-1">{{ $filename }}</p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $type }}
                                            </span>
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $when }}
                                            </span>
                                            <span class="text-[11px] px-2 py-1 rounded-full bg-white text-gray-700 border">
                                                {{ $d->caseFile->case_reference ?? ('Case #' . $d->case_file_id) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 shrink-0">
                                        <a href="{{ route('carer.documents.download', $d) }}"
                                           class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                            Download
                                        </a>

                                        <form method="POST"
                                              action="{{ route('carer.documents.destroy', $d) }}"
                                              onsubmit="return confirm('Delete this document?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="px-4 py-2 rounded-xl bg-white text-gray-700 text-sm border hover:bg-gray-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Wellbeing --}}
                <div x-show="tab === 'wellbeing'" x-transition class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-emerald-700">Wellbeing Update</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Complete this update for the social worker based on the young person’s recent wellbeing
                        </p>
                    </div>

                    <form method="POST" action="{{ route('carer.wellbeing.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Case File</label>
                                <select name="case_file_id"
                                        class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                        required>
                                    <option value="">Select case file</option>
                                    @foreach($caseFiles as $caseFile)
                                        <option value="{{ $caseFile->id }}" @selected(old('case_file_id', $case->id ?? null) == $caseFile->id)>
                                            {{ $caseFile->case_reference ?? ('Case File #' . $caseFile->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Overall wellbeing (1–10)</label>
                                <input type="number" name="overall_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">1 = very poor, 10 = very positive</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emotional wellbeing (1–10)</label>
                                <input type="number" name="emotional_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Behaviour at home (1–10)</label>
                                <input type="number" name="behavioural_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Physical health (1–10)</label>
                                <input type="number" name="physical_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Feeling safe (1–10)</label>
                                <input type="number" name="safety_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">School / daily routine (1–10)</label>
                                <input type="number" name="school_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Relationships / social interaction (1–10)</label>
                                <input type="number" name="relationship_score" min="1" max="10"
                                       class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       required>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carer notes / observations</label>
                            <textarea name="journal_notes" rows="5"
                                      class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                      placeholder="Example: Settled well this week, attended school regularly, seemed anxious on Tuesday, responded well to reassurance."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 shadow-sm">
                                Send Update to Social Worker
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        const receivedSearch = document.getElementById('receivedSearch');
        const receivedList = document.getElementById('receivedList');

        if (receivedSearch && receivedList) {
            receivedSearch.addEventListener('input', () => {
                const q = receivedSearch.value.toLowerCase();
                receivedList.querySelectorAll('[data-name]').forEach(card => {
                    card.style.display = card.getAttribute('data-name').includes(q) ? '' : 'none';
                });
            });
        }

        const sentSearch = document.getElementById('sentSearch');
        const sentList = document.getElementById('sentList');

        if (sentSearch && sentList) {
            sentSearch.addEventListener('input', () => {
                const q = sentSearch.value.toLowerCase();
                sentList.querySelectorAll('[data-name]').forEach(card => {
                    card.style.display = card.getAttribute('data-name').includes(q) ? '' : 'none';
                });
            });
        }
    </script>
</x-app-layout>