<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CouponCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couponCodes = [
            [
                'code' => 'BG10',
                'value' => 10,
                'status' => 'active',
            ],
            [
                'code' => 'BG20',
                'value' => 20,
                'status' => 'active',
            ],
            [
                'code' => 'BG30',
                'value' => 30,
                'status' => 'inactive',
            ],
        ];
        foreach ($couponCodes as $couponCode) {

            DB::table('coupon_codes')->insert([
                'code' => $couponCode['code'],
                'value' => $couponCode['value'],
                'status' => $couponCode['status'],
            ]);
        }
    }
}
