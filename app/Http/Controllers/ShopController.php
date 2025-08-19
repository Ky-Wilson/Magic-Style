<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size', 12);
        $categories = Category::orderBy('name', 'ASC')->get();
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands', '');
        $f_categories = $request->query('categories', '');
        $min_price = $request->query('min_price') ? $request->query('min_price') : 1;
        $max_price = $request->query('max_price') ? $request->query('max_price') : 150000;

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
                $o_column = 'sale_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'sale_price';
                $o_order = 'DESC';
                break;
        }

        $brands = Brand::orderBy('name', 'ASC')->get();
        $productsQuery = Product::query();

        // Appliquer le filtre des marques
        if (!empty($f_brands)) {
            $brandIds = explode(',', $f_brands);
            $productsQuery->whereIn('brand_id', $brandIds);
        }
        
        // Appliquer le filtre de prix
        $productsQuery->where(function ($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])
                  ->orWhere(function ($q) use ($min_price, $max_price) {
                      $q->whereNotNull('sale_price')
                        ->whereBetween('sale_price', [$min_price, $max_price]);
                  });
        });

        // Appliquer le filtre des catégories
        if (!empty($f_categories)) {
            $categoryIds = explode(',', $f_categories);
            $productsQuery->whereIn('category_id', $categoryIds);
        }

        $products = $productsQuery->orderBy($o_column, $o_order)->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)->latest()->take(8)->get();
        return view('details', compact('product', 'rproducts'));
    }
}
?>