<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => [
                'required',
                Rule::exists('users', 'id')->withoutTrashed(), // For soft deletes
            ],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}

