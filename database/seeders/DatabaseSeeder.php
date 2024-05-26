<?php

namespace Database\Seeders;

use App\Models\Roles;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
        * Create initial roles
        */
        Roles::insert([
            ["name" => "Admin", "type" => "administrator"],
            ["name" => "Moderator", "type" => "moderator"],
            ["name" => "Writer", "type" => "writer"],
        ]);

        /**
         * Creates the test values
        */
        // $this->call(TestingSeeder::class);
    }
}