<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::unprepared(file_get_contents(public_path('data.sql')));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
