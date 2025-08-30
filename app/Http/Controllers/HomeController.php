<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index()
    {
        $slides = Slide::where('status', 1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        
        $Mcategories = Category::with(['products' => function($query) {
            $query->whereNotNull('sale_price')
                ->where('sale_price', '>', 0);
        }])->orderBy('created_at', 'desc')->take(3)->get();
        
        $Mcategories->each(function($category) {
            $category->min_price = $category->products->min('sale_price') ?? 0;
        });
        $sproducts = Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(10);
        $fproducts = Product::where('featured', 1)->get()->take(8);
        return view('index', compact('slides','Mcategories', 'categories','sproducts', 'fproducts'));
    }
}
