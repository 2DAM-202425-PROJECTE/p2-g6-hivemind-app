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
        // //delete all data from the tables
        // User::truncate();
        // Post::truncate();

        // User::factory(10)->withPersonalTeam()->create();

         User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'profile_photo_path' => 'https://c.files.bbci.co.uk/assets/4da9473d-2f23-4b23-aac5-32c728a4da8f'
        ]);

        User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'profile_photo_path' => 'https://www.mskcc.org/sites/default/files/styles/large/public/node/226378/3x2/gettyimages-508687706_1200x800-tight.jpg'
        ]);

        User::factory()->withPersonalTeam()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'profile_photo_path' => 'https://img.freepik.com/foto-gratis/retrato-hombre-blanco-aislado_53876-40306.jpg?size=626&ext=jpg'
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
