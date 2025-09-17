<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Get all Active Products
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

    // Get all Deleted Products
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
            'price'      => 'required|numeric|min:1',
            'stock'      => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id'
        ]);

        $product = Product::create($request->only(['name', 'price', 'stock']));

        $product->categories()->sync($request->categories);


        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    // Update product
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


    // Delete product
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

    // Restore deleted products
    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        if (is_null($product->deleted_at)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product is already Active',
            ], 400);
        }

        $product->restore();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product restored successfully',
            'data'    => $product,
        ]);
    }
}
