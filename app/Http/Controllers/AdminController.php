<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Laravel\Facades\Image; // Ensure you have this facade for image manipulation

class AdminController extends Controller
{
    //
    public function index(){
        return view('admin.index');
        // This method can be used to display admin-related information
    }

    public  function brands(){
        $brands = Brand::orderBy(
            'id', 'DESC'
        )->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand(){
        return view('admin.add-brand');
        // This method can be used to display the form for adding a new brand
    }

    public function store_brand(Request $request){
        // Logic to store the new brand in the database
        // Validate and save the brand data
        // Redirect or return a response after saving
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
        // Redirect to the brands page with a success message
        

    }

    public function GenerateBrandThumbnailsImage($image, $imageName){
    $destinationPath = public_path('uploads/brands');
    $img = image::read($image->path());
    
    $img->cover(124, 124, "top")
        ->save($destinationPath.'/'.$imageName);
}
   /*  public function GenerateBrandThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/brands');
       $img = image::read($image->path);
       $img->cover(124, 124, "top");
       $img->resize(124,124, function($constraint){
        $constraint->aspectRatio();
       })->save($destinationPath.'/'.$imageName);
        // This method can be used to generate and save the brand image
    } */
}
