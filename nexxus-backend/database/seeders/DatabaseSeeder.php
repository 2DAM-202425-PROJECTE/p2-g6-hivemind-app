<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Story;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ItemsTableSeeder::class);

        User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
             'profile_photo_path' => null,
             'banner_photo_path' => null,
        ]);

        User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'profile_photo_path' => null,
            'banner_photo_path' => null,
        ]);

        User::factory()->withPersonalTeam()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'profile_photo_path' => null,
            'banner_photo_path' => null,
        ]);

        Post::factory()->create([
            'file_path' => '/uploads/1.png',
            'description' => 'This is a test post',
            'publish_date' => now(),
            'id_user' => 1,
        ]);

        Post::factory()->create([
            'file_path' => '/uploads/1.png',
            'description' => 'This is another test post',
            'publish_date' => now(),
            'id_user' => 2,
        ]);

        Post::factory()->create([
            'file_path' => '/uploads/1.png',
            'description' => 'This is the last test post',
            'publish_date' => now(),
            'id_user' => 3,
        ]);

        Like::factory()->create([
            'post_id' => 1,
            'user_id' => 2,
        ]);

        Like::factory()->create([
            'post_id' => 2,
            'user_id' => 3,
        ]);

        Like::factory()->create([
            'post_id' => 3,
            'user_id' => 1,
        ]);

        Comment::factory()->create([
            'post_id' => 1,
            'user_id' => 2,
            'content' => 'This is a test comment',
        ]);

        Comment::factory()->create([
            'post_id' => 2,
            'user_id' => 3,
            'content' => 'This is another test comment',
        ]);

        Comment::factory()->create([
            'post_id' => 3,
            'user_id' => 1,
            'content' => 'This is the last test comment',
        ]);

        Story::factory()->create([
            'file_path' => '/uploads/1.png',
            'description' => 'This is a test story',
            'publish_date' => now(),
            'id_user' => 1,
        ]);
    }
}
