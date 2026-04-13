<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Wellbeing Check
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div 
                x-data="wellbeingCheck({{ $questions->toJson() }})"
                x-init="init()"
            >

                <div x-show="phase === 'intro'" x-transition>
                    <div class="bg-white rounded-xl shadow p-8 text-center space-y-4">
                        <div class="text-5xl">👋</div>
                        <h2 class="text-2xl font-semibold text-gray-800">How are you doing?</h2>
                        <div class="flex justify-center gap-3 text-xs text-gray-400 font-medium">
                            <span class="bg-gray-100 rounded-full px-3 py-1">⏱ About 3 minutes</span>
                            <span class="bg-gray-100 rounded-full px-3 py-1"
                                  x-text="`✦ ${questions.length || 8} questions`"></span>
                        </div>
                        <button
                            @click="begin()"
                            :disabled="loading"
                            class="mt-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-text="loading ? 'Getting your check ready…' : 'Get Started'"></span>
                        </button>
                    </div>
                </div>

                {{-- ── QUESTION ──────────────────────────────────── --}}
                <div x-show="phase === 'question'" x-transition>

                    {{-- Progress bar --}}
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-400 font-medium mb-1.5">
                            <span x-text="`Question ${currentIndex + 1} of ${questions.length}`"></span>
                            <span x-text="`${progressPct}%`"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500"
                                 :style="`width: ${progressPct}%`"
                                 role="progressbar"
                                 :aria-valuenow="progressPct"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    {{-- Question card --}}
                    <div class="bg-white rounded-xl shadow p-6 space-y-5">


                        {{-- Question text --}}
                        <p class="text-gray-800 font-medium text-lg leading-snug"
                           x-text="currentQuestion?.text"
                           aria-live="polite">
                        </p>

                        {{-- ── Slider ────────────────────────────── --}}
                        <div x-show="currentQuestion?.response_type === 'slider'" class="pt-2 pb-1 space-y-3">
                            <div class="flex justify-center h-12 items-end">
                                <span class="text-4xl leading-none" x-text="sliderEmoji" aria-hidden="true"></span>
                            </div>
                            <input
                                type="range"
                                class="w-full accent-indigo-600 cursor-pointer"
                                :min="currentQuestion?.min_value ?? 0"
                                :max="currentQuestion?.max_value ?? 10"
                                :value="currentRaw ?? midpoint"
                                @input="onSliderInput($event.target.value)"
                                :aria-label="currentQuestion?.text"
                                :aria-valuenow="currentRaw"
                            >
                            <div class="flex justify-between text-xs text-gray-400 font-medium">
                                <span>Not at all</span>
                                <span class="text-indigo-600 font-semibold" x-text="currentRaw ?? '–'"></span>
                                <span>Completely</span>
                            </div>
                        </div>

                        {{-- ── Emoji scale ───────────────────────── --}}
                        <div x-show="currentQuestion?.response_type === 'emoji_scale'"
                             class="flex justify-between gap-2"
                             role="radiogroup"
                             :aria-label="currentQuestion?.text">
                            <template x-for="opt in emojiOptions" :key="opt.value">
                                <button
                                    @click="pickValue(opt.value)"
                                    role="radio"
                                    :aria-checked="currentRaw === opt.value"
                                    :aria-label="opt.label"
                                    class="flex-1 flex flex-col items-center gap-1 py-3 rounded-lg border-2 transition-all"
                                    :class="currentRaw === opt.value
                                        ? 'border-indigo-500 bg-indigo-50'
                                        : currentRaw !== null
                                            ? 'border-gray-100 opacity-40'
                                            : 'border-gray-100 hover:border-indigo-300 hover:bg-indigo-50'"
                                >
                                    <span class="text-2xl" x-text="opt.emoji"></span>
                                    <span class="text-xs font-semibold text-gray-500" x-text="opt.label"></span>
                                </button>
                            </template>
                        </div>

                        {{-- ── Likert ────────────────────────────── --}}
                        <div x-show="currentQuestion?.response_type === 'likert_3' || currentQuestion?.response_type === 'likert_5'"
                             class="space-y-2"
                             role="radiogroup"
                             :aria-label="currentQuestion?.text">
                            <template x-for="opt in likertOptions" :key="opt.value">
                                <button
                                    @click="pickValue(opt.value)"
                                    role="radio"
                                    :aria-checked="currentRaw === opt.value"
                                    :aria-label="opt.label"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg border-2 text-left transition-all"
                                    :class="currentRaw === opt.value
                                        ? 'border-indigo-500 bg-indigo-50'
                                        : currentRaw !== null
                                            ? 'border-gray-100 opacity-40'
                                            : 'border-gray-100 hover:border-indigo-300 hover:bg-indigo-50'"
                                >
                                    <span class="w-4 h-4 rounded-full border-2 flex-shrink-0 transition-all"
                                          :class="currentRaw === opt.value
                                              ? 'border-indigo-500 bg-indigo-500'
                                              : 'border-gray-300'">
                                    </span>
                                    <span class="text-sm font-medium"
                                          :class="currentRaw === opt.value ? 'text-indigo-700' : 'text-gray-600'"
                                          x-text="opt.label">
                                    </span>
                                </button>
                            </template>
                        </div>

                    </div>{{-- /.card --}}

                    {{-- Navigation --}}
                    <div class="flex justify-between items-center mt-4">
                        <button
                            @click="goBack()"
                            :disabled="currentIndex === 0"
                            class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back
                        </button>
                        <button
                            @click="goNext()"
                            :disabled="currentRaw === null"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1"
                        >
                            <span x-text="isLastQuestion ? 'Finish' : 'Next'"></span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                </div>{{-- /.question --}}

                {{-- ── SUBMITTING ────────────────────────────────── --}}
                <div x-show="phase === 'submitting'" x-transition>
                    <div class="bg-white rounded-xl shadow p-10 text-center space-y-4">
                        <svg class="animate-spin w-10 h-10 text-indigo-500 mx-auto"
                             fill="none" viewBox="0 0 24 24"
                             role="status" aria-label="Saving your answers">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Saving your answers…</p>
                    </div>
                </div>

                {{-- ── COMPLETE ──────────────────────────────────── --}}
                <div x-show="phase === 'complete'" x-transition>
                    <div class="bg-white rounded-xl shadow p-8 text-center space-y-5">
                        <div class="text-5xl">🌟</div>
                        <h2 class="text-2xl font-semibold text-gray-800">All done!</h2>
                        <p class="text-gray-500 text-sm">Thanks for sharing how you're feeling.</p>

                        {{-- Domain score rings --}}
                        <div class="flex flex-wrap justify-center gap-4 pt-2">
                            <template x-for="ds in result.domain_scores" :key="ds.domain">
                                <div class="flex flex-col items-center gap-1">
                                    <svg width="72" height="72" viewBox="0 0 72 72" aria-hidden="true">
                                        <circle cx="36" cy="36" r="28" fill="none"
                                                stroke="#e5e7eb" stroke-width="6"/>
                                        <circle cx="36" cy="36" r="28" fill="none"
                                                :stroke="ringColour(ds.wb_score)"
                                                stroke-width="6"
                                                stroke-linecap="round"
                                                :stroke-dasharray="ringC"
                                                :stroke-dashoffset="ringOffset(ds.wb_score)"
                                                transform="rotate(-90 36 36)"
                                                style="transition: stroke-dashoffset 1s ease"/>
                                        <text x="36" y="41" text-anchor="middle"
                                              font-size="13" font-weight="700" fill="#374151"
                                              font-family="ui-sans-serif,system-ui,sans-serif"
                                              x-text="Math.round(ds.wb_score)">
                                        </text>
                                    </svg>
                                    <span class="text-xs text-gray-400 font-medium text-center leading-tight"
                                          style="max-width:64px"
                                          x-text="ds.domain">
                                    </span>
                                </div>
                            </template>
                        </div>

                        <a href="{{ route('dashboard') }}"
                           class="inline-block mt-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">
                            Back to home
                        </a>
                    </div>
                </div>

                {{-- ── ERROR ─────────────────────────────────────── --}}
                <div x-show="phase === 'error'" x-transition>
                    <div class="bg-white rounded-xl shadow p-8 text-center space-y-4">
                        <div class="text-5xl">😕</div>
                        <h2 class="text-xl font-semibold text-gray-800">Something went wrong</h2>
                        <p class="text-gray-500 text-sm" x-text="errorMsg"></p>
                        <button @click="retry()"
                                class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                            Try again
                        </button>
                    </div>
                </div>

                {{-- ── TOAST ─────────────────────────────────────── --}}
                <div x-show="toast"
                     x-transition
                     class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-full shadow-lg z-50 pointer-events-none"
                     role="alert"
                     aria-live="assertive"
                     x-text="toast">
                </div>

            </div>{{-- /x-data --}}
        </div>
    </div>

    @push('scripts')
    <script>
function wellbeingCheck(initialQuestions) {
    return {
        phase:        'intro',
        loading:      false,
        toast:        null,
        errorMsg:     '',
        checkId:      null,
        questions:    [],
        answers:      {},
        currentIndex: 0,
        result:       { domain_scores: [] },
        ringC:        2 * Math.PI * 28,

        init() {
            this.$watch('currentIndex', () => {
                this.initCurrentAnswer()
            })
        },

        initCurrentAnswer() {
            const q = this.currentQuestion
            if (!q || this.answers[q.id] !== undefined) return
            if (q.response_type === 'slider') {
                this.answers[q.id] = Math.round((q.min_value + q.max_value) / 2)
            }
        },

        get currentQuestion() {
            return this.questions[this.currentIndex] ?? null
        },
        get currentRaw() {
            if (!this.currentQuestion) return null
            const v = this.answers[this.currentQuestion.id]
            return v !== undefined ? v : null
        },
        get isLastQuestion() {
            return this.currentIndex === this.questions.length - 1
        },
        get progressPct() {
            if (!this.questions.length) return 0
            return Math.round(((this.currentIndex + 1) / this.questions.length) * 100)
        },
        get midpoint() {
            if (!this.currentQuestion) return 5
            const q = this.currentQuestion
            return Math.round((q.min_value + q.max_value) / 2)
        },
        get sliderFillPct() {
            if (!this.currentQuestion) return 50
            const q   = this.currentQuestion
            const raw = this.currentRaw ?? this.midpoint
            return ((raw - q.min_value) / (q.max_value - q.min_value)) * 100
        },
        get sliderEmoji() {
            const p = this.sliderFillPct
            if (p <= 15) return '😢'
            if (p <= 35) return '😕'
            if (p <= 55) return '😐'
            if (p <= 75) return '🙂'
            return '😄'
        },

        get likertOptions() {
            if (!this.currentQuestion) return []
            const q = this.currentQuestion
            const count = q.max_value - q.min_value + 1
            const labels = q.option_labels ?? (
                count <= 3
                    ? ['No', 'Sometimes', 'Yes']
                    : ['Never', 'Rarely', 'Sometimes', 'Often', 'Always']
            )
            return labels.map((label, i) => ({ label, value: q.min_value + i }))
        },

        get emojiOptions() {
            if (!this.currentQuestion) return []
            const q = this.currentQuestion
            const count = q.max_value - q.min_value + 1
            const defaultEmojis = count <= 3
                ? ['😢', '😐', '😄']
                : ['😢', '😕', '😐', '🙂', '😄']
            const labels = q.option_labels ?? (
                count <= 3
                    ? ['Not really', 'Sometimes', 'Yes!']
                    : ['Very bad', 'Not great', 'Okay', 'Good', 'Great']
            )
            return labels.map((label, i) => ({
                emoji: defaultEmojis[i],
                label,
                value: q.min_value + i,
            }))
        },

        async begin() {
            this.loading = true
            try {
                const data = await this.api('POST', '{{ route("child.wellbeing.start") }}')
                this.checkId = data.check_id
                this.questions = data.questions
                this.phase = 'question'
                this.initCurrentAnswer()
            } catch (e) {
                this.errorMsg = e.message
                this.phase = 'error'
            } finally {
                this.loading = false
            }
        },

        async submitCheck() {
            this.phase = 'submitting'
            try {
                const responses = Object.entries(this.answers).map(([question_id, raw_value]) => ({
                    question_id: parseInt(question_id),
                    raw_value,
                }))
                const data = await this.api(
                    'POST',
                    `/child/wellbeing/${this.checkId}/submit`,
                    { responses }
                )
                this.result = data
                this.phase = 'complete'
            } catch (e) {
                this.errorMsg = e.message
                this.phase = 'error'
            }
        },

        goNext() {
            if (this.currentRaw === null) return
            if (this.isLastQuestion) { this.submitCheck(); return }
            this.currentIndex++
        },

        goBack() {
            if (this.currentIndex > 0) this.currentIndex--
        },

        retry() { this.phase = 'intro' },

        onSliderInput(val) {
            if (this.currentQuestion)
                this.answers[this.currentQuestion.id] = parseInt(val)
        },

        pickValue(value) {
            if (this.currentQuestion)
                this.answers[this.currentQuestion.id] = value
        },

        ringColour(score) {
            if (score >= 70) return '#22c55e'
            if (score >= 45) return '#f59e0b'
            return '#ef4444'
        },
        ringOffset(score) {
            return this.ringC * (1 - score / 100)
        },

        domainBadgeClass(domain) {
            const map = {
                'Emotional':   'bg-pink-100 text-pink-700',
                'Behavioural': 'bg-amber-100 text-amber-700',
                'Social':      'bg-blue-100 text-blue-700',
                'Physical':    'bg-green-100 text-green-700',
                'Education':   'bg-purple-100 text-purple-700',
                'Safety':      'bg-red-100 text-red-700',
            }
            return map[domain] ?? 'bg-gray-100 text-gray-600'
        },

        showToast(msg) {
            this.toast = msg
            setTimeout(() => { this.toast = null }, 4500)
        },

        async api(method, url, body = null) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: body ? JSON.stringify(body) : null,
            })
            if (!res.ok) {
                const err = await res.json().catch(() => ({}))
                throw new Error(err.message ?? `HTTP ${res.status}`)
            }
            return res.json()
        },
    }
}
    </script>
    @endpush

</x-app-layout>
