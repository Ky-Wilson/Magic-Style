<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details'); 
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
 // Admin routes
    //Brands management routes   
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
        Route::get('/admin/brands/add-brand', [AdminController::class, 'add_brand'])->name('admin.add_brand');
        Route::post('/admin/brands/store-brand', [AdminController::class, 'store_brand'])->name('admin.store_brand');
        Route::get('/admin/brands/edit-brand/{id}', [AdminController::class, 'edit_brand'])->name('admin.edit_brand');
        Route::put('/admin/brands/update-brand', [AdminController::class, 'update_brand'])->name('admin.update_brand');
        Route::delete('/admin/brands/{id}/delete', [AdminController::class, 'delete_brand'])->name('admin.delete_brand');
    
    // Categories management routes
        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::get('/admin/category/add', [AdminController::class, 'add_category'])->name('admin.add_category');
        Route::post('/admin/category/store', [AdminController::class, 'store_category'])->name('admin.store_category');
        Route::get('/admin/category/{id}/edit', [AdminController::class, 'edit_category'])->name('admin.edit_category');
        Route::put('/admin/category/update', [AdminController::class, 'update_category'])->name('admin.update_category');
        Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.delete_category');
   
    // Products management routes
        Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/admin/product/add-product', [AdminController::class, 'add_product'])->name('admin.add_product');
        Route::post('/admin/product/store', [AdminController::class, 'store_product'])->name('admin.store_product');
        Route::get('/admin/product/{id}/edit', [AdminController::class, 'edit_product'])->name('admin.edit_product');
        Route::put('/admin/product/{id}', [AdminController::class, 'update_product'])->name('admin.update_product');
        Route::delete('/admin/product/{id}/delete', [AdminController::class, 'delete_product'])->name('admin.delete_product');
});  