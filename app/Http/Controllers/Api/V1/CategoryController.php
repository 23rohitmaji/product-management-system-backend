<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Categories retrieved successfully',
            'data'   => Category::all()
        ]);
    }

    // Create category (admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $category = Category::create(['name' => $request->name]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Category created successfully',
            'data'    => $category
        ]);
    }

    // Update category (admin only)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $category->update(['name' => $request->name]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Category updated successfully',
            'data'    => $category
        ]);
    }
}
