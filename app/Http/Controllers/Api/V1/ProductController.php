<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // List products (public)
    public function index()
    {
        $products = Product::with('categories')->paginate(10);

        $productsData = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'categories' => $product->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
            ];
        });

        $products->setCollection($productsData);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Products retrieved successfully',
            'data' => $products
        ]);
    }

    public function deleted()
    {
        $products = Product::onlyTrashed()->with('categories')->get();

        $mappedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'categories' => $product->categories->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name
                    ];
                })
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted Products retrieved successfully',
            'data' => $mappedProducts
        ]);
    }



    // Create product (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'price'      => 'required|numeric|min:1',   // must be > 0
            'stock'      => 'required|integer|min:0',   // must be ≥ 0
            'categories' => 'required|array|min:1',     // must send at least one category
            'categories.*' => 'exists:categories,id'    // each id must exist in DB
        ]);

        $product = Product::create($request->only(['name', 'price', 'stock']));

        $product->categories()->sync($request->categories);


        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    // Update product (admin only)
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $request->validate([
            'name'       => 'sometimes|string',
            'price'      => 'sometimes|numeric|min:1',
            'stock'      => 'sometimes|integer|min:0',
            'categories' => 'sometimes|array|min:1',
            'categories.*' => 'exists:categories,id'
        ]);

        $product->update($request->only(['name', 'price', 'stock']));

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }


    // Delete (soft delete, admin only)
    public function destroy(Request $request, $id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }

    // BONUS: Restore soft-deleted product
    public function restore($id)
    {
        // Fetch product including soft-deleted ones
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        // Check if product is actually soft-deleted
        if (is_null($product->deleted_at)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product is already Active',
            ], 400);
        }

        // Restore the product
        $product->restore();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product restored successfully',
            'data'    => $product,
        ]);
    }
}
