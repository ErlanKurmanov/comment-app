<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $me = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users = User::factory(10)->create();

        $news = News::factory()->create([
            'title' => 'Laravel 12 Release News',
            'description' => 'This is the main news post to test cursor pagination.',
        ]);

        $this->command->info('Creating comments tree for News ID: ' . $news->id);


        $rootComments = Comment::factory(30)
            ->recycle($users)
            ->create([
                'commentable_id' => $news->id,
                'commentable_type' => 'news',
                'parent_id' => null,
            ]);

        foreach ($rootComments->take(10) as $rootComment) {
            $repliesLevel1 = Comment::factory(rand(2, 5))
            ->recycle($users)
                ->create([
                    'commentable_id' => $news->id,
                    'commentable_type' => 'news',
                    'parent_id' => $rootComment->id,
                ]);

            foreach ($repliesLevel1->take(2) as $replyL1) {
                Comment::factory(rand(1, 3))
                    ->recycle($users)
                    ->create([
                        'commentable_id' => $news->id,
                        'commentable_type' => 'news',
                        'parent_id' => $replyL1->id,
                        'body' => 'Deep nested reply check @' . $replyL1->id
                    ]);
            }
        }

        $video = VideoPost::factory()->create(['title' => 'Funny Cat Video']);
        Comment::factory(5)->create([
            'commentable_id' => $video->id,
            'commentable_type' => 'video',
            'parent_id' => null,
        ]);

        $this->command->info('Database seeded! Check News ID ' . $news->id);
    }
}
