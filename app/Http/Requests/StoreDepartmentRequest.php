<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:20','unique:departments,code'],
            'description' => ['nullable','string'],
            'type' => ['required','in:rektorat,unit_kerja'],
            'is_active' => ['sometimes','boolean']
        ];
    }
}
