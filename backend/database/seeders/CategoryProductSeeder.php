<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Product::truncate();
        Category::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // === Top-level categories ===
        $food = Category::create(['name' => 'Food & Beverages', 'description' => 'Edible export products']);
        $textiles = Category::create(['name' => 'Textiles', 'description' => 'Fabric and garments']);
        $handicraft = Category::create(['name' => 'Handicrafts', 'description' => 'Handmade export crafts']);

        // === Subcategories ===
        $rice = Category::create(['name' => 'Rice & Grains', 'parent_id' => $food->id]);
        $tea = Category::create(['name' => 'Tea & Coffee', 'parent_id' => $food->id]);
        $spices = Category::create(['name' => 'Spices', 'parent_id' => $food->id]);

        $cotton = Category::create(['name' => 'Cotton Fabrics', 'parent_id' => $textiles->id]);
        $readywear = Category::create(['name' => 'Readywear Garments', 'parent_id' => $textiles->id]);

        $woodcraft = Category::create(['name' => 'Wooden Items', 'parent_id' => $handicraft->id]);
        $decor = Category::create(['name' => 'Home Décor', 'parent_id' => $handicraft->id]);

        // === Products ===
        Product::insert([
            [
                'sku' => 'FD100',
                'name' => 'Premium Basmati Rice',
                'description' => 'Long-grain aromatic rice ideal for export.',
                'price' => 45.50,
                'unit' => 'kg',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Basmati+Rice',
                'category_id' => $rice->id,
                'sort_order' => 1,
                'published' => true,
            ],
            [
                'sku' => 'FD200',
                'name' => 'Organic Green Tea',
                'description' => 'Hand-picked green tea leaves with refreshing aroma.',
                'price' => 38.20,
                'unit' => 'kg',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Green+Tea',
                'category_id' => $tea->id,
                'sort_order' => 2,
                'published' => true,
            ],
            [
                'sku' => 'FD300',
                'name' => 'Ground Turmeric',
                'description' => 'Pure turmeric powder with strong flavor and color.',
                'price' => 28.00,
                'unit' => 'kg',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Turmeric',
                'category_id' => $spices->id,
                'sort_order' => 3,
                'published' => true,
            ],
            [
                'sku' => 'TX400',
                'name' => 'Cotton Bedsheet Set',
                'description' => 'Soft and breathable cotton bedsheets for export.',
                'price' => 125.00,
                'unit' => 'set',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Cotton+Bedsheet',
                'category_id' => $cotton->id,
                'sort_order' => 4,
                'published' => true,
            ],
            [
                'sku' => 'TX500',
                'name' => 'Men’s T-Shirt (Bulk)',
                'description' => 'High-quality cotton T-shirts available in assorted colors.',
                'price' => 7.80,
                'unit' => 'piece',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=T-Shirt',
                'category_id' => $readywear->id,
                'sort_order' => 5,
                'published' => true,
            ],
            [
                'sku' => 'HC600',
                'name' => 'Carved Wooden Vase',
                'description' => 'Handcrafted decorative vase made from rosewood.',
                'price' => 80.00,
                'unit' => 'piece',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Wooden+Vase',
                'category_id' => $woodcraft->id,
                'sort_order' => 6,
                'published' => true,
            ],
            [
                'sku' => 'HC700',
                'name' => 'Bamboo Lamp Shade',
                'description' => 'Eco-friendly lamp shade for modern interiors.',
                'price' => 45.00,
                'unit' => 'piece',
                'image_url' => 'https://dummyimage.com/400x300/cccccc/000000&text=Bamboo+Lamp',
                'category_id' => $decor->id,
                'sort_order' => 7,
                'published' => true,
            ],
        ]);
    }
}
