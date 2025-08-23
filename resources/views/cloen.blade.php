@extends('layouts.app')
@section('content')
 <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Cart</h2>
      <div class="checkout-steps">
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
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
      <div class="shopping-cart">
        @if($items->count() > 0)
          <div class="cart-table__wrapper">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th></th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($items as $item)
                  <tr data-rowid="{{ $item->rowId }}">
                    <td>
                      <div class="shopping-cart__product-item">
                        <img loading="lazy" src="{{ asset('uploads/products/thumbnails') }}/{{ $item->model->image }}" width="120" height="120" alt="" />
                      </div>
                    </td>
                    <td>
                      <div class="shopping-cart__product-item__detail">
                        <h4>{{ $item->name }}</h4>
                        <ul class="shopping-cart__product-item__options">
                          <li>Color: Yellow</li>
                          <li>Size: L</li>
                        </ul>
                      </div>
                    </td>
                    <td>
                      <span class="shopping-cart__product-price">${{ $item->price }}</span>
                    </td>
                    <td>
                      <div class="qty-control position-relative">
                        <input type="number" name="quantity" value="{{ $item->qty }}" min="1" class="qty-control__number text-center" readonly>
                        <div class="qty-control__reduce" onclick="updateQuantity('{{ $item->rowId }}', 'decrease')">-</div>
                        <div class="qty-control__increase" onclick="updateQuantity('{{ $item->rowId }}', 'increase')">+</div>
                      </div>
                    </td>
                    <td>
                      <span class="shopping-cart__subtotal">${{ $item->subTotal() }}</span>
                    </td>
                    <td>
                      <div class="remove-cart" onclick="removeItem('{{ $item->rowId }}')">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                          <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                        </svg>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="cart-table-footer">
              <form id="coupon-form" class="position-relative bg-body">
                @csrf
                <input class="form-control" type="text" name="coupon_code" id="coupon_code" placeholder="Coupon Code" value="@if(Session::has('coupon')) {{ Session::get('coupon')['code'] }} @endif">
                <button type="submit" class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4">APPLY COUPON</button>
              </form>
              <button class="btn btn-light" onclick="clearCart()">CLEAR CART</button>
            </div>
            <div id="coupon-messages">
              @if(Session::has('success'))
              <p class="text-success">{{ Session::get('success') }}</p>
              @elseif(Session::has('error'))
              <p class="text-danger">{{ Session::get('error') }}</p>
              @endif
            </div>
          </div>
          <div class="shopping-cart__totals-wrapper">
            <div class="sticky-content">
              <div class="shopping-cart__totals">
                <h3>Cart Totals</h3>
                <table class="cart-totals">
                  <tbody>
                    <tr>
                      <th>Subtotal</th>
                      <td id="cart-subtotal">
                        @if(Session::has('discounts'))
                          ${{ Session::get('discounts')['subtotal'] }}
                        @else
                          ${{ Cart::instance('cart')->subtotal() }}
                        @endif
                      </td>
                    </tr>
                    @if(Session::has('discounts'))
                    <tr>
                      <th>Discount</th>
                      <td id="cart-discount" class="text-success">-${{ Session::get('discounts')['discount'] }}</td>
                    </tr>
                    @endif
                    <tr>
                      <th>Shipping</th>
                      <td>Free</td>
                    </tr>
                    <tr>
                      <th>VAT</th>
                      <td id="cart-tax">
                        @if(Session::has('discounts'))
                          ${{ Session::get('discounts')['tax'] }}
                        @else
                          ${{ Cart::instance('cart')->tax() }}
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Total</th>
                      <td id="cart-total">
                        @if(Session::has('discounts'))
                          ${{ Session::get('discounts')['total'] }}
                        @else
                          ${{ Cart::instance('cart')->total() }}
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="mobile_fixed-btn_wrapper">
                <div class="button-wrapper container">
                  <a href="checkout.html" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-12 text-center pt-5 bp-5">
              <p>item no found</p>
              <a href="{{ route('shop.index') }}" class="btn btn-info">Continue</a>
            </div>
          </div>
        @endif
      </div>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    // Application du coupon en AJAX
    document.getElementById('coupon-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const couponCode = document.getElementById('coupon_code').value.trim();
      const messagesDiv = document.getElementById('coupon-messages');
      const submitButton = this.querySelector('button[type="submit"]');
      
      // Désactiver le bouton pendant le traitement
      submitButton.disabled = true;
      submitButton.textContent = 'APPLYING...';
      
      try {
        const response = await fetch('{{ route("cart.apply_coupon") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            coupon_code: couponCode
          })
        });
        
        const data = await response.json();
        
        // Nettoyer les anciens messages
        messagesDiv.innerHTML = '';
        
        if (data.success) {
          // Afficher le message de succès
          messagesDiv.innerHTML = `<p class="text-success">${data.message}</p>`;
          
          // Mettre à jour les totaux
          document.getElementById('cart-subtotal').textContent = `$${data.cartSubtotal}`;
          document.getElementById('cart-tax').textContent = `$${data.cartTax}`;
          document.getElementById('cart-total').textContent = `$${data.cartTotal}`;
          
          // Ajouter ou mettre à jour la ligne de réduction
          const cartTotalsTable = document.querySelector('.cart-totals tbody');
          let discountRow = document.getElementById('discount-row');
          
          if (data.discount && parseFloat(data.discount) > 0) {
            if (!discountRow) {
              // Créer la ligne de réduction
              const subtotalRow = cartTotalsTable.querySelector('tr:first-child');
              discountRow = document.createElement('tr');
              discountRow.id = 'discount-row';
              discountRow.innerHTML = `
                <th>Discount</th>
                <td id="cart-discount" class="text-success">-$${data.discount}</td>
              `;
              subtotalRow.insertAdjacentElement('afterend', discountRow);
            } else {
              // Mettre à jour la ligne existante
              document.getElementById('cart-discount').textContent = `-$${data.discount}`;
            }
          }
          
        } else {
          // Afficher le message d'erreur
          messagesDiv.innerHTML = `<p class="text-danger">${data.message}</p>`;
        }
        
      } catch (error) {
        console.error('Erreur:', error);
        messagesDiv.innerHTML = `<p class="text-danger">Une erreur s'est produite lors de l'application du coupon</p>`;
      } finally {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = 'APPLY COUPON';
      }
    });

    async function updateQuantity(rowId, action) {
      try {
        const response = await fetch(`/cart/${action}-quantity/${rowId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        const data = await response.json();
        if (data.success) {
          const row = document.querySelector(`tr[data-rowid="${rowId}"]`);
          row.querySelector('.qty-control__number').value = data.quantity;
          row.querySelector('.shopping-cart__subtotal').textContent = `$${data.subtotal}`;
          document.getElementById('cart-subtotal').textContent = `$${data.cartSubtotal}`;
          document.getElementById('cart-tax').textContent = `$${data.cartTax}`;
          document.getElementById('cart-total').textContent = `$${data.cartTotal}`;
        } else {
          alert(data.message || 'Erreur lors de la mise à jour de la quantité');
        }
      } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la mise à jour de la quantité');
      }
    }

    async function removeItem(rowId) {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) return;
      try {
        const response = await fetch(`/cart/remove/${rowId}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        const data = await response.json();
        if (data.success) {
          const row = document.querySelector(`tr[data-rowid="${rowId}"]`);
          row.remove();
          document.getElementById('cart-subtotal').textContent = `$${data.cartSubtotal}`;
          document.getElementById('cart-tax').textContent = `$${data.cartTax}`;
          document.getElementById('cart-total').textContent = `$${data.cartTotal}`;
          if (data.cartCount === 0) {
            document.querySelector('.shopping-cart').innerHTML = `
              <div class="row">
                <div class="col-md-12 text-center pt-5 bp-5">
                  <p>Aucun article dans votre panier</p>
                  <a href="{{ route('shop.index') }}" class="btn btn-info">Continuer vos achats</a>
                </div>
              </div>`;
          }
        } else {
          alert(data.message || 'Erreur lors de la suppression de l\'article');
        }
      } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la suppression de l\'article');
      }
    }

    async function clearCart() {
      if (!confirm('Êtes-vous sûr de vouloir vider le panier ?')) return;
      try {
        const response = await fetch('/cart/remove-all', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        const data = await response.json();
        if (data.success) {
          document.querySelector('.shopping-cart').innerHTML = `
            <div class="row">
              <div class="col-md-12 text-center pt-5 bp-5">
                <p>Aucun article dans votre panier</p>
                <a href="{{ route('shop.index') }}" class="btn btn-info">Continuer vos achats</a>
              </div>
            </div>`;
        } else {
          alert(data.message || 'Erreur lors de la suppression du panier');
        }
      } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la suppression du panier');
      }
    }
  </script>
@endpush