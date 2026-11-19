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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_regular_user_can_view_dashboard_with_empty_states(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Maria Santos',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Welcome back, Maria Santos');
        $response->assertSee('Active Lost Reports');
        $response->assertSee('Found Items Logged');
        $response->assertSee('Active Claims');
        $response->assertSee('Reunited Belongings');
        $response->assertSee('No lost items reported yet');
        $response->assertSee('No found items reported yet');
        $response->assertSee('No claim requests submitted');
    }

    public function test_dashboard_displays_user_reports_and_counts_accurately(): void
    {
        $category = ItemCategory::first() ?? ItemCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics-test',
        ]);

        $admin = User::factory()->admin()->create();

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        // Create a lost item for this user
        $lostItem = LostItem::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'item_name' => 'Graphing Calculator',
            'description' => 'TI-84 Plus CE Silver Edition',
            'date_lost' => now()->subDays(2),
            'location_last_seen' => 'Science Lab 3',
            'status' => LostItemStatus::PendingReview,
        ]);

        // Create a found item for this user (approved by admin)
        $foundItem = FoundItem::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'item_name' => 'Set of Dorm Keys',
            'public_description' => 'Two brass keys on a lanyard',
            'date_found' => now()->subDay(),
            'location_found' => 'Library 2nd Floor',
            'photo_path' => 'photos/keys.jpg',
            'private_details' => 'Room 204 stamped on one key',
            'status' => FoundItemStatus::Approved,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // Create a found item by another user, approved by admin
        $anotherFoundItem = FoundItem::create([
            'user_id' => $otherUser->id,
            'category_id' => $category->id,
            'item_name' => 'Black Backpack',
            'public_description' => 'Black nylon laptop backpack',
            'date_found' => now()->subDays(3),
            'location_found' => 'Cafeteria',
            'photo_path' => 'photos/backpack.jpg',
            'private_details' => 'Has a cracked zipper pull',
            'status' => FoundItemStatus::Approved,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // Claim request on the anotherFoundItem
        $claim = ClaimRequest::create([
            'found_item_id' => $anotherFoundItem->id,
            'claimant_id' => $user->id,
            'proof_description' => 'Contains my ID and headphones inside front pocket',
            'status' => ClaimRequestStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Graphing Calculator');
        $response->assertSee('Set of Dorm Keys');
        $response->assertSee('Black Backpack');
        $response->assertSee('Science Lab 3');
        $response->assertSee('Pending Review');
    }
}
