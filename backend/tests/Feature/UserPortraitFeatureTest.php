<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\UserPortrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPortraitFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_returns_422_when_tag_ids_do_not_exist_and_no_dirty_data(): void
    {
        $user = User::factory()->create();
        $nonExistentTagId = 99999;

        $response = $this->actingAs($user)->postJson('/api/portraits', [
            'name' => 'Test Portrait',
            'age' => 30,
            'tag_ids' => [$nonExistentTagId],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('user_portraits', 0);
        $this->assertDatabaseCount('portrait_tags', 0);
    }

    public function test_destroy_returns_403_when_deleting_another_users_portrait(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $portrait = UserPortrait::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->deleteJson('/api/portraits/' . $portrait->getKey());

        $response->assertStatus(403);
        $this->assertModelExists($portrait);
        $this->assertDatabaseHas('user_portraits', ['id' => $portrait->id]);
    }

    public function test_store_with_multiple_tags_inserts_all_associations_idempotently(): void
    {
        $user = User::factory()->create();
        $category = TagCategory::factory()->create(['user_id' => $user->id]);
        $tag1 = Tag::factory()->create(['user_id' => $user->id, 'tag_category_id' => $category->id]);
        $tag2 = Tag::factory()->create(['user_id' => $user->id, 'tag_category_id' => $category->id]);
        $tag3 = Tag::factory()->create(['user_id' => $user->id, 'tag_category_id' => $category->id]);

        $tagIds = [$tag1->id, $tag2->id, $tag3->id];

        $response = $this->actingAs($user)->postJson('/api/portraits', [
            'name' => 'Multi-tag Portrait',
            'age' => 28,
            'tag_ids' => $tagIds,
        ]);

        $response->assertStatus(201);
        $portraitId = $response->json('id');
        $this->assertNotNull($portraitId);

        $this->assertDatabaseHas('user_portraits', ['id' => $portraitId, 'name' => 'Multi-tag Portrait']);

        foreach ($tagIds as $tagId) {
            $this->assertDatabaseHas('portrait_tags', [
                'user_portrait_id' => $portraitId,
                'tag_id' => $tagId,
            ]);
        }

        $this->assertDatabaseCount('portrait_tags', 3);
    }
}
