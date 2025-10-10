<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        return [
            'name' => ['sometimes','required','string','max:255'],
            'email' => ['sometimes','required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'username' => ['sometimes','nullable','string','max:50', Rule::unique('users','username')->ignore($userId)],
            'nip' => ['sometimes','nullable','string','max:30', Rule::unique('users','nip')->ignore($userId)],
            'phone' => ['sometimes','nullable','string','max:30'],
            'position' => ['sometimes','nullable','string','max:100'],
            'role' => ['sometimes','required','in:admin,rektorat,unit_kerja'],
            'status' => ['sometimes','nullable','in:active,inactive'],
            'department_id' => ['sometimes','nullable','exists:departments,id'],
            'password' => ['sometimes','nullable','string','min:8'],
        ];
    }
}
