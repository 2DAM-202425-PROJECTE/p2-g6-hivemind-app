<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class Hivemind_Comment extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create_comment(): void
    {
        $id_post = Post::factory()->create();
        $id_user = User::factory()->create();

        // Arrange: Prepare comment data
        $commentData = [
            'id_post' => $id_post->id,
            'id_user' => $id_user->id,
            'comment_text' => 'This is a test comment',
            'comment_date' => now(),
        ];

        // Act: Create a comment
        $comment = Comment::create($commentData);

        // Assert: Check if the comment was created successfully
        $this->assertDatabaseHas('comment', [
            'comment_text' => 'This is a test comment',
        ]);

        $this->assertEquals('This is a test comment', $comment->comment_text);
        $this->assertEquals(now()->format('d-m-Y'), $comment->comment_date);
    }
}
