<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function contact(){
        return view('contact');
    }

    public function contact_store(Request $request){
        $request->validate([
            'name'=>'required|max:300',
            'email'=>'required',
            'phone'=>'required|numeric|digits:10',
            'comment'=>'required'
        ]);

        $contact = new Contact();
        $contact -> name = $request->name;
        $contact -> email = $request->email;
        $contact -> phone = $request->phone;
        $contact -> comment = $request->comment;
        $contact -> save();

        return redirect()->back()->with('success', 'Your message has been sent successfully !');
    }

      public function aboutus(){
        return view('about');
    }
}
