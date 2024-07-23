<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Role;
use App\Models\User;
use App\Models\Badge;
use App\Models\Warning;
use App\Models\Reaction;
use App\Models\BadgeImage;
use App\Models\NewsImages;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\NewsReactions;
use App\Models\UserAuthTokens;
use App\Models\UserPermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRoles as DefaultRoles;

class TestingSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $user = User::create([
            'name' => fake()->name(),
            'lastname' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->email(),
            'password' => Hash::make('Abcdef123'),
            'role_id' => DefaultRoles::Admin->value
        ]);

        // Create blank users
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name' => fake()->name(),
                'lastname' => fake()->lastName(),
                'username' => fake()->userName(),
                'email' => fake()->email(),
                'password' => Hash::make('Abcdef123'),
                'role_id' => random_int(2, 3)
            ]);
        }

        // Create and assign permissions
        for ($i = 0; $i < 5; $i++) {
            $permission = Permission::create([
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'granted' => (bool) random_int(0, 1),
            ]);

            $user->permissions()->attach($permission->id, ['created_at' => now(), 'updated_at' => now()]);
        }

        $token = Auth::guard('api')->login($user);

        UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

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
            ]);

            $image = BadgeImage::create([
                'name' => fake()->name(),
                'ext' => fake()->fileExtension(),
                'fullpath' => fake()->filePath(),
                'badge_id' => $badge->id,
            ]);

            $badge->badgeImages()->save($image);
            $user->badges()->attach($badge->id, ['created_at' => now(), 'updated_at' => now()]);
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
            $startDate = '2024-01-01 00:00:00';
            $endDate = '2024-12-31 23:59:59';
            $randomDate = $this->randomDateBetween($startDate, $endDate);
            $notif = Notification::create([
                'type' => fake()->word(),
                'title' => fake()->sentence(),
                'message' => fake()->paragraph(),
                'is_read' => (bool)rand(0, 1),
                'user_id' => $user->id,
                'created_at' => $randomDate,
                'updated_at' => $randomDate
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

    function randomDateBetween($startDate, $endDate) {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);

        return date("Y-m-d H:i:s", $randomTimestamp);
    }
}
