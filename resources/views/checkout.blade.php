@extends('layouts.app')
@section('content')
<main class="pt-90">
  <div class="mb-4 pb-4"></div>
  <section class="shop-checkout container">
    <h2 class="page-title">Shipping and Checkout</h2>
    <div class="checkout-steps">
      <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
        <span class="checkout-steps__item-number">01</span>
        <span class="checkout-steps__item-title">
          <span>Shopping Bag</span>
          <em>Manage Your Items List</em>
        </span>
      </a>
      <a href="javascript:void(0)" class="checkout-steps__item active">
        <span class="checkout-steps__item-number">02</span>
        <span class="checkout-steps__item-title">
          <span>Shipping and Checkout</span>
          <em>Checkout Your Items List</em>
        </span>
      </a>
      <a href="javascript:void(0)" class="checkout-steps__item">
        <span class="checkout-steps__item-number">03</span>
        <span class="checkout-steps__item-title">
          <span>Confirmation</span>
          <em>Review And Submit Your Order</em>
        </span>
      </a>
    </div>
    <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
      @csrf
      <div class="checkout-form">
        <div class="billing-info__wrapper">
          <div class="row">
            <div class="col-6">
              <h4>SHIPPING DETAILS</h4>
            </div>
            <div class="col-6">
            </div>
          </div>
          @if($address)
          <div class="row">
            <div class="col-md-12">
              <div class="my-account__address-list">
                <div class="my-account__address-list-item">
                  <div class="my-account__address-item__detail">
                    <p>{{ $address->name }}</p>
                    <p>{{ $address->address }}</p>
                    <p>{{ $address->landmark }}</p>
                    <p>{{ $address->city }}, {{ $address->state }}, {{ $address->country }}</p>
                    <p>{{ $address->zip }}</p>
                    <br>
                    <p>{{ $address->phone }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="row mt-5">
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="name" required="" value="{{ old('name') }}">
                <label for="name">Full Name *</label>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="phone" required="" value="{{ old('phone') }}">
                <label for="phone">Phone Number *</label>
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="zip" required="" value="{{ old('zip') }}">
                <label for="zip">Pincode *</label>
                @error('zip')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mt-3 mb-3">
                <input type="text" class="form-control" name="state" required="" value="{{ old('state') }}">
                <label for="state">State *</label>
                @error('state')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="city" required="" value="{{ old('city') }}">
                <label for="city">Town / City *</label>
                @error('city')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="address" required="" value="{{ old('address') }}">
                <label for="address">House no, Building Name *</label>
                @error('address')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="locality" required="" value="{{ old('locality') }}">
                <label for="locality">Road Name, Area, Colony *</label>
                @error('locality')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="landmark" required="" value="{{ old('landmark') }}">
                <label for="landmark">Landmark *</label>
                @error('landmark')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>
          @endif
        </div>
        <div class="checkout__totals-wrapper">
          <div class="sticky-content">
            <div class="checkout__totals">
              <h3>Your Order</h3>
              <table class="checkout-cart-items">
                <thead>
                  <tr>
                    <th>PRODUCT</th>
                    <th align="right">SUBTOTAL</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(Cart::instance('cart') as $item)
                  <tr>
                    <td>
                      {{ $item->name }} x {{ $item->qty }}
                    </td>
                    <td align="right">
                      ${{ $item->subtotal() }}
                    </td>
                  </tr>
                  {{-- <tr>
                    <td>
                      Kirby T-Shirt
                    </td>
                    <td align="right">
                      $29.90
                    </td>
                  </tr> --}}
                  @endforeach
                </tbody>
              </table>
              @if(Session::has('discounts'))
              <table class="checkout-totals">
                <tbody>
                  <tr>
                    <th>Subtotal</th>
                    <td id="cart-original-subtotal" class="text-right">${{
                      Session::get('discounts')['original_subtotal'] }}</td>
                  </tr>
                  <tr id="discount-row">
                    <th>Discount {{ Session::get('coupon')['code'] }}
                      {{-- <button type="button" onclick="removeCoupon()" class="btn btn-sm btn-outline-danger ms-2"
                        title="Supprimer le coupon">×</button> --}}
                    </th>
                    <td id="cart-discount" class="text-success">-${{ Session::get('discounts')['discount'] }}</td>
                  </tr>
                  <tr>
                    <th>Subtotal after discount</th>
                    <td id="cart-subtotal" class="text-right">${{ Session::get('discounts')['subtotal'] }}</td>
                  </tr>
                  <tr>
                    <th>Shipping</th>
                    <td class="text-right">Free</td>
                  </tr>
                  <tr>
                    <th>VAT</th>
                    <td id="cart-tax" class="text-right">${{ Session::get('discounts')['tax'] }}</td>
                  </tr>
                  <tr>
                    <th>Total</th>
                    <td id="cart-total" class="text-right">${{ Session::get('discounts')['total'] }}</td>
                  </tr>
                </tbody>
              </table>
              @else
              <table class="checkout-totals">
                <tbody>
                  <tr>
                    <th>SUBTOTAL</th>
                    <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                  </tr>
                  <tr>
                    <th>SHIPPING</th>
                    <td class="text-right">Free shipping</td>
                  </tr>
                  <tr>
                    <th>VAT</th>
                    <td class="text-right">${{ Cart::instance('cart')->tax() }}</td>
                  </tr>
                  <tr>
                    <th>TOTAL</th>
                    <td class="text-right">${{ Cart::instance('cart')->total() }}</td>
                  </tr>
                </tbody>
              </table>
              @endif
            </div>
            <div class="checkout__payment-methods">
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode" id="mode3" value="cod"
                  required>
                <label class="form-check-label" for="mode3">
                  Cash on delivery
                </label>
              </div>

              <div class="policy-text">
                Your personal data will be used to process your order, support your experience throughout this
                website, and for other purposes described in our <a href="#" target="_blank">privacy
                  policy</a>.
              </div>
            </div>
            <button class="btn btn-primary btn-checkout" id="placeOrderBtn" disabled>PLACE ORDER</button>
          </div>
        </div>
    </form>
  </section>
</main>
@endsection
@push('scripts')
<script>
  function checkPaymentMethod() {
    const radioButtons = document.querySelectorAll('input[name="mode"]');
    const placeOrderBtn = document.getElementById('placeOrderBtn'); // ou document.querySelector('.btn-checkout')
    
    const isSelected = Array.from(radioButtons).some(radio => radio.checked);
    
    placeOrderBtn.disabled = !isSelected;
}

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="mode"]');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', checkPaymentMethod);
    });
    
    checkPaymentMethod();
});
</script>
@endpush
{{-- <div class="form-check">
  <input class="form-check-input form-check-input_fill" type="radio" name="mode" id="mode1" value="card">
  <label class="form-check-label" for="mode1">
    Debit or Credit Cart

  </label>
</div>
<div class="form-check">
  <input class="form-check-input form-check-input_fill" type="radio" name="mode" id="mode2" value="paypal">
  <label class="form-check-label" for="mode2">
    Paypal

  </label>
</div> --}}