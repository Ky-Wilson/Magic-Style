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
                                    <input type="text" placeholder="Search here..." class="" name="name" tabindex="2" value="" aria-required="true" required="">
                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                            <a class="tf-button style-1 w208" href="{{ route('admin.add_product') }}"><i class="icon-plus"></i>Add new</a>
                    </div>
                            <div class="table-responsive">
    <table class="table table-striped table-bordered table-sm w-100">
        <thead>
            <tr>
                <th class="text-nowrap">#</th>
                <th class="text-nowrap w-auto">Name</th>
                <th class="text-nowrap">Price</th>
                <th class="text-nowrap">SalePrice</th>
                <th class="text-nowrap w-auto">SKU</th>
                <th class="text-nowrap w-auto">Category</th>
                <th class="text-nowrap w-auto">Brand</th>
                <th class="text-nowrap">Featured</th>
                <th class="text-nowrap">Stock</th>
                <th class="text-nowrap">Quantity</th>
                <th class="text-nowrap">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td class="text-nowrap">{{ $product->id }}</td>
                    <td class="text-nowrap w-auto">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}" 
                                 alt="{{ $product->name }}" class="me-2" style="width:40px;height:auto;">
                            <div>
                                <a href="#" class="body-title-2 text-nowrap">{{ $product->name }}</a>
                                <div class="text-tiny text-muted text-nowrap">{{ $product->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap">${{ $product->regular_price }}</td>
                    <td class="text-nowrap">${{ $product->sale_price }}</td>
                    <td class="text-nowrap w-auto">{{ $product->SKU }}</td>
                    <td class="text-nowrap w-auto">{{ $product->category->name }}</td>
                    <td class="text-nowrap w-auto">{{ $product->brand->name }}</td>
                    <td class="text-nowrap">{{ $product->featured == 0 ? "NO":"YES" }}</td>
                    <td class="text-nowrap">{{ $product->stock_status }}</td>
                    <td class="text-nowrap">{{ $product->quantity }}</td>
                    <td class="text-nowrap">
                        <div class="list-icon-function d-flex">
                            <!-- Voir -->
                            <a href="#" target="_blank" class="me-2 text-primary">
                                <i class="icon-eye"></i>
                            </a>

                            <!-- Éditer -->
                            <a href="{{ route('admin.edit_product', ['id' => $product->id]) }}" class="me-2 text-success">
                                <i class="icon-edit-3"></i>
                            </a>

                            <!-- Supprimer -->
                            <form action="{{ route('admin.delete_product', ['id' => $product->id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 text-danger delete-btn">
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
