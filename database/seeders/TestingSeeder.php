<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use App\Models\News;
use App\Models\NewsImages;
use App\Models\NewsReactions;
use App\Models\Permissions;
use App\Models\Roles;
use App\Models\UserAuthTokens;
use App\Models\UserMessages;
use App\Models\UserNews;
use App\Models\UserNotifications;
use App\Models\UserPermissions;
use App\Models\UserReactions;
use App\Models\User;
use App\Models\UserWarnings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestingSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [];
        for ($i = 0; $i < 5; $i++) {
            $permissions[$i] = Permissions::create([
                'name' => Str::random(10),
                'description' => Str::random(30),
                'granted_by_default' => (bool)random_int(0, 1),
            ]);
        }

        // Create users
        $users = User::create([
            'name' => 'test',
            'lastname' => 'test',
            'username' => 'test',
            'email' => 'test123@gmail.com',
            'password' => Hash::make('Abcdef123')
        ]);

        UserRoles::create([
            'user_id' => $users->id,
            'role_id' => rand(1, 3)
        ]);

        $token = Auth::guard('api')->login($users);

        UserAuthTokens::create([
            'token' => $token,
            'user_id' => $users->id
        ]);

        // Assign permissions to users
        for ($i = 0; $i < 5; $i++) {
            UserPermissions::create([
                'granted' => (bool)random_int(0, 1),
                'permission_id' => $permissions[$i]->id,
                'user_id' => $users->id
            ]);
        }

        // Create news
        $news = [];
        for ($i = 0; $i < 5; $i++) {
            $news[$i] = News::create([
                'title' => Str::random(10),
                'content' => Str::random(10),
                'user_id' => $users->id,
            ]);
        }

        // Create news images
        for ($i = 0; $i < 5; $i++) {
            NewsImages::create([
                'name' => Str::random(50),
                'ext' => Str::random(50),
                'fullpath' => Str::random(50),
                'user_id' => $users->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create news reactions
        $newsReactions = [];
        for ($i = 0; $i < 5; $i++) {
            $newsReactions[$i] = NewsReactions::create([
                'reaction' => Str::random(50),
                'user_id' => $users->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create user reactions
        $userReactions = [];
        for ($i = 0; $i < 5; $i++) {
            $userReactions[$i] = UserReactions::create([
                'reaction' => Str::random(50),
                'user_id' => $users->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create user news
        for ($i = 0; $i < 5; $i++) {
            UserNews::create([
                'news_id' => $news[$i]->id,
                'user_id' => $users->id,
                'reaction_id' => $newsReactions[$i]->id // veya $userReactions[$i]->id
            ]);
        }

        // Create user messages
        for ($i = 0; $i < 5; $i++) {
            UserMessages::create([
                'warning_text' => Str::random(50),
                'user_id' => $users->id
            ]);
        }

        // Create user notifications
        for ($i = 0; $i < 5; $i++) {
            UserNotifications::create([
                'notification' => Str::random(50),
                'user_id' => $users->id
            ]);
        }

        // Create user warnings
        for ($i = 0; $i < 5; $i++) {
            UserWarnings::create([
                'message' => Str::random(50),
                'warning_level' => rand(1, 5),
                'user_id' => $users->id
            ]);
        }
    }
}
