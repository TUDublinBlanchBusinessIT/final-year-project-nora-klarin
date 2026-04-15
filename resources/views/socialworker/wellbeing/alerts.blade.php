<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between w-full">

            <div>

                <h2 class="text-2xl font-semibold text-gray-800">

                    Wellbeing Alerts

                </h2>

                <p class="text-sm text-gray-500 mt-1">

                    High-risk and safeguarding wellbeing checks for your assigned children

                </p>

            </div>



            <a href="{{ route('socialworker.dashboard') }}"

               class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">

                Back to Dashboard

            </a>

        </div>

    </x-slot>



    <div class="min-h-screen py-8 bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">



            @if(session('success'))

                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-medium">

                    {{ session('success') }}

                </div>

            @endif



            @if($checks->isEmpty())

                <div class="rounded-3xl bg-white border border-dashed border-gray-300 shadow-sm p-10 text-center">

                    <div class="text-4xl mb-3">✅</div>

                    <h3 class="text-lg font-semibold text-gray-900">No active wellbeing alerts</h3>

                    <p class="text-sm text-gray-500 mt-2">

                        There are currently no high-risk or safeguarding-flagged wellbeing checks for your assigned cases.

                    </p>

                </div>

            @else

                @foreach($checks as $check)

                    @php

                        $triggeredTags = collect(data_get($check->tag_summary, 'triggered_safeguarding_tags', []));

                    @endphp



                    <div class="rounded-3xl bg-white shadow-sm border border-gray-100 p-6">

                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                            <div>

                                <div class="flex items-center gap-3 flex-wrap">

                                    <h3 class="text-xl font-bold text-gray-900">

                                        {{ $check->child->name ?? 'Unknown child' }}

                                    </h3>



                                    <span class="px-3 py-1 rounded-full text-xs font-semibold

                                        @if(($check->risk_level ?? null) === 'low') bg-green-100 text-green-700

                                        @elseif(($check->risk_level ?? null) === 'medium' || ($check->risk_level ?? null) === 'moderate') bg-yellow-100 text-yellow-700

                                        @elseif(($check->risk_level ?? null) === 'high') bg-red-100 text-red-700

                                        @elseif(($check->risk_level ?? null) === 'critical') bg-red-600 text-white

                                        @else bg-gray-100 text-gray-700

                                        @endif">

                                        {{ ucfirst($check->risk_level ?? 'unknown') }}

                                    </span>



                                    @if($check->safeguarding_flag)

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-600 text-white">

                                            Safeguarding alert

                                        </span>

                                    @endif

                                </div>



                                <div class="text-sm text-gray-500 mt-2 space-y-1">

                                    <div>

                                        Case:

                                        <span class="font-medium text-gray-700">

                                            {{ $check->caseFile->case_reference ?? ('Case #' . ($check->case_file_id ?? 'N/A')) }}

                                        </span>

                                    </div>



                                    <div>

                                        Submitted:

                                        <span class="font-medium text-gray-700">

                                            {{ $check->created_at ? $check->created_at->format('d M Y H:i') : '-' }}

                                        </span>

                                    </div>



                                    @if(!empty($check->week_start))

                                        <div>

                                            Week starting:

                                            <span class="font-medium text-gray-700">

                                                {{ \Carbon\Carbon::parse($check->week_start)->format('d M Y') }}

                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </div>



                            <div class="grid grid-cols-2 gap-3 min-w-[220px]">

                                <div class="rounded-2xl bg-indigo-50 p-4 text-center">

                                    <div class="text-xs text-gray-500">Overall Score</div>

                                    <div class="text-2xl font-bold text-indigo-700 mt-1">

                                        {{ round($check->overall_score ?? 0, 1) }}

                                    </div>

                                </div>



                                <div class="rounded-2xl bg-red-50 p-4 text-center">

                                    <div class="text-xs text-gray-500">Risk Score</div>

                                    <div class="text-2xl font-bold text-red-700 mt-1">

                                        {{ round($check->overall_risk_score ?? 0, 1) }}

                                    </div>

                                </div>

                            </div>

                        </div>



                        @if($triggeredTags->count())

                            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4">

                                <p class="text-sm font-semibold text-red-700">

                                    ⚠️ Triggered safeguarding tags

                                </p>



                                <div class="mt-3 flex flex-wrap gap-2">

                                    @foreach($triggeredTags as $tag)

                                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                            {{ ucwords(str_replace('_', ' ', $tag)) }}

                                        </span>

                                    @endforeach

                                </div>

                            </div>

                        @endif



                        @if($check->domainScores && $check->domainScores->count())

                            <div class="mt-5">

                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Domain breakdown</h4>



                                <div class="space-y-3">

                                    @foreach($check->domainScores as $domainScore)

                                        <div>

                                            <div class="flex justify-between items-center text-sm mb-1">

                                                <span class="text-gray-700 font-medium">

                                                    {{ $domainScore->domain->name ?? 'Unknown domain' }}

                                                </span>

                                                <span class="text-indigo-600 font-semibold">

                                                    {{ round($domainScore->average_score ?? 0, 1) }}

                                                </span>

                                            </div>



                                            <div class="h-2 bg-gray-200 rounded-full">

                                                <div

                                                    class="h-2 bg-indigo-500 rounded-full"

                                                    style="width: {{ min(max($domainScore->average_score ?? 0, 0), 100) }}%;"

                                                ></div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif



                        <div class="mt-5 flex justify-end">

                            <a href="{{ route('wellbeing.result', $check) }}"

                               class="text-indigo-600 hover:text-indigo-800 hover:underline text-sm font-medium">

                                View full wellbeing result

                            </a>

                        </div>

                    </div>

                @endforeach

            @endif



        </div>

    </div>

</x-app-layout>