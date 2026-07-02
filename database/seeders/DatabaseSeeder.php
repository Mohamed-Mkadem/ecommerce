<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\User;
use App\Models\Wrapper;
use Database\Seeders\ShippingSettingsSeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\TopBarSettingSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        //  Shipper::factory(3)->create();
        // Product::factory(5)->pack()->create();
        // $this->call([
        //     AdminSeeder::class,
        //     StateSeeder::class,
        //     TopBarSettingSeeder::class,
        //     CouponCodeSeeder::class,
        //     ShippingSettingsSeeder::class
        // ]);
        // Client::factory(10)->create();

        Order::factory(120)->create();
        // Product::factory(1)->create();
        // Wrapper::factory(1)->create();
    }
}
