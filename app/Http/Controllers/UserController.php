<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    //
    public function index(){
        return view('user.index');
    }
    public function orders(){
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id){
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if($order){
            $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order_id)->first();
            return view('user.order_details', compact('order', 'orderItems', 'transaction'));
        }
        else{
            return redirect()->route('user.orders');
        }
    }

    public function cancel_order(Request $request){
    $order = Order::find($request->id); 
    
    if (!$order) {
        return back()->with('error', 'Order not found!');
    }
    
    if (in_array($order->status, ['delivered', 'canceled'])) {
        return back()->with('error', 'This order cannot be canceled!');
    }
    
    $order->status = 'canceled';
    $order->canceled_date = Carbon::now();
    $order->save();
    
    return back()->with('status', 'Order canceled successfully!');
    }


    
    public function edit(){
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil (nom, email, mobile, mot de passe)
     */
    public function update(Request $request){
        /** @var User $user */
        $user = Auth::user();
        
        // Règles de validation de base
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id)
            ],
        ];

        // Si l'utilisateur veut changer son mot de passe
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:8|confirmed';
            $rules['new_password_confirmation'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Vérifier si l'email a changé avant de le modifier
            $emailChanged = $user->email !== $request->email;
            
            // Mettre à jour les informations de base
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            
            // Si l'email a changé, marquer comme non vérifié
            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            // Si l'utilisateur veut changer son mot de passe
            if ($request->filled('current_password') && $request->filled('new_password')) {
                // Vérifier le mot de passe actuel
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
                }

                // Vérifier que le nouveau mot de passe est différent de l'ancien
                if (Hash::check($request->new_password, $user->password)) {
                    return back()->withErrors(['new_password' => 'New password must be different from current password.'])->withInput();
                }

                // Mettre à jour le mot de passe
                $user->password = Hash::make($request->new_password);
            }
            
            $user->save();

            $message = 'Profile updated successfully!';
            if ($request->filled('new_password')) {
                $message = 'Profile and password updated successfully!';
            }

            return back()->with('status', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while updating your profile. Please try again.')->withInput();
        }
    }}