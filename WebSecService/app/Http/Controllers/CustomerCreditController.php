<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCreditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:employee');
    }

    public function index()
    {
        $customers = User::role('customer')->get();
        return view('customers.index', compact('customers'));
    }

    public function addCredit(Request $request, User $customer)
    {
        if (!$customer->hasRole('customer')) {
            return redirect()->back()->with('error', 'Can only add credit to customer accounts.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $customer->credit += $request->amount;
        $customer->save();

        return redirect()->back()->with('success', 'Credit added successfully.');
    }

    public function show(User $customer)
    {
        if (!$customer->hasRole('customer')) {
            return redirect()->back()->with('error', 'Can only view customer accounts.');
        }

        return view('customers.show', compact('customer'));
    }
}
