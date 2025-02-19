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

Route::middleware(['auth:sanctum', 'role:super admin'])->group(function() {
    Route::post('/register-user', [AuthController::class, 'registerUser']); #Done
    Route::post('/register-market', [AuthController::class, 'registerMarket']); #Done
});


Route::middleware(['auth:sanctum', 'role:super admin'])->prefix('dashboard')->group( function () {
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
});
// Handel offers and sales #Done

Route::middleware(['auth:sanctum', 'role:user'])->prefix('application')->group( function () {
    // categories routes
    Route::get('/categories', [CategoryController::class, 'index']); # show categories
    Route::get('/category/product/{category}', [CategoryController::class, 'show']); # show categories and related products
    // products routes
    Route::get('/products', [ProductController::class, 'index']); # show all products
    Route::get('/product/{id}', [ProductController::class, 'show']); # view product
    // Cart routes
    Route::get('/cart', [CartController::class, 'index']); # view user cart
    Route::post('/cart/add', [CartController::class, 'store']); # add product to cart
    Route::put('/cart/edit/{id}', [CartController::class, 'update']); # edit quantity
    Route::delete('/cart/delete/{id}', [CartController::class, 'destroy']); # delete product from cart
    // order routes
    Route::get('/orders', [OrderController::class, 'index']); # show all orders history
    Route::get('/order/{id}', [OrderController::class, 'show']); # view order details
    Route::post('/order/checkout', [OrderController::class, 'store']); # take orders from cart 

});

// handle banners and offers announcement
// handle representatives