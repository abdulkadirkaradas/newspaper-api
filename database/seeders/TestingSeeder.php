<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use App\Models\Badge;
use App\Models\Warning;
use App\Models\Reaction;
use App\Models\UserRoles;
use App\Models\NewsImages;
use App\Models\BadgeImages;
use App\Models\Permissions;
use App\Models\NewsReactions;
use App\Models\Notification;
use App\Models\UserAuthTokens;
use App\Models\UserPermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                'granted' => (bool) random_int(0, 1),
            ]);
        }

        // Create user
        $user = User::create([
            'name' => fake()->name(),
            'lastname' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->email(),
            'password' => Hash::make('Abcdef123')
        ]);

        $userRole = UserRoles::create([
            'user_id' => $user->id,
            'role_id' => rand(1, 3)
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

            $user->news()->save($news[$i]);
        }

        // Create news images and reactions
        for ($i = 0; $i < 5; $i++) {
            $image = NewsImages::create([
                'name' => fake()->name(),
                'ext' => fake()->fileExtension(),
                'fullpath' => fake()->filePath(),
                'user_id' => $user->id,
                'news_id' => $news[$i]->id,
            ]);

            $reaction = NewsReactions::create([
                'reaction' => fake()->word(),
                'type' => fake()->word(),
                'user_id' => $user->id,
                'news_id' => $news[$i]->id
            ]);

            $news[$i]->newsImages()->save($image);
            $news[$i]->newsReactions()->save($reaction);
        }

        // Create badges and images
        for ($i = 0; $i < 5; $i++) {
            $badge = Badge::create([
                'name' => fake()->title(),
                'description' => fake()->paragraph(),
                'type' => fake()->word(),
                'user_id' => $user->id,
            ]);

            $image = BadgeImages::create([
                'name' => fake()->name(),
                'ext' => fake()->fileExtension(),
                'fullpath' => fake()->filePath(),
                'badge_id' => $badge->id
            ]);

            $badge->badgeImages()->save($image);
            $user->badges()->save($badge);
        }

        ///

        // Create reactions
        for ($i = 0; $i < 5; $i++) {
            $reaction = Reaction::create([
                'reaction_type' => fake()->word(),
                'user_id' => $user->id,
            ]);

            $user->reactions()->save($reaction);
        }

        // Create user notifications
        for ($i = 0; $i < 5; $i++) {
            $notif = Notification::create([
                'type' => fake()->word(),
                'message' => fake()->sentence(),
                'is_read' => false,
                'user_id' => $user->id
            ]);

            $user->notifications()->save($notif);
        }

        // Create user warnings
        for ($i = 0; $i < 5; $i++) {
            $warns = Warning::create([
                'message' => fake()->sentence(),
                'reason' => fake()->word(),
                'warning_level' => rand(1, 5),
                'user_id' => $user->id
            ]);

            $user->warnings()->save($warns);
        }
    }
}
