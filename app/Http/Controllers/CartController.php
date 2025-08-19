<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add(
            $request->id,
            $request->name,
            $request->quantity,
            $request->price
        )->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ]);
        }
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return response()->json([
            'success' => true,
            'quantity' => $qty,
            'subtotal' => $product->subTotal(),
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ]);
        }
        $qty = $product->qty - 1;
        if ($qty < 1) {
            return response()->json([
                'success' => false,
                'message' => 'La quantité ne peut pas être inférieure à 1'
            ]);
        }
        Cart::instance('cart')->update($rowId, $qty);
        return response()->json([
            'success' => true,
            'quantity' => $qty,
            'subtotal' => $product->subTotal(),
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }

    public function remove_item($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ]);
        }
        Cart::instance('cart')->remove($rowId);
        return response()->json([
            'success' => true,
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        return response()->json([
            'success' => true,
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }
}
?>