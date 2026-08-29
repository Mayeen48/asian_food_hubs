<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_categories(): void
    {
        Category::create(['name' => 'Drinks']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Drinks']);
    }

    public function test_guest_can_load_products_by_category_and_only_sees_published(): void
    {
        $parent = Category::create(['name' => 'Drinks']);
        $sub = Category::create(['name' => 'Juice', 'parent_id' => $parent->id]);

        Product::create(['sku' => 'SKU-PUB', 'name' => 'Mango Juice', 'price' => 3.5, 'category_id' => $sub->id, 'published' => 1]);
        Product::create(['sku' => 'SKU-DRAFT', 'name' => 'Draft Juice', 'price' => 4.0, 'category_id' => $sub->id, 'published' => 0]);

        $response = $this->getJson("/api/products/by-category/{$parent->id}")->assertOk();

        $response->assertJsonFragment(['name' => 'Mango Juice']);
        $response->assertJsonMissing(['name' => 'Draft Juice']);
    }

    public function test_write_endpoints_still_require_authentication(): void
    {
        $this->postJson('/api/categories', ['name' => 'Hack'])->assertUnauthorized();
        $this->getJson('/api/products')->assertUnauthorized();
    }
}
