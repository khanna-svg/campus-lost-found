<?php

namespace Tests\Feature;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\UserRole;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClaimWorkflowTest extends TestCase
{
    protected User $finder;
    protected User $claimant;
    protected User $otherUser;
    protected User $admin;
    protected ItemCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = User::updateOrCreate(
            ['email' => 'finder.claim@school.edu'],
            [
                'name' => 'Finder User',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->claimant = User::updateOrCreate(
            ['email' => 'claimant.claim@school.edu'],
            [
                'name' => 'Claimant User',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->otherUser = User::updateOrCreate(
            ['email' => 'other.claim@school.edu'],
            [
                'name' => 'Other User',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.claim@school.edu'],
            [
                'name' => 'Admin User',
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );
        $this->admin->role = UserRole::Admin;
        $this->admin->save();

        $this->category = ItemCategory::first();
    }

    public function test_unauthenticated_user_cannot_access_claims(): void
    {
        $response = $this->get(route('claims.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_finder_cannot_claim_their_own_found_report(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Keys with Blue Lanyard',
            'public_description' => 'A set of keys found at cafeteria',
            'date_found' => now()->toDateString(),
            'location_found' => 'Cafeteria',
            'photo_path' => 'test/keys.jpg',
            'private_details' => 'Has 3 silver keys and 1 brass key',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        // Attempt to access form
        $response = $this->actingAs($this->finder)->get(route('claims.create', $foundItem));
        $response->assertRedirect(route('found-items.show', $foundItem));
        $response->assertSessionHas('error');

        // Attempt to post claim
        $postResponse = $this->actingAs($this->finder)->post(route('claims.store'), [
            'found_item_id' => $foundItem->id,
            'proof_description' => 'This is my own keys that I reported earlier.',
        ]);

        $postResponse->assertSessionHasErrors('found_item_id');
        $this->assertDatabaseMissing('claim_requests', [
            'found_item_id' => $foundItem->id,
            'claimant_id' => $this->finder->id,
        ]);
    }

    public function test_user_cannot_claim_pending_found_item(): void
    {
        $pendingItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Pending Review Laptop',
            'public_description' => 'Found in library',
            'date_found' => now()->toDateString(),
            'location_found' => 'Library',
            'photo_path' => 'test/laptop.jpg',
            'private_details' => 'Sticker of cat on lid',
            'status' => FoundItemStatus::PendingReview->value,
        ]);

        $response = $this->actingAs($this->claimant)->get(route('claims.create', $pendingItem));
        $response->assertRedirect(route('found-items.show', $pendingItem));
        $response->assertSessionHas('error');
    }

    public function test_user_can_submit_valid_claim(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Wireless Earbuds in Black Case',
            'public_description' => 'Black charging case found in room 302',
            'date_found' => now()->toDateString(),
            'location_found' => 'Room 302',
            'photo_path' => 'test/earbuds.jpg',
            'private_details' => 'Small scratch near USB-C port, engraved with letter M',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $proofText = 'These are my Sony WF-1000XM4 earbuds. They have a tiny scratch directly adjacent to the USB-C charging port and my name initials M.K. are on the bottom.';

        $response = $this->actingAs($this->claimant)->post(route('claims.store'), [
            'found_item_id' => $foundItem->id,
            'proof_description' => $proofText,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('claim_requests', [
            'found_item_id' => $foundItem->id,
            'claimant_id' => $this->claimant->id,
            'status' => ClaimRequestStatus::Pending->value,
            'proof_description' => $proofText,
        ]);
    }

    public function test_user_cannot_submit_duplicate_active_claims(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Scientific Calculator',
            'public_description' => 'Casio calculator found in Math building',
            'date_found' => now()->toDateString(),
            'location_found' => 'Math Hall',
            'photo_path' => 'test/calc.jpg',
            'private_details' => 'Serial number FX-991EX-00234',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        ClaimRequest::create([
            'found_item_id' => $foundItem->id,
            'claimant_id' => $this->claimant->id,
            'proof_description' => 'First valid proof of ownership with serial number FX-991EX-00234.',
            'status' => ClaimRequestStatus::Pending,
        ]);

        // Attempting to post a second claim while first is open
        $response = $this->actingAs($this->claimant)->post(route('claims.store'), [
            'found_item_id' => $foundItem->id,
            'proof_description' => 'Second attempt to claim same calculator.',
        ]);

        $response->assertSessionHasErrors('found_item_id');
    }

    public function test_claimant_can_view_own_claim_but_other_user_is_forbidden(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Thermal Water Bottle',
            'public_description' => 'White flask found on bench',
            'date_found' => now()->toDateString(),
            'location_found' => 'Sports Field',
            'photo_path' => 'test/bottle.jpg',
            'private_details' => 'Has dent on the bottom rim',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $claim = ClaimRequest::create([
            'found_item_id' => $foundItem->id,
            'claimant_id' => $this->claimant->id,
            'proof_description' => 'My Hydroflask flask with dent on the bottom and sticker.',
            'status' => ClaimRequestStatus::Pending,
        ]);

        // Claimant viewing own claim
        $responseClaimant = $this->actingAs($this->claimant)->get(route('claims.show', $claim));
        $responseClaimant->assertOk();
        $responseClaimant->assertSee('Claim Verification Details');

        // Other regular user cannot view
        $responseOther = $this->actingAs($this->otherUser)->get(route('claims.show', $claim));
        $responseOther->assertForbidden();

        // Admin can view
        $responseAdmin = $this->actingAs($this->admin)->get(route('claims.show', $claim));
        $responseAdmin->assertOk();
    }

    public function test_claimant_can_respond_to_additional_information_request(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->finder->id,
            'category_id' => $this->category->id,
            'item_name' => 'Leather Wallet',
            'public_description' => 'Brown bi-fold wallet',
            'date_found' => now()->toDateString(),
            'location_found' => 'Student Union',
            'photo_path' => 'test/wallet.jpg',
            'private_details' => 'Contains gym pass and metro card',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $claim = ClaimRequest::create([
            'found_item_id' => $foundItem->id,
            'claimant_id' => $this->claimant->id,
            'proof_description' => 'Brown fossil wallet with my cards inside.',
            'status' => ClaimRequestStatus::AdditionalInformationRequired,
            'admin_message' => 'Please specify what membership card is located in the left slot.',
        ]);

        $response = $this->actingAs($this->claimant)->post(route('claims.respond', $claim), [
            'claimant_response' => 'The left card slot contains an Anytime Fitness gym membership card with barcode #88412.',
        ]);

        $response->assertRedirect(route('claims.show', $claim));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('claim_requests', [
            'id' => $claim->id,
            'status' => ClaimRequestStatus::UnderReview->value,
            'claimant_response' => 'The left card slot contains an Anytime Fitness gym membership card with barcode #88412.',
        ]);
    }
}
