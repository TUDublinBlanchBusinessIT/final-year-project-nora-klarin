<x-app-layout>



    <x-slot name="header">

        <h2 class="text-2xl font-semibold text-gray-800">

            Your Wellbeing Results

        </h2>

    </x-slot>



    <div class="bg-white p-8 rounded-2xl shadow space-y-8">



        {{-- Overall Score --}}

        <div class="text-center">

            <p class="text-gray-500">Overall Score</p>

            <p class="text-4xl font-bold text-indigo-600">

                {{ round($check->overall_score, 1) }}

            </p>

        </div>



        {{-- Risk Level --}}

        <div class="text-center">

            <p class="text-gray-500 mb-2">Risk Level</p>



            <span class="px-4 py-2 rounded-full text-sm font-semibold

                @if($check->risk_level === 'low') bg-green-100 text-green-700

                @elseif($check->risk_level === 'medium') bg-yellow-100 text-yellow-700

                @elseif($check->risk_level === 'high') bg-red-100 text-red-700

                @elseif($check->risk_level === 'critical') bg-red-600 text-white

                @endif">

                {{ ucfirst($check->risk_level) }}

            </span>

        </div>



        {{-- Triggered Tags --}}

        <div class="text-center">

            @php

                $triggeredTags = collect();



                foreach ($check->responses as $response) {

                    if (!$response->question) {

                        continue;

                    }



                    foreach ($response->question->tags as $tag) {

                        if (($response->normalized_score ?? 0) < 40 && ($tag->category ?? null) === 'safeguarding') {

                            $triggeredTags->push($tag->name);

                        }

                    }

                }



                $triggeredTags = $triggeredTags->unique();

            @endphp



            @if($triggeredTags->count())

                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">

                    <p class="text-red-700 font-semibold">

                        ⚠️ Safeguarding concerns detected:

                    </p>



                    <div class="mt-3 flex flex-wrap justify-center gap-2">

                        @foreach($triggeredTags as $tag)

                            <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">

                                {{ str_replace('_', ' ', ucfirst($tag)) }}

                            </span>

                        @endforeach

                    </div>



                    <p class="text-sm text-red-600 mt-3">

                        These areas may need attention from your social worker.

                    </p>

                </div>

            @endif

        </div>



        {{-- Domain Breakdown --}}

        <div>

            <h3 class="text-xl font-semibold mb-4">Domain Breakdown</h3>



            <div class="space-y-4">

                @foreach($check->domainScores as $domainScore)

                    <div class="p-4 border rounded-xl">

                        <div class="flex justify-between items-center">

                            <span class="font-medium text-gray-700">

                                {{ $domainScore->domain->name }}

                            </span>



                            <span class="text-indigo-600 font-semibold">

                                {{ round($domainScore->average_score) }}

                            </span>

                        </div>



                        <div class="mt-2 h-2 bg-gray-200 rounded-full">

                            <div

                                class="h-2 bg-indigo-500 rounded-full"

                                style="width: {{ $domainScore->average_score }}%"

                            ></div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>



        <div class="text-center pt-4">

            <a href="{{ route('child.wellbeing.form') }}"

               class="text-indigo-600 hover:underline">

                Submit Another Check

            </a>

        </div>



    </div>



</x-app-layout>

