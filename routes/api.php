<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use  App\Http\Controllers\Auth\AuthController;
use  App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MarketController as AdminMarketController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:super admin'])->group(function () {
    Route::post('/register-user', [AuthController::class, 'registerUser']); #Done
    Route::post('/register-market', [AuthController::class, 'registerMarket']); #Done
});


Route::middleware(['auth:sanctum', 'role:super admin'])->prefix('dashboard')->group(function () {
    // users routes
    Route::get('/users', [AdminUserController::class, 'index']); #Done
    Route::put('/user/edit/{id}', [AdminUserController::class, 'update']); #Done
    Route::get('/user/markets/{id}', [AdminUserController::class, 'getUserMarkets']); #Done
    // market routes
    Route::get('/markets', [AdminMarketController::class, 'getAllMarkets']); #Done
    Route::get('/market/{id}', [AdminMarketController::class, 'getMarket']); #Done
    Route::put('/market/edit/{id}', [AdminMarketController::class, 'updateMarket']); #Done
    // category routs
    Route::get('/categories', [AdminCategoryController::class, 'index']); #Done
    Route::get('/category/{id}', [AdminCategoryController::class, 'show']); #Done
    Route::post('/category/create', [AdminCategoryController::class, 'store']); #Done
    Route::put('/category/edit/{id}', [AdminCategoryController::class, 'update']); #Done
    Route::delete('/category/delete/{id}', [AdminCategoryController::class, 'destroy']); #Done
    // products routs
    Route::get('/products', [AdminProductController::class, 'index']); #Done
    Route::get('/product/{id}', [AdminProductController::class, 'show']); #Done
    Route::post('/product/create', [AdminProductController::class, 'store']); #Done
    Route::put('/product/edit/{id}', [AdminProductController::class, 'update']); #Done
    Route::delete('/product/delete/{id}', [AdminProductController::class, 'destroy']); #Done
    // orders routes
    Route::get('/orders', [AdminOrderController::class, 'index']); #Done
    Route::get('/order/{id}', [AdminOrderController::class, 'show']); #Done
    Route::post('/order/create', [AdminOrderController::class, 'store']); #Done
    Route::put('/order/edit/{id}', [AdminOrderController::class, 'update']); #Done
    Route::delete('/order/delete/{id}', [AdminOrderController::class, 'destroy']); #Done
    Route::post('/order/assign', [AdminUserController::class, 'assign']); # assign orders to representative
});

// Handel offers and sales #Done

Route::middleware(['auth:sanctum', 'role:user'])->prefix('application')->group(function () {
    // categories routes
    # show categories
    Route::get('/categories', [CategoryController::class, 'index']); #Done  
    # show categories and related products
    Route::get('/category/product/{category}', [CategoryController::class, 'show']); #Done
    // products routes
    # show all products
    Route::get('/products', [ProductController::class, 'index']); #Done
    # view product
    Route::get('/product/{product}', [ProductController::class, 'show']); #Done
    // Cart routes 
    # view user cart
    Route::get('/cart', [CartController::class, 'index']); #Done
    # add product to cart
    Route::post('/cart/add', [CartController::class, 'store']); #Done
    # edit quantity
    Route::put('/cart/edit/{cart}', [CartController::class, 'update']); #Done
    # delete product from cart
    Route::delete('/cart/delete/{cart}', [CartController::class, 'destroy']);  #Done
    // order routes 
    # show all orders history
    Route::get('/orders', [OrderController::class, 'index']); #Done
    # view order details
    Route::get('/order/{order}', [OrderController::class, 'show']); #Done
    # take orders from cart 
    Route::post('/order/checkout', [OrderController::class, 'store']); #Done
});

// handle banners and offers announcement
// handle representatives