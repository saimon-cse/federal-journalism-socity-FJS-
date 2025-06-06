<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Other seeders
            RolePermissionSeeder::class,
            // DivisionSeeder::class, // etc.
        ]);
    }
}
