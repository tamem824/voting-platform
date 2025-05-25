<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'starting_vote' => Carbon::now()->subMinutes(5),   // بدأ التصويت قبل 5 دقائق
            'ending_vote'   => Carbon::now()->addHours(50),    // ينتهي بعد 24 ساعة
            'is_active'     => true,
        ]);
    }
}
