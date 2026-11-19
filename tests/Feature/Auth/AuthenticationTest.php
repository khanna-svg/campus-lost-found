<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'student@selftest.invalid',
            'password' => 'Password123',
            'role' => UserRole::User,
        ]);

        $response = $this->post('/login', [
            'email' => 'student@selftest.invalid',
            'password' => 'Password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/home');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'student@selftest.invalid',
            'password' => 'Password123',
        ]);

        $this->post('/login', [
            'email' => 'student@selftest.invalid',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@selftest.invalid',
            'password' => 'Password123',
        ]);

        $this->post('/login', [
            'email' => 'inactive@selftest.invalid',
            'password' => 'Password123',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Administrative Dashboard');
    }
}
