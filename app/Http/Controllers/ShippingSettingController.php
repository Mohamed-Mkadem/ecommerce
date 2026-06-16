<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingSetting;
use Inertia\Inertia;

class ShippingSettingController extends Controller
{

    protected $messages = [
        'en' => [
            'tunisDeliveryDate.required' => 'The Tunis delivery date is required.',
            'wilayetDeliveryDate.required' => 'The Wilayet delivery date is required.',
            'tunisDeliveryDate.date' => 'The Tunis delivery date must be a valid date.',
            'wilayetDeliveryDate.date' => 'The Wilayet delivery date must be a valid date.',
            'tunisDeliveryDate.after' => 'The Tunis delivery date must be in the future.',
            'wilayetDeliveryDate.after' => 'The Wilayet delivery date must be in the future.',
        ],
        'fr' => [
            'tunisDeliveryDate.required' => 'La date de livraison à Tunis est requise.',
            'wilayetDeliveryDate.required' => 'La date de livraison à la wilaya est requise.',
            'tunisDeliveryDate.date' => 'La date de livraison à Tunis doit être une date valide.',
            'wilayetDeliveryDate.date' => 'La date de livraison à la wilaya doit être une date valide.',
            'tunisDeliveryDate.after' => 'La date de livraison à Tunis doit être dans le futur.',
            'wilayetDeliveryDate.after' => 'La date de livraison à la wilaya doit être dans le futur.',

        ],
        'ar' => [
            'tunisDeliveryDate.required' => 'تاريخ التسليم في تونس الكبرى مطلوب.',
            'wilayetDeliveryDate.required' => 'تاريخ التسليم في الولايات مطلوب.',
            'tunisDeliveryDate.date' => 'يجب أن يكون تاريخ التسليم في تونس الكبرى تاريخًا صالحًا.',
            'wilayetDeliveryDate.date' => 'يجب أن يكون تاريخ التسليم في الولاية تاريخًا صالحًا.',
            'tunisDeliveryDate.after' => 'يجب أن يكون تاريخ التسليم في تونس الكبرى في المستقبل.',
            'wilayetDeliveryDate.after' => 'يجب أن يكون تاريخ التسليم في الولاية في المستقبل.',

        ],
    ];

    public function edit($id)
    {
        $shippingSetting = ShippingSetting::findOrFail($id);

        return Inertia::render('Admin/ShippingSettings/Edit', [
            'shippingSetting' => $shippingSetting,
        ]);
    }
    public function update(Request $request, $id)
    {

        $shippingSetting = ShippingSetting::findOrFail($id);

        $validated =  $request->validate([
            'tunisDeliveryDate' => ['required', 'date', 'after:today'],
            'wilayetDeliveryDate' => ['required', 'date', 'after:today'],
        ], $this->messages[app()->getLocale()]);
        $shippingSetting->update([
            'tunis_acceptance_delivery_date' => $validated['tunisDeliveryDate'],
            'wilayet_acceptance_delivery_date' => $validated['wilayetDeliveryDate'],
        ]);
        return redirect()->back()->with('success', 'Shipping setting updated successfully.');
    }
}
