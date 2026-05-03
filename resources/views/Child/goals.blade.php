{{-- resources/views/child/goals.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚡</span>
            <h2 class="font-semibold text-xl leading-tight">My Missions</h2>
        </div>
    </x-slot>

    {{-- Confetti canvas (sits above everything, pointer-events-none) --}}
    <canvas id="confetti-canvas"
            class="fixed inset-0 z-50 pointer-events-none"
            style="width:100%;height:100%"></canvas>

    <div class="theme-page min-h-[calc(100vh-6rem)]">
        <div class="relative mx-auto max-w-2xl px-4 sm:px-6 py-8">

            @if (session('success'))
                <div id="successBanner"
                     class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900 flex items-center gap-3">
                    <span class="text-2xl">🎉</span>
                    <div class="font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            {{-- ── XP / LEVEL BAR ── --}}
            @php
                $totalTasks   = 0;
                $doneTasks    = 0;
                foreach ($activeGoals as $cg) {
                    $t = $tasksByCaseGoal[$cg->case_goal_id] ?? collect();
                    $totalTasks += $t->count();
                    $doneTasks  += $t->whereNotNull('completed_at')->count();
                }
                $xp       = $doneTasks * 20;
                $level    = max(1, intdiv($doneTasks, 3) + 1);
                $nextXp   = $level * 60;
                $xpPct    = $nextXp > 0 ? min(100, round(($xp % 60) / 60 * 100)) : 0;
            @endphp

            <div class="mb-8 theme-card rounded-3xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-xs font-semibold opacity-60 uppercase tracking-widest">Your level</div>
                        <div class="text-3xl font-black">⚡ Level {{ $level }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-semibold opacity-60">Missions completed</div>
                        <div class="text-3xl font-black text-indigo-600">{{ $doneTasks }}</div>
                    </div>
                </div>
                <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                    <div id="xpBar"
                         class="h-3 rounded-full bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-sky-400 transition-all duration-1000"
                         style="width: 0%"
                         data-target="{{ $xpPct }}"></div>
                </div>
                <div class="flex justify-between text-xs opacity-50 mt-1">
                    <span>{{ $xp % 60 }} XP</span>
                    <span>{{ $nextXp }} XP to next level</span>
                </div>
            </div>

            {{-- ── NEW GOALS TO ACCEPT ── --}}
            @if ($pendingGoals->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-lg font-extrabold mb-3">🌟 New journeys available</h2>
                    <div class="space-y-3">
                        @foreach ($pendingGoals as $cg)
                            <div class="theme-card rounded-2xl p-5 shadow border-2 border-amber-200 relative overflow-hidden">
                                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-400"></div>
                                <div class="flex items-start gap-4">
                                    <div class="text-4xl">🗺️</div>
                                    <div class="flex-1">
                                        <div class="font-bold text-lg">{{ $cg->title }}</div>
                                        @if ($cg->description)
                                            <div class="text-sm opacity-70 mt-1">{{ $cg->description }}</div>
                                        @endif
                                        @if ($cg->domain_name)
                                            <span class="inline-block mt-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                                {{ $cg->domain_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('child.goals.accept', $cg->case_goal_id) }}" class="mt-4">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3
                                               font-bold bg-gradient-to-r from-amber-400 to-orange-500 text-white
                                               hover:from-amber-500 hover:to-orange-600 transition shadow-md active:scale-95">
                                        🚀 Start this journey
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── ACTIVE GOALS / JOURNEYS ── --}}
            @if ($activeGoals->isNotEmpty())
                <h2 class="text-lg font-extrabold mb-3">🚀 Your active journeys</h2>
                <div class="space-y-6 mb-8">
                    @foreach ($activeGoals as $cg)
                        @php
                            $tasks      = $tasksByCaseGoal[$cg->case_goal_id] ?? collect();
                            $total      = $tasks->count();
                            $done       = $tasks->whereNotNull('completed_at')->count();
                            $pct        = $total > 0 ? round(($done / $total) * 100) : 0;
                            $domainIcon = match(strtolower($cg->domain_name ?? '')) {
                                'social'        => '🧑‍🤝‍🧑',
                                'emotional'     => '💜',
                                'physical'      => '💪',
                                'education'     => '📚',
                                'safety'        => '🛡️',
                                'behavioural'   => '🧠',
                                default         => '⭐',
                            };
                        @endphp

                        <div class="theme-card rounded-3xl shadow-lg overflow-hidden">
                            {{-- Goal header --}}
                            <div class="p-5 pb-3">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-3xl flex-shrink-0">
                                        {{ $domainIcon }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-extrabold text-lg leading-tight">{{ $cg->title }}</div>
                                        @if ($cg->domain_name)
                                            <span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                                {{ $cg->domain_name }}
                                            </span>
                                        @endif
                                        @if ($cg->due_date)
                                            <span class="inline-block mt-1 ml-1 text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                                🗓 Due {{ \Carbon\Carbon::parse($cg->due_date)->format('d M') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs font-semibold mb-1.5">
                                        <span class="opacity-60">
                                            @if ($total === 0)
                                                Journey starting soon…
                                            @elseif ($done === $total)
                                                🏆 All missions complete!
                                            @else
                                                {{ $done }} of {{ $total }} missions done
                                            @endif
                                        </span>
                                        <span class="text-indigo-600">{{ $pct }}%</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-3 rounded-full transition-all duration-700
                                                    {{ $pct === 100
                                                        ? 'bg-gradient-to-r from-emerald-400 to-teal-500'
                                                        : 'bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-sky-400' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Mission cards --}}
                            @if ($tasks->isNotEmpty())
                                <div class="px-4 pb-4 space-y-2 mt-1">
                                    @foreach ($tasks as $task)
                                        @php
                                            $isDone     = !is_null($task->completed_at);
                                            $missionIcon = match(true) {
                                                str_contains(strtolower($task->title), 'walk')    => '🚶',
                                                str_contains(strtolower($task->title), 'water')   => '💧',
                                                str_contains(strtolower($task->title), 'sleep')
                                                    || str_contains(strtolower($task->title), 'bed') => '🛏️',
                                                str_contains(strtolower($task->title), 'talk')
                                                    || str_contains(strtolower($task->title), 'say')
                                                    || str_contains(strtolower($task->title), 'ask') => '💬',
                                                str_contains(strtolower($task->title), 'breath')  => '🌬️',
                                                str_contains(strtolower($task->title), 'write')
                                                    || str_contains(strtolower($task->title), 'list') => '✏️',
                                                str_contains(strtolower($task->title), 'school')
                                                    || str_contains(strtolower($task->title), 'class') => '🎒',
                                                str_contains(strtolower($task->title), 'eat')
                                                    || str_contains(strtolower($task->title), 'food') => '🥗',
                                                str_contains(strtolower($task->title), 'friend')  => '🤝',
                                                $isDone => '✅',
                                                default => '⚡',
                                            };
                                        @endphp

                                        <div class="mission-card rounded-2xl px-4 py-3 flex items-start gap-3 transition-all
                                                    {{ $isDone
                                                        ? 'bg-emerald-50 border border-emerald-200'
                                                        : 'bg-slate-50 border border-slate-200 hover:border-indigo-300' }}"
                                             data-task-id="{{ $task->id }}">

                                            {{-- Tap button --}}
                                            <form method="POST"
                                                  action="{{ $isDone
                                                      ? route('child.goals.tasks.uncomplete', $task->id)
                                                      : route('child.goals.tasks.complete',   $task->id) }}"
                                                  class="flex-shrink-0 mt-0.5">
                                                @csrf
                                                <button type="submit"
                                                    class="mission-btn h-9 w-9 rounded-full border-2 flex items-center justify-center transition-all active:scale-90
                                                           {{ $isDone
                                                               ? 'border-emerald-500 bg-emerald-500 text-white shadow-lg shadow-emerald-200'
                                                               : 'border-slate-300 bg-white hover:border-indigo-400 hover:bg-indigo-50' }}"
                                                    data-done="{{ $isDone ? '1' : '0' }}">
                                                    @if ($isDone)
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                  d="M16.704 5.29a1 1 0 01.006 1.414l-7.5 7.57a1 1 0 01-1.42 0L3.29 9.78a1 1 0 011.42-1.4l3.08 3.12 6.79-6.86a1 1 0 011.414-.01z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            {{-- Content --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-lg">{{ $missionIcon }}</span>
                                                    <span class="font-semibold text-sm {{ $isDone ? 'line-through opacity-50' : '' }}">
                                                        {{ $task->title }}
                                                    </span>
                                                </div>
                                                @if ($task->description)
                                                    <div class="text-xs opacity-60 mt-0.5 ml-7">{{ $task->description }}</div>
                                                @endif
                                            </div>

                                            {{-- Done badge --}}
                                            @if ($isDone)
                                                <div class="flex-shrink-0 text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">
                                                    Done ✓
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full">
                                                    +20 XP
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            @elseif (!$cg->child_accepted_at)
                                <div class="px-5 pb-5 text-sm opacity-50 italic">
                                    Accept this journey to unlock your missions 🔒
                                </div>
                            @else
                                <div class="px-5 pb-5 text-sm opacity-50 italic">
                                    Missions loading… check back soon 🌱
                                </div>
                            @endif

                            {{-- Completion celebration --}}
                            @if ($total > 0 && $done === $total)
                                <div class="mx-4 mb-4 rounded-2xl bg-gradient-to-r from-emerald-400 to-teal-500 p-4 text-white text-center">
                                    <div class="text-2xl mb-1">🏆</div>
                                    <div class="font-extrabold">Journey complete!</div>
                                    <div class="text-sm opacity-90 mt-0.5">Amazing work — you did it!</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="theme-card rounded-3xl p-10 text-center shadow mb-8">
                    <div class="text-6xl mb-4">🌱</div>
                    <div class="font-extrabold text-lg">No journeys yet</div>
                    <div class="text-sm opacity-60 mt-1">Your social worker will set up your first journey soon.</div>
                </div>
            @endif

            {{-- ── COMPLETED JOURNEYS ── --}}
            @if ($completedGoals->isNotEmpty())
                <h2 class="text-lg font-extrabold mb-3">🏅 Completed journeys</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
                    @foreach ($completedGoals as $cg)
                        <div class="theme-card rounded-2xl p-4 shadow opacity-80 flex items-center gap-3">
                            <div class="text-3xl">🏆</div>
                            <div>
                                <div class="font-bold text-sm line-through opacity-70">{{ $cg->title }}</div>
                                <div class="text-xs opacity-50 mt-0.5">
                                    Completed {{ \Carbon\Carbon::parse($cg->completed_at)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <a href="{{ route('child.dashboard') }}"
               class="inline-flex items-center gap-2 rounded-2xl px-6 py-3.5
                      font-semibold bg-slate-100 text-slate-800 hover:bg-slate-200 transition">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Feedback toast --}}
    <div id="toast"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 pointer-events-none
                transition-all duration-500 opacity-0 translate-y-4">
        <div class="bg-gray-900 text-white px-6 py-3 rounded-2xl shadow-2xl font-semibold text-sm flex items-center gap-2">
            <span id="toastIcon">🎉</span>
            <span id="toastMsg">Nice one!</span>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script>
    // ── XP bar animate on load ──
    document.addEventListener('DOMContentLoaded', () => {
        const bar = document.getElementById('xpBar');
        if (bar) {
            setTimeout(() => {
                bar.style.width = bar.dataset.target + '%';
            }, 300);
        }

        // Fire confetti if a task was just completed (session flash)
        @if(session('task_completed'))
            fireConfetti();
            showToast('{{ session('toast_message', '🎉 Mission complete!') }}', '⚡');
        @endif

        @if(session('success') && str_contains(session('success', ''), 'accepted'))
            showToast('Journey started! Time to level up 🚀', '🗺️');
        @endif
    });

    const messages = [
        ['Nice one 👏', '⚡'],
        ['You\'re on a roll!', '🔥'],
        ['Keep it up!', '💪'],
        ['That\'s the way!', '🌟'],
        ['Building good habits!', '🧠'],
        ['One step at a time 🙌', '👟'],
    ];

    function fireConfetti() {
        const canvas = document.getElementById('confetti-canvas');
        const myConfetti = confetti.create(canvas, { resize: true, useWorker: false });
        myConfetti({
            particleCount: 120,
            spread: 80,
            origin: { y: 0.6 },
            colors: ['#6366f1', '#a855f7', '#38bdf8', '#34d399', '#fbbf24'],
        });
        setTimeout(() => myConfetti({ particleCount: 40, spread: 50, origin: { y: 0.7 } }), 300);
    }

    function showToast(msg, icon = '🎉') {
        const toast   = document.getElementById('toast');
        const toastMsg  = document.getElementById('toastMsg');
        const toastIcon = document.getElementById('toastIcon');
        toastMsg.textContent  = msg;
        toastIcon.textContent = icon;
        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            toast.classList.remove('opacity-100', 'translate-y-0');
        }, 3000);
    }

    // Intercept mission button clicks for instant feedback before form submits
    document.querySelectorAll('.mission-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const isDone = btn.dataset.done === '0'; // about to become done
            if (isDone) {
                const rand = messages[Math.floor(Math.random() * messages.length)];
                fireConfetti();
                showToast(rand[0], rand[1]);
            }
        });
    });
    </script>
    @endpush
</x-app-layout>