<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            Wellbeing Check

        </h2>

    </x-slot>



    @php

        $flatQuestions = collect();



        foreach ($questions as $domainName => $domainQuestions) {

            foreach ($domainQuestions as $question) {

                $labels = $question->option_labels;



                if (is_string($labels)) {

                    $decoded = json_decode($labels, true);

                    if (json_last_error() === JSON_ERROR_NONE) {

                        $labels = $decoded;

                    }

                }



                $flatQuestions->push([

                    'id' => $question->id,

                    'text' => $question->text ?? $question->question_text ?? 'Untitled question',

                    'domain' => $domainName,

                    'response_type' => $question->response_type ?? 'likert_5',

                    'min_value' => $question->min_value ?? 0,

                    'max_value' => $question->max_value ?? 4,

                    'option_labels' => $labels,

                ]);

            }

        }



        $flatQuestions = $flatQuestions->values();

    @endphp



    <div class="py-6">

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div

                x-data="wellbeingCheck(@js($flatQuestions))"

                x-init="init()"

            >

                <div x-show="phase === 'intro'" x-transition>

                    <div class="bg-white rounded-xl shadow p-8 text-center space-y-4">

                        <div class="text-5xl">👋</div>

                        <h2 class="text-2xl font-semibold text-gray-800">How are you doing?</h2>

                        <div class="flex justify-center gap-3 text-xs text-gray-400 font-medium">

                            <span class="bg-gray-100 rounded-full px-3 py-1">⏱ About 3 minutes</span>

                            <span class="bg-gray-100 rounded-full px-3 py-1" x-text="`✦ ${questions.length} questions`"></span>

                        </div>

                        <button

                            type="button"

                            @click="begin()"

                            class="mt-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition"

                        >

                            Get Started

                        </button>

                    </div>

                </div>



                <div x-show="phase === 'question'" x-transition>

                    <div class="mb-4">

                        <div class="flex justify-between text-xs text-gray-400 font-medium mb-1.5">

                            <span x-text="`Question ${currentIndex + 1} of ${questions.length}`"></span>

                            <span x-text="`${progressPct}%`"></span>

                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-2">

                            <div

                                class="bg-indigo-500 h-2 rounded-full transition-all duration-500"

                                :style="`width: ${progressPct}%`"

                            ></div>

                        </div>

                    </div>



                    <div class="bg-white rounded-xl shadow p-6 space-y-5">

                        <div class="flex items-center justify-between">

                            <span

                                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold"

                                :class="domainBadgeClass(currentQuestion?.domain)"

                                x-text="currentQuestion?.domain"

                            ></span>

                        </div>



                        <p

                            class="text-gray-800 font-medium text-lg leading-snug"

                            x-text="currentQuestion?.text"

                        ></p>



                        <div class="space-y-2">

                            <template x-for="opt in likertOptions" :key="opt.value">

                                <label

                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg border-2 text-left transition-all cursor-pointer"

                                    :class="currentRaw == opt.value

                                        ? 'border-indigo-500 bg-indigo-50'

                                        : 'border-gray-100 hover:border-indigo-300 hover:bg-indigo-50'"

                                >

                                    <input

                                        type="radio"

                                        class="hidden"

                                        :name="`question_${currentQuestion.id}`"

                                        :value="opt.value"

                                        x-model="answers[currentQuestion.id]"

                                    >



                                    <span

                                        class="w-4 h-4 rounded-full border-2 flex-shrink-0 transition-all"

                                        :class="currentRaw == opt.value

                                            ? 'border-indigo-500 bg-indigo-500'

                                            : 'border-gray-300'"

                                    ></span>



                                    <span

                                        class="text-sm font-medium"

                                        :class="currentRaw == opt.value ? 'text-indigo-700' : 'text-gray-600'"

                                        x-text="opt.label"

                                    ></span>

                                </label>

                            </template>

                        </div>

                    </div>



                    <div class="flex justify-between items-center mt-4">

                        <button

                            type="button"

                            @click="goBack()"

                            :disabled="currentIndex === 0"

                            class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"

                        >

                            ← Back

                        </button>



                        <button

                            type="button"

                            @click="goNext()"

                            :disabled="currentRaw === null"

                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-40 disabled:cursor-not-allowed"

                        >

                            <span x-text="isLastQuestion ? 'Finish' : 'Next'"></span>

                        </button>

                    </div>

                </div>



                <div x-show="phase === 'complete'" x-transition>

                    <div class="bg-white rounded-xl shadow p-8 text-center space-y-5">

                        <div class="text-5xl">🌟</div>

                        <h2 class="text-2xl font-semibold text-gray-800">All done!</h2>

                        <p class="text-gray-500 text-sm">Thanks for sharing how you're feeling.</p>



                        <div class="flex flex-wrap justify-center gap-4 pt-2">

                            <template x-for="ds in previewDomainScores" :key="ds.domain">

                                <div class="flex flex-col items-center gap-1">

                                    <svg width="72" height="72" viewBox="0 0 72 72">

                                        <circle cx="36" cy="36" r="28" fill="none" stroke="#e5e7eb" stroke-width="6"/>

                                        <circle

                                            cx="36"

                                            cy="36"

                                            r="28"

                                            fill="none"

                                            :stroke="ringColour(ds.wb_score)"

                                            stroke-width="6"

                                            stroke-linecap="round"

                                            :stroke-dasharray="ringC"

                                            :stroke-dashoffset="ringOffset(ds.wb_score)"

                                            transform="rotate(-90 36 36)"

                                            style="transition: stroke-dashoffset 1s ease"

                                        />

                                        <text

                                            x="36"

                                            y="41"

                                            text-anchor="middle"

                                            font-size="13"

                                            font-weight="700"

                                            fill="#374151"

                                            font-family="ui-sans-serif,system-ui,sans-serif"

                                            x-text="Math.round(ds.wb_score)"

                                        ></text>

                                    </svg>

                                    <span

                                        class="text-xs text-gray-400 font-medium text-center leading-tight"

                                        style="max-width:64px"

                                        x-text="ds.domain"

                                    ></span>

                                </div>

                            </template>

                        </div>



                        <form method="POST" action="{{ route('child.wellbeing.submit') }}">

                            @csrf

                            <template x-for="(value, questionId) in answers" :key="questionId">

                                <input type="hidden" :name="`question_${questionId}`" :value="value">

                            </template>



                            <button

                                type="submit"

                                class="inline-block mt-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition"

                            >

                                Submit Check

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <script>

        function wellbeingCheck(initialQuestions) {

            return {

                phase: 'intro',

                questions: initialQuestions || [],

                answers: {},

                currentIndex: 0,

                ringC: 2 * Math.PI * 28,



                init() {

                    console.log('Wellbeing wizard loaded', this.questions);

                },



                begin() {

                    this.phase = 'question';

                },



                get currentQuestion() {

                    return this.questions[this.currentIndex] ?? null;

                },



                get currentRaw() {

                    if (!this.currentQuestion) return null;

                    const v = this.answers[this.currentQuestion.id];

                    return v !== undefined ? v : null;

                },



                get isLastQuestion() {

                    return this.currentIndex === this.questions.length - 1;

                },



                get progressPct() {

                    if (!this.questions.length) return 0;

                    return Math.round(((this.currentIndex + 1) / this.questions.length) * 100);

                },



                get likertOptions() {

                    if (!this.currentQuestion) return [];



                    const q = this.currentQuestion;

                    const count = (q.max_value - q.min_value + 1);



                    let labels = q.option_labels;



                    if (!Array.isArray(labels) || !labels.length) {

                        labels = count <= 3

                            ? ['No', 'Sometimes', 'Yes']

                            : ['At no time', 'Some of the time', 'Less than half the time', 'Most of the time', 'All the time'];

                    }



                    return labels.map((label, i) => ({

                        label,

                        value: q.min_value + i

                    }));

                },



                get previewDomainScores() {

                    const grouped = {};



                    this.questions.forEach(q => {

                        const value = this.answers[q.id];

                        if (value === undefined || value === null) return;



                        if (!grouped[q.domain]) grouped[q.domain] = [];



                        const min = Number(q.min_value ?? 0);

                        const max = Number(q.max_value ?? 4);

                        const normalised = max > min

                            ? ((Number(value) - min) / (max - min)) * 100

                            : 0;



                        grouped[q.domain].push(normalised);

                    });



                    return Object.entries(grouped).map(([domain, scores]) => ({

                        domain,

                        wb_score: scores.length

                            ? scores.reduce((a, b) => a + b, 0) / scores.length

                            : 0

                    }));

                },



                goNext() {

                    if (this.currentRaw === null) return;



                    if (this.isLastQuestion) {

                        this.phase = 'complete';

                        return;

                    }



                    this.currentIndex++;

                },



                goBack() {

                    if (this.currentIndex > 0) {

                        this.currentIndex--;

                    }

                },



                ringColour(score) {

                    if (score >= 70) return '#22c55e';

                    if (score >= 45) return '#f59e0b';

                    return '#ef4444';

                },



                ringOffset(score) {

                    return this.ringC * (1 - score / 100);

                },



                domainBadgeClass(domain) {

                    const map = {

                        'Emotional': 'bg-pink-100 text-pink-700',

                        'Behavioural': 'bg-amber-100 text-amber-700',

                        'Social': 'bg-blue-100 text-blue-700',

                        'Physical': 'bg-green-100 text-green-700',

                        'Education': 'bg-purple-100 text-purple-700',

                        'Safety': 'bg-red-100 text-red-700',

                        'Life Satisfaction': 'bg-orange-100 text-orange-700',

                    };



                    return map[domain] ?? 'bg-gray-100 text-gray-600';

                }

            }

        }

    </script>

</x-app-layout>