@extends('layouts.app')
@section('content')
<style>
  .text-success{
    color: #008000 !important;
  }
</style>
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
                <div id="cart-totals-container">
                  @if(Session::has('discounts'))
                    <table class="cart-totals">
                    <tbody>
                      <tr>
                        <th>Subtotal</th>
                        <td id="cart-original-subtotal">${{ Session::get('discounts')['original_subtotal'] }}</td>
                      </tr>
                      <tr id="discount-row">
                        <th>Discount {{ Session::get('coupon')['code'] }} 
                          <button type="button" onclick="removeCoupon()" class="btn btn-sm btn-outline-danger ms-2" title="Supprimer le coupon">×</button>
                        </th>
                        <td id="cart-discount" class="text-success">-${{ Session::get('discounts')['discount'] }}</td>
                      </tr>
                      <tr>
                        <th>Subtotal after discount</th>
                        <td id="cart-subtotal">${{ Session::get('discounts')['subtotal'] }}</td>
                      </tr>
                      <tr>
                        <th>Shipping</th>
                        <td>Free</td>
                      </tr>
                      <tr>
                        <th>VAT</th>
                        <td id="cart-tax">${{ Session::get('discounts')['tax'] }}</td>
                      </tr>
                      <tr>
                        <th>Total</th>
                        <td id="cart-total">${{ Session::get('discounts')['total'] }}</td>
                      </tr>
                    </tbody>
                  </table>
                  @else
                  <table class="cart-totals">
                    <tbody>
                      <tr>
                        <th>Subtotal</th>
                        <td id="cart-subtotal">${{ Cart::instance('cart')->subtotal() }}</td>
                      </tr>
                      <tr>
                        <th>Shipping</th>
                        <td>Free</td>
                      </tr>
                      <tr>
                        <th>VAT</th>
                        <td id="cart-tax">${{ Cart::instance('cart')->tax() }}</td>
                      </tr>
                      <tr>
                        <th>Total</th>
                        <td id="cart-total">${{ Cart::instance('cart')->total() }}</td>
                      </tr>
                    </tbody>
                  </table>
                  @endif
                </div>
              </div>
              <div class="mobile_fixed-btn_wrapper">
                <div class="button-wrapper container">
                  <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
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
let hasDiscount = {{ Session::has('discounts') ? 'true' : 'false' }};

// Fonction globale pour supprimer le coupon
window.removeCoupon = async function() {
    console.log('Fonction removeCoupon appelée');
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce coupon ?')) return;
    
    try {
        const response = await fetch('{{ route("cart.remove_coupon") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('Response status:', response.status);
        console.log('Response data:', data);
        
        if (response.ok && data.success) {
            // Afficher message de succès
            const messagesDiv = document.getElementById('coupon-messages');
            messagesDiv.innerHTML = `<p class="text-success">${data.message}</p>`;
            
            // Vider le champ coupon
            document.getElementById('coupon_code').value = '';
            
            // Mettre à jour l'affichage sans remise
            updateCartDisplayWithoutDiscount(data);
            
        } else {
            console.error('Server error:', data);
            alert(data.message || 'Erreur lors de la suppression du coupon');
        }
    } catch (error) {
        console.error('Network/Parse error:', error);
        alert('Une erreur réseau s\'est produite: ' + error.message);
    }
};

// Application du coupon en AJAX
document.getElementById('coupon-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const couponCode = document.getElementById('coupon_code').value.trim();
    const messagesDiv = document.getElementById('coupon-messages');
    const submitButton = this.querySelector('button[type="submit"]');
    
    if (!couponCode) {
        messagesDiv.innerHTML = '<p class="text-danger">Veuillez entrer un code de coupon</p>';
        return;
    }
    
    // Désactiver le bouton pendant le traitement
    submitButton.disabled = true;
    submitButton.textContent = 'APPLYING...';
    
    try {
        const formData = new FormData();
        formData.append('coupon_code', couponCode);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        const response = await fetch('{{ route("cart.apply_coupon") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        console.log('Coupon response status:', response.status);
        console.log('Coupon response data:', data);
        
        // Nettoyer les anciens messages
        messagesDiv.innerHTML = '';
        
        if (response.ok && data.success) {
            // Afficher le message de succès
            messagesDiv.innerHTML = `<p class="text-success">${data.message}</p>`;
            
            // Mettre à jour l'affichage avec remise
            updateCartDisplayWithDiscount(data);
            
        } else {
            // Afficher le message d'erreur
            messagesDiv.innerHTML = `<p class="text-danger">${data.message}</p>`;
        }
        
    } catch (error) {
        console.error('Coupon error:', error);
        messagesDiv.innerHTML = `<p class="text-danger">Erreur réseau: ${error.message}</p>`;
    } finally {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = 'APPLY COUPON';
    }
});

function updateCartDisplayWithDiscount(data) {
    const cartTotalsContainer = document.getElementById('cart-totals-container');
    
    console.log('Mise à jour avec remise:', data);
    
    // Créer le nouveau tableau avec remise
    const newTable = `
        <table class="cart-totals">
            <tbody>
                <tr>
                    <th>Subtotal</th>
                    <td id="cart-original-subtotal">$${data.originalSubtotal}</td>
                </tr>
                <tr id="discount-row">
                    <th>Discount ${data.coupon.code} 
                        <button type="button" onclick="removeCoupon()" class="btn btn-sm btn-outline-danger ms-2" title="Supprimer le coupon">×</button>
                    </th>
                    <td id="cart-discount" class="text-success">-$${data.discount}</td>
                </tr>
                <tr>
                    <th>Subtotal after discount</th>
                    <td id="cart-subtotal">$${data.cartSubtotal}</td>
                </tr>
                <tr>
                    <th>Shipping</th>
                    <td>Free</td>
                </tr>
                <tr>
                    <th>VAT</th>
                    <td id="cart-tax">$${data.cartTax}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td id="cart-total">$${data.cartTotal}</td>
                </tr>
            </tbody>
        </table>
    `;
    
    cartTotalsContainer.innerHTML = newTable;
    hasDiscount = true;
}

function updateCartDisplayWithoutDiscount(data) {
    const cartTotalsContainer = document.getElementById('cart-totals-container');
    
    console.log('Mise à jour sans remise:', data);
    
    const newTable = `
        <table class="cart-totals">
            <tbody>
                <tr>
                    <th>Subtotal</th>
                    <td id="cart-subtotal">$${data.cartSubtotal}</td>
                </tr>
                <tr>
                    <th>Shipping</th>
                    <td>Free</td>
                </tr>
                <tr>
                    <th>VAT</th>
                    <td id="cart-tax">$${data.cartTax}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td id="cart-total">$${data.cartTotal}</td>
                </tr>
            </tbody>
        </table>
    `;
    
    cartTotalsContainer.innerHTML = newTable;
    hasDiscount = false;
}

function updateCartTotals(data) {
    console.log('Mise à jour totaux avec:', data);
    
    if (hasDiscount && data.originalSubtotal && data.discount) {
        // Mise à jour avec remise - utiliser les nouvelles données
        const originalSubtotalEl = document.getElementById('cart-original-subtotal');
        const discountEl = document.getElementById('cart-discount');
        const subtotalEl = document.getElementById('cart-subtotal');
        
        if (originalSubtotalEl) originalSubtotalEl.textContent = `$${data.originalSubtotal}`;
        if (discountEl) discountEl.textContent = `-$${data.discount}`;
        if (subtotalEl) subtotalEl.textContent = `$${data.cartSubtotal}`;
    } else {
        // Mise à jour sans remise
        const subtotalEl = document.getElementById('cart-subtotal');
        if (subtotalEl) subtotalEl.textContent = `$${data.cartSubtotal}`;
    }
    
    // Mettre à jour VAT et Total dans tous les cas
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');
    
    if (taxEl) taxEl.textContent = `$${data.cartTax}`;
    if (totalEl) totalEl.textContent = `$${data.cartTotal}`;
}

async function updateQuantity(rowId, action) {
    console.log(`Starting quantity update: ${action} for rowId: ${rowId}`);
    
    // Construire l'URL correcte
    let url;
    if (action === 'increase') {
        url = `/cart/increase-quantity/${rowId}`;
    } else if (action === 'decrease') {
        url = `/cart/decrease-quantity/${rowId}`;
    } else {
        console.error('Action invalide:', action);
        alert('Action invalide');
        return;
    }
    
    console.log('URL construite:', url);
    
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response error:', errorText);
            throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            const row = document.querySelector(`tr[data-rowid="${rowId}"]`);
            if (row) {
                const qtyInput = row.querySelector('.qty-control__number');
                const subtotalSpan = row.querySelector('.shopping-cart__subtotal');
                
                if (qtyInput) qtyInput.value = data.quantity;
                if (subtotalSpan) subtotalSpan.textContent = `$${data.subtotal}`;
            } else {
                console.warn('Row not found for rowId:', rowId);
            }
            
            updateCartTotals(data);
        } else {
            console.error('Server returned error:', data.message);
            alert(data.message || 'Erreur lors de la mise à jour de la quantité');
        }
    } catch (error) {
        console.error('Complete error object:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        alert(`Erreur lors de la mise à jour: ${error.message}`);
    }
}

async function removeItem(rowId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) return;
    
    console.log('Removing item with rowId:', rowId);
    
    try {
        const response = await fetch(`/cart/remove/${rowId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        console.log('Remove response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Remove error response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Remove response data:', data);
        
        if (data.success) {
            const row = document.querySelector(`tr[data-rowid="${rowId}"]`);
            if (row) row.remove();
            
            updateCartTotals(data);
            
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
        console.error('Remove item error:', error);
        alert(`Erreur lors de la suppression: ${error.message}`);
    }
}

async function clearCart() {
    if (!confirm('Êtes-vous sûr de vouloir vider le panier ?')) return;
    
    try {
        const response = await fetch('/cart/remove-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        console.log('Clear cart response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Clear cart error:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Clear cart response data:', data);
        
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
        console.error('Clear cart error:', error);
        alert(`Erreur lors du vidage du panier: ${error.message}`);
    }
}
</script>
@endpush