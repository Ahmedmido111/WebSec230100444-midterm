<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:employee')->except(['show']);
    }

    public function index()
    {
        $customers = User::role('customer')->get();
        return view('customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if (!auth()->user()->hasRole('employee') && auth()->id() !== $customer->id) {
            abort(403);
        }
        return view('customers.show', compact('customer'));
    }

    public function addCredit(Request $request, User $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $customer->credit += $request->amount;
        $customer->save();

        return redirect()->back()->with('success', 'Credit added successfully');
    }
} 