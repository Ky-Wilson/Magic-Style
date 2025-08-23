<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
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

    /* public function apply_coupon_code(Request $request){
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code)){
            $coupon = Coupon::where('code', $coupon_code)->where('expiry_date', '>=', Carbon::today())->where('cart_value', '<=', Cart::instance('cart')->subtotal())->first();
            if(!$coupon){
                return redirect()->back()->with('error', 'invalid Coupon code');
            }
            else{
                Session::put('coupon',[
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon code applied successfully');
            }
        }else{
            return redirect()->back()->with('error', 'invalid Coupon code');
        }
    }

    public function calculateDiscount(){
        $discount = 0;
        if(Session::has('coupon')){
            if(Session::get('coupon')['type'] == 'fixed'){
                $discount = Session::get('coupon')['value'];
            }
            else{
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            Session::put('discounts',[
                'discount' => number_format(floatval($discount), 2, '-','') ,
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '-',''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '-',''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '-','')
            ]);
        }
    } */
   public function apply_coupon_code(Request $request){
    try {
        $coupon_code = $request->coupon_code;
        
        if(empty($coupon_code)){
            return response()->json([
                'success' => false,
                'message' => 'Code de coupon invalide'
            ], 400);
        }

        // Nettoyer le subtotal du panier pour la comparaison
        $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());

        $coupon = Coupon::where('code', $coupon_code)
                       ->where('expiry_date', '>=', Carbon::today())
                       ->where('cart_value', '<=', $cartSubtotal)
                       ->first();

        if(!$coupon){
            return response()->json([
                'success' => false,
                'message' => 'Code de coupon invalide ou expiré'
            ], 404);
        }

        Session::put('coupon',[
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'cart_value' => $coupon->cart_value
        ]);

        $this->calculateDiscount();

        // Retourner les nouveaux totaux avec nettoyage des valeurs
        $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());
        $cartTax = $this->cleanNumericValue(Cart::instance('cart')->tax());
        $cartTotal = $this->cleanNumericValue(Cart::instance('cart')->total());

        // Si on a une remise, utiliser les valeurs calculées
        if(Session::has('discounts')){
            $discounts = Session::get('discounts');
            $cartSubtotal = $discounts['subtotal'];
            $cartTax = $discounts['tax'];
            $cartTotal = $discounts['total'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Code de coupon appliqué avec succès',
            'coupon' => Session::get('coupon'),
            'discount' => Session::has('discounts') ? Session::get('discounts')['discount'] : '0.00',
            'cartSubtotal' => $cartSubtotal,
            'cartTax' => $cartTax,
            'cartTotal' => $cartTotal
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur lors de l\'application du coupon: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Une erreur interne s\'est produite: ' . $e->getMessage()
        ], 500);
    }
}

// Méthode helper pour nettoyer les valeurs numériques
private function cleanNumericValue($value) {
    if (is_numeric($value)) {
        return (float) $value;
    }
    
    // Supprimer les virgules, espaces et autres caractères non numériques
    $cleaned = preg_replace('/[^0-9.-]/', '', $value);
    return (float) $cleaned;
}

public function calculateDiscount(){
    $discount = 0;
    if(Session::has('coupon')){
        $coupon = Session::get('coupon');
        $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());
        
        if($coupon['type'] == 'fixed'){
            $discount = (float) $coupon['value'];
        } else {
            $discount = ($cartSubtotal * (float) $coupon['value']) / 100;
        }

        $subtotalAfterDiscount = $cartSubtotal - $discount;
        
        // Récupérer le taux de taxe depuis la config
        $taxRate = (float) config('cart.tax', 0);
        $taxAfterDiscount = ($subtotalAfterDiscount * $taxRate) / 100;
        $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
        
        Session::put('discounts',[
            'discount' => number_format($discount, 2, '.', ''),
            'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
            'tax' => number_format($taxAfterDiscount, 2, '.', ''),
            'total' => number_format($totalAfterDiscount, 2, '.', '')
        ]);
    }
}
}


