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
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'granted' => (bool)random_int(0, 1),
            ]);
        }

        // Create users
        $user = User::create([
            'name' => fake()->name(),
            'lastname' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->email(),
            'password' => Hash::make('Abcdef123')
        ]);

        $userRole = UserRoles::create([
            'user_id' => $user->id,
            'roles_id' => rand(1, 3)
        ]);

        $token = Auth::guard('api')->login($user);

        UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        // Assign permissions to users
        for ($i = 0; $i < 5; $i++) {
            UserPermissions::create([
                'user_id' => $user->id,
                'user_role_id' => $userRole->id,
                'permission_id' => $permissions[$i]->id,
            ]);
        }

        // Create news
        $news = [];
        for ($i = 0; $i < 5; $i++) {
            $news[$i] = News::create([
                'title' => fake()->title(),
                'content' => fake()->paragraph(),
                'user_id' => $user->id,
            ]);
        }

        // Create news images
        for ($i = 0; $i < 5; $i++) {
            NewsImages::create([
                'name' => fake()->name(),
                'ext' => fake()->fileExtension(),
                'fullpath' => fake()->filePath(),
                'user_id' => $user->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create news reactions
        $newsReactions = [];
        for ($i = 0; $i < 5; $i++) {
            $newsReactions[$i] = NewsReactions::create([
                'reaction' => fake()->word(),
                'user_id' => $user->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create user reactions
        $userReactions = [];
        for ($i = 0; $i < 5; $i++) {
            $userReactions[$i] = UserReactions::create([
                'reaction' => fake()->word(),
                'user_id' => $user->id,
                'news_id' => $news[$i]->id
            ]);
        }

        // Create user news
        for ($i = 0; $i < 5; $i++) {
            UserNews::create([
                'news_id' => $news[$i]->id,
                'user_id' => $user->id,
                'reaction_id' => $newsReactions[$i]->id // veya $userReactions[$i]->id
            ]);
        }

        // Create user messages
        for ($i = 0; $i < 5; $i++) {
            UserMessages::create([
                'warning_text' => fake()->sentence(),
                'user_id' => $user->id
            ]);
        }

        // Create user notifications
        for ($i = 0; $i < 5; $i++) {
            UserNotifications::create([
                'notification' => fake()->sentence(),
                'user_id' => $user->id
            ]);
        }

        // Create user warnings
        for ($i = 0; $i < 5; $i++) {
            UserWarnings::create([
                'message' => fake()->sentence(),
                'warning_level' => rand(1, 5),
                'user_id' => $user->id
            ]);
        }
    }
}
