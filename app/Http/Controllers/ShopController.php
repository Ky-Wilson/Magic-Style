<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    //
    public function index(){
        $products = Product::orderBy('created_at', 'DESC')->paginate(12);
        return view('shop', compact('products'));
    }

   public function product_details($product_slug){
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)->latest()->take(8)->get();
        return view('details', compact('product', 'rproducts'));
    }

    public function increase_cart_quantity($rowid){
        $produit = Cart::instance('cart')->get($rowid);
        $qty = $produit->qty + 1;
        Cart::instance('cart')->update($rowid, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowid){
        $produit = Cart::instance('cart')->get($rowid);
        $qty = $produit->qty - 1;
        Cart::instance('cart')->update($rowid, $qty);
        return redirect()->back();
    }
}
