<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\Users;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
        * Create initial roles
        */
        $roles = [
            ["name" => "Admin", "type" => "administrator"],
            ["name" => "Moderator", "type" => "moderator"],
            ["name" => "Writer", "type" => "writer"],
        ];

        Roles::insert($roles);
    }
}
