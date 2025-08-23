<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
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
        
        // Recalculer les remises si un coupon est appliqué
        if(Session::has('coupon')){
            $this->calculateDiscount();
        }
        
        return response()->json([
            'success' => true,
            'quantity' => $qty,
            'subtotal' => $product->subTotal(),
            'originalSubtotal' => $this->getOriginalSubtotal(), // Subtotal original
            'cartSubtotal' => $this->getDisplaySubtotal(), // Subtotal après remise
            'cartTax' => $this->getDisplayTax(),
            'cartTotal' => $this->getDisplayTotal(),
            'cartCount' => Cart::instance('cart')->count(),
            'discount' => $this->getDisplayDiscount()
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
        
        // Recalculer les remises si un coupon est appliqué
        if(Session::has('coupon')){
            $this->calculateDiscount();
        }
        
        return response()->json([
            'success' => true,
            'quantity' => $qty,
            'subtotal' => $product->subTotal(),
            'originalSubtotal' => $this->getOriginalSubtotal(), // Subtotal original
            'cartSubtotal' => $this->getDisplaySubtotal(), // Subtotal après remise
            'cartTax' => $this->getDisplayTax(),
            'cartTotal' => $this->getDisplayTotal(),
            'cartCount' => Cart::instance('cart')->count(),
            'discount' => $this->getDisplayDiscount()
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
        
        // Recalculer les remises si un coupon est appliqué
        if(Session::has('coupon')){
            $this->calculateDiscount();
        }
        
        return response()->json([
            'success' => true,
            'originalSubtotal' => $this->getOriginalSubtotal(),
            'cartSubtotal' => $this->getDisplaySubtotal(),
            'cartTax' => $this->getDisplayTax(),
            'cartTotal' => $this->getDisplayTotal(),
            'cartCount' => Cart::instance('cart')->count(),
            'discount' => $this->getDisplayDiscount()
        ]);
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        
        // Supprimer les coupons et remises
        Session::forget(['coupon', 'discounts']);
        
        return response()->json([
            'success' => true,
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }

    public function remove_coupon_code(){
        Session::forget('coupon');
        Session::forget('discounts');
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon supprimé avec succès',
            'cartSubtotal' => Cart::instance('cart')->subtotal(),
            'cartTax' => Cart::instance('cart')->tax(),
            'cartTotal' => Cart::instance('cart')->total(),
            'cartCount' => Cart::instance('cart')->count()
        ]);
    }

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

            // Vérifier d'abord si le coupon existe
            $coupon = Coupon::where('code', $coupon_code)->first();

            if(!$coupon){
                return response()->json([
                    'success' => false,
                    'message' => 'Code de coupon invalide'
                ], 400);
            }

            // Vérifier si le coupon est expiré
            if($coupon->expiry_date < Carbon::today()){
                return response()->json([
                    'success' => false,
                    'message' => 'Code de coupon expiré'
                ], 400);
            }

            // Vérifier si le montant minimum du panier est atteint
            if($coupon->cart_value > $cartSubtotal){
                return response()->json([
                    'success' => false,
                    'message' => 'Montant minimum de ' . number_format($coupon->cart_value, 2) . ' € non atteint pour utiliser ce coupon'
                ], 400);
            }

            Session::put('coupon',[
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);

            $this->calculateDiscount();

            return response()->json([
                'success' => true,
                'message' => 'Code de coupon appliqué avec succès',
                'coupon' => Session::get('coupon'),
                'discount' => $this->getDisplayDiscount(),
                'originalSubtotal' => $this->getOriginalSubtotal(),
                'cartSubtotal' => $this->getDisplaySubtotal(),
                'cartTax' => $this->getDisplayTax(),
                'cartTotal' => $this->getDisplayTotal(),
                'showDiscount' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'application du coupon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne s\'est produite'
            ], 500);
        }
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

            // S'assurer que la remise ne dépasse pas le subtotal
            $discount = min($discount, $cartSubtotal);
            
            $subtotalAfterDiscount = $cartSubtotal - $discount;
            
            // Récupérer le taux de taxe depuis la config
            $taxRate = (float) config('cart.tax', 0);
            $taxAfterDiscount = ($subtotalAfterDiscount * $taxRate) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            
            Session::put('discounts',[
                'original_subtotal' => number_format($cartSubtotal, 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
                'tax' => number_format($taxAfterDiscount, 2, '.', ''),
                'total' => number_format($totalAfterDiscount, 2, '.', '')
            ]);
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

    // Nouvelle méthode pour obtenir le subtotal original
    private function getOriginalSubtotal() {
        if(Session::has('discounts')){
            return Session::get('discounts')['original_subtotal'];
        }
        return Cart::instance('cart')->subtotal();
    }

    // Méthodes helper pour obtenir les valeurs d'affichage
    private function getDisplaySubtotal() {
        if(Session::has('discounts')){
            return Session::get('discounts')['subtotal']; // Subtotal APRÈS remise
        }
        return Cart::instance('cart')->subtotal(); // Subtotal original si pas de remise
    }

    private function getDisplayTax() {
        if(Session::has('discounts')){
            return Session::get('discounts')['tax'];
        }
        return Cart::instance('cart')->tax();
    }

    private function getDisplayTotal() {
        if(Session::has('discounts')){
            return Session::get('discounts')['total'];
        }
        return Cart::instance('cart')->total();
    }

    private function getDisplayDiscount() {
        if(Session::has('discounts')){
            return Session::get('discounts')['discount'];
        }
        return '0.00';
    }
}