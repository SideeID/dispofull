<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:30','unique:letter_types,code'],
            'description' => ['nullable','string'],
            'number_format' => ['nullable','string','max:255'],
            'is_active' => ['sometimes','boolean']
        ];
    }
}
