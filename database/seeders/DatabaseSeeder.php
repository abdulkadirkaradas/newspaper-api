<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
        * Create initial roles
        */
        Role::create(["name" => "Admin", "type" => "administrator"]);
        Role::create(["name" => "Moderator", "type" => "moderator"]);
        Role::create(["name" => "Writer", "type" => "writer"]);

        /**
         * Create dummy Admin user
        */
        $this->call(TestingSeeder::class);

        /**
         * Create dummy users
         */
        User::factory()->count(2)->create();
    }
}