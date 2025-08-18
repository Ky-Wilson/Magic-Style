@extends('layouts.admin')
@section('content')
     <!-- main-content-wrap -->
                        <div class="main-content-inner">
                            <!-- main-content-wrap -->
                            <div class="main-content-wrap">
                                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                                    <h3>Edit Product</h3>
                                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                                        <li>
                                            <a href="index-2.html">
                                                <div class="text-tiny">Dashboard</div>
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon-chevron-right"></i>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.products') }}">
                                                <div class="text-tiny">Products</div>
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon-chevron-right"></i>
                                        </li>
                                        <li>
                                            <div class="text-tiny">Add product</div>
                                        </li>
                                    </ul>
                                </div>
                                <!-- form-add-product -->
                                <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action=" {{ route('admin.update_product', $product->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="wg-box">
                                        <fieldset class="name">
                                            <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                                            </div>
                                            <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{ $product->name }}" aria-required="true" required="">
                                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                                        </fieldset>
                                        @error('name') <span class="alert text-danger text-center">{{ $message }} @enderror
                                         
                                        <fieldset class="name">
                                            <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                                            <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" tabindex="0" value="{{ $product->slug }}" aria-required="true" required="">
                                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                                        </fieldset>
                                        @error('slug') <span class="alert text-danger text-center">{{ $message }} @enderror
                                        <div class="gap22 cols">
                                            <fieldset class="category">
                                                <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                                                </div>
                                                <div class="select">
                                                    <select class="" name="category_id">
                                                        <option>Choose category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : ""}}>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </fieldset>
                                            @error('category_id') <span class="alert text-danger text-center">{{ $message }} @enderror
                                            <fieldset class="brand">
                                                <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                                                </div>
                                                <div class="select">
                                                    <select class="" name="brand_id">
                                                        <option>Choose Brand</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : ""}}>{{ $brand->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </fieldset>
                                            @error('brand_id') <span class="alert text-danger text-center">{{ $message }} @enderror
                                        </div>

                                        <fieldset class="shortdescription">
                                            <div class="body-title mb-10">Short Description <span
                                                    class="tf-color-1">*</span></div>
                                            <textarea class="mb-10 ht-150" name="short_description"
                                                placeholder="Short Description" tabindex="0" aria-required="true" required="">{{ $product->short_description }}</textarea>
                                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                                        </fieldset>
                                        @error('short_description') <span class="alert text-danger text-center">{{ $message }} @enderror

                                        <fieldset class="description">
                                            <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                                            </div>
                                            <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true" required="">{{ $product->description }}</textarea>
                                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                                        </fieldset>
                                        @error('description') <span class="alert text-danger text-center">{{ $message }} @enderror
                                    </div>
                                    <div class="wg-box">
                                        <fieldset>
                                            <div class="body-title">Upload images <span class="tf-color-1">*</span>
                                            </div>
                                            <div class="upload-image flex-grow">
                                                @if($product->image)
                                                    <div class="item" id="imgpreview">
                                                        <img src="{{ asset('uploads/products/') }}/{{ $product->image }}" class="effect8" alt="{{ $product->name }}">
                                                    </div>
                                                @endif
                                                <div id="upload-file" class="item up-load">
                                                    <label class="uploadfile" for="myFile">
                                                        <span class="icon">
                                                            <i class="icon-upload-cloud"></i>
                                                        </span>
                                                        <span class="body-text">Drop your images here or select <span
                                                                class="tf-color">click to browse</span></span>
                                                        <input type="file" id="myFile" name="image" accept="image/*">
                                                    </label>
                                                </div>
                                            </div>
                                        </fieldset>
                                        @error('image') <span class="alert text-danger text-center">{{ $message }} @enderror

                                        <fieldset>
                                            <div class="body-title mb-10">Upload Gallery Images</div>
                                            <div class="upload-image mb-16">
                                                @if($product->images)
                                                  @foreach(explode(',', $product->images) as $img)
                                                    <div class="item gitems">
                                                        <img src="{{ asset('uploads/products') }}/{{ trim($img) }}" alt="">
                                                    </div> 
                                                  @endforeach 
                                                @endif
                                                         
                                                <div id="galUpload" class="item up-load">
                                                    <label class="uploadfile" for="gFile">
                                                        <span class="icon">
                                                            <i class="icon-upload-cloud"></i>
                                                        </span>
                                                        <span class="text-tiny">Drop your images here or select <span
                                                                class="tf-color">click to browse</span></span>
                                                        <input type="file" id="gFile" name="images[]" accept="image/*"
                                                            multiple="">
                                                    </label>
                                                </div>
                                            </div>
                                        </fieldset>
                                        @error('images') <span class="alert text-danger text-center">{{ $message }} @enderror

                                        <div class="cols gap22">
                                            <fieldset class="name">
                                                <div class="body-title mb-10">Regular Price <span
                                                        class="tf-color-1">*</span></div>
                                                <input class="mb-10" type="text" placeholder="Enter regular price" name="regular_price" tabindex="0" value="{{ $product->regular_price }}" aria-required="true" required="">
                                            </fieldset>
                                            @error('regular_price') <span class="alert text-danger text-center">{{ $message }} @enderror
                                            <fieldset class="name">
                                                <div class="body-title mb-10">Sale Price <span
                                                        class="tf-color-1">*</span></div>
                                                <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price" tabindex="0" value="{{ $product->sale_price }}" aria-required="true" required="">
                                            </fieldset>
                                            @error('sale_price') <span class="alert text-danger text-center">{{ $message }} @enderror
                                        </div>


                                        <div class="cols gap22">
                                            <fieldset class="name">
                                                <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                                                </div>
                                                <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU" tabindex="0" value="{{ $product->SKU }}" aria-required="true" required="">
                                            </fieldset>
                                            @error('SKU') <span class="alert text-danger text-center">{{ $message }} @enderror
                                            <fieldset class="name">
                                                <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span>
                                                </div>
                                                <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity" tabindex="0" value="{{ $product->quantity }}" aria-required="true" required="">
                                            </fieldset>
                                            @error('quantity') <span class="alert text-danger text-center">{{ $message }} @enderror
                                        </div>

                                        <div class="cols gap22">
                                            <fieldset class="name">
                                                <div class="body-title mb-10">Stock</div>
                                                <div class="select mb-10">
                                                    <select class="" name="stock_status">
                                                        <option value="instock" {{ $product->stock_status == "instock" ? 'selected' : ""}}>InStock</option>
                                                        <option value="outofstock" {{ $product->stock_status == "instock" ? 'selected' : ""}}>Out of Stock</option>
                                                    </select>
                                                </div>
                                            </fieldset>
                                            @error('stock_status') <span class="alert text-danger text-center">{{ $message }} @enderror
                                            <fieldset class="name">
                                                <div class="body-title mb-10">Featured</div>
                                                <div class="select mb-10">
                                                    <select class="" name="featured">
                                                        <option value="0" {{ $product->featured == "0" ? 'selected' : ""}}>No</option>
                                                        <option value="1" {{ $product->featured == "1" ? 'selected' : ""}}>Yes</option>
                                                    </select>
                                                </div>
                                            </fieldset>
                                            @error('featured') <span class="alert text-danger text-center">{{ $message }} @enderror
                                        </div>
                                        <div class="cols gap10">
                                            <button class="tf-button w-full" type="submit">Edit product</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- /form-add-product -->
                            </div>
                            <!-- /main-content-wrap -->
                        </div>
                        <!-- /main-content-wrap -->
@endsection
@push('scripts')
<script>
    $(function(){
        // Prévisualisation de l'image principale
        $('#myFile').on("change", function(e){
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr("src", URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        // Prévisualisation des images de la galerie
        $('#gFile').on("change", function(e){
            const files = this.files;
            // Vider les anciennes prévisualisations
            $("#galUpload").find('.gitems').remove();
            
            // Ajouter chaque image en prévisualisation
            $.each(files, function(index, file) {
                if (file.type.match('image.*')) {
                    $("#galUpload").append(`
                        <div class="item gitems">
                            <img src="${URL.createObjectURL(file)}" alt="Gallery Image ${index + 1}" style="max-width: 100px; margin: 5px;">
                        </div>
                    `);
                }
            });
        });

        // Génération automatique du slug à partir du nom
        $("input[name='name']").on("input", function() {
            const nameValue = $(this).val();
            const slugValue = StringToSlug(nameValue);
            $("input[name='slug']").val(slugValue);
        });

        // Fonction pour convertir le texte en slug
        function StringToSlug(text) {
            return text.toLowerCase()
                .replace(/^\s+|\s+$/g, '')        
                .replace(/[^a-z0-9 -]/g, '')     
                .replace(/\s+/g, '-')             
                .replace(/-+/g, '-');            
        }
    });
</script>
@endpush