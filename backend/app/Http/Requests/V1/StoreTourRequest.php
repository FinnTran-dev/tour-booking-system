<?php

namespace App\Http\Requests\V1;

use App\Models\Tour;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            // The default status Draft is enforced via the service
            'dates' => ['nullable', 'array'],
            'dates.*.date' => ['required', 'date', 'after_or_equal:today'],
            'dates.*.end_date' => ['required', 'date', 'after_or_equal:dates.*.date'],
            'dates.*.capacity' => ['required', 'integer', 'min:1'],
            'dates.*.status' => ['sometimes', 'required', \Illuminate\Validation\Rule::in([\App\Models\TourDate::STATUS_ENABLED, \App\Models\TourDate::STATUS_DISABLED])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tour name',
            'dates.*.date' => 'start date',
            'dates.*.end_date' => 'end date',
        ];
    }

    public function messages(): array
    {
        return [
            'dates.*.date.required' => 'A tour date is required.',
            'dates.*.date.after_or_equal' => 'The tour start date must be today or a future date.',
            'dates.*.end_date.required' => 'The tour end date is required.',
            'dates.*.end_date.after_or_equal' => 'The tour end date must be after or equal to the start date.',
        ];
    }
}
