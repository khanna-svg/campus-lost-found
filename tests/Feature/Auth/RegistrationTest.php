<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_returns_404_when_admin_managed(): void
    {
        config(['campus.auth_mode' => 'admin_managed']);

        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_registration_screen_can_be_rendered_when_self_register_enabled(): void
    {
        config(['campus.auth_mode' => 'self_register']);

        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Create your account');
    }

    public function test_new_users_cannot_register_with_disallowed_email_domain(): void
    {
        config([
            'campus.auth_mode' => 'self_register',
            'campus.allowed_email_domains' => ['myschool.edu'],
        ]);

        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_new_users_can_register_with_allowed_email_domain(): void
    {
        config([
            'campus.auth_mode' => 'self_register',
            'campus.allowed_email_domains' => ['myschool.edu'],
        ]);

        $response = $this->post('/register', [
            'name' => 'Valid Student',
            'email' => 'student@myschool.edu',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $response->assertRedirect('/home');
    }
}
