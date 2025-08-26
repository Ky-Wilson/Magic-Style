<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Slide;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    //
    public function index(){
        return view('admin.index');
    }
// Brands management methods
    public  function brands(){
        $brands = Brand::orderBy(
            'id', 'DESC'
        )->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand(){
        return view('admin.add-brand');
    }

    public function store_brand(Request $request){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug =Str::slug( $request->name);
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand added successfully!');
    }

    public function edit_brand($id){
        $brand = Brand::find($id);
        return view('admin.edit-brand', compact('brand'));
    }

    public function update_brand(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'. $request->id,
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brand::find($request ->id);
        $brand->name = $request->name;
        $brand->slug =Str::slug( $request->name);
        if($request->hasFile('image')) {
            if(File::exists(public_path('uploads/brands/'. '/' .$brand->image))) {
                File::delete(public_path('uploads/brands/'. '/' .$brand->image));
        }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name; 
    }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand updated successfully!');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/brands');
        $img = image::read($image->path());
        $img->cover(124, 124, "top")
            ->save($destinationPath.'/'.$imageName);
    }

   public function delete_brand($id){
        $brand = Brand::findOrFail($id);
        $imagePath = public_path('uploads/brands/' . $brand->image);
        if ($brand->image && File::exists($imagePath)) {
            File::delete($imagePath);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand deleted successfully!');
    }

// Categories management methods

    public function categories(){
        // Logic to display categories
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function add_category(){
        return view('admin.categories.add');
    }

    public function store_category(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $Caterory = new Category();
        $Caterory->name = $request->name;
        $Caterory->slug =Str::slug( $request->name);
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $Caterory->image = $file_name;
        $Caterory->save();
        return redirect()->route('admin.categories')->with('status', 'Category added successfully!');
        
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/categories');
        $img = image::read($image->path());
            
        $img->cover(124, 124, "top")
            ->save($destinationPath.'/'.$imageName);
    }

    public function edit_category($id){
        $category = Category::find($id);
        return view('admin.categories.edit', compact('category'));
    }
    public function update_category(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'. $request->id,
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if($request->hasFile('image')) {
            if(File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }

            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category updated successfully!');
    }

   public function category_delete($id){
        $category = Category::findOrFail($id);
        $imagePath = public_path('uploads/categories/' . $category->image);
        if ($category->image && File::exists($imagePath)) {
            File::delete($imagePath);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category deleted successfully!');
        }
    
    // products management methods
    public function products(){
        // Logic to display products
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products.index', data: compact('products'));
    }

    public function add_product(){
        $categories = Category::select('id', 'name')->orderBy('name', 'ASC')->get();
        $brands = Brand::select('id', 'name')->orderBy('name', 'ASC')->get();
        return view('admin.products.add', compact('categories', 'brands'));
    }

   /*  public function store_product(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'regular_price' => 'required',
            'sale_price' => 'nullable',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp. '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $current_timestamp);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if ($request->hasFile('images')) {
            $allowedfileExtension = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtension);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;

        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product added successfully!');
    }

    public function GenerateProductThumbnailImage($image, $imageName){
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = image::read($image->path());
            
        $img->cover(540, 689, "top")
            ->save($destinationPath.'/'.$imageName);

         $img->cover(104, 104)
            ->save($destinationPathThumbnail.'/'.$imageName);
    } */
    
   public function store_product(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'regular_price' => 'required',
            'sale_price' => 'nullable',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if ($request->hasFile('images')) {
            $allowedfileExtension = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtension);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;

        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product added successfully!');
    }

    public function edit_product($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::select('id', 'name')->orderBy('name', 'ASC')->get();
        $brands = Brand::select('id', 'name')->orderBy('name', 'ASC')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update_product(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:products,slug,' . $id,
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'regular_price' => 'required',
            'sale_price' => 'nullable',
            'SKU' => 'required|unique:products,SKU,' . $id,
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path('uploads/products/' . $product->image))) {
                File::delete(public_path('uploads/products/' . $product->image));
            }
            if ($product->image && File::exists(public_path('uploads/products/thumbnails/' . $product->image))) {
                File::delete(public_path('uploads/products/thumbnails/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = $product->images ? explode(',', $product->images) : [];
        $counter = count($gallery_images) + 1;
        if ($request->hasFile('images')) {
            foreach ($gallery_images as $gallery_image) {
                if (File::exists(public_path('uploads/products/' . $gallery_image))) {
                    File::delete(public_path('uploads/products/' . $gallery_image));
                }
                if (File::exists(public_path('uploads/products/thumbnails/' . $gallery_image))) {
                    File::delete(public_path('uploads/products/thumbnails/' . $gallery_image));
                }
            }
            $gallery_images = "";
            $gallery_arr = array();

            $allowedfileExtension = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtension);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;

        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product updated successfully!');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        if (!File::exists($destinationPathThumbnail)) {
            File::makeDirectory($destinationPathThumbnail, 0755, true);
        }

        $img = Image::read($image->path());

        $img->cover(540, 689, "top")
            ->save($destinationPath . '/' . $imageName);

        $img->cover(104, 104)
            ->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function delete_product($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && File::exists(public_path('uploads/products/' . $product->image))) {
            File::delete(public_path('uploads/products/' . $product->image));
        }
        if ($product->image && File::exists(public_path('uploads/products/thumbnails/' . $product->image))) {
            File::delete(public_path('uploads/products/thumbnails/' . $product->image));
        }
        if ($product->images) {
            foreach (explode(',', $product->images) as $img) {
                if (File::exists(public_path('uploads/products/' . trim($img)))) {
                    File::delete(public_path('uploads/products/' . trim($img)));
                }
                if (File::exists(public_path('uploads/products/thumbnails/' . trim($img)))) {
                    File::delete(public_path('uploads/products/thumbnails/' . trim($img)));
                }
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product deleted successfully!');
    }

    public function coupons(){
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function coupon_add(){
        return view('admin.coupons.add-coupons');
    }

    public function coupon_store(Request $request){
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required',
            'value' => 'required',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon added successfully!');
    }

    public function coupon_edit($id){
        $coupon = Coupon::find($id);
        return view('admin.coupons.edit-coupons', compact('coupon'));
    }

    public function coupon_update(Request $request, $id){
        $coupon = Coupon::find($id);

        // Validation des données avec la règle 'unique'
        // qui ignore l'ID du coupon en cours de modification.
        $request->validate([
            'code' => 'required|string|unique:coupons,code,'.$coupon->id,
            'type' => 'required',
            'value' => 'required',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);
        
        // Mise à jour des attributs du coupon
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon mis à jour avec succès !');
    }

    public function coupon_delete($id){
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon supprimé avec succès !');
    }

    public function orders(){
        $orders = Order::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.orders.index', compact('orders'));
    }

    public function order_details($order_id){
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.orders.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function order_update_status(Request $request){
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if($request->order_status == 'delivered'){
            $order->delivered_date = Carbon::now();
        }
        else if($request->order_status == 'canceled'){
            $order->canceled_date = Carbon::now();
        }

        $order->save();

        if($request->order_status == 'delivered'){
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }

        return back()->with('status', 'order status updated succesfully !');
    }

    public function slides(){
        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides.index', compact('slides'));
    }

    public function slide_add(){
        return view('admin.slides.add');
    }

    public function slide_store(Request $request){
        $request ->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|mine:png,jpg,jpeg|max:2048'
        ]);
        $slide = new Slide();
        $slide -> tagline = $request->tagline;
        $slide -> title = $request->title;
        $slide -> subtitle = $request->subtitle;
        $slide -> link = $request->link;
        $slide -> status = $request->status;

       $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateSlidehumbnailsImage($image, $file_name);
            $slide->image = $file_name;
            $slide->save();
            return back()->route('admin.slides.index')->with("status", "Slide added successfully !");
    }

    public function GenerateSlidehumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/slides');
        $img = image::read($image->path());
            
        $img->cover(400, 600, "top")
            ->save($destinationPath.'/'.$imageName);
    }

    

    
}
