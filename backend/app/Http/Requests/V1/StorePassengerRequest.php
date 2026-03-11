<?php

namespace App\Http\Requests\V1;

use App\Models\Passenger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePassengerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'given_name'    => ['required', 'string', 'max:255'],
            'surname'       => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'status'        => ['nullable', Rule::in([Passenger::STATUS_ENABLED, Passenger::STATUS_DISABLED])],
        ];
    }

    public function attributes(): array
    {
        return [
            'given_name' => 'first name',
            'surname' => 'surname',
            'date_of_birth' => 'date of birth',
        ];
    }
}
