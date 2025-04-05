<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        // Check if user has customer role
        if (!$user->hasRole('customer')) {
            return redirect()->back()->with('error', 'Only customers can make purchases.');
        }

        // Check if product is in stock
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Product is out of stock.');
        }

        // Check if customer has sufficient credit
        if ($user->credit < $product->price) {
            return redirect()->back()->with('error', 'Insufficient credit. Your current credit: $' . number_format($user->credit, 2));
        }

        try {
            DB::beginTransaction();

            // Create purchase
            $purchase = Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price_at_purchase' => $product->price,
                'purchase_date' => now()
            ]);

            // Update product stock
            $product->stock -= 1;
            $product->save();

            // Update user credit
            $user->credit -= $product->price;
            $user->save();

            DB::commit();

            return redirect()->route('purchases.history')
                ->with('success', 'Purchase successful! You bought ' . $product->name . ' for $' . number_format($product->price, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred during the purchase. Please try again.');
        }
    }

    public function history()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('customer')) {
            return redirect()->back()->with('error', 'Only customers can view purchase history.');
        }

        $purchases = Purchase::where('user_id', $user->id)
            ->with('product')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return view('purchases.history', compact('purchases'));
    }
} 