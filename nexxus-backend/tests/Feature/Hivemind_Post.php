<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;


class Hivemind_Post extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test to create a post.
     */
    public function test_create_post(): void
    {
        $id_user = User::factory()->create();

        // Arrange: Prepare post data
        $postData = [
            'content' => 'Hello, this is a test. BTW, how are you?',
            'publish_date' => now(),
            'id_user' => $id_user->id,
        ];

        // Act: Create a post
        $post = Post::create($postData);

        // Assert: Check if the post was created successfully
        $this->assertDatabaseHas('post', [
            'content' => 'Hello, this is a test. BTW, how are you?',
        ]);

        $this->assertEquals('Hello, this is a test. BTW, how are you?', $post->content);
    }
}
