<x-app-layout>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My trusted people 💙</h1>
            <p class="text-sm text-gray-500 mt-0.5">People you can reach out to when you need support</p>
        </div>
        <a href="{{ route('child.dashboard') }}"
           class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-slate-50 transition">
            ← Back
        </a>
    </div>
</x-slot>

@if(session('support_sent'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-sm text-green-800 mb-5 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Your social worker and carer have been notified. Someone will be in touch soon.</span>
    </div>
@endif

<div class="space-y-5">

    {{-- ── Ask for help card ──────────────────────────────────── --}}
    <div class="bg-white border border-indigo-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">Feeling like you need to talk to someone?</p>
            <p class="text-xs text-gray-500 mt-0.5">
                Tap the button below to let your social worker and carer know you'd like some support.
                They'll get a notification and reach out to you.
            </p>
        </div>
        <div class="px-5 py-4 space-y-3">
            <form method="POST" action="{{ route('child.support.request') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
                               rounded-xl py-3 transition active:scale-95">
                    Let someone know I need support 💬
                </button>
            </form>

            {{-- Assigned SW and carer --}}
            <div class="flex flex-wrap gap-3 pt-1">
                @if(!empty($socialWorker))
                <div class="flex items-center gap-2 bg-indigo-50 rounded-xl px-3 py-2 flex-1 min-w-48">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wide">Social worker</p>
                        <p class="text-xs font-semibold text-indigo-800 truncate">{{ $socialWorker->name }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($carer))
                <div class="flex items-center gap-2 bg-purple-50 rounded-xl px-3 py-2 flex-1 min-w-48">
                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-purple-400 font-semibold uppercase tracking-wide">Carer</p>
                        <p class="text-xs font-semibold text-purple-800 truncate">{{ $carer->name }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Emergency note --}}
            <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <p class="text-xs text-amber-700">
                    If you are in immediate danger, call <strong>999</strong> or contact a trusted adult nearby.
                    This button is for non-emergency support only.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Trusted people list ─────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-900">Your trusted people</p>
                <p class="text-xs text-gray-400 mt-0.5">Added by your social worker or carer</p>
            </div>
            @if($people->isNotEmpty())
                <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                    {{ $people->count() }} {{ Str::plural('person', $people->count()) }}
                </span>
            @endif
        </div>

        @forelse($people as $p)
        <div class="flex items-start gap-4 px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0 text-lg">
                @php
                    $rel = strtolower($p->relationship ?? '');
                    $icon = str_contains($rel, 'social') ? '👩‍💼'
                        : (str_contains($rel, 'carer') || str_contains($rel, 'foster') ? '🏠'
                        : (str_contains($rel, 'teacher') ? '📚'
                        : (str_contains($rel, 'family') || str_contains($rel, 'aunt') || str_contains($rel, 'uncle') || str_contains($rel, 'gran') ? '👨‍👩‍👧'
                        : '👤')));
                @endphp
                {{ $icon }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $p->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $p->relationship }}</p>

                <div class="flex flex-wrap gap-2 mt-2">
                    @if(!empty($p->phone))
                    <a href="tel:{{ $p->phone }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium bg-green-50 text-green-700
                              border border-green-100 px-2.5 py-1 rounded-lg hover:bg-green-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                        </svg>
                        {{ $p->phone }}
                    </a>
                    @endif
                    @if(!empty($p->email))
                    <a href="mailto:{{ $p->email }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium bg-blue-50 text-blue-700
                              border border-blue-100 px-2.5 py-1 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        {{ $p->email }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-12 text-center">
            <p class="text-3xl mb-2">💙</p>
            <p class="text-sm font-medium text-gray-700">No trusted people added yet</p>
            <p class="text-xs text-gray-400 mt-1">
                Your social worker or carer will add your trusted contacts here.
            </p>
        </div>
        @endforelse
    </div>

    {{-- ── Useful numbers ─────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">Useful numbers</p>
            <p class="text-xs text-gray-400 mt-0.5">Free helplines — you can call or text any time</p>
        </div>
        @foreach([
            ['name' => 'Emergency services', 'number' => '999',      'note' => 'Police, fire, ambulance',           'color' => 'red'],
            ['name' => 'Childline',          'number' => '116 123',   'note' => 'Free, 24/7, confidential',         'color' => 'green'],
            ['name' => 'Tusla',              'number' => '1800 882 444', 'note' => 'Child protection referrals',    'color' => 'blue'],
            ['name' => 'Samaritans',         'number' => '116 123',   'note' => 'Emotional support, free, 24/7',    'color' => 'indigo'],
        ] as $line)
        @php
            $c = $line['color'];
            $bg   = "bg-{$c}-50";
            $text = "text-{$c}-700";
            $btn  = "bg-{$c}-100 text-{$c}-700 hover:bg-{$c}-200";
        @endphp
        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $line['name'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $line['note'] }}</p>
            </div>
            <a href="tel:{{ str_replace(' ', '', $line['number']) }}"
               class="shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition
                      {{ $bg }} {{ $text }}">
                {{ $line['number'] }}
            </a>
        </div>
        @endforeach
    </div>

</div>

</x-app-layout>
