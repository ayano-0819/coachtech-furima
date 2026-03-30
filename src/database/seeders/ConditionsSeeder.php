<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conditions')->insertOrIgnore([
            ['name' => '良好'],
            ['name' => '目立った傷や汚れなし'],
            ['name' => 'やや傷や汚れあり'],
            ['name' => '状態が悪い'],
        ]);
    }
}