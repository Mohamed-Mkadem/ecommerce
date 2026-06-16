<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'in:admin,moderator'],
            'status' => ['required', 'in:active,banned'],
        ];
    }
    public function messages()
    {
        $messages = [
            'en' => [
                'role.required' => 'The role field is required',
                'role.in' => 'The role must be \'admin\' or \'moderaotr\'',
                'status.required' => 'The status field is required',
                'status.in' => 'The status must be \'active\' or \'banned\'',
            ],
            'ar' => [
                'role.required' => 'حقل الدور مطلوب',
                'role.in' => 'يجب أن يكون الدور \'مدير\' أو \'مشرف\'',
                'status.required' => 'حقل الحالة مطلوب',
                'status.in' => 'يجب أن تكون الحالة \'نشط\' أو \'غير نشط\'',
            ],
            'fr' => [
                'role.required' => 'Le champ rôle est requis',
                'role.in' => 'Le rôle doit être \'admin\' ou \'moderateur\'',
                'status.required' => 'Le champ status est requis',
                'status.in' => 'La status doit être \'actif\' ou \'inactif\'',
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
