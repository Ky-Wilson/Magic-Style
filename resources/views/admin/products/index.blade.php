@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Products</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}" class="flex items-center gap5">
                        <i class="icon-home"></i>
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">All Products</div>
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
                <a class="tf-button style-1 w208" href="{{ route('admin.add_product') }}"><i class="icon-plus"></i>Add
                    new</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="min-width: 40px;">#</th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 80px;">Price</th>
                            <th style="min-width: 90px;">SalePrice</th>
                            <th style="min-width: 80px;">SKU</th>
                            <th style="min-width: 100px;">Category</th>
                            <th style="min-width: 100px;">Brand</th>
                            <th style="min-width: 80px;">Featured</th>
                            <th style="min-width: 80px;">Stock</th>
                            <th style="min-width: 80px;">Quantity</th>
                            <th style="min-width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}"
                                        alt="{{ $product->name }}" class="me-2"
                                        style="width:35px;height:35px;object-fit:cover;flex-shrink:0;">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <a href="#" class="body-title-2 d-block text-truncate"
                                            title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </a>
                                        <div class="text-tiny text-muted text-truncate" title="{{ $product->slug }}">
                                            {{ $product->slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap">${{ number_format($product->regular_price, 2) }}</td>
                            <td class="text-nowrap">${{ number_format($product->sale_price, 2) }}</td>
                            <td class="text-truncate" title="{{ $product->SKU }}">
                                {{ $product->SKU ?: 'N/A' }}
                            </td>
                            <td class="text-truncate" title="{{ $product->category->name }}">
                                {{ $product->category->name }}
                            </td>
                            <td class="text-truncate" title="{{ $product->brand->name }}">
                                {{ $product->brand->name }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $product->featured == 0 ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $product->featured == 0 ? "NO" : "YES" }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $product->stock_status == 'instock' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock_status }}
                                </span>
                            </td>
                            <td class="text-center">{{ $product->quantity }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="#" target="_blank" class="btn btn-ml btn-outline-primary me-1"
                                        title="Voir">
                                        <i class="icon-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.edit_product', ['id' => $product->id]) }}"
                                        class="btn btn-ml btn-outline-success me-1" title="Éditer">
                                        <i class="icon-edit-3"></i>
                                    </a>

                                    <form action="{{ route('admin.delete_product', ['id' => $product->id]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ml btn-outline-danger delete-btn"
                                            title="Supprimer">
                                            <i class="icon-trash-2"></i>
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
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    @media (max-width: 992px) {
        .table-responsive table {
            font-size: 0.875rem;
        }

        .table-responsive td img {
            width: 30px !important;
            height: 30px !important;
        }
    }

    @media (max-width: 768px) {
        .table-responsive table {
            font-size: 0.8rem;
        }

        .table-responsive td img {
            width: 25px !important;
            height: 25px !important;
        }

        .table-responsive .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }
    .text-truncate {
        max-width: 150px;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@push('scripts')
<script>
    $(document).on('click', '.delete-btn', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: "Are you sure?",
        text: "You will not be able to recover this category!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

</script>
@endpush