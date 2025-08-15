<?php

namespace Tests\Feature;

use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->otherUser = User::factory()->create([
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    #[Test]
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                    'token',
                ]
            ]);
    }

    #[Test]
    public function register_fails_with_missing_or_invalid_data()
    {
        // Attempt registration without email and password
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            // 'email' is missing
            'password' => 'pass123',
            // 'password_confirmation' missing
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',   // 'error'
                'message',  // 'Validation errors'
                'errors'    // validation error details
            ]);

        // Attempt registration with duplicate email
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $response2 = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'test@example.com', // duplicate
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response2->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors'
            ]);
    }

    #[Test]
    public function user_can_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'created_at', 'updated_at', 'deleted_at'],
                    'token',
                ]
            ]);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        // Attempt login with wrong password
        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout successful',
                'data' => null
            ]);

        // Check that the token was deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
    }

    #[Test]
    public function authenticated_user_can_get_their_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);//login user by sanctum

        $response = $this->getJson('/api/me');//call api

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User profile',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            ]);
    }

    #[Test]
    public function authenticated_user_can_get_users_list_with_unread_count()
    {
        $me = User::factory()->create();
        $otherUser1 = User::factory()->create(['name' => 'Alice']);
        $otherUser2 = User::factory()->create(['name' => 'Bob']);


        // Create some messages from other users to $me
        Message::create([
            'sender_id' => $otherUser1->id,
            'receiver_id' => $me->id,
            'body' => 'Unread message 1',
            'read_at' => null,
        ]);

        Message::create([
            'sender_id' => $otherUser2->id,
            'receiver_id' => $me->id,
            'body' => 'Unread message 2',
            'read_at' => now(),
        ]);

        Sanctum::actingAs($me);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Users list',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'email', 'unread_count']
                ]
            ]);

        // Check that unread_count matches expected value
        $data = $response->json('data');

        $alice = collect($data)->firstWhere('id', $otherUser1->id);
        $bob   = collect($data)->firstWhere('id', $otherUser2->id);

        $this->assertEquals(1, $alice['unread_count']); // Alice has 1 unread
        $this->assertEquals(0, $bob['unread_count']);   // Bob has 0 unread
    }

    #[Test]
    public function user_can_send_message_to_another_user()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Sanctum::actingAs($sender);

        $response = $this->postJson('/api/messages', [
            'receiver_id' => $receiver->id,
            'body'        => 'Hello, this is a test message',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'sender_id',
                    'receiver_id',
                    'body',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Message sent',
                'data' => [
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'body' => 'Hello, this is a test message',
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'Hello, this is a test message',
        ]);
    }

    #[Test]
    public function user_can_fetch_conversation_and_messages_to_them_are_marked_as_read()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create messages: some from user->otherUser, some from otherUser->user
        $message1 = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'body' => 'Hello other user',
            'read_at' => null,
        ]);

        $message2 = Message::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'body' => 'Hello me',
            'read_at' => null, // unread initially
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/messages/{$otherUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Conversation',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'sender_id', 'receiver_id', 'body', 'read_at', 'created_at', 'updated_at']
                ]
            ]);

        $data = $response->json('data');

        // Check that all messages between the two users are returned
        $this->assertCount(2, $data);

        // Reload message2 from DB to check if it was marked as read
        $message2->refresh();
        $this->assertNotNull($message2->read_at);
    }

    #[Test]
    public function user_can_send_typing_indicator()
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        Sanctum::actingAs($user);

        Event::fake(); // prevent actual broadcast

        $response = $this->postJson('/api/typing', [
            'receiver_id' => $receiver->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Typing indicator',
                'data' => ['ok' => true]
            ]);

        // Assert that the UserTyping event was dispatched
        Event::assertDispatched(UserTyping::class, function ($event) use ($user, $receiver) {
            return $event->from === $user->id
                && $event->to === $receiver->id;
        });
    }


}
