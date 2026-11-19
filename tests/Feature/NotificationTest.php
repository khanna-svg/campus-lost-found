<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\NotificationService;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'student.notification@school.edu'],
            [
                'name' => 'Notification Student',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->otherUser = User::firstOrCreate(
            ['email' => 'other.notification@school.edu'],
            [
                'name' => 'Other Notification User',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->user->notifications()->delete();
        $this->otherUser->notifications()->delete();
    }

    public function test_unauthenticated_user_cannot_access_notifications(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_notifications(): void
    {
        $service = app(NotificationService::class);
        $service->send(
            $this->user,
            'report_approved',
            'Test Item Approved',
            'Your test lost item was verified by security.'
        );

        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Test Item Approved');
        $response->assertSee('Your test lost item was verified by security.');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $service = app(NotificationService::class);
        $notification = $service->send(
            $this->user,
            'announcement',
            'Campus Lost Office Schedule',
            'Office hours will be extended next week.'
        );

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->user)->post(route('notifications.read', $notification));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_other_users_notification(): void
    {
        $service = app(NotificationService::class);
        $notification = $service->send(
            $this->otherUser,
            'announcement',
            'Private Notice',
            'For other user only.'
        );

        $response = $this->actingAs($this->user)->post(route('notifications.read', $notification));

        $response->assertForbidden();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $service = app(NotificationService::class);
        $service->send($this->user, 'announcement', 'Note 1', 'Message 1');
        $service->send($this->user, 'announcement', 'Note 2', 'Message 2');

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }
}
