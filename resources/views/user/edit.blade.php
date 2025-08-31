@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Account Details</h2>
      
      @if(session('status'))
          <div class="alert alert-success">
              {{ session('status') }}
          </div>
      @endif

      @if(session('error'))
          <div class="alert alert-danger">
              {{ session('error') }}
          </div>
      @endif

      <div class="row">
        <div class="col-lg-3">
          @include('user.account-navbar')
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
          </form>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__edit">
            <div class="my-account__edit-form">
              <form name="account_edit_form" action="{{ route('user.update') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-floating my-3">
                      <input type="text" class="form-control @error('name') is-invalid @enderror" 
                             placeholder="Full Name" name="name" id="name" 
                             value="{{ old('name', $user->name) }}" required>
                      <label for="name">Name</label>
                      @error('name')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                             placeholder="Mobile Number" name="mobile" id="mobile" 
                             value="{{ old('mobile', $user->mobile) }}" required>
                      <label for="mobile">Mobile Number</label>
                      @error('mobile')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="email" class="form-control @error('email') is-invalid @enderror" 
                             placeholder="Email Address" name="email" id="account_email" 
                             value="{{ old('email', $user->email) }}" required>
                      <label for="account_email">Email Address</label>
                      @error('email')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="my-3">
                      <h5 class="text-uppercase mb-0">Password Change <small class="text-muted">(Leave blank if you don't want to change)</small></h5>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                             id="current_password" name="current_password" placeholder="Current password">
                      <label for="current_password">Current password</label>
                      @error('current_password')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                             id="new_password" name="new_password" placeholder="New password">
                      <label for="new_password">New password</label>
                      @error('new_password')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                             id="new_password_confirmation" name="new_password_confirmation"
                             placeholder="Confirm new password">
                      <label for="new_password_confirmation">Confirm new password</label>
                      @error('new_password_confirmation')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <div class="invalid-feedback" id="password-match-error" style="display: none;">Passwords did not match!</div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="my-3">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    const passwordMatchError = document.getElementById('password-match-error');
    
    // Validation conditionnelle du mot de passe
    function togglePasswordValidation() {
        const hasCurrentPassword = currentPasswordInput.value.trim() !== '';
        const hasNewPassword = newPasswordInput.value.trim() !== '';
        
        if (hasCurrentPassword || hasNewPassword) {
            currentPasswordInput.setAttribute('required', 'required');
            newPasswordInput.setAttribute('required', 'required');
            confirmPasswordInput.setAttribute('required', 'required');
        } else {
            currentPasswordInput.removeAttribute('required');
            newPasswordInput.removeAttribute('required');
            confirmPasswordInput.removeAttribute('required');
        }
    }
    
    // Vérifier que les mots de passe correspondent
    function checkPasswordMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword && newPassword !== confirmPassword) {
            confirmPasswordInput.setCustomValidity('Passwords do not match');
            passwordMatchError.style.display = 'block';
            confirmPasswordInput.classList.add('is-invalid');
        } else {
            confirmPasswordInput.setCustomValidity('');
            passwordMatchError.style.display = 'none';
            confirmPasswordInput.classList.remove('is-invalid');
        }
    }
    
    // Écouter les changements dans les champs de mot de passe
    currentPasswordInput.addEventListener('input', togglePasswordValidation);
    newPasswordInput.addEventListener('input', function() {
        togglePasswordValidation();
        checkPasswordMatch();
    });
    confirmPasswordInput.addEventListener('input', function() {
        togglePasswordValidation();
        checkPasswordMatch();
    });
    
    // Validation du formulaire
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(e) {
        togglePasswordValidation();
        checkPasswordMatch();
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>

@endsection