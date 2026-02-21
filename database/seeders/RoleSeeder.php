<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->upsert([
            ['name' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'petugas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'user', 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['updated_at']);
    }
}
