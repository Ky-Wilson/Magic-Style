<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image; // Ensure you have this facade for image manipulation

class AdminController extends Controller
{
    //
    public function index(){
        return view('admin.index');
        // This method can be used to display admin-related information
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
            // Generate the thumbnail image
            // Save the brand image
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

   public function delete_brand($id)
    {
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
        return redirect()->route('admin.categories.index')->with('status', 'Category added successfully!');
        
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
public function update_category(Request $request)
{
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

   public function category_delete($id)
{
    $category = Category::findOrFail($id);
    $imagePath = public_path('uploads/categories/' . $category->image);
    if ($category->image && File::exists($imagePath)) {
        File::delete($imagePath);
    }
    $category->delete();
    return redirect()->route('admin.categories')->with('status', 'Category deleted successfully!');
}
}
