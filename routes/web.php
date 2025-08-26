<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;




Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details'); 
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');

Route::put('/cart/increase-quantity/{rowid}', [CartController::class, 'increase_cart_quantity'])->name('cart.increase_quantity');
Route::put('/cart/decrease-quantity/{rowid}', [CartController::class, 'decrease_cart_quantity'])->name('cart.decrease_quantity');
Route::delete('/cart/remove/{rowid}', [CartController::class, 'remove_item'])->name('cart.remove');
Route::delete('/cart/remove-all', [CartController::class, 'clear_cart'])->name('cart.clear');

Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.apply_coupon');
Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon_code'])->name(name: 'cart.remove_coupon');


Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowid}', [WishlistController::class, 'remove_from_wishlist'])->name('wishlist.item.remove');
Route::delete('/wishlist/remove-all', [WishlistController::class, 'empty_wishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowid}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move_to_cart');


Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-an-order', [CartController::class, 'place_an_order'])->name('cart.place.an.order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel', [UserController::class, 'cancel_order'])->name('user.order.cancel');
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

    // Coupons management routes
        Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
        Route::get('/admin/coupon/add-coupon', [AdminController::class, 'coupon_add'])->name('admin.add_coupon');
        Route::post('/admin/coupon/store', [AdminController::class, 'coupon_store'])->name('admin.store_coupon');
        Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'coupon_edit'])->name('admin.edit_coupon');
        Route::put('/admin/coupon/{id}', [AdminController::class, 'coupon_update'])->name('admin.update_coupon');
        Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'coupon_delete'])->name('admin.delete_coupon');

    // Orders management routes
        Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order_details');
        Route::put('/admin/order/update-status', [AdminController::class, 'order_update_status'])->name('admin.update_order_status');


    //Sliders managements routes
    Route::get('admin/slides', [AdminController::class, 'slides'])->name('admin.slides.index');
    Route::get('admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
        Route::get('admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');


 });  







