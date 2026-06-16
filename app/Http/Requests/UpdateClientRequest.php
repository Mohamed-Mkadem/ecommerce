<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin', 'moderator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'digits:8',
                'regex:/^[234579]\d{7}$/',
                Rule::unique('clients', 'phone')->ignore($this->client->id)

            ],
            'phone2' => [
                'nullable',
                'digits:8',
                'regex:/^[234579]\d{7}$/',

            ],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'exists:states,id'],
            'city' => ['required', 'exists:cities,id'],
            'locality' => ['required', 'exists:localities,id'],
        ];
    }

    public function  messages(): array
    {

        $locales = [
            'ar' => [
                'phone.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'phone.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
                'phone2.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'phone2.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'name.max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
                'address.max' => 'يجب ألا يتجاوز العنوان 500 حرف.',

            ],
            'en' => [
                'phone2.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'phone2.digits' => 'The phone field must be 8 digits.',
                'phone.unique' => 'The phone number has already been taken.',
                'phone.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'phone.digits' => 'The phone field must be 8 digits.',
                'name.max' => 'The name must not exceed 255 characters.',
                'address.max' => 'The address must not exceed 500 characters.',
            ],
            'fr' => [
                'phone.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'phone.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'phone.unique' => 'Le numéro de téléphone est déjà utilisé.',
                'phone2.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'phone2.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'address.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',

            ]
        ];

        return $locales[app()->getLocale()];
    }
}
