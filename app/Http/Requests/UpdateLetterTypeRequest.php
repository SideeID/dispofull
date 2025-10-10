<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLetterTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('letterType')?->id;
        return [
            'name' => ['sometimes','required','string','max:150'],
            'code' => ['sometimes','required','string','max:30', Rule::unique('letter_types','code')->ignore($id)],
            'description' => ['nullable','string'],
            'number_format' => ['sometimes','nullable','string','max:255'],
            'is_active' => ['sometimes','boolean']
        ];
    }
}
