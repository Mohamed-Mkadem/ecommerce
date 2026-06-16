<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
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
        $rules = [
            'images' => ['required', 'array'],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
                Rule::dimensions()->height(1350)->width(1350)
            ]
        ];

        return $rules;
    }
    public function messages()
    {


        $messages = [
            'ar' => [
                'images.required' => 'يرجى تحميل صورة واحدة على الأقل.',
                'images.*.image' => 'يجب أن يكون كل ملف عبارة عن صورة.',
                'images.*.mimes' => 'يُسمح فقط بتنسيقات JPG وPNG وWebP.',
                'images.*.max' => 'يجب ألا يتجاوز حجم كل صورة 2 ميجابايت.',
            ],
            'fr' => [
                'images.required' => 'Veuillez télécharger au moins une image.',
                'images.*.image' => 'Chaque fichier doit être une image.',
                'images.*.mimes' => 'Seuls les formats JPG, PNG et WebP sont autorisés.',
                'images.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.'
            ],

            'en' => [
                'images.required' => 'Please upload at least one image.',
                'images.*.image' => 'Each file must be an image.',
                'images.*.mimes' => 'Only JPG, PNG and WebP formats are allowed.',
                'images.*.max' => 'Each image must not exceed 2MB.',
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
