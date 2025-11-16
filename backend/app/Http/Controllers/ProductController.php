<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with(['category.parent']);

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('sku', 'LIKE', "%$search%");
        }

        $perPage = $request->per_page ?? 10;

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function showByCategory($categoryId)
    {
        return \App\Models\Category::with([
            'children.products'
        ])->findOrFail($categoryId);
    }


   public function store(Request $request)
    {
        // Fix: If image is empty string, treat it as null
        if ($request->image === "null" || $request->image === "" || $request->image === "undefined") {
            $request->merge(['image' => null]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency' => 'nullable|string|max:3',
            'image_storage' => 'nullable|string|max:10',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'published' => 'boolean',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();
            $filename = substr($name, 0, 50) . '_' . time() . '.' . $ext;

            $path = $file->storeAs('products', $filename, 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $product = Product::create($validated);
        return response()->json($product);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency' => 'nullable|string|max:3',
            'image_storage' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0',
            'published' => 'boolean',
            'stock' => 'nullable|integer|min:0', // Added for stock
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
            }

            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $truncatedName = substr($originalName, 0, 50);
            $filename = $truncatedName . '_' . time() . '.' . $extension;
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = '/storage/' . $path;
        }

        $product->update($data);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function grouped()
    {
        return Category::with(['children.products' => function ($q) {
            $q->select('id', 'sku', 'name', 'description', 'price','currency', 'unit', 'image','image_storage','sort_order', 'stock', 'category_id')
              ->where('published', 1);
        }])->whereNull('parent_id')->get()->map(function ($cat) {
            $cat->children->each(function ($sub) {
            $sub->products->transform(function ($p) {
                $p->price = (float)$p->price;
                return $p;
            });
            });
            return $cat;
        });
    }

}
