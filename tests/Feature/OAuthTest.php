<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_google_oauth_redirect_works()
    {
        $response = $this->get('/auth/google');

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_google_oauth_callback_creates_user()
    {
        $mockGoogleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $mockGoogleUser->shouldReceive('getId')->andReturn('google123');
        $mockGoogleUser->shouldReceive('getName')->andReturn('João Silva');
        $mockGoogleUser->shouldReceive('getEmail')->andReturn('joao@gmail.com');
        $mockGoogleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        $this->mock('Laravel\Socialite\Contracts\Factory', function ($mock) use ($mockGoogleUser) {
            $mock->shouldReceive('driver->user')->andReturn($mockGoogleUser);
        });

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);

        // Check if user was created
        $this->assertDatabaseHas('users', [
            'email' => 'joao@gmail.com',
            'google_id' => 'google123',
        ]);
    }

    public function test_google_oauth_callback_updates_existing_user()
    {
        // Create existing user
        $user = User::factory()->create([
            'email' => 'joao@gmail.com',
            'google_id' => null,
        ]);

        $mockGoogleUser = Mockery::mock('Laravel\Socialite\Two\User');
        $mockGoogleUser->shouldReceive('getId')->andReturn('google123');
        $mockGoogleUser->shouldReceive('getName')->andReturn('João Silva');
        $mockGoogleUser->shouldReceive('getEmail')->andReturn('joao@gmail.com');
        $mockGoogleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        $this->mock('Laravel\Socialite\Contracts\Factory', function ($mock) use ($mockGoogleUser) {
            $mock->shouldReceive('driver->user')->andReturn($mockGoogleUser);
        });

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);

        // Check if user was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google123',
        ]);
    }

    public function test_google_oauth_url_endpoint_works()
    {
        $response = $this->getJson('/auth/google/url');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'url',
                ],
            ]);
    }

    public function test_google_oauth_revoke_requires_authentication()
    {
        $response = $this->postJson('/api/auth/google/revoke');

        $response->assertStatus(401);
    }

    public function test_google_oauth_revoke_works_for_authenticated_users()
    {
        $user = User::factory()->create(['google_id' => 'google123']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/google/revoke');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Google token revoked successfully',
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
