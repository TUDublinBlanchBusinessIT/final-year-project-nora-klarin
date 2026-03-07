<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Carer Dashboard</h2>
                <p class="text-sm text-gray-500">Overview of your week</p>
            </div>

            <a href="{{ route('carer.messages.index') }}"
               class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow-sm">
                Messages
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Hero -->
            <div class="rounded-2xl p-6 bg-indigo-700 bg-gradient-to-r from-indigo-600 to-sky-500 shadow-sm">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

        <div class="text-white">
            <p class="text-sm font-medium opacity-90">
                Welcome back
            </p>

            <h1 class="text-3xl font-bold mt-1">
                {{ $user->name }}
            </h1>

            <p class="text-sm mt-2 opacity-90">
                Here’s what’s coming up.
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('carer.calendar') }}"
               class="px-4 py-2 rounded-xl bg-white/20 text-white hover:bg-white/30 text-sm backdrop-blur">
                View Calendar
            </a>

            <a href="{{ route('carer.messages.index') }}"
               class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm shadow">
                Open Messages
            </a>
        </div>

    </div>
</div>


            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Upcoming appointments</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $appointments->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Next 5 shown below</div>
                </div>

                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Unread messages</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-2">
                        {{ $unreadCount ?? '—' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">From your social worker</div>
                </div>

                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Alerts</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $alerts->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Latest 5 shown</div>
                </div>
            </div>

            <!-- Main -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Appointments -->
                <div class="lg:col-span-2 rounded-2xl border bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Upcoming Appointments</h3>
                        <a href="{{ route('carer.calendar') }}" class="text-sm text-indigo-600 hover:underline">
                            View all →
                        </a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($appointments as $appt)
                            <div class="rounded-xl border p-4 hover:bg-gray-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($appt->starttime)->format('D d M Y, H:i') }}
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            {{ $appt->notes ?? 'No notes provided' }}
                                        </div>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Scheduled
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">
                                No upcoming appointments.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Alerts -->
<div class="rounded-2xl border bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-gray-900">Alerts</h3>
            <p class="text-xs text-gray-500 mt-0.5">Updates that may need your attention</p>
        </div>

        <span class="text-xs px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100">
            Recent
        </span>
    </div>

    <div class="mt-4 space-y-3">
        @forelse($alerts as $a)

            @php
                $risk = strtolower($a->risklevel ?? '');

                // Foster-parent friendly label
                $riskLabel = match ($risk) {
                    'critical' => 'Urgent',
                    'high' => 'Important',
                    'medium' => 'Reminder',
                    'low' => 'Info',
                    default => 'Update',
                };

                // Label colour + left border colour
                $riskClasses = match ($risk) {
                    'critical' => ['pill' => 'bg-red-50 text-red-700 border-red-100', 'bar' => 'bg-red-500'],
                    'high' => ['pill' => 'bg-amber-50 text-amber-700 border-amber-100', 'bar' => 'bg-amber-500'],
                    'medium' => ['pill' => 'bg-sky-50 text-sky-700 border-sky-100', 'bar' => 'bg-sky-500'],
                    'low' => ['pill' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'bar' => 'bg-emerald-500'],
                    default => ['pill' => 'bg-gray-50 text-gray-700 border-gray-100', 'bar' => 'bg-gray-400'],
                };

                // Parent-friendly title/description rewrite
                $title = $a->title ?? 'Update';
                $desc  = $a->description ?? '';

                $prettyTitle = $title;
                $prettyDesc  = $desc;
                $icon = '🔔';

                if (str_contains(strtolower($title), 'callback')) {
                    $prettyTitle = 'Support call requested';
                    $prettyDesc  = 'A support call was requested. You may be contacted soon, or you can message your social worker.';
                    $icon = '📞';
                }

                if (str_contains(strtolower($title), 'missed appointment')) {
                    $prettyTitle = 'Appointment missed';
                    $prettyDesc  = 'An appointment was missed. If you need to reschedule, message your social worker.';
                    $icon = '📅';
                }

                if (str_contains(strtolower($title), 'emotional') || str_contains(strtolower($title), 'wellbeing')) {
                    $prettyTitle = 'Wellbeing check flagged';
                    $prettyDesc  = $desc ?: 'The latest wellbeing check suggests your young person may be finding things tougher than last time.';
                    $icon = '💛';
                }

                if (str_contains(strtolower($title), 'urgent') || $risk === 'critical') {
                    $prettyTitle = 'Urgent support needed';
                    $prettyDesc  = $desc ?: 'Something needs quick attention. Please contact your social worker as soon as possible.';
                    $icon = '⚠️';
                }

                // remove "client" wording if it appears
                $prettyDesc = str_ireplace('client', 'young person', $prettyDesc);
            @endphp

            <div class="rounded-2xl border overflow-hidden hover:bg-gray-50 transition">
                <div class="flex">
                    <div class="w-1.5 {{ $riskClasses['bar'] }}"></div>

                    <div class="p-4 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ $icon }}</span>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $prettyTitle }}
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($a->createdat)->format('D H:i') }}
                                </div>
                            </div>

                            <span class="text-xs px-2 py-1 rounded-full border {{ $riskClasses['pill'] }} whitespace-nowrap">
                                {{ $riskLabel }}
                            </span>
                        </div>

                        <div class="text-sm text-gray-600 mt-2">
                            {{ \Illuminate\Support\Str::limit($prettyDesc, 140) }}
                        </div>

                        <div class="mt-3 flex flex-wrap gap-3">
                            <a href="{{ route('carer.messages.index') }}"
                               class="text-sm text-indigo-600 hover:underline">
                                Message social worker →
                            </a>

                            <a href="{{ route('carer.calendar') }}"
                               class="text-sm text-gray-600 hover:underline">
                                Check calendar →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="rounded-xl border border-dashed p-8 text-center text-gray-600">
                No alerts.
            </div>
        @endforelse
    </div>
</div>
    

            </div>
        </div>
    </div>
</x-app-layout>
