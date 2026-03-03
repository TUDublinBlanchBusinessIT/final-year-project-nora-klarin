<x-app-layout>

    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800">
            Weekly Wellbeing Check
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('child.wellbeing.submit') }}"
          class="space-y-8 bg-white p-8 rounded-2xl shadow">
        @csrf

        @foreach($questions as $domainName => $domainQuestions)

            <div class="border-b pb-6">
                <h3 class="text-xl font-semibold text-indigo-600 mb-4">
                    {{ $domainName }}
                </h3>

                <div class="space-y-6">
                    @foreach($domainQuestions as $question)

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">
                                {{ $question->text }}
                            </label>

                            <div class="flex gap-4">
                                @for($i = $question->min_value; $i <= $question->max_value; $i++)
                                    <label class="flex items-center gap-1">
                                        <input type="radio"
                                               name="question_{{ $question->id }}"
                                               value="{{ $i }}"
                                               required
                                               class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600">
                                            {{ $i }}
                                        </span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>

        @endforeach

        <div class="text-right">
            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-xl
                           hover:bg-indigo-700 transition font-semibold shadow">
                Submit Check
            </button>
        </div>

    </form>

</x-app-layout>