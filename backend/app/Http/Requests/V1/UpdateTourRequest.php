<?php

namespace App\Http\Requests\V1;

use App\Models\Tour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in([Tour::STATUS_DRAFT, Tour::STATUS_PUBLIC])],
            'dates' => ['nullable', 'array'],
            'dates.*' => ['date', 'after_or_equal:today'],
        ];
    }
}
