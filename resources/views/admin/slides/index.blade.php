@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Slider</h3>
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
                    <div class="text-tiny">Slider</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name" tabindex="2" value=""
                                aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.slide.add') }}"><i class="icon-plus"></i>Add
                    new</a>
            </div>

            <div class="wg-table table-all-user" style="overflow-x: auto;">
                <table class="table table-striped table-bordered" style="table-layout: auto; min-width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 80px;">Image</th>
                            <th style="min-width: 100px; max-width: 150px;">Tagline</th>
                            <th style="min-width: 120px; max-width: 200px;">Title</th>
                            <th style="min-width: 120px; max-width: 200px;">Subtitle</th>
                            <th style="min-width: 150px; max-width: 250px;">Link</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slides as $slide)
                        <tr>
                            <td style="text-align: center;">{{ $slide->id }}</td>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{ asset('uploads/slides')}}/{{ $slide->image }}"
                                        alt="{{ $slide->title }}"
                                        style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                </div>
                            </td>
                            <td style="word-wrap: break-word; max-width: 150px;" title="{{ $slide->tagline }}">
                                {{ Str::limit($slide->tagline, 30, '...') }}
                            </td>
                            <td style="word-wrap: break-word; max-width: 200px;" title="{{ $slide->title }}">
                                {{ Str::limit($slide->title, 40, '...') }}
                            </td>
                            <td style="word-wrap: break-word; max-width: 200px;" title="{{ $slide->subtitle }}">
                                {{ Str::limit($slide->subtitle, 40, '...') }}
                            </td>
                            <td style="word-wrap: break-word; max-width: 250px;">
                                <a href="{{ $slide->link }}" target="_blank" title="{{ $slide->link }}"
                                    style="color: #007bff; text-decoration: none;">
                                    {{ Str::limit($slide->link, 35, '...') }}
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <div class="list-icon-function">
                                    <a href="{{ route('admin.slide.edit', ['id'=>$slide->id]) }}" title="Modifier">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                    <form action="{{ route('admin.slide.delete', ['id' => $slide->id]) }}" method="POST"
                                        style="display: inline;" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-link p-0 m-0 delete-btn"
                                            style="border: none; background: none;" data-id="{{ $slide->id }}"
                                            data-title="{{ $slide->title }}">
                                            <div class="item text-danger delete" title="Supprimer">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $slides->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        const slideId = $(this).data('id');
        const slideTitle = $(this).data('title');
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `Vous allez supprimer le slide "${slideTitle}". Cette action est irréversible !`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush