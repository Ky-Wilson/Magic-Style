<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    // Ajouter cette méthode pour nettoyer les données de session liées au panier
    private function clearUserCartSession() {
        Session::forget(['checkout', 'discounts', 'coupon', 'order_id']);
        Cart::instance('cart')->destroy();
    }

    // Méthode pour s'assurer que les données de session appartiennent à l'utilisateur actuel
    private function validateUserSession() {
        $currentUserId = Auth::id();
        $sessionUserId = Session::get('cart_user_id');
        
        // Si l'utilisateur de session est différent de l'utilisateur actuel, nettoyer
        if ($sessionUserId && $sessionUserId !== $currentUserId) {
            $this->clearUserCartSession();
            Log::info("Session cleared for user switch: old user {$sessionUserId}, new user {$currentUserId}");
        }
        
        // Définir l'utilisateur actuel dans la session
        Session::put('cart_user_id', $currentUserId);
    }

    public function index()
    {
        if (Auth::check()) {
            $this->validateUserSession();
        }
        
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        if (Auth::check()) {
            $this->validateUserSession();
        }
        
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
            $this->clearUserCartSession();
            
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
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
            if (Auth::check()) {
                $this->validateUserSession();
            }
            
            $coupon_code = trim($request->coupon_code ?? '');
            
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
                'cart_value' => $coupon->cart_value ?? 0,
                'user_id' => Auth::id() // Ajouter l'ID utilisateur au coupon
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
                
                // FIX: Check if coupon is null or not an array
                if (!is_array($coupon)) {
                    Session::forget(['coupon', 'discounts']);
                    Log::warning('Invalid coupon data in session - cleared');
                    return;
                }
                
                // Vérifier que le coupon appartient à l'utilisateur actuel
                if (Auth::check() && isset($coupon['user_id']) && $coupon['user_id'] !== Auth::id()) {
                    Session::forget(['coupon', 'discounts']);
                    Log::warning('Coupon session cleared for user mismatch');
                    return;
                }
                
                $cartSubtotal = $this->cleanNumericValue(Cart::instance('cart')->subtotal());
                
                Log::info("Calculating discount - Cart subtotal: " . $cartSubtotal);
                Log::info("Coupon details: " . json_encode($coupon));
                
                // FIX: Add null checks for coupon array keys
                $couponType = $coupon['type'] ?? '';
                $couponValue = $coupon['value'] ?? 0;
                
                if($couponType == 'fixed'){
                    $discount = (float) $couponValue;
                    Log::info("Fixed discount: " . $discount);
                } else if($couponType == 'percent') {
                    $discount = ($cartSubtotal * (float) $couponValue) / 100;
                    Log::info("Percent discount: " . $discount . " (". $couponValue ."% of " . $cartSubtotal . ")");
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
                    'user_id' => Auth::id(), // Ajouter l'ID utilisateur aux remises
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
                $discounts = Session::get('discounts');
                
                // FIX: Check if discounts is null or not an array
                if (!is_array($discounts)) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', '');
                }
                
                // Vérifier que les remises appartiennent à l'utilisateur actuel
                if (Auth::check() && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', '');
                }
                
                // FIX: Use null coalescing operator to prevent null array access
                return $discounts['original_subtotal'] ?? '0.00';
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
                $discounts = Session::get('discounts');
                
                // FIX: Check if discounts is null or not an array
                if (!is_array($discounts)) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', '');
                }
                
                // Vérifier que les remises appartiennent à l'utilisateur actuel
                if (Auth::check() && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->subtotal()), 2, '.', '');
                }
                
                // FIX: Use null coalescing operator
                return $discounts['subtotal'] ?? '0.00'; // Subtotal APRÈS remise
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
                $discounts = Session::get('discounts');
                
                // FIX: Check if discounts is null or not an array
                if (!is_array($discounts)) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->tax()), 2, '.', '');
                }
                
                // Vérifier que les remises appartiennent à l'utilisateur actuel
                if (Auth::check() && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->tax()), 2, '.', '');
                }
                
                // FIX: Use null coalescing operator
                return $discounts['tax'] ?? '0.00';
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
                $discounts = Session::get('discounts');
                
                // FIX: Check if discounts is null or not an array
                if (!is_array($discounts)) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->total()), 2, '.', '');
                }
                
                // Vérifier que les remises appartiennent à l'utilisateur actuel
                if (Auth::check() && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
                    Session::forget('discounts');
                    return number_format($this->cleanNumericValue(Cart::instance('cart')->total()), 2, '.', '');
                }
                
                // FIX: Use null coalescing operator
                return $discounts['total'] ?? '0.00';
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
                $discounts = Session::get('discounts');
                
                // FIX: Check if discounts is null or not an array
                if (!is_array($discounts)) {
                    Session::forget('discounts');
                    return '0.00';
                }
                
                // Vérifier que les remises appartiennent à l'utilisateur actuel
                if (Auth::check() && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
                    Session::forget('discounts');
                    return '0.00';
                }
                
                // FIX: Use null coalescing operator
                return $discounts['discount'] ?? '0.00';
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
        
        $this->validateUserSession();
        
        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

     public function place_an_order(Request $request){
        if(!Auth::check()){
            return redirect()->route('login');
        }
        
        $this->validateUserSession();
        
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if(!$address){
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'zip' => ['required', 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/'],
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required',
            ]);

            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = 'Canada';
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountforcheckout();

        $order = new Order();

        $order->user_id = $user_id;
        
        // FIX: Add null checks for checkout session data
        $checkoutData = Session::get('checkout', []);
        
        // Convert session values to clean numeric values for database insertion
        $order->subtotal = $this->cleanNumericValue($checkoutData['subtotal'] ?? Cart::instance('cart')->subtotal());
        $order->discount = $this->cleanNumericValue($checkoutData['discount'] ?? 0);
        $order->tax = $this->cleanNumericValue($checkoutData['tax'] ?? Cart::instance('cart')->tax());
        $order->total = $this->cleanNumericValue($checkoutData['total'] ?? Cart::instance('cart')->total());
        
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;

        $order->save();

        foreach(Cart::instance('cart')->content() as $item){
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
        }
        
        if($request->mode == 'card'){
            //
        }
        elseif($request->mode == 'paypal'){
            //
        }
        elseif($request->mode == 'cod'){
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode ?? 'cod';
            $transaction->status = 'pending';
            $transaction->save();
        }

        // Nettoyer complètement les données de session après la commande
        $this->clearUserCartSession();
        
        // Définir seulement l'ID de commande pour la page de confirmation
        Session::put('order_id', $order->id);
        Session::put('order_user_id', $user_id); // Sécuriser l'ordre avec l'ID utilisateur
        
        // Load the order with its relationships
        $order->load(['orderItems.product', 'transaction']);
        
        return view('order_confirmation', compact('order'));
    }


    // FIX: Updated setAmountforcheckout method to ensure clean numeric values
    public function setAmountforcheckout(){
        if(!Cart::instance('cart')->content()->count() > 0){
            Session::forget(['checkout', 'discounts', 'coupon']);
            return;
        }
        
        // Récupérer les données de la session de manière sécurisée
        $discounts = Session::get('discounts');
        
        // FIX: Check if discounts is null or not an array before accessing
        if (!is_array($discounts)) {
            $discounts = null;
        }
        
        // Vérifier que les remises appartiennent à l'utilisateur actuel
        if (Auth::check() && is_array($discounts) && isset($discounts['user_id']) && $discounts['user_id'] !== Auth::id()) {
            Session::forget(['discounts', 'coupon']);
            $discounts = null;
        }
        
        // Vérifier si la session 'discounts' existe et est un tableau valide
        if(Session::has('coupon') && is_array($discounts)){
            Session::put('checkout',[
                // FIX: Use null coalescing operators to prevent null array access
                'discount' => $this->cleanNumericValue($discounts['discount'] ?? 0),
                'subtotal' => $this->cleanNumericValue($discounts['subtotal'] ?? Cart::instance('cart')->subtotal()),
                'tax' => $this->cleanNumericValue($discounts['tax'] ?? Cart::instance('cart')->tax()),
                'total' => $this->cleanNumericValue($discounts['total'] ?? Cart::instance('cart')->total()),
            ]);
        }
        else {
            // FIX: Logique de secours avec valeurs numériques nettoyées
            Session::put('checkout',[
                'discount' => 0,
                'subtotal' => $this->cleanNumericValue(Cart::instance('cart')->subtotal()),
                'tax' => $this->cleanNumericValue(Cart::instance('cart')->tax()),
                'total' => $this->cleanNumericValue(Cart::instance('cart')->total()),
            ]);
        }
    }

    public function order_confirmation(){
    if(Session::has('order_id')){
        $orderId = Session::get('order_id');
        $orderUserId = Session::get('order_user_id');
        
        // Vérifier que la commande appartient à l'utilisateur actuel
        if (Auth::check() && $orderUserId && $orderUserId !== Auth::id()) {
            Session::forget(['order_id', 'order_user_id']);
            return redirect()->route('cart.index')->with('error', 'Commande non trouvée.');
        }
        
        // Charger la commande avec toutes ses relations
        $order = Order::with(['orderItems.product', 'transaction'])->find($orderId);
        
        if (!$order) {
            Session::forget(['order_id', 'order_user_id']);
            return redirect()->route('cart.index')->with('error', 'Commande non trouvée.');
        }
        
        // Vérification supplémentaire de sécurité
        if (Auth::check() && $order->user_id !== Auth::id()) {
            Session::forget(['order_id', 'order_user_id']);
            return redirect()->route('cart.index')->with('error', 'Accès non autorisé.');
        }
        
        // Debug : Vérifier les données chargées
        Log::info("Order confirmation - Order ID: " . $order->id);
        Log::info("Order confirmation - Order Items count: " . $order->orderItems->count());
        Log::info("Order confirmation - Transaction exists: " . ($order->transaction ? 'Yes' : 'No'));
        
        if ($order->transaction) {
            Log::info("Order confirmation - Transaction ID: " . $order->transaction->id);
            Log::info("Order confirmation - Transaction mode: " . $order->transaction->mode);
            Log::info("Order confirmation - Transaction status: " . $order->transaction->status);
        } else {
            // Si pas de transaction, essayer de la récupérer manuellement
            Log::warning("Transaction not found via relation, trying manual query");
            $transaction = Transaction::where('order_id', $order->id)->first();
            if ($transaction) {
                Log::info("Manual query found transaction ID: " . $transaction->id);
                // Forcer la relation
                $order->setRelation('transaction', $transaction);
            } else {
                Log::error("No transaction found for order ID: " . $order->id);
            }
        }
        
        return view('order_confirmation', compact('order'));
    }
    
    return redirect()->route('cart.index');
}
}