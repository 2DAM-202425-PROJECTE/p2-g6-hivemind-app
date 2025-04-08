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

        // Crear usuarios específicos
        $testUser = User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'profile_photo_path' => null,
            'banner_photo_path' => null,
            'email_verified_at' => now(),
        ]);

        $adminUser = User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'profile_photo_path' => null,
            'banner_photo_path' => null,
            'email_verified_at' => now(),
        ]);

        $normalUser = User::factory()->withPersonalTeam()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'profile_photo_path' => null,
            'banner_photo_path' => null,
            'email_verified_at' => now(),
        ]);

        // Crear usuarios aleatorios
        User::factory()->count(20)->create();

        // Crear publicaciones, likes y comentarios
        Post::factory()->count(35)->create([
            'id_user' => fn() => User::inRandomOrder()->first()->id,
        ]);

        Like::factory()->count(1000)->create([
            'post_id' => fn() => Post::inRandomOrder()->first()->id,
            'user_id' => fn() => User::inRandomOrder()->first()->id,
        ]);

        Comment::factory()->count(500)->create([
            'post_id' => fn() => Post::inRandomOrder()->first()->id,
            'user_id' => fn() => User::inRandomOrder()->first()->id,
        ]);

        Story::factory()->count(10)->create([
            'id_user' => fn() => User::inRandomOrder()->first()->id,
        ]);
    }
}
