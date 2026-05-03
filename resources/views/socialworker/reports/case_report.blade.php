<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; line-height: 1.5; }

    .page { padding: 28px 32px; }

    /* Header */
    .report-header { border-bottom: 3px solid #4f46e5; padding-bottom: 14px; margin-bottom: 18px; }
    .report-header h1 { font-size: 20px; font-weight: bold; color: #4f46e5; }
    .report-header .meta { font-size: 9px; color: #6b7280; margin-top: 3px; }
    .report-header .ref { font-size: 11px; font-weight: bold; color: #111827; }

    /* Sections */
    .section { margin-bottom: 18px; }
    .section-title {
        font-size: 11px; font-weight: bold; color: #4f46e5;
        border-left: 4px solid #4f46e5; padding-left: 8px;
        margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* Grid layouts */
    .grid-2 { display: table; width: 100%; }
    .grid-2 .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
    .grid-2 .col:last-child { padding-right: 0; }

    .grid-3 { display: table; width: 100%; }
    .grid-3 .col { display: table-cell; width: 33.33%; vertical-align: top; padding-right: 12px; }
    .grid-3 .col:last-child { padding-right: 0; }

    /* Info card */
    .info-row { display: table; width: 100%; margin-bottom: 5px; }
    .info-label { display: table-cell; width: 38%; font-size: 9px; color: #6b7280; }
    .info-value { display: table-cell; font-size: 10px; color: #111827; font-weight: 500; }

    /* Stat boxes */
    .stat-box {
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 6px; padding: 10px; text-align: center; margin-bottom: 8px;
    }
    .stat-box .stat-value { font-size: 22px; font-weight: bold; color: #4f46e5; }
    .stat-box .stat-label { font-size: 8px; color: #6b7280; margin-top: 2px; }

    /* Risk badge */
    .badge {
        display: inline-block; padding: 2px 8px;
        border-radius: 999px; font-size: 9px; font-weight: bold;
    }
    .badge-high     { background: #fee2e2; color: #991b1b; }
    .badge-medium   { background: #fef3c7; color: #92400e; }
    .badge-low      { background: #dcfce7; color: #166534; }
    .badge-indigo   { background: #ede9fe; color: #4338ca; }
    .badge-green    { background: #dcfce7; color: #166534; }
    .badge-gray     { background: #f3f4f6; color: #374151; }

    /* Progress bar */
    .progress-wrap { background: #e5e7eb; border-radius: 999px; height: 7px; width: 100%; margin-top: 3px; }
    .progress-fill { height: 7px; border-radius: 999px; background: #4f46e5; }
    .progress-fill-green { background: #22c55e; }
    .progress-fill-red   { background: #ef4444; }

    /* Domain table */
    .domain-table { width: 100%; border-collapse: collapse; }
    .domain-table th {
        font-size: 8px; color: #6b7280; text-transform: uppercase;
        letter-spacing: 0.05em; padding: 4px 6px; border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }
    .domain-table td { padding: 5px 6px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
    .domain-table td.score { font-weight: bold; text-align: right; }
    .domain-table td.change-pos { color: #16a34a; font-weight: bold; text-align: right; }
    .domain-table td.change-neg { color: #dc2626; font-weight: bold; text-align: right; }

    /* Goals */
    .goal-card {
        border: 1px solid #e5e7eb; border-radius: 6px;
        padding: 10px 12px; margin-bottom: 8px;
    }
    .goal-card.completed { background: #f0fdf4; border-color: #bbf7d0; }
    .goal-card.in_progress { background: #fefce8; border-color: #fde68a; }
    .goal-title { font-size: 11px; font-weight: bold; color: #111827; margin-bottom: 3px; }
    .goal-meta  { font-size: 8px; color: #6b7280; margin-bottom: 6px; }

    /* Task list */
    .task-item { display: table; width: 100%; padding: 3px 0; border-bottom: 1px solid #f3f4f6; }
    .task-check { display: table-cell; width: 14px; vertical-align: top; }
    .task-body  { display: table-cell; font-size: 9px; color: #374151; vertical-align: top; }
    .task-done  { color: #9ca3af; text-decoration: line-through; }
    .task-xp    { display: table-cell; width: 36px; text-align: right; font-size: 8px; color: #6366f1; }

    /* Wellbeing history mini-table */
    .wb-table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
    .wb-table th { background: #f3f4f6; padding: 4px 5px; text-align: center; border: 1px solid #e5e7eb; }
    .wb-table td { padding: 3px 5px; text-align: center; border: 1px solid #f3f4f6; }

    /* Footer */
    .report-footer {
        border-top: 1px solid #e5e7eb; padding-top: 10px;
        font-size: 8px; color: #9ca3af; margin-top: 24px;
        display: table; width: 100%;
    }
    .report-footer .left  { display: table-cell; }
    .report-footer .right { display: table-cell; text-align: right; }

    .page-break { page-break-after: always; }
</style>
</head>
<body>
<div class="page">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="report-header">
        <div style="display:table;width:100%">
            <div style="display:table-cell;vertical-align:bottom">
                <h1>CareHub — Case Report</h1>
                <div class="meta">Generated {{ now()->format('d M Y, H:i') }} · Confidential</div>
            </div>
            <div style="display:table-cell;text-align:right;vertical-align:bottom">
                <div class="ref">{{ $case->case_reference ?? ('Case #'.$case->id) }}</div>
                <div class="meta">{{ $case->youngPerson->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- ══ SECTION 1: PERSONAL & CASE INFO ════════════════════════════════ --}}
    <div class="section">
        <div class="section-title">1. Case Overview</div>
        <div class="grid-2">
            <div class="col">
                <div class="info-row">
                    <div class="info-label">Young person</div>
                    <div class="info-value">{{ $case->youngPerson->name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date of birth</div>
                    <div class="info-value">
                        @if($case->youngPerson?->dob)
                            {{ \Carbon\Carbon::parse($case->youngPerson->dob)->format('d M Y') }}
                            (Age {{ \Carbon\Carbon::parse($case->youngPerson->dob)->age }})
                        @else — @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Case reference</div>
                    <div class="info-value">{{ $case->case_reference ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ ucfirst($case->status ?? '—') }}</div>
                </div>
            </div>
            <div class="col">
                <div class="info-row">
                    <div class="info-label">Risk level</div>
                    <div class="info-value">
                        @php $risk = strtolower($case->risk_level ?? 'low'); @endphp
                        <span class="badge badge-{{ $risk }}">{{ ucfirst($risk) }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Case opened</div>
                    <div class="info-value">
                        {{ $case->opened_at ? \Carbon\Carbon::parse($case->opened_at)->format('d M Y') : '—' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Last reviewed</div>
                    <div class="info-value">
                        {{ $case->last_reviewed_at ? \Carbon\Carbon::parse($case->last_reviewed_at)->format('d M Y') : 'Not reviewed' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Placement type</div>
                    <div class="info-value">{{ $case->placement_type ?? '—' }}</div>
                </div>
            </div>
        </div>

        @if($case->summary)
            <div style="margin-top:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:5px;padding:8px 10px;">
                <div style="font-size:8px;color:#6b7280;margin-bottom:3px;font-weight:bold;">CASE SUMMARY</div>
                <div style="font-size:9px;color:#374151;">{{ $case->summary }}</div>
            </div>
        @endif
    </div>

    {{-- ══ SECTION 2: WELLBEING SUMMARY ═══════════════════════════════════ --}}
    <div class="section">
        <div class="section-title">2. Wellbeing Summary</div>

        @if($latestCheck)
            {{-- Stat row --}}
            <div class="grid-3" style="margin-bottom:12px">
                <div class="col">
                    <div class="stat-box">
                        <div class="stat-value">{{ round($latestCheck->overall_score ?? 0) }}</div>
                        <div class="stat-label">Overall score (latest)</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box">
                        <div class="stat-value" style="color:
                            @if(($latestCheck->risk_level ?? 'low') === 'high') #dc2626
                            @elseif(($latestCheck->risk_level ?? 'low') === 'medium') #d97706
                            @else #16a34a @endif">
                            {{ ucfirst($latestCheck->risk_level ?? 'low') }}
                        </div>
                        <div class="stat-label">Current risk level</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-box">
                        <div class="stat-value">{{ $checks->count() }}</div>
                        <div class="stat-label">Total checks completed</div>
                    </div>
                </div>
            </div>

            {{-- Domain scores table --}}
            <table class="domain-table">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th style="text-align:right">Latest score</th>
                        @if(!empty($domainTrend))<th style="text-align:right">vs Previous</th>@endif
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['emotional','behavioural','social','physical','education','safety'] as $d)
                        @php
                            $score  = $latestCheck->{$d.'_score'} ?? 0;
                            $change = $domainTrend[$d]['change'] ?? null;
                            $pct    = min(100, $score);
                            $barCls = $pct < 35 ? 'progress-fill-red' : ($pct < 60 ? '' : 'progress-fill-green');
                        @endphp
                        <tr>
                            <td>{{ ucfirst($d) }}</td>
                            <td class="score">{{ $score }}</td>
                            @if(!empty($domainTrend))
                                <td class="{{ $change >= 0 ? 'change-pos' : 'change-neg' }}">
                                    {{ $change !== null ? ($change >= 0 ? '+' : '').$change : '—' }}
                                </td>
                            @endif
                            <td style="width:120px">
                                <div class="progress-wrap">
                                    <div class="progress-fill {{ $barCls }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Wellbeing check history --}}
            @if($checks->count() > 1)
                <div style="margin-top:12px">
                    <div style="font-size:9px;font-weight:bold;color:#374151;margin-bottom:5px;">Check history</div>
                    <table class="wb-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Overall</th>
                                <th>Risk</th>
                                <th>Emotional</th>
                                <th>Social</th>
                                <th>Behavioural</th>
                                <th>Physical</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checks->sortByDesc('created_at')->take(6) as $c)
                                <tr>
                                    <td>{{ $c->created_at?->format('d M Y') }}</td>
                                    <td style="font-weight:bold">{{ round($c->overall_score ?? 0) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $c->risk_level ?? 'low' }}">
                                            {{ ucfirst($c->risk_level ?? 'low') }}
                                        </span>
                                    </td>
                                    <td>{{ $c->emotional_score ?? '—' }}</td>
                                    <td>{{ $c->social_score ?? '—' }}</td>
                                    <td>{{ $c->behavioural_score ?? '—' }}</td>
                                    <td>{{ $c->physical_score ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div style="color:#9ca3af;font-size:9px;font-style:italic">No wellbeing checks recorded for this case.</div>
        @endif
    </div>

    <div class="page-break"></div>

    {{-- ══ SECTION 3: GOAL & MISSION PROGRESS ═════════════════════════════ --}}
    <div class="section">
        <div class="section-title">3. Goal & Mission Progress</div>

        {{-- Engagement summary --}}
        <div class="grid-3" style="margin-bottom:14px">
            <div class="col">
                <div class="stat-box">
                    <div class="stat-value" style="color:#16a34a">{{ $goalsCompleted }}</div>
                    <div class="stat-label">Goals completed</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-box">
                    <div class="stat-value" style="color:#d97706">{{ $goalsActive }}</div>
                    <div class="stat-label">Goals in progress</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-box">
                    <div class="stat-value">{{ $engagementPct }}%</div>
                    <div class="stat-label">Mission completion rate</div>
                </div>
            </div>
        </div>

        {{-- Engagement bar --}}
        <div style="margin-bottom:14px">
            <div style="display:table;width:100%;font-size:8px;color:#6b7280;margin-bottom:3px">
                <div style="display:table-cell">Mission engagement: {{ $doneMissions }}/{{ $totalMissions }} completed</div>
                <div style="display:table-cell;text-align:right">{{ $engagementPct }}%</div>
            </div>
            <div class="progress-wrap" style="height:10px">
                <div class="progress-fill {{ $engagementPct >= 70 ? 'progress-fill-green' : ($engagementPct >= 40 ? '' : 'progress-fill-red') }}"
                     style="width:{{ $engagementPct }}%;height:10px"></div>
            </div>
        </div>

        {{-- Per-goal breakdown --}}
        @forelse($activeGoals as $goal)
            @php
                $tasks = $tasksByCaseGoal[$goal->case_goal_id] ?? collect();
                $done  = $tasks->whereNotNull('completed_at')->count();
                $total = $tasks->count();
                $pct   = $total > 0 ? round(($done / $total) * 100) : 0;
            @endphp
            <div class="goal-card {{ $goal->status }}">
                <div style="display:table;width:100%">
                    <div style="display:table-cell;vertical-align:top">
                        <div class="goal-title">{{ $goal->title }}</div>
                        <div class="goal-meta">
                            @if($goal->domain_name)
                                <span class="badge badge-indigo">{{ $goal->domain_name }}</span>
                            @endif
                            <span class="badge {{ $goal->status === 'completed' ? 'badge-green' : 'badge-gray' }}" style="margin-left:4px">
                                {{ ucfirst(str_replace('_',' ',$goal->status)) }}
                            </span>
                            @if($goal->child_accepted_at)
                                <span class="badge badge-green" style="margin-left:4px">Accepted by child</span>
                            @endif
                            @if($goal->due_date)
                                · Due {{ \Carbon\Carbon::parse($goal->due_date)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                    <div style="display:table-cell;text-align:right;vertical-align:top;width:80px">
                        <div style="font-size:16px;font-weight:bold;color:#4f46e5">{{ $pct }}%</div>
                        <div style="font-size:8px;color:#6b7280">{{ $done }}/{{ $total }} missions</div>
                    </div>
                </div>

                @if($total > 0)
                    <div class="progress-wrap" style="margin:6px 0 8px">
                        <div class="progress-fill {{ $pct === 100 ? 'progress-fill-green' : '' }}" style="width:{{ $pct }}%"></div>
                    </div>

                    @foreach($tasks as $task)
                        <div class="task-item">
                            <div class="task-check">
                                @if($task->completed_at) ✓ @else ○ @endif
                            </div>
                            <div class="task-body {{ $task->completed_at ? 'task-done' : '' }}">
                                {{ $task->title }}
                                @if($task->ai_suggested) <span style="color:#8b5cf6">✨</span> @endif
                            </div>
                            <div class="task-xp">
                                @if($task->completed_at)
                                    <span style="color:#16a34a">+20 XP</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="font-size:8px;color:#9ca3af;font-style:italic;margin-top:4px">No missions assigned yet.</div>
                @endif
            </div>
        @empty
            <div style="color:#9ca3af;font-size:9px;font-style:italic">No goals have been set for this case.</div>
        @endforelse
    </div>

    {{-- ══ SECTION 4: MEDICAL & EDUCATION ═════════════════════════════════ --}}
    @if($case->medicalInfos->isNotEmpty() || $case->educationInfos->isNotEmpty())
    <div class="section">
        <div class="section-title">4. Medical & Education</div>
        <div class="grid-2">
            <div class="col">
                @if($case->medicalInfos->isNotEmpty())
                    <div style="font-size:9px;font-weight:bold;margin-bottom:5px">Medical records</div>
                    @foreach($case->medicalInfos as $m)
                        <div style="font-size:9px;padding:4px 0;border-bottom:1px solid #f3f4f6">
                            <strong>{{ $m->condition }}</strong>
                            @if($m->notes)<div style="color:#6b7280">{{ $m->notes }}</div>@endif
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="col">
                @if($case->educationInfos->isNotEmpty())
                    <div style="font-size:9px;font-weight:bold;margin-bottom:5px">Education records</div>
                    @foreach($case->educationInfos as $e)
                        <div style="font-size:9px;padding:4px 0;border-bottom:1px solid #f3f4f6">
                            <strong>{{ $e->school_name }}</strong>
                            @if($e->grade) · Year {{ $e->grade }}@endif
                            @if($e->notes)<div style="color:#6b7280">{{ $e->notes }}</div>@endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ══ FOOTER ═══════════════════════════════════════════════════════════ --}}
    <div class="report-footer">
        <div class="left">CareHub — Confidential Case Report · {{ $case->case_reference ?? 'Case #'.$case->id }}</div>
        <div class="right">Generated {{ now()->format('d M Y H:i') }} · {{ auth()->user()->name }}</div>
    </div>

</div>
</body>
</html>