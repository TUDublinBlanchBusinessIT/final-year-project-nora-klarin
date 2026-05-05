<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitWellbeingCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses'               => ['required', 'array', 'min:1'],
            'responses.*.question_id' => ['required', 'integer', 'distinct'],
            'responses.*.raw_value'   => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'responses.required'               => 'At least one response is required.',
            'responses.*.question_id.required' => 'Each response must include a question_id.',
            'responses.*.question_id.distinct' => 'Duplicate question IDs are not allowed.',
            'responses.*.raw_value.required'   => 'Each response must include a raw_value.',
            'responses.*.raw_value.integer'    => 'raw_value must be an integer.',
        ];
    }
}
