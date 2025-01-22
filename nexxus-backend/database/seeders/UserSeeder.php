<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpass'), // Usa un hash seguro para las contraseñas
            'roles' => '["admin", "user"]',
        ]);

        $user2 =User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('userpass'),
        ]);

        Team::create([
            'name' => $user->name.'\'s Team',
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        Team::create([
            'name' => $user2->name.'\'s Team',
            'user_id' => $user2->id,
            'personal_team' => true,
        ]);
    }
}
