<?php

namespace App\Http\Requests;

use App\Models\WellbeingCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * SubmitWellbeingCheckRequest
 *
 * Validates the response payload submitted by a young person completing
 * a wellbeing check.
 *
 * Validation rules:
 *   - responses array must be present and non-empty
 *   - each response must reference a question that belongs to this check
 *     (was selected and logged in check_question_log for this check_id)
 *   - raw_value must be an integer within the question's min/max range
 *   - no duplicate question_ids within the same submission
 */
class SubmitWellbeingCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled in controller via policy
        return true;
    }

    public function rules(): array
    {
        $check = $this->route('check');

        return [
            'responses'                => ['required', 'array', 'min:1'],
            'responses.*.question_id'  => [
                'required',
                'integer',
                'distinct',
                // Question must have been selected for this specific check
                function (string $attribute, mixed $value, \Closure $fail) use ($check) {
                    $inLog = DB::table('check_question_log')
                        ->where('wellbeing_check_id', $check->id)
                        ->where('question_id', $value)
                        ->exists();

                    if (!$inLog) {
                        $fail("Question {$value} was not part of this check.");
                    }
                },
            ],
            'responses.*.raw_value' => [
                'required',
                'integer',
                // Value must be within the question's defined min/max range
                function (string $attribute, mixed $value, \Closure $fail) use ($check) {
                    // Extract question_id from sibling field in same array index
                    // e.g. attribute = "responses.2.raw_value" -> index = 2
                    preg_match('/responses\.(\d+)\.raw_value/', $attribute, $matches);
                    $index      = $matches[1] ?? null;
                    $questionId = $this->input("responses.{$index}.question_id");

                    if (!$questionId) return;

                    $question = DB::table('questions')
                        ->where('id', $questionId)
                        ->select('min_value', 'max_value')
                        ->first();

                    if (!$question) return;

                    if ($value < $question->min_value || $value > $question->max_value) {
                        $fail("Value {$value} is out of range for question {$questionId} "
                            . "({$question->min_value}–{$question->max_value}).");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'responses.required'              => 'At least one response is required.',
            'responses.array'                 => 'Responses must be an array.',
            'responses.*.question_id.required'=> 'Each response must include a question_id.',
            'responses.*.question_id.distinct' => 'Duplicate question IDs are not allowed.',
            'responses.*.raw_value.required'  => 'Each response must include a raw_value.',
            'responses.*.raw_value.integer'   => 'raw_value must be an integer.',
        ];
    }
}
