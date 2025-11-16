<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::orderBy('sort_order')->orderBy('id','desc')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'required|string|max:100|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'image_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'published' => 'boolean',
        ]);
        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,'.$product->id,
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'image_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'published' => 'boolean',
        ]);
        $product->update($data);
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
