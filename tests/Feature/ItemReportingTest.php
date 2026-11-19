<?php

namespace Tests\Feature;

use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Enums\UserRole;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\LostItem;
use App\Models\User;
use App\Services\SupabaseStorageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class ItemReportingTest extends TestCase
{
    use DatabaseTransactions;

    protected ItemCategory $category;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = ItemCategory::first() ?? ItemCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics-test-' . uniqid(),
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role' => UserRole::User,
        ]);
    }

    public function test_user_can_view_report_lost_item_form(): void
    {
        $response = $this->actingAs($this->user)->get('/report-lost');

        $response->assertOk();
        $response->assertSee('Report a Lost Item');
        $response->assertSee($this->category->name);
    }

    public function test_user_can_submit_lost_item_report_without_photo(): void
    {
        $response = $this->actingAs($this->user)->post('/lost-items', [
            'item_name' => 'Calculus Textbook',
            'category_id' => $this->category->id,
            'description' => 'Hardcover 9th edition with green cover',
            'date_lost' => now()->subDay()->format('Y-m-d'),
            'location_last_seen' => 'Room 402 Math Wing',
            'identifying_details' => 'My initials "JD" written on page 50',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lost_items', [
            'user_id' => $this->user->id,
            'item_name' => 'Calculus Textbook',
            'status' => LostItemStatus::PendingReview->value,
            'location_last_seen' => 'Room 402 Math Wing',
        ]);
    }

    public function test_user_can_submit_lost_item_report_with_photo(): void
    {
        $storageMock = Mockery::mock(SupabaseStorageService::class);
        $storageMock->shouldReceive('upload')
            ->once()
            ->andReturn('lost/sample-uuid.jpg');
        $this->app->instance(SupabaseStorageService::class, $storageMock);

        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('calculator.jpg', $png1x1, 'image/jpeg');

        $response = $this->actingAs($this->user)->post('/lost-items', [
            'item_name' => 'Casio Scientific Calculator',
            'category_id' => $this->category->id,
            'description' => 'Black calculator with sliding case',
            'date_lost' => now()->format('Y-m-d'),
            'location_last_seen' => 'Engineering Hallway',
            'photo' => $file,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lost_items', [
            'item_name' => 'Casio Scientific Calculator',
            'photo_path' => 'lost/sample-uuid.jpg',
        ]);
    }

    public function test_lost_item_validation_rejects_future_date(): void
    {
        $response = $this->actingAs($this->user)->post('/lost-items', [
            'item_name' => 'Bad Date Item',
            'category_id' => $this->category->id,
            'description' => 'Description here',
            'date_lost' => now()->addDays(2)->format('Y-m-d'),
            'location_last_seen' => 'Library',
        ]);

        $response->assertSessionHasErrors('date_lost');
    }

    public function test_user_can_view_report_found_item_form(): void
    {
        $response = $this->actingAs($this->user)->get('/report-found');

        $response->assertOk();
        $response->assertSee('Report a Found Item');
        $response->assertSee('Fraud Prevention Requirement');
    }

    public function test_found_item_requires_photo_and_private_details(): void
    {
        $response = $this->actingAs($this->user)->post('/found-items', [
            'item_name' => 'Keys',
            'category_id' => $this->category->id,
            'public_description' => 'Bunch of keys',
            'date_found' => now()->format('Y-m-d'),
            'location_found' => 'Bench outside canteen',
            // Missing photo and private_details
        ]);

        $response->assertSessionHasErrors(['photo', 'private_details']);
    }

    public function test_user_can_submit_found_item_report_successfully(): void
    {
        $storageMock = Mockery::mock(SupabaseStorageService::class);
        $storageMock->shouldReceive('upload')
            ->once()
            ->andReturn('found/found-item-uuid.jpg');
        $this->app->instance(SupabaseStorageService::class, $storageMock);

        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('found_wallet.png', $png1x1, 'image/png');

        $response = $this->actingAs($this->user)->post('/found-items', [
            'item_name' => 'Brown Leather Wallet',
            'category_id' => $this->category->id,
            'public_description' => 'A foldable brown leather wallet',
            'date_found' => now()->format('Y-m-d'),
            'location_found' => 'Cafeteria Table 12',
            'photo' => $file,
            'private_details' => 'Contains 350 pesos in cash and a cinema ticket from May 10',
            'remarks' => 'Handed over to security guard at building entrance',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('found_items', [
            'user_id' => $this->user->id,
            'item_name' => 'Brown Leather Wallet',
            'photo_path' => 'found/found-item-uuid.jpg',
            'status' => FoundItemStatus::PendingReview->value,
            'private_details' => 'Contains 350 pesos in cash and a cinema ticket from May 10',
        ]);
    }

    public function test_user_can_view_own_report_details_with_private_details(): void
    {
        $foundItem = FoundItem::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'item_name' => 'Found Flash Drive',
            'public_description' => '64GB Kingston USB drive',
            'date_found' => now()->subDay(),
            'location_found' => 'Computer Lab 1',
            'photo_path' => 'found/flashdrive.jpg',
            'private_details' => 'Has a blue Marvel sticker on the back',
            'status' => FoundItemStatus::PendingReview,
        ]);

        $response = $this->actingAs($this->user)->get("/found-items/{$foundItem->id}");

        $response->assertOk();
        $response->assertSee('Found Flash Drive');
        $response->assertSee('Has a blue Marvel sticker on the back');
    }

    public function test_other_user_cannot_view_confidential_private_details(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $foundItem = FoundItem::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'item_name' => 'Stainless Water Flask',
            'public_description' => 'Silver vacuum insulated flask',
            'date_found' => now()->subDay(),
            'location_found' => 'Soccer Field',
            'photo_path' => 'found/flask.jpg',
            'private_details' => 'Dent on the bottom and engraving: "For Mark"',
            'status' => FoundItemStatus::PendingReview,
        ]);

        $response = $this->actingAs($this->user)->get("/found-items/{$foundItem->id}");

        $response->assertOk();
        $response->assertSee('Stainless Water Flask');
        $response->assertSee('Silver vacuum insulated flask');
        // Confidential details must NOT be visible to other regular users
        $response->assertDontSee('Dent on the bottom and engraving: "For Mark"');
    }
}
