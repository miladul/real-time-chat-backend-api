<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TypingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or add auth logic if needed
    }

    public function rules(): array
    {
        return [
            'receiver_id' => [
                'required',
                Rule::exists('users', 'id')->withoutTrashed(),
            ],
        ];
    }
}
