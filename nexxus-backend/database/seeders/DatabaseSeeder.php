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

        // User::factory()->withPersonalTeam()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        // ]);

        Post::factory()->create([
            'content' => 'This is a test post',
            'publish_date' => now(),
            'id_user' => 1,
        ]);

        Post::factory()->create([
            'content' => 'This is another test post',
            'publish_date' => now(),
            'id_user' => 2,

        User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
        ]);

        User::factory()->withPersonalTeam()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),

        ]);
    }
}
