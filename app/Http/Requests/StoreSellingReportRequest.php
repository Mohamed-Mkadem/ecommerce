<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellingReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:selling_reports,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages()
    {
        $messages = [
            'en' => [
                'name.required' => 'The report name is required.',
                'name.string' => 'The report name must be a string.',
                'name.max' => 'The report name may not be greater than 255 characters.',
                'name.unique' => 'The report name has already been taken.',
                'start_date.required' => 'The start date is required.',
                'start_date.date' => 'The start date is not a valid date.',
                'end_date.required' => 'The end date is required.',
                'end_date.date' => 'The end date is not a valid date.',
                'end_date.after_or_equal' => 'The end date must be a date after or equal to the start date.',
            ],
            'ar' => [
                'name.required' => 'اسم التقرير مطلوب.',
                'name.string' => 'يجب أن يكون اسم التقرير نصًا.',
                'name.max' => 'قد لا يكون اسم التقرير أكبر من 255 حرفًا.',
                'name.unique' => 'تم أخذ اسم التقرير بالفعل.',
                'start_date.required' => 'تاريخ البدء مطلوب.',
                'start_date.date' => 'تاريخ البدء ليس تاريخًا صالحًا.',
                'end_date.required' => 'تاريخ الانتهاء مطلوب.',
                'end_date.date' => 'تاريخ الانتهاء ليس تاريخًا صالحًا.',
                'end_date.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء بعد أو يساوي تاريخ البدء.',
            ],
            'fr' => [
                'name.required' => 'Le nom du rapport est requis.',
                'name.string' => 'Le nom du rapport doit être une chaîne de caractères.',
                'name.max' => 'Le nom du rapport ne peut pas dépasser 255 caractères.',
                'name.unique' => 'Le nom du rapport a déjà été pris.',
                'start_date.required' => 'La date de début est requise.',
                'start_date.date' => 'La date de début n\'est pas une date valide.',
                'end_date.required' => 'La date de fin est requise.',
                'end_date.date' => 'La date de fin n\'est pas une date valide.',
                'end_date.after_or_equal' => 'La date de fin doit être une date postérieure ou égale à la date de début.',
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
