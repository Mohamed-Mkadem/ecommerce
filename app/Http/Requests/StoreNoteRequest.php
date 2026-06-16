<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
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
        return [
            'content' => ['required', 'string'],
            'type' => ['required', 'string'],
            'id' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {

        $messages = [
            'ar' => [
                'content.string' =>  'حقل المحتوى يجب أن يكون نص',
            ],
            'fr' => [
                'content.string' =>  'Le champ contenu doit être une chaîne de caractères',
            ],
            'en' => [
                'content.string' =>  'The note field must be a string',
            ],
        ];
        return $messages[app()->getLocale()];
    }
}
