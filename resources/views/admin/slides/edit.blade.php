@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Slide</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.slides.index') }}">
                        <div class="text-tiny">Slides</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Slide</div>
                </li>
            </ul>
        </div>
        <!-- edit-slide -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.slide.update', ['id' => $slide->id]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Champ caché pour l'ID du slide --}}
                <input type="hidden" name="id" value="{{ $slide->id }}">

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <fieldset class="name">
                    <div class="body-title">Tagline <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Tagline" name="tagline" tabindex="0"
                        value="{{ old('tagline', $slide->tagline) }}" aria-required="true" required="">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Title" name="title" tabindex="0"
                        value="{{ old('title', $slide->title) }}" aria-required="true" required="">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Subtitle <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" name="subtitle" tabindex="0"
                        value="{{ old('subtitle', $slide->subtitle) }}" aria-required="true" required="">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Link <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Link" name="link" tabindex="0"
                        value="{{ old('link', $slide->link) }}" aria-required="true" required="">
                </fieldset>

                <fieldset>
                    <div class="body-title">Upload images</div>
                    <div class="upload-image flex-grow">
                        @if($slide->image)
                        <div class="item" id="imgpreview">
                            <img src="{{ asset('uploads/slides') }}/{{ $slide->image }}" class="effect8"
                                alt="Current image">
                        </div>
                        @endif
                        <div class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span class="tf-color">click to
                                        browse</span></span>
                                <input type="file" id="myFile" name="image">
                            </label>
                        </div>
                    </div>
                    <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                </fieldset>

                <fieldset class="category">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select class="" name="status" required>
                            <option value="">Select</option>
                            <option value="1" @if(old('status', $slide->status) == "1") selected @endif>Active</option>
                            <option value="0" @if(old('status', $slide->status) == "0") selected @endif>Inactive
                            </option>
                        </select>
                    </div>
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Update Slide</button>
                </div>
            </form>
        </div>
        <!-- /edit-slide -->
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection

@push('scripts')
<script>
    $(function(){
            // Gestion du changement d'image
            $('#myFile').on("change", function(e){
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr("src", URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
        });
</script>
@endpush