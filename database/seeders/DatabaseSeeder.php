<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // remainder of the model seeders
        $this->call([
            RoleAndPermissionSeeder::class,
            CommunitySeeder::class,
            EntitlementSeeder::class,
            MembershipSeeder::class,
            StatusSeeder::class,
            ReasonSeeder::class,
            CategorySeeder::class,
            FoodPreferenceSeeder::class,
            DrinkPreferenceSeeder::class,
            UserSeeder::class,
            EmailSeeder::class,
            ReplySeeder::class,
            AllergySeeder::class,
            PostSeeder::class,
            ArticleSeeder::class,
            NewsSeeder::class,
            StorySeeder::class,
            VenueSeeder::class,
            EventTitleSeeder::class,
            EventSeeder::class,
            CategoryEventSeeder::class,
            CategoryNewsSeeder::class,
            CategoryArticleSeeder::class,
            CategoryStorySeeder::class,
            //CategorySeeder::class,
            EntitlementUserSeeder::class,
            EventGuestSeeder::class,
            EventUserSeeder::class,
            GallerySeeder::class,
            AlbumSeeder::class,
            ImageSeeder::class,
            CommentSeeder::class,
            CommentReplySeeder::class,
            DislikeSeeder::class,
            LikeSeeder::class,
            ParticipantDetailSeeder::class,
            RatingSeeder::class,
            ReportSeeder::class,
            FollowSeeder::class,
        ]);
    }
}
