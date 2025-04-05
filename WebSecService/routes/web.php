<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\CustomerCreditController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\CustomersController;

// Authentication Routes
Route::get('register', [UsersController::class, 'register'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('do_register');
Route::get('login', [UsersController::class, 'login'])->name('login');
Route::post('login', [UsersController::class, 'doLogin'])->name('do_login');
Route::post('logout', [UsersController::class, 'doLogout'])->name('do_logout');

// Protected routes that require authentication
Route::middleware(['auth'])->group(function () {
    // User management routes
    Route::middleware(['permission:view_users'])->group(function () {
        Route::get('users', [UsersController::class, 'list'])->name('users');
        Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
    });

    Route::middleware(['permission:edit_users'])->group(function () {
        Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
        Route::post('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
        Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
        Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');
    });

    Route::middleware(['permission:delete_users'])->group(function () {
        Route::get('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
    });

    // Employee management routes (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('users/create-employee', [RegisterController::class, 'showCreateEmployeeForm'])->name('users.create-employee');
        Route::post('users/create-employee', [RegisterController::class, 'createEmployee'])->name('users.store-employee');
    });

    // Product routes
    Route::get('products', [ProductsController::class, 'list'])->name('products_list');
    
    Route::middleware(['permission:edit_products'])->group(function () {
        Route::get('products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
        Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
    });

    Route::middleware(['permission:delete_products'])->group(function () {
        Route::get('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
    });

    // Purchase routes
    Route::middleware(['permission:make_purchases'])->group(function () {
        Route::post('/products/{product}/purchase', [PurchaseController::class, 'store'])->name('purchases.store');
    });

    Route::middleware(['permission:view_purchase_history'])->group(function () {
        Route::get('/purchases/history', [PurchaseController::class, 'history'])->name('purchases.history');
    });

    // Customer credit management routes
    Route::middleware(['permission:manage_customers'])->group(function () {
        Route::get('/customers', [CustomerCreditController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerCreditController::class, 'show'])->name('customers.show');
        Route::post('/customers/{customer}/add-credit', [CustomerCreditController::class, 'addCredit'])->name('customers.add-credit');
    });
});

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/multable', function (Request $request) {
    $j = $request->number??5;
    $msg = $request->msg;
    return view('multable', compact("j", "msg"));
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/test', function () {
    return view('test');
});

// Customer Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomersController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/add-credit', [CustomersController::class, 'addCredit'])->name('customers.add-credit');
});
