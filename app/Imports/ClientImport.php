<?php

namespace App\Imports;

use App\Models\Client;
use App\Rules\UniqueNamePhone;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{

    use SkipsFailures;

    protected $failures = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $client = Client::where('phone', $row['mobile'])->exists();
        if (!$client) {
            return new Client([
                'name' => $row['nom'],
                'phone' => $row['mobile'],
                'mobile2' => $row['mobile2'],
                'address' => $row['adresse'],
                'state_id' => $row['gouvernorat'],
            ]);
        }
    }
    public function rules(): array
    {

        return [
            'nom' => ['required', 'string', 'max:255'],
            'mobile' => [
                'required',
                'digits:8',
                'regex:/^[234579]\d{7}$/',
            ],
            'mobile2' => [
                'nullable',
                'digits:8',
                'regex:/^[234579]\d{7}$/',
            ],
            'adresse' => ['required', 'string', 'max:500'],
            'gouvernorat' => ['required', 'exists:states,id'],
        ];
    }

    public function  customValidationMessages(): array
    {

        $locales = [
            'ar' => [
                'mobile.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'mobile.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'mobile2.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'mobile2.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'nom.max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
                'adresse.max' => 'يجب ألا يتجاوز العنوان 500 حرف.',
                'gouvernorat.exists' => 'الولاية غير موجودة',
                'mobile.unique' => 'رقم الهاتف مستخدم بالفعل.'
            ],
            'en' => [
                'mobile.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'mobile.digits' => 'The phone field must be 8 digits.',
                'mobile2.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'mobile2.digits' => 'The phone field must be 8 digits.',
                'nom.max' => 'The name must not exceed 255 characters.',
                'adresse.max' => 'The address must not exceed 500 characters.',
                'gouvernorat.exists' => 'The state doesn\'t exist',
                'mobile.unique' => 'The phone number has already been taken.'
            ],
            'fr' => [
                'mobile.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'mobile.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'mobile2.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'mobile2.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'adresse.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',
                'gouvernorat.exists' => 'Le gouvernorat n\'existe pas.',
                'mobile.unique' => 'Le numéro de téléphone est déjà utilisé.'
            ]
        ];

        return $locales[app()->getLocale()];
    }
    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }
    public function chunkSize(): int
    {
        return 25;
    }
}
