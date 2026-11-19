<?php

namespace Tests\Feature;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Enums\UserRole;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\LostItem;
use App\Models\User;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    protected User $admin;
    protected User $student;
    protected ItemCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.management@school.edu'],
            [
                'name' => 'Admin Management',
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );
        $this->admin->role = UserRole::Admin;
        $this->admin->save();

        $this->student = User::firstOrCreate(
            ['email' => 'student.management@school.edu'],
            [
                'name' => 'Student Management',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->category = ItemCategory::first();
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Administrative Dashboard');
    }

    public function test_admin_can_approve_found_item(): void
    {
        $found = FoundItem::create([
            'user_id' => $this->student->id,
            'category_id' => $this->category->id,
            'item_name' => 'Review Item Test',
            'public_description' => 'Left in hallway',
            'date_found' => now()->toDateString(),
            'location_found' => 'Hallway',
            'photo_path' => 'test/item.jpg',
            'private_details' => 'Has serial 998811',
            'status' => FoundItemStatus::PendingReview->value,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.reports.found.approve', $found), [
            'storage_location' => 'Locker 4',
        ]);

        $response->assertRedirect();
        $this->assertEquals(FoundItemStatus::Approved, $found->fresh()->status);
        $this->assertEquals('Locker 4', $found->fresh()->storage_location);
        $this->assertEquals($this->admin->id, $found->fresh()->reviewed_by);
    }

    public function test_admin_can_reject_lost_item_with_reason(): void
    {
        $lost = LostItem::create([
            'user_id' => $this->student->id,
            'category_id' => $this->category->id,
            'item_name' => 'Reject Lost Item Test',
            'description' => 'Lost my calculator',
            'date_lost' => now()->toDateString(),
            'location_last_seen' => 'Room 201',
            'status' => LostItemStatus::PendingReview->value,
        ]);

        $reason = 'Incomplete description and duplicate entry submitted earlier.';

        $response = $this->actingAs($this->admin)->post(route('admin.reports.lost.reject', $lost), [
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect();
        $this->assertEquals(LostItemStatus::Rejected, $lost->fresh()->status);
        $this->assertEquals($reason, $lost->fresh()->rejection_reason);
    }

    public function test_admin_can_approve_claim_and_record_physical_return(): void
    {
        $found = FoundItem::create([
            'user_id' => $this->student->id,
            'category_id' => $this->category->id,
            'item_name' => 'Full Workflow Earphones',
            'public_description' => 'White case earphones',
            'date_found' => now()->toDateString(),
            'location_found' => 'Gym',
            'photo_path' => 'test/ear.jpg',
            'private_details' => 'Engraved with initials AB',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $claimant = User::firstOrCreate(
            ['email' => 'workflow.claimant@school.edu'],
            ['name' => 'Claimant Person', 'role' => UserRole::User, 'password' => bcrypt('Password123'), 'password_changed_at' => now()]
        );

        $claim = ClaimRequest::create([
            'found_item_id' => $found->id,
            'claimant_id' => $claimant->id,
            'proof_description' => 'These are my earphones with initials AB engraved on the lid.',
            'status' => ClaimRequestStatus::Pending,
        ]);

        // 1. Admin Approves Claim
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.claims.approve', $claim), [
            'pickup_instructions' => 'Bring student ID to room 102.',
        ]);

        $approveResponse->assertRedirect();
        $this->assertEquals(ClaimRequestStatus::Approved, $claim->fresh()->status);
        $this->assertEquals(FoundItemStatus::Claimed, $found->fresh()->status);

        // 2. Admin Records Physical Return Hand-Over
        $returnResponse = $this->actingAs($this->admin)->post(route('admin.claims.record-return', $claim), [
            'returned_to_name' => 'Claimant Person',
            'id_presented' => 'Student ID #99881',
            'notes' => 'Ownership verified, item handed over.',
        ]);

        $returnResponse->assertRedirect();
        $this->assertEquals(ClaimRequestStatus::Completed, $claim->fresh()->status);
        $this->assertEquals(FoundItemStatus::Returned, $found->fresh()->status);
        $this->assertDatabaseHas('item_returns', [
            'claim_request_id' => $claim->id,
            'found_item_id' => $found->id,
            'returned_to_name' => 'Claimant Person',
        ]);
    }

    public function test_admin_can_toggle_category_active(): void
    {
        $category = ItemCategory::first();
        $initialState = $category->is_active;

        $response = $this->actingAs($this->admin)->post(route('admin.categories.toggle-active', $category));

        $response->assertRedirect();
        $this->assertEquals(! $initialState, $category->fresh()->is_active);

        // Toggle back to preserve state
        $this->actingAs($this->admin)->post(route('admin.categories.toggle-active', $category));
        $this->assertEquals($initialState, $category->fresh()->is_active);
    }
}
