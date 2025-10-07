<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sudah dilindungi middleware role:admin
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'username' => ['nullable','string','max:50','unique:users,username'],
            'nip' => ['nullable','string','max:30','unique:users,nip'],
            'phone' => ['nullable','string','max:30'],
            'position' => ['nullable','string','max:100'],
            'role' => ['required','in:admin,rektorat,unit_kerja'],
            'status' => ['nullable','in:active,inactive'],
            'department_id' => ['nullable','exists:departments,id'],
            'password' => ['required','string','min:8'],
        ];
    }
}
