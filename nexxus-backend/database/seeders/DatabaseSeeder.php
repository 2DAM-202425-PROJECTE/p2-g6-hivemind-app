<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
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

        ]);

        Post::factory()->create([
            'content' => 'https://c.files.bbci.co.uk/assets/4da9473d-2f23-4b23-aac5-32c728a4da8f',
            'file_path' => '/uploads/1.png',
            'description' => 'This is a test post',
            'publish_date' => now(),
            'id_user' => 1,
        ]);

        Post::factory()->create([
            'content' => 'https://www.mskcc.org/sites/default/files/styles/large/public/node/226378/3x2/gettyimages-508687706_1200x800-tight.jpg',
            'file_path' => '/uploads/1.png',
            'description' => 'This is another test post',
            'publish_date' => now(),
            'id_user' => 2,
        ]);
    }
}
