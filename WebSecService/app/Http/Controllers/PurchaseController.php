<?php

namespace App\Http\Controllers;

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

    public function purchase(Product $product)
    {
        $user = Auth::user();

        if (!$user->hasRole('customer')) {
            return redirect()->back()->with('error', 'Only customers can make purchases.');
        }

        if (!$product->isInStock()) {
            return redirect()->back()->with('error', 'Product is out of stock.');
        }

        if (!$user->hasSufficientCredit($product->price)) {
            return redirect()->back()->with('error', 'Insufficient credit.');
        }

        try {
            DB::beginTransaction();

            // Create purchase record
            Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price_at_purchase' => $product->price,
                'purchase_date' => now(),
            ]);

            // Update user's credit
            $user->credit -= $product->price;
            $user->save();

            // Decrease product stock
            $product->decreaseStock();

            DB::commit();

            return redirect()->back()->with('success', 'Purchase completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred during the purchase.');
        }
    }

    public function history()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('customer')) {
            return redirect()->back()->with('error', 'Only customers can view purchase history.');
        }

        $purchases = $user->purchases()->with('product')->latest()->get();
        
        return view('purchases.history', compact('purchases'));
    }
}
