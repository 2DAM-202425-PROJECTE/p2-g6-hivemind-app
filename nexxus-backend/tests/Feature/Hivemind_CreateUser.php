<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class Hivemind_CreateUser extends TestCase
{
    /**
     * A basic feature test to create a user.
     */

     public function test_create_user(): void
     {
         // Arrange: Prepare user data
         $userData = [
             'name' => 'John Doe',
             'email' => 'john.doe@dasrado.com',
             'password' => bcrypt('password123'),
         ];
 
         // Act: Create a user
         $user = User::create($userData);
 
         // Assert: Check if the user was created successfully
         $this->assertDatabaseHas('users', [
             'email' => 'john.doe@dasrado.com',
         ]);
 
         $this->assertEquals('John Doe', $user->name);
     }


}
