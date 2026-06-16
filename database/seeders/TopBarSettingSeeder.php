<?php

namespace Database\Seeders;

use App\Models\TopBarSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TopBarSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TopBarSetting::create([
            'is_visible' => true,
            'text_content' => 'مرحبا بكم في حلويات البوغانمي',
        ]);
    }
}
