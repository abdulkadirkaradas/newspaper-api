<?php

namespace Database\Seeders;

use App\Models\{
    Announcement,
    Badge,
    BadgeImage,
    News,
    NewsCategories,
    NewsImages,
    NewsReactions,
    Notification,
    Permission,
    Reaction,
    User,
    UserAuthTokens,
    Warning
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRoles as DefaultRoles;

class TestingSeeder extends Seeder
{
    private const ITEM_COUNT = 5;

    public function run(): void
    {
        $user = $this->createAdminUser();
        $this->assignPermissions($user);
        $this->generateAuthToken($user);

        $categories = $this->createCategories();
        $news = $this->createNews($user, $categories);
        $this->createNewsDetails($user, $news);
        $this->createBadges($user);
        $this->createReactions($user);
        $this->createNotifications($user);
        $this->createWarnings($user);
        $this->createAnnouncements();
    }

    private function createAdminUser(): User
    {
        return User::create([
            'name' => fake()->name(),
            'lastname' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->email(),
            'password' => Hash::make('Asf123123'),
            'role_id' => DefaultRoles::Admin->value,
        ]);
    }

    private function assignPermissions(User $user): void
    {
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $permission = Permission::create([
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'granted' => (bool) random_int(0, 1),
            ]);
            $user->permissions()->attach($permission->id, ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function generateAuthToken(User $user): void
    {
        $token = Auth::guard('api')->login($user);
        UserAuthTokens::create(['token' => $token, 'user_id' => $user->id]);
    }

    private function createCategories(): array
    {
        $categories = [];
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $categories[] = NewsCategories::create([
                'name' => fake()->word(),
                'description' => fake()->paragraph(),
            ]);
        }
        return $categories;
    }

    private function createNews(User $user, array $categories): array
    {
        $news = [];
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $randomBool = (bool) random_int(0, 1);
            $news[] = News::create([
                'title' => fake()->title(),
                'content' => fake()->paragraph(10),
                'priority' => random_int(1, 3),
                'pinned' => $randomBool,
                'visibility' => $randomBool,
                'approved' => $randomBool,
                'approved_by' => $randomBool ? $user->id : null,
                'user_id' => $user->id,
                'category_id' => $categories[$i]->id,
            ]);
        }
        return $news;
    }

    private function createNewsDetails(User $user, array $news): void
    {
        foreach ($news as $item) {
            $image = NewsImages::create([
                'name' => fake()->name(),
                'ext' => fake()->fileExtension(),
                'fullpath' => fake()->filePath(),
                'user_id' => $user->id,
                'news_id' => $item->id,
            ]);
            $reaction = NewsReactions::create([
                'reaction' => fake()->word(),
                'type' => fake()->word(),
                'user_id' => $user->id,
                'news_id' => $item->id,
            ]);

            $item->newsImages()->save($image);
            $item->newsReactions()->save($reaction);
        }
    }

    private function createBadges(User $user): void
    {
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $badge = Badge::create([
                'name' => fake()->title(),
                'description' => fake()->paragraph(1),
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
    }

    private function createAnnouncements()
    {
        Announcement::factory()->count(self::ITEM_COUNT)->create();
    }

    private function createReactions(User $user): void
    {
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $reaction = Reaction::create([
                'reaction_type' => fake()->word(),
                'user_id' => $user->id,
            ]);
            $user->reactions()->save($reaction);
        }
    }

    private function createNotifications(User $user): void
    {
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $randomDate = $this->randomDateBetween('2024-01-01', '2024-12-31');
            $notif = Notification::create([
                'type' => fake()->word(),
                'title' => fake()->sentence(),
                'message' => fake()->paragraph(),
                'is_read' => (bool) random_int(0, 1),
                'user_id' => $user->id,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
            $user->notifications()->save($notif);
        }
    }

    private function createWarnings(User $user): void
    {
        for ($i = 0; $i < self::ITEM_COUNT; $i++) {
            $warns = Warning::create([
                'message' => fake()->sentence(),
                'reason' => fake()->word(),
                'warning_level' => random_int(1, 5),
                'user_id' => $user->id,
            ]);
            $user->warnings()->save($warns);
        }
    }

    private function randomDateBetween(string $startDate, string $endDate): string
    {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        return date('Y-m-d H:i:s', random_int($startTimestamp, $endTimestamp));
    }
}
