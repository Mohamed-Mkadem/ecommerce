<?php

namespace Database\Seeders;

use App\Models\ShippingSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingSetting::create([
            'tunis_acceptance_delivery_date' => now()->addDays(2),
            'wilayet_acceptance_delivery_date' => now()->addDays(2),
        ]);
    }
}
