<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Assume authenticated for this assessment
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'tour_date_id' => ['required', 'integer', 'exists:tour_dates,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.id' => ['nullable', 'integer', 'exists:passengers,id'],
            'passengers.*.given_name' => ['required_without:passengers.*.id', 'string', 'max:255'],
            'passengers.*.surname' => ['required_without:passengers.*.id', 'string', 'max:255'],
            'passengers.*.email' => ['nullable', 'email', 'max:255'],
            'passengers.*.phone' => ['nullable', 'string', 'max:20'],
            'passengers.*.date_of_birth' => ['required_without:passengers.*.id', 'date', 'before:today'],
            'passengers.*.special_request' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tour_id' => 'tour',
            'tour_date_id' => 'tour date',
            'customer_name' => 'contact name',
            'customer_email' => 'contact email',
            'passengers.*.given_name' => 'passenger given name',
            'passengers.*.surname' => 'passenger surname',
            'passengers.*.date_of_birth' => 'passenger date of birth',
        ];
    }

    public function messages(): array
    {
        return [
            'passengers.*.given_name.required_without' => 'Each passenger must have a given name.',
            'passengers.*.surname.required_without' => 'Each passenger must have a surname.',
            'passengers.*.date_of_birth.required_without' => 'Each passenger must have a date of birth.',
            'passengers.*.date_of_birth.before' => 'Passenger date of birth must be in the past.',
        ];
    }
}
