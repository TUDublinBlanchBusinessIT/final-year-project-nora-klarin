<x-app-layout>

    <x-slot name="header">

        <div class="flex items-start justify-between gap-4">

            <div>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Wellbeing, Documents & Forms</h2>

                <p class="text-sm text-gray-500">Upload case documents, view shared files, and send wellbeing updates</p>

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



    <div class="py-10 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">



            {{-- Hero --}}

            <div class="rounded-2xl p-6 bg-indigo-700 bg-gradient-to-r from-indigo-600 to-sky-500 shadow-sm text-white">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                    <div>

                        <div class="text-sm opacity-90">Secure document exchange</div>

                        <div class="text-2xl font-bold mt-1">Carer Wellbeing, Documents & Forms</div>

                        <div class="text-sm opacity-90 mt-2">

                            Share documents with the social worker, view shared files, and record wellbeing updates.

                        </div>

                    </div>



                    <div class="flex flex-wrap gap-3">

                        <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur px-4 py-3">

                            <div class="text-xs opacity-90">Sent by you</div>

                            <div class="text-lg font-semibold">{{ $sentDocs->count() }}</div>

                        </div>



                        <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur px-4 py-3">

                            <div class="text-xs opacity-90">Shared with you</div>

                            <div class="text-lg font-semibold">{{ $receivedDocs->count() }}</div>

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



            {{-- Tabs --}}

            <div class="rounded-2xl bg-white border shadow-sm p-3">

                <div class="flex flex-wrap gap-2">

                    <button type="button" onclick="showSection('upload', this)"

                            class="tab-btn px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">

                        Upload

                    </button>



                    <button type="button" onclick="showSection('received', this)"

                            class="tab-btn px-4 py-2 rounded-xl bg-white border text-sm font-semibold text-gray-700">

                        Shared With Me

                    </button>



                    <button type="button" onclick="showSection('sent', this)"

                            class="tab-btn px-4 py-2 rounded-xl bg-white border text-sm font-semibold text-gray-700">

                        Sent By Me

                    </button>



                    <button type="button" onclick="showSection('wellbeing', this)"

                            class="tab-btn px-4 py-2 rounded-xl bg-white border text-sm font-semibold text-gray-700">

                        Wellbeing Update

                    </button>

                </div>

            </div>



            {{-- Upload Section --}}

            <div id="upload" class="section">

                <div class="rounded-2xl border bg-white shadow-sm overflow-hidden max-w-xl">

                    <div class="p-5 border-b bg-gray-50">

                        <div class="flex items-center gap-2">

                            <span class="text-lg">📤</span>

                            <h3 class="font-semibold text-gray-900">Upload Document</h3>

                        </div>

                        <p class="text-xs text-gray-500 mt-1">Send a PDF to the social worker</p>

                    </div>



                    <form method="POST" action="{{ route('carer.documents.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">

                        @csrf



                        <div>

                            <label class="text-sm text-gray-700 font-medium">Case File</label>

                            <select name="case_file_id"

                                    class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"

                                    required>

                                <option value="">Select case file</option>

                                @foreach($caseFiles as $caseFile)

                                    <option value="{{ $caseFile->id }}" @selected(old('case_file_id') == $caseFile->id)>

                                        Case File #{{ $caseFile->id }}

                                    </option>

                                @endforeach

                            </select>

                        </div>



                        <div>

                            <label class="text-sm text-gray-700 font-medium">Title</label>

                            <input name="title"

                                   class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"

                                   placeholder="e.g. Consent Form, Care Plan"

                                   value="{{ old('title') }}"

                                   required>

                        </div>



                        <div>

                            <label class="text-sm text-gray-700 font-medium">PDF File</label>



                            <div class="mt-2 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">

                                <div class="text-center">

                                    <div class="text-2xl">📄</div>

                                    <div class="text-sm text-gray-700 font-medium mt-2">Choose a PDF to upload</div>

                                    <div class="text-xs text-gray-500 mt-1">This will be linked to the selected case file.</div>



                                    <input type="file" name="file" accept="application/pdf"

                                           class="mt-4 block w-full text-sm" required>

                                </div>

                            </div>

                        </div>



                        <button

                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">

                            <span>Send to Social Worker</span>

                            <span>→</span>

                        </button>



                        <p class="text-[11px] text-gray-500 text-center">

                            Tip: Use clear titles so files are easy to find.

                        </p>

                    </form>

                </div>

            </div>



            {{-- Received Section --}}

            <div id="received" class="section hidden">

                <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">

                    <div class="p-5 border-b">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="text-lg">📥</span>

                                    <h3 class="font-semibold text-gray-900">Shared by Social Worker</h3>

                                </div>

                                <p class="text-xs text-gray-500 mt-1">Documents shared with you.</p>

                            </div>



                            <div class="relative">

                                <input id="receivedSearch" type="text"

                                       class="w-full sm:w-64 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"

                                       placeholder="Search received files…">

                            </div>

                        </div>

                    </div>



                    @if($receivedDocs->isEmpty())

                        <div class="p-8 text-center bg-gray-50">

                            <div class="text-3xl">📭</div>

                            <div class="text-lg font-semibold text-gray-900 mt-3">No shared documents yet</div>

                            <div class="text-sm text-gray-600 mt-1">Documents from the social worker will appear here.</div>

                        </div>

                    @else

                        <div id="receivedList" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50">

                            @foreach($receivedDocs as $d)

                                @php

                                    $display = $d->title;

                                    $when = $d->created_at->format('D d M, H:i');

                                    $type = strtoupper($d->file_type ?? 'PDF');

                                    $filename = basename($d->file_path);

                                @endphp



                                <div class="rounded-2xl border bg-white p-4 hover:shadow-sm transition"

                                     data-name="{{ strtolower($display . ' ' . $filename) }}">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="flex items-start gap-3 min-w-0">

                                            <div class="h-11 w-11 rounded-2xl bg-green-50 text-green-700 flex items-center justify-center text-xl shrink-0">

                                                📄

                                            </div>



                                            <div class="min-w-0">

                                                <div class="font-semibold text-gray-900 truncate">

                                                    {{ $display }}

                                                </div>

                                                <div class="text-xs text-gray-500 truncate mt-1">

                                                    {{ $filename }}

                                                </div>



                                                <div class="mt-2 flex flex-wrap gap-2">

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        {{ $type }}

                                                    </span>

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        {{ $when }}

                                                    </span>

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        Case #{{ $d->case_file_id }}

                                                    </span>

                                                </div>

                                            </div>

                                        </div>



                                        <div class="shrink-0">

                                            <a href="{{ route('carer.documents.download', $d) }}"

                                               class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-gray-900 text-white text-sm hover:bg-gray-800">

                                                Download

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>

            </div>



            {{-- Sent Section --}}

            <div id="sent" class="section hidden">

                <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">

                    <div class="p-5 border-b">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="text-lg">🗂️</span>

                                    <h3 class="font-semibold text-gray-900">Sent by You</h3>

                                </div>

                                <p class="text-xs text-gray-500 mt-1">Documents you uploaded for the case.</p>

                            </div>



                            <div class="relative">

                                <input id="sentSearch" type="text"

                                       class="w-full sm:w-64 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"

                                       placeholder="Search your files…">

                            </div>

                        </div>

                    </div>



                    @if($sentDocs->isEmpty())

                        <div class="p-8 text-center bg-gray-50">

                            <div class="text-3xl">📁</div>

                            <div class="text-lg font-semibold text-gray-900 mt-3">No uploaded documents yet</div>

                            <div class="text-sm text-gray-600 mt-1">Upload your first PDF in the Upload tab.</div>

                        </div>

                    @else

                        <div id="sentList" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50">

                            @foreach($sentDocs as $d)

                                @php

                                    $display = $d->title;

                                    $when = $d->created_at->format('D d M, H:i');

                                    $type = strtoupper($d->file_type ?? 'PDF');

                                    $filename = basename($d->file_path);

                                @endphp



                                <div class="rounded-2xl border bg-white p-4 hover:shadow-sm transition"

                                     data-name="{{ strtolower($display . ' ' . $filename) }}">

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

                                                    {{ $filename }}

                                                </div>



                                                <div class="mt-2 flex flex-wrap gap-2">

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        {{ $type }}

                                                    </span>

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        {{ $when }}

                                                    </span>

                                                    <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">

                                                        Case #{{ $d->case_file_id }}

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

                    @endif

                </div>

            </div>



            {{-- Wellbeing Section --}}

            <div id="wellbeing" class="section hidden">

                <div class="rounded-3xl p-8 shadow-lg bg-white border border-green-100">

                    <div class="mb-6">

                        <h3 class="text-xl font-extrabold text-green-700">Young Person Wellbeing Update</h3>

                        <p class="text-sm text-gray-600 mt-1">

                            Complete this update for the social worker based on the young person’s week at home, school, and daily life.

                        </p>

                    </div>



                    <form method="POST" action="{{ route('carer.wellbeing.store') }}">

                        @csrf



                        <input type="hidden" name="case_file_id" value="{{ $case->id ?? 1 }}">



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>

                                <label class="block text-sm font-medium text-gray-700">Overall wellbeing (1–10)</label>

                                <input type="number" name="overall_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                                <p class="text-xs text-gray-500 mt-1">1 = very poor, 10 = very positive</p>

                            </div>



                            <div>

                                <label class="block text-sm font-medium text-gray-700">Emotional wellbeing (1–10)</label>

                                <input type="number" name="emotional_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>



                            <div>

                                <label class="block text-sm font-medium text-gray-700">Behaviour at home (1–10)</label>

                                <input type="number" name="behavioural_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>



                            <div>

                                <label class="block text-sm font-medium text-gray-700">Physical health (1–10)</label>

                                <input type="number" name="physical_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>



                            <div>

                                <label class="block text-sm font-medium text-gray-700">Feeling safe (1–10)</label>

                                <input type="number" name="safety_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>



                            <div>

                                <label class="block text-sm font-medium text-gray-700">School / daily routine (1–10)</label>

                                <input type="number" name="school_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>



                            <div class="md:col-span-2">

                                <label class="block text-sm font-medium text-gray-700">Relationships / social interaction (1–10)</label>

                                <input type="number" name="relationship_score" min="1" max="10"

                                       class="mt-1 w-full border rounded-xl p-3" required>

                            </div>

                        </div>



                        <div class="mt-5">

                            <label class="block text-sm font-medium text-gray-700">Carer notes / observations</label>

                            <textarea name="journal_notes" rows="5"

                                      class="mt-1 w-full border rounded-xl p-3"

                                      placeholder="Example: Settled well this week, attended school regularly, seemed anxious on Tuesday, responded well to reassurance."></textarea>

                        </div>



                        <div class="mt-6 flex justify-end">

                            <button type="submit"

                                    class="inline-flex items-center px-6 py-3 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 shadow-sm">

                                Send Update to Social Worker

                            </button>

                        </div>

                    </form>

                </div>

            </div>



        </div>

    </div>



    <script>

        function showSection(sectionId, btn) {

            document.querySelectorAll('.section').forEach(section => {

                section.classList.add('hidden');

            });



            document.getElementById(sectionId).classList.remove('hidden');



            document.querySelectorAll('.tab-btn').forEach(button => {

                button.classList.remove('bg-indigo-600', 'text-white');

                button.classList.add('bg-white', 'border', 'text-gray-700');

            });



            btn.classList.add('bg-indigo-600', 'text-white');

            btn.classList.remove('bg-white', 'border', 'text-gray-700');

        }



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

