<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign customer role by default
        $user->assignRole('customer');

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function showCreateEmployeeForm()
    {
        // Check if user is admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('users.create-employee');
    }

    public function createEmployee(Request $request)
    {
        // Check if user is admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign employee role
        $employee->assignRole('employee');

        return redirect()->route('users')->with('success', 'Employee created successfully.');
    }
} 