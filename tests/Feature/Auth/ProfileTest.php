<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('My profile');
        $response->assertSee($user->email);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'department' => 'Computer Science',
            'phone' => '+1234567890',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('Computer Science', $user->department);
        $this->assertSame('+1234567890', $user->phone);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');
    }

    public function test_user_with_temporary_password_is_forced_to_profile(): void
    {
        $user = User::factory()->mustChangePassword()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/profile');
        $response->assertSessionHas('warning');
    }
}
