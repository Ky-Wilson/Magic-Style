<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size', 12);
        $order = $request->query('order', -1);

        $o_column = 'id';
        $o_order = 'DESC';
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order = 'DESC';
                break;
        }

        $products = Product::orderBy($o_column, $o_order)->paginate($size);

        return view('shop', compact('products', 'size', 'order'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)->latest()->take(8)->get();
        return view('details', compact('product', 'rproducts'));
    }
}
?>