<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AuditLogger;
use Tests\TestCase;

class AnalyticsAndAuditTest extends TestCase
{
    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.analytics@school.edu'],
            [
                'name' => 'Admin Analytics',
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );
        $this->admin->role = UserRole::Admin;
        $this->admin->save();

        $this->student = User::firstOrCreate(
            ['email' => 'student.analytics@school.edu'],
            [
                'name' => 'Student Analytics',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );
    }

    public function test_regular_user_cannot_access_analytics_or_logs(): void
    {
        $this->actingAs($this->student)->get(route('admin.analytics.index'))->assertForbidden();
        $this->actingAs($this->student)->get(route('admin.logs.index'))->assertForbidden();
        $this->actingAs($this->student)->get(route('admin.reports.export-csv'))->assertForbidden();
    }

    public function test_admin_can_view_analytics_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.analytics.index'));
        $response->assertOk();
        $response->assertSee('Overall Recovery Rate');
    }

    public function test_admin_can_export_reports_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export-csv'));
        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_admin_can_export_claims_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.claims.export-csv'));
        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_audit_logger_records_immutable_activity_log(): void
    {
        $logger = app(AuditLogger::class);

        $log = $logger->log(
            'system.test_action',
            $this->admin,
            'Test audit trail description for security verification',
            ['key' => 'value']
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'action' => 'system.test_action',
            'description' => 'Test audit trail description for security verification',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.index'));
        $response->assertOk();
        $response->assertSee('system.test_action');
    }
}
