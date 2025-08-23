<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
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
        try {
            $product = Cart::instance('cart')->get($rowId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }
            
            $qty = $product->qty + 1;
            Cart::instance('cart')->update($rowId, $qty);
            
            // Recalculer les remises si un coupon est appliqué
            if(Session::has('coupon')){
                $this->calculateDiscount();
            }
            
            // Récupérer le produit mis à jour pour obtenir le nouveau subtotal
            $updatedProduct = Cart::instance('cart')->get($rowId);
            
            return response()->json([
                'success' => true,
                'quantity' => $qty,
                'subtotal' => number_format($this->cleanNumericValue($updatedProduct->subTotal()), 2, '.', ''),
                'originalSubtotal' => $this->getOriginalSubtotal(),
                'cartSubtotal' => $this->getDisplaySubtotal(),
                'cartTax' => $this->getDisplayTax(),
                'cartTotal' => $this->getDisplayTotal(),
                'cartCount' => Cart::instance('cart')->count(),
                'discount' => $this->getDisplayDiscount()
            ]);
        } catch (\Exception $e) {
            Log::error('Error increasing cart quantity: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la quantité: ' . $e->getMessage()
            ], 500);
        }
    }

    public function decrease_cart_quantity($rowId)
    {
        try {
            $product = Cart::instance('cart')->get($rowId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }
            
            $qty = $product->qty - 1;
            if ($qty < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'La quantité ne peut pas être inférieure à 1'
                ], 400);
            }
            
            Cart::instance('cart')->update($rowId, $qty);
            
            // Recalculer les remises si un coupon est appliqué
            if(Session::has('coupon')){
                $this->calculateDiscount();
            }
            
            // Récupérer le produit mis à jour pour obtenir le nouveau subtotal
            $updatedProduct = Cart::instance('cart')->get($rowId);
            
            return response()->json([
                'success' => true,
                'quantity' => $qty,
                'subtotal' => number_format($this->cleanNumericValue($updatedProduct->subTotal()), 2, '.', ''),
                'originalSubtotal' => $this->getOriginalSubtotal(),
                'cartSubtotal' => $this->getDisplaySubtotal(),
                'cartTax' => $this->getDisplayTax(),
                'cartTotal' => $this->getDisplayTotal(),
                'cartCount' => Cart::instance('cart')->count(),
                'discount' => $this->getDisplayDiscount()
            ]);
        } catch (\Exception $e) {
            Log::error('Error decreasing cart quantity: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la quantité: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove_item($rowId)
    {
        try {
            $product = Cart::instance('cart')->get($rowId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
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
        } catch (\Exception $e) {
            Log::error('Error removing cart item: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clear_cart()
    {
        try {
            Cart::instance('cart')->destroy();
            
            // Supprimer les coupons et remises
            Session::forget(['coupon', 'discounts']);
            
            return response()->json([
                'success' => true,
                'cartSubtotal' => '0.00',
                'cartTax' => '0.00',
                'cartTotal' => '0.00',
                'cartCount' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du vidage du panier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove_coupon_code(){
        try {
            Session::forget('coupon');
            Session::forget('discounts');
            
            return response()->json([
                'success' => true,
                'message' => 'Coupon supprimé avec succès',
                'cartSubtotal' => number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', ''),
                'cartTax' => number_format($this->cleanNumericValue(Cart::instance('cart')->tax()), 2, '.', ''),
                'cartTotal' => number_format($this->cleanNumericValue(Cart::instance('cart')->total()), 2, '.', ''),
                'cartCount' => Cart::instance('cart')->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing coupon: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du coupon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apply_coupon_code(Request $request){
        try {
            $coupon_code = trim($request->coupon_code);
            
            if(empty($coupon_code)){
                return response()->json([
                    'success' => false,
                    'message' => 'Code de coupon invalide'
                ], 400);
            }

            // Nettoyer le subtotal du panier pour la comparaison
            $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());

            Log::info("Cart subtotal before coupon: " . $cartSubtotal);

            // Vérifier d'abord si le coupon existe
            $coupon = Coupon::where('code', $coupon_code)->first();

            if(!$coupon){
                return response()->json([
                    'success' => false,
                    'message' => 'Code de coupon invalide'
                ], 400);
            }

            Log::info("Coupon found: " . json_encode([
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]));

            // Vérifier si le coupon est expiré
            if($coupon->expiry_date && Carbon::parse($coupon->expiry_date)->lt(Carbon::today())){
                return response()->json([
                    'success' => false,
                    'message' => 'Code de coupon expiré'
                ], 400);
            }

            // Vérifier si le montant minimum du panier est atteint
            if($coupon->cart_value && $coupon->cart_value > $cartSubtotal){
                return response()->json([
                    'success' => false,
                    'message' => 'Montant minimum de ' . number_format($coupon->cart_value, 2) . ' € non atteint pour utiliser ce coupon'
                ], 400);
            }

            Session::put('coupon',[
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value ?? 0
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
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne s\'est produite: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateDiscount(){
        try {
            $discount = 0;
            if(Session::has('coupon')){
                $coupon = Session::get('coupon');
                $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());
                
                Log::info("Calculating discount - Cart subtotal: " . $cartSubtotal);
                Log::info("Coupon details: " . json_encode($coupon));
                
                if($coupon['type'] == 'fixed'){
                    $discount = (float) $coupon['value'];
                    Log::info("Fixed discount: " . $discount);
                } else if($coupon['type'] == 'percent') {
                    $discount = ($cartSubtotal * (float) $coupon['value']) / 100;
                    Log::info("Percent discount: " . $discount . " (". $coupon['value'] ."% of " . $cartSubtotal . ")");
                }

                // S'assurer que la remise ne dépasse pas le subtotal
                $discount = min($discount, $cartSubtotal);
                $subtotalAfterDiscount = max(0, $cartSubtotal - $discount);
                
                Log::info("Final discount: " . $discount);
                Log::info("Subtotal after discount: " . $subtotalAfterDiscount);
                
                // Calculer la taxe sur le subtotal après remise
                $cartTaxRate = $this->getCartTaxRate();
                $taxAfterDiscount = ($subtotalAfterDiscount * $cartTaxRate) / 100;
                $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
                
                Session::put('discounts',[
                    'original_subtotal' => number_format($cartSubtotal, 2, '.', ''),
                    'discount' => number_format($discount, 2, '.', ''),
                    'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
                    'tax' => number_format($taxAfterDiscount, 2, '.', ''),
                    'total' => number_format($totalAfterDiscount, 2, '.', '')
                ]);

                Log::info("Discounts stored in session: " . json_encode(Session::get('discounts')));
            }
        } catch (\Exception $e) {
            Log::error('Error calculating discount: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            // En cas d'erreur, supprimer les données de remise pour éviter des erreurs futures
            Session::forget('discounts');
        }
    }

    // Méthode pour récupérer le taux de taxe
    private function getCartTaxRate() {
        try {
            // Si vous avez configuré la taxe dans config/cart.php
            if (config('cart.tax')) {
                return (float) config('cart.tax');
            }
            
            // Sinon, essayer de calculer à partir du panier actuel
            $currentTax = $this->cleanNumericValue(Cart::instance('cart')->tax());
            $currentSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());
            
            if ($currentSubtotal > 0) {
                return ($currentTax / $currentSubtotal) * 100;
            }
            
            return 0; // Pas de taxe par défaut
        } catch (\Exception $e) {
            Log::error('Error getting tax rate: ' . $e->getMessage());
            return 0; // Retourner 0 en cas d'erreur
        }
    }

    // Méthode helper pour nettoyer les valeurs numériques
    private function cleanNumericValue($value) {
        try {
            if (is_numeric($value)) {
                return (float) $value;
            }
            
            // Supprimer les virgules, espaces et autres caractères non numériques sauf le point décimal
            $cleaned = preg_replace('/[^\d.-]/', '', $value);
            
            // S'assurer qu'on a une valeur numérique valide
            return is_numeric($cleaned) ? (float) $cleaned : 0.0;
        } catch (\Exception $e) {
            Log::error('Error cleaning numeric value: ' . $e->getMessage());
            return 0.0;
        }
    }

    // Nouvelle méthode pour obtenir le subtotal original
    private function getOriginalSubtotal() {
        try {
            if(Session::has('discounts')){
                return Session::get('discounts')['original_subtotal'];
            }
            return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', '');
        } catch (\Exception $e) {
            Log::error('Error getting original subtotal: ' . $e->getMessage());
            return '0.00';
        }
    }

    // Méthodes helper pour obtenir les valeurs d'affichage
    private function getDisplaySubtotal() {
        try {
            if(Session::has('discounts')){
                return Session::get('discounts')['subtotal']; // Subtotal APRÈS remise
            }
            return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', ''); // Subtotal original si pas de remise
        } catch (\Exception $e) {
            Log::error('Error getting display subtotal: ' . $e->getMessage());
            return '0.00';
        }
    }

    private function getDisplayTax() {
        try {
            if(Session::has('discounts')){
                return Session::get('discounts')['tax'];
            }
            return number_format($this->cleanNumericValue(Cart::instance('cart')->tax()), 2, '.', '');
        } catch (\Exception $e) {
            Log::error('Error getting display tax: ' . $e->getMessage());
            return '0.00';
        }
    }

    private function getDisplayTotal() {
        try {
            if(Session::has('discounts')){
                return Session::get('discounts')['total'];
            }
            return number_format($this->cleanNumericValue(Cart::instance('cart')->total()), 2, '.', '');
        } catch (\Exception $e) {
            Log::error('Error getting display total: ' . $e->getMessage());
            return '0.00';
        }
    }

    private function getDisplayDiscount() {
        try {
            if(Session::has('discounts')){
                return Session::get('discounts')['discount'];
            }
            return '0.00';
        } catch (\Exception $e) {
            Log::error('Error getting display discount: ' . $e->getMessage());
            return '0.00';
        }
    }

     public function checkout(){
        if(!Auth::check()){
            return redirect()->route('login');
        }
        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }
}