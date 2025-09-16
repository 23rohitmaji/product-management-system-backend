<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // Add product to cart
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Product not found',
            ], 404);
        }

        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Quantity exceeds stock'], 400);
        }

        $cartItem = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'status' => 'success',
            'data'   => $cartItem,
        ], 201);
    }

    // Update quantity
    public function update(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        if ($request->quantity > $cartItem->product->stock) {
            return response()->json(['message' => 'Quantity exceeds stock'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['status' => 'success', 'cart' => $cartItem]);
    }

    // Remove from cart
    public function destroy(Request $request, $productId)
    {
        Cart::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['status' => 'success', 'message' => 'Item removed from cart']);
    }

    // Get cart
    public function index(Request $request)
    {
        $cart = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $subtotal = $cart->map(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'status'   => 'success',
            'data'     => $cart,
            'subtotal' => $subtotal->sum(),
            'total'    => $subtotal->sum(), // (add taxes/shipping later if needed)
        ]);
    }
}
