@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Édition du Coupon</h3>
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
                        <a href="{{ route('admin.coupons') }}">
                            <div class="text-tiny">Coupons</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit Coupon</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" method="POST" action="{{ route('admin.update_coupon', ['id' => $coupon->id]) }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $coupon->id }}">
                    @method('PUT')
                    
                    <fieldset class="name">
                        <div class="body-title">Coupon code <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Code du Coupon" name="code" value="{{ old('code', $coupon->code) }}" required="">
                    </fieldset>
                    @error('code') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                    <fieldset class="category">
                        <div class="body-title">Coupon type</div>
                        <div class="select flex-grow">
                            <select class="" name="type">
                                <option value="">Sélectionner</option>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Percent</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('type') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                    <fieldset class="name">
                        <div class="body-title">Value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Valeur du Coupon" name="value" value="{{ old('value', $coupon->value) }}" required="">
                    </fieldset>
                    @error('value') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                    <fieldset class="name">
                        <div class="body-title">Cart value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Valeur du Panier" name="cart_value" value="{{ old('cart_value', $coupon->cart_value) }}" required="">
                    </fieldset>
                    @error('cart_value') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                    <fieldset class="name">
                        <div class="body-title">Expiry date <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="date" placeholder="Date d'Expiration" name="expiry_date" value="{{ old('expiry_date', \Carbon\Carbon::parse($coupon->expiry_date)->format('Y-m-d')) }}" required="">
                    </fieldset>
                    @error('expiry_date') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection