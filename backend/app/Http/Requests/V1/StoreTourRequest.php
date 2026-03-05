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
            'dates.*' => ['date', 'after_or_equal:today'],
        ];
    }
}
