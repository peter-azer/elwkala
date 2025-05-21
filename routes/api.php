<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use  App\Http\Controllers\Auth\AuthController;
use  App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MarketController as AdminMarketController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SubCategoryController as AdminSubCategoryController;
use App\Http\Controllers\Admin\ProductsPacksController as AdminProductsPacksController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\Admin\RecomendedProductsController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;

Route::get('/user', function (Request $request) {
    return response()->json(
        [
            'user' => $request->user(),
            'role' => $request->user()->getRoleNames(),
            'market' => $request->user()->market()->get()
        ]
    );
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:super admin'])->group(function () {
    Route::post('/register-user', [AuthController::class, 'registerUser']); #Done
    Route::post('/register-market', [AuthController::class, 'registerMarket']); #Done
});


Route::middleware(['auth:sanctum', 'role:super admin'])->prefix('dashboard')->group(function () {
    // users routes
    Route::get('/Admins', [AdminUserController::class, 'index']); #Done
    Route::get('/users', [AdminUserController::class, 'getUsers']); #Done
    Route::put('/user/edit/{id}', [AdminUserController::class, 'update']); #Done
    Route::get('/user/markets/{id}', [AdminUserController::class, 'getUserMarkets']); #Done
    //areas routes
    Route::get('/areas', [AreaController::class, 'index']);
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
    Route::get('/category/visibility/{id}', [AdminCategoryController::class, 'visibility']); #Done
    // subcategory routes
    Route::get('/subcategories', [AdminSubCategoryController::class, 'index']); #Done
    Route::get('/subcategory/{id}', [AdminSubCategoryController::class, 'show']); #Done
    Route::post('/subcategory/create', [AdminSubCategoryController::class, 'store']); #Done
    Route::put('/subcategory/edit/{id}', [AdminSubCategoryController::class, 'update']); #Done
    Route::delete('/subcategory/delete/{id}', [AdminSubCategoryController::class, 'destroy']); #Done
    Route::get('/subcategory/visibility/{id}', [AdminSubCategoryController::class, 'visibility']); #Done
    // products routs
    Route::get('/products', [AdminProductController::class, 'index']); #Done
    Route::get('/product/{id}', [AdminProductController::class, 'show']); #Done
    Route::post('/product/create', [AdminProductController::class, 'store']); #Done
    Route::put('/product/edit/{id}', [AdminProductController::class, 'update']); #Done
    Route::delete('/product/delete/{id}', [AdminProductController::class, 'destroy']); #Done
    Route::get('/product/visibility/{id}', [AdminProductController::class, 'visibility']); #Done
    Route::post('/product/assign', [RecomendedProductsController::class, 'store']);
    Route::delete('/product/unassign/{id}', [RecomendedProductsController::class, 'destroy']);
    // Brands routes
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brand/{id}', [BrandController::class, 'show']);
    Route::post('/brand/create', [BrandController::class, 'store']);
    Route::put('/brand/edit/{brand}', [BrandController::class, 'update']);
    Route::delete('/brand/delete/{brand}', [BrandController::class, 'destroy']);
    // orders routes
    Route::get('/orders', [AdminOrderController::class, 'index']); #Done
    Route::get('/order/{id}', [AdminOrderController::class, 'show']); #Done
    Route::post('/order/create', [AdminOrderController::class, 'store']); #Done
    Route::put('/order/edit/{id}', [AdminOrderController::class, 'update']); #Done
    Route::delete('/order/delete/{id}', [AdminOrderController::class, 'destroy']); #Done
    Route::post('/order/assign', [AdminUserController::class, 'assign']); # assign orders to representative
    Route::delete('/order/unassign/{id}', [AdminUserController::class, 'unassign']); # unassign orders from representative
    Route::post('/order/assigned', [AdminUserController::class, 'getAssignedOrders']); # show each representative orders
    // handle packs and sizes
    Route::get('/packs', [AdminProductsPacksController::class, 'index']); #Done
    Route::post('/pack/create', [AdminProductsPacksController::class, 'store']); #Done
    Route::put('/pack/edit/{id}', [AdminProductsPacksController::class, 'update']); #Done
    Route::delete('/pack/delete/{id}', [AdminProductsPacksController::class, 'destroy']); #Done


});

// Handel offers and sales #Done

Route::middleware(['auth:sanctum', 'role:user|super admin'])->prefix('application')->group(function () {
    // categories routes
    # show categories
    Route::get('/categories', [CategoryController::class, 'index']); #Done  
    # show categories and related products
    Route::get('/category/product/{category}', [CategoryController::class, 'show']); #Done
    # show subcategories
    Route::get('/subcategories', [SubCategoryController::class, 'index']); #Done
    Route::get('/subcategory/{id}', [SubCategoryController::class, 'show']); #Done
    // products routes
    # show all products
    Route::get('/products', [ProductController::class, 'index']); #Done
    # view product
    Route::get('/product/{product}', [ProductController::class, 'show']); #Done
    # view recommended products
    Route::get('products/recommended', [RecomendedProductsController::class, 'index']);
    // brand routes
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brand/{brand}', [BrandController::class, 'show']);
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