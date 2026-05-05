<x-app-layout>

<x-slot name="header">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('carer.cases.show', $case) }}" class="hover:text-gray-700">
                    {{ $case->case_reference ?? ('Case #' . $case->id) }}
                </a>
                <span>/</span>
                <span class="text-gray-900">Wellbeing update</span>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">
                Wellbeing update — {{ $case->youngPerson->name ?? 'Young person' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Based on your observations over the past week
            </p>
        </div>
        <a href="{{ route('carer.cases.show', $case) }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition shrink-0">
            ← Back to case
        </a>
    </div>
</x-slot>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-800 mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-800 mb-4">
        <p class="font-medium mb-1">Please fix the following:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- HBSC context banner --}}
<div class="bg-indigo-50 border border-indigo-100 rounded-xl px-5 py-4 mb-5 flex gap-4">
    <div class="shrink-0 mt-0.5">
        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
    </div>
    <div>
        <p class="text-sm font-semibold text-indigo-800 mb-1">Observation-based assessment</p>
        <p class="text-xs text-indigo-600 leading-relaxed">
            These questions are based on the Health Behaviour in School-aged Children (HBSC) proxy-report methodology.
            Each question asks you to describe what you have <strong>observed</strong> about the child's behaviour and
            wellbeing over the past 7 days. Select the option that best reflects your observations —
            there are no right or wrong answers. Your responses are used by the social worker to monitor
            wellbeing trends and identify any areas needing support.
        </p>
    </div>
</div>

<form method="POST" action="{{ route('carer.proxy.wellbeing.store') }}" x-data="proxyForm()" class="space-y-4">
    @csrf
    <input type="hidden" name="case_file_id" value="{{ $case->id }}">

    {{-- Domain questions --}}
    @php
        $domains = [
            'emotional' => [
                'label'   => 'Emotional wellbeing',
                'icon'    => '💛',
                'hbsc'    => 'Based on HBSC subjective health complaint items',
                'question'=> "How would you describe this child's general mood over the past week?",
                'options' => ['Very low', 'Mostly low', 'Mixed', 'Mostly positive', 'Very positive'],
                'followup'=> "Have you noticed signs of anxiety, worry, or distress?",
                'fOptions'=> ['Most of the time', 'Often', 'Sometimes', 'Rarely', 'Not at all'],
                'fField'  => 'notes_emotional',
            ],
            'behavioural' => [
                'label'   => 'Behaviour',
                'icon'    => '🔵',
                'hbsc'    => 'Based on HBSC conduct and peer problem items',
                'question'=> "How has the child's behaviour been at home over the past week?",
                'options' => ['Very difficult', 'Frequent difficulties', 'Some difficulties', 'Mostly settled', 'Very settled'],
                'followup'=> null,
                'fField'  => null,
            ],
            'social' => [
                'label'   => 'Social & relationships',
                'icon'    => '🟢',
                'hbsc'    => 'Based on HBSC peer relationship and family communication items',
                'question'=> "How well is the child interacting with others (family, peers)?",
                'options' => ['Withdrawn', 'Poor', 'Some difficulty', 'Well', 'Very well'],
                'followup'=> null,
                'fField'  => null,
            ],
            'physical' => [
                'label'   => 'Physical health',
                'icon'    => '🟣',
                'hbsc'    => 'Based on HBSC physical health complaint and sleep items',
                'question'=> "How would you describe the child's sleep, appetite, and physical health this week?",
                'options' => ['Very poor', 'Poor', 'Fair', 'Good', 'Very good'],
                'followup'=> null,
                'fField'  => null,
            ],
            'education' => [
                'label'   => 'School & daily routine',
                'icon'    => '📚',
                'hbsc'    => 'Based on HBSC school attendance and liking school items',
                'question'=> "Is the child attending school or following their daily routine?",
                'options' => ['Not attending', 'Frequent absences', 'Some absences', 'Mostly attending', 'Full attendance'],
                'followup'=> null,
                'fField'  => null,
            ],
            'safety' => [
                'label'   => 'Safety',
                'icon'    => '🔴',
                'hbsc'    => 'Based on WHO safeguarding observation criteria',
                'question'=> "Do you have any concerns about the child's safety or wellbeing at home?",
                'options' => ['Immediate concern', 'Significant concern', 'Moderate concern', 'Minor concern', 'No concerns'],
                'followup'=> "Has the child disclosed anything worrying to you this week?",
                'fOptions'=> ['Yes — serious concern', 'Yes — something concerning', 'Minor mention', 'Not really', 'Nothing disclosed'],
                'fField'  => 'notes_safety',
            ],
            'life satisfaction' => [
                'label'   => 'Life satisfaction',
                'icon'    => '⭐',
                'hbsc'    => 'Based on HBSC Cantril life satisfaction ladder (proxy-adapted)',
                'question'=> "How does the child seem to feel about their life overall?",
                'options' => ['Very unhappy', 'Mostly unhappy', 'Mixed', 'Mostly happy', 'Very happy'],
                'followup'=> null,
                'fField'  => null,
            ],
        ];
        $domainKeys = array_keys($domains);
        $total = count($domainKeys);
    @endphp

    {{-- Progress bar --}}
    <div class="bg-white border border-gray-100 rounded-xl px-5 py-3 flex items-center gap-4">
        <span class="text-xs font-medium text-gray-500 shrink-0">Progress</span>
        <div class="flex-1 bg-gray-100 rounded-full h-2">
            <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300"
                 :style="'width: ' + Math.round((answered / {{ $total }}) * 100) + '%'"></div>
        </div>
        <span class="text-xs font-medium text-indigo-600 shrink-0" x-text="answered + ' / {{ $total }}'"></span>
    </div>

    @foreach($domains as $domainKey => $domain)
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden"
         x-bind:class="responses['{{ $domainKey }}'] !== undefined ? 'ring-1 ring-indigo-200' : ''">

        {{-- Domain header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-3">
            <span style="font-size:18px">{{ $domain['icon'] }}</span>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">{{ $domain['label'] }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $domain['hbsc'] }}</p>
            </div>
            {{-- Checkmark when answered --}}
            <div x-show="responses['{{ $domainKey }}'] !== undefined"
                 class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
        </div>

        <div class="px-5 py-4 space-y-4">
            {{-- Main question --}}
            <div>
                <p class="text-sm text-gray-800 font-medium mb-3">{{ $domain['question'] }}</p>
                <div class="space-y-2">
                    @foreach($domain['options'] as $i => $option)
                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition
                                  hover:border-indigo-300 hover:bg-indigo-50"
                           x-bind:class="responses['{{ $domainKey }}'] == {{ $i }}
                               ? 'border-indigo-400 bg-indigo-50'
                               : 'border-gray-100 bg-white'">
                        <input type="radio"
                               name="responses[{{ $domainKey }}]"
                               value="{{ $i }}"
                               x-model="responses['{{ $domainKey }}']"
                               @change="onAnswer('{{ $domainKey }}')"
                               class="shrink-0 accent-indigo-600">
                        {{-- Visual scale indicator --}}
                        <span class="w-2 h-2 rounded-full shrink-0"
                              style="background: {{ ['#ef4444','#f97316','#eab308','#84cc16','#22c55e'][$i] }}"></span>
                        <span class="text-sm text-gray-700">{{ $option }}</span>
                        {{-- Score hint --}}
                        <span class="ml-auto text-[11px] text-gray-300 shrink-0">{{ $i * 25 }}/100</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Follow-up question (Emotional + Safety only) --}}
            @if($domain['followup'])
            <div x-show="responses['{{ $domainKey }}'] !== undefined" x-transition class="pt-2 border-t border-gray-50">
                <p class="text-sm text-gray-700 font-medium mb-3">{{ $domain['followup'] }}</p>
                <div class="space-y-2">
                    @foreach($domain['fOptions'] as $fi => $fOption)
                    <label class="flex items-center gap-3 p-2.5 rounded-xl border cursor-pointer transition
                                  hover:border-indigo-300 hover:bg-indigo-50"
                           x-bind:class="followups['{{ $domain['fField'] }}'] == {{ $fi }}
                               ? 'border-indigo-300 bg-indigo-50'
                               : 'border-gray-100 bg-white'">
                        <input type="radio"
                               name="{{ $domain['fField'] }}"
                               value="{{ $fi }}"
                               x-model="followups['{{ $domain['fField'] }}']"
                               class="shrink-0 accent-indigo-600">
                        <span class="text-sm text-gray-700">{{ $fOption }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    {{-- Carer notes --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 space-y-3">
        <div>
            <p class="text-sm font-semibold text-gray-900">Carer observations</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Anything else you'd like the social worker to know about this week.
                This is stored as a private note and shared with your social worker.
            </p>
        </div>
        <textarea name="notes" rows="4"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                  placeholder="e.g. Settled well this week, attended school regularly, seemed anxious on Tuesday, responded well to reassurance.">{{ old('notes') }}</textarea>
    </div>

    {{-- Safety escalation notice --}}
    <div x-show="safetyFlag" x-transition
         class="bg-red-50 border-2 border-red-300 rounded-xl px-5 py-4 flex gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-800">Immediate concern flagged</p>
            <p class="text-xs text-red-700 mt-0.5">
                You have indicated an immediate or significant safety concern. Your update will be marked as urgent
                and a safeguarding alert will be raised for the social worker immediately upon submission.
                If this is an emergency, call 999 or Tusla on 1800 882 444.
            </p>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl px-5 py-4">
        <div>
            <p class="text-sm font-medium text-gray-700">
                <span x-text="{{ $total }} - answered"></span> questions remaining
            </p>
            <p class="text-xs text-gray-400 mt-0.5">All 7 domains must be completed before submitting</p>
        </div>
        <button type="submit"
                x-bind:disabled="answered < {{ $total }}"
                x-bind:class="answered >= {{ $total }}
                    ? 'bg-indigo-600 hover:bg-indigo-700 cursor-pointer'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                class="text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
            Submit update
        </button>
    </div>
</form>

@push('scripts')
<script>
function proxyForm() {
    return {
        responses: {},
        followups: {},
        answered:  0,
        safetyFlag: false,

        onAnswer(domain) {
            this.answered = Object.keys(this.responses).filter(
                k => this.responses[k] !== undefined && this.responses[k] !== ''
            ).length;

            // Flag safety concern if safety domain rated 0 or 1 (Immediate/Significant concern)
            if (domain === 'safety') {
                const val = parseInt(this.responses['safety']);
                this.safetyFlag = !isNaN(val) && val <= 1;
            }
        }
    }
}
</script>
@endpush

</x-app-layout>
