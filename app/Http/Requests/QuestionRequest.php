<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['required','in:short_text,long_text,single_choice,multi_choice'],
            'label' => ['required','string','max:500'],
            'required' => ['sometimes','boolean'],
        ];
    }
}

