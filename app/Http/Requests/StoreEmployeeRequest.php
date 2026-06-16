<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:50'],
            'role' => ['required', 'in:moderator,admin']
        ];
    }

    public function messages()
    {
        $messages = [
            'en' => [
                'first_name.required' => 'The first name is required',
                'first_name.string' => 'The first name must be a string',
                'first_name.max' => 'The maximum number of characters for the first name is 255',
                'last_name.required' => 'The last name is required',
                'last_name.string' => 'The last name must be a string',
                'last_name.max' => 'The maximum number of characters for the last name is 255',
                'email.required' => 'The email is required',
                'email.email' => 'The email must be valid email',
                'password.required' => 'The password is required',
                'password.min' => 'The minimum number of characters for the password is 8',
                'password.max' => 'The maximum number of characters for the password is 50',
                'role.required' => 'The role field is required',
                'role.in' => 'The role must be \'admin\' or \'moderaotr\'',
            ],
            'ar' => [
                'first_name.required' => 'الاسم الأول مطلوب',
                'first_name.string' => 'يجب أن يكون الاسم الأول نصّا',
                'first_name.max' => 'الحد الأقصى لعدد الأحرف للاسم الأول هو 255',
                'last_name.required' => ' اللقب مطلوب',
                'last_name.string' => 'يجب أن يكون  اللقب نصّا',
                'last_name.max' => 'الحد الأقصى لعدد أحرف اللقب هو 255',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'يجب أن يكون البريد الإلكتروني بريدًا إلكترونيًا صالحًا',
                'password.required' => 'كلمة المرور مطلوبة',
                'password.min' => 'الحد الأدنى لعدد الأحرف لكلمة المرور هو 8',
                'password.max' => 'الحد الأقصى لعدد الأحرف لكلمة المرور هو 50',
                'role.required' => 'حقل الدور مطلوب',
                'role.in' => 'يجب أن يكون الدور \'مدير\' أو \'مشرف\'',
            ],
            'fr' => [
                'first_name.required' => 'Le prénom est requis',
                'first_name.string' => 'Le prénom doit être une chaîne de caractères',
                'first_name.max' => 'Le nombre maximum de caractères pour le prénom est de 255',
                'last_name.required' => 'Le nom de famille est requis',
                'last_name.string' => 'Le nom de famille doit être une chaîne de caractères',
                'last_name.max' => 'Le nombre maximum de caractères pour le nom de famille est de 255',
                'email.required' => 'L\'email est requis',
                'email.email' => 'L\'email doit être un email valide',
                'password.required' => 'Le mot de passe est requis',
                'password.min' => 'Le nombre minimum de caractères pour le mot de passe est de 8',
                'password.max' => 'Le nombre maximum de caractères pour le mot de passe est de 50',
                'role.required' => 'Le champ rôle est requis',
                'role.in' => 'Le rôle doit être \'admin\' ou \'moderateur\'',
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
