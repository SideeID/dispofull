<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('department')?->id;
        return [
            'name' => ['sometimes','required','string','max:150'],
            'code' => ['sometimes','required','string','max:20', Rule::unique('departments','code')->ignore($id)],
            'description' => ['nullable','string'],
            'type' => ['sometimes','required','in:rektorat,unit_kerja'],
            'is_active' => ['sometimes','boolean']
        ];
    }
}
