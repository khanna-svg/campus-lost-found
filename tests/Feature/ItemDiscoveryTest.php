<?php

namespace Tests\Feature;

use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Enums\UserRole;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemDiscoveryTest extends TestCase
{
    protected User $user;
    protected User $admin;
    protected ItemCategory $categoryElectronics;
    protected ItemCategory $categoryBooks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'student.discovery@school.edu'],
            [
                'name' => 'Discovery Student',
                'role' => UserRole::User,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.discovery@school.edu'],
            [
                'name' => 'Discovery Admin',
                'role' => UserRole::Admin,
                'is_active' => true,
                'password' => bcrypt('Password123'),
                'password_changed_at' => now(),
            ]
        );

        $this->categoryElectronics = ItemCategory::where('slug', 'electronics')->first()
            ?? ItemCategory::first();

        $this->categoryBooks = ItemCategory::where('slug', 'books-stationery')->first()
            ?? ItemCategory::skip(1)->first()
            ?? $this->categoryElectronics;
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('items.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_discovery_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('items.index'));

        $response->assertOk();
        $response->assertViewIs('items.index');
        $response->assertSee('Campus Belongings');
    }

    public function test_only_approved_items_appear_in_public_directory(): void
    {
        $uniquePendingFound = 'PendingFoundItem_' . Str::random(8);
        $uniqueApprovedFound = 'ApprovedFoundItem_' . Str::random(8);
        $uniquePendingLost = 'PendingLostItem_' . Str::random(8);
        $uniqueApprovedLost = 'ApprovedLostItem_' . Str::random(8);

        // 1. Pending found item (should NOT appear)
        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryElectronics->id,
            'item_name' => $uniquePendingFound,
            'public_description' => 'Should not be seen publicly',
            'date_found' => now()->toDateString(),
            'location_found' => 'Secret Room',
            'photo_path' => 'test/pending.jpg',
            'private_details' => 'Private secret details',
            'status' => FoundItemStatus::PendingReview->value,
        ]);

        // 2. Approved found item (MUST appear)
        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryElectronics->id,
            'item_name' => $uniqueApprovedFound,
            'public_description' => 'Public approved item description',
            'date_found' => now()->toDateString(),
            'location_found' => 'Main Hallway',
            'photo_path' => 'test/approved.jpg',
            'private_details' => 'Private serial 123456789',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        // 3. Pending lost item (should NOT appear)
        LostItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryBooks->id,
            'item_name' => $uniquePendingLost,
            'description' => 'Pending lost item description',
            'date_lost' => now()->toDateString(),
            'location_last_seen' => 'Classroom 101',
            'status' => LostItemStatus::PendingReview->value,
        ]);

        // 4. Approved lost item (MUST appear)
        LostItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryBooks->id,
            'item_name' => $uniqueApprovedLost,
            'description' => 'Approved lost item description',
            'date_lost' => now()->toDateString(),
            'location_last_seen' => 'Science Lab',
            'status' => LostItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('items.index'));

        $response->assertOk();
        $response->assertSee($uniqueApprovedFound);
        $response->assertSee($uniqueApprovedLost);
        $response->assertDontSee($uniquePendingFound);
        $response->assertDontSee($uniquePendingLost);
    }

    public function test_confidential_private_details_are_never_rendered_on_discovery(): void
    {
        $uniqueSecretCode = 'SUPER_SECRET_TOKEN_99999';

        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryElectronics->id,
            'item_name' => 'Gold Watch With Secret Token',
            'public_description' => 'A metallic wrist watch found in locker area',
            'date_found' => now()->toDateString(),
            'location_found' => 'Gym Lockers',
            'photo_path' => 'test/watch.jpg',
            'private_details' => $uniqueSecretCode,
            'remarks' => 'Found under bench #4',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('items.index'));

        $response->assertOk();
        $response->assertSee('Gold Watch With Secret Token');
        $response->assertDontSee($uniqueSecretCode);
        $response->assertDontSee('Found under bench #4');
    }

    public function test_type_filter_distinguishes_found_and_lost(): void
    {
        $foundTitle = 'DistinctFoundItem_' . Str::random(6);
        $lostTitle = 'DistinctLostItem_' . Str::random(6);

        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryElectronics->id,
            'item_name' => $foundTitle,
            'public_description' => 'Blue umbrella found at east gate',
            'date_found' => now()->toDateString(),
            'location_found' => 'East Gate',
            'photo_path' => 'test/umbrella.jpg',
            'private_details' => 'Has floral pattern inside',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        LostItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryBooks->id,
            'item_name' => $lostTitle,
            'description' => 'Green notebook lost near cafeteria',
            'date_lost' => now()->toDateString(),
            'location_last_seen' => 'Cafeteria',
            'status' => LostItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        // Filter type=found
        $responseFound = $this->actingAs($this->user)->get(route('items.index', ['type' => 'found']));
        $responseFound->assertOk();
        $responseFound->assertSee($foundTitle);
        $responseFound->assertDontSee($lostTitle);

        // Filter type=lost
        $responseLost = $this->actingAs($this->user)->get(route('items.index', ['type' => 'lost']));
        $responseLost->assertOk();
        $responseLost->assertSee($lostTitle);
        $responseLost->assertDontSee($foundTitle);
    }

    public function test_keyword_and_location_search(): void
    {
        $keyword = 'HydroFlask_' . Str::random(5);

        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->categoryElectronics->id,
            'item_name' => "Stainless Steel {$keyword}",
            'public_description' => 'Insulated water bottle left on lecture desk',
            'date_found' => now()->toDateString(),
            'location_found' => 'Auditorium Hall B',
            'photo_path' => 'test/flask.jpg',
            'private_details' => 'Sticker with initials RL',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        // Search by keyword
        $responseSearch = $this->actingAs($this->user)->get(route('items.index', ['q' => $keyword]));
        $responseSearch->assertOk();
        $responseSearch->assertSee($keyword);

        // Search by location
        $responseLocation = $this->actingAs($this->user)->get(route('items.index', ['location' => 'Auditorium Hall B']));
        $responseLocation->assertOk();
        $responseLocation->assertSee($keyword);

        // Search with non-matching query
        $responseNone = $this->actingAs($this->user)->get(route('items.index', ['q' => 'NONEXISTENT_QUERY_XYZ']));
        $responseNone->assertOk();
        $responseNone->assertDontSee($keyword);
        $responseNone->assertSee('No items found');
    }

    public function test_category_filter(): void
    {
        $categoryTarget = $this->categoryElectronics;
        $categoryOther = $this->categoryBooks;

        $targetItem = 'CategoryTargetItem_' . Str::random(6);
        $otherItem = 'CategoryOtherItem_' . Str::random(6);

        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $categoryTarget->id,
            'item_name' => $targetItem,
            'public_description' => 'Target electronics item',
            'date_found' => now()->toDateString(),
            'location_found' => 'Tech Center',
            'photo_path' => 'test/item1.jpg',
            'private_details' => 'Private serial',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $categoryOther->id,
            'item_name' => $otherItem,
            'public_description' => 'Other books item',
            'date_found' => now()->toDateString(),
            'location_found' => 'Library',
            'photo_path' => 'test/item2.jpg',
            'private_details' => 'Private name inside cover',
            'status' => FoundItemStatus::Approved->value,
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('items.index', ['category' => $categoryTarget->id]));

        $response->assertOk();
        $response->assertSee($targetItem);
        $response->assertDontSee($otherItem);
    }
}
