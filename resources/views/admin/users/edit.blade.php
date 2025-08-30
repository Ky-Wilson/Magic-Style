@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit User</h3>
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
                    <a href="{{ route('admin.get.users') }}">
                        <div class="text-tiny">All Users</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit User</div>
                </li>
            </ul>
        </div>
        
        <div class="wg-box">
            @if(Session::has('status'))
                <div class="alert alert-success mb-20">{{ Session::get('status') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger mb-20">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="form-new-product form-style-1" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <fieldset class="name">
                    <div class="body-title">Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Enter user name" name="name" 
                           value="{{ old('name', $user->name) }}" tabindex="0" aria-required="true" required>
                </fieldset>

                <fieldset class="email">
                    <div class="body-title">Email <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="email" placeholder="Enter email address" name="email" 
                           value="{{ old('email', $user->email) }}" tabindex="0" aria-required="true" required>
                </fieldset>

                <fieldset class="mobile">
                    <div class="body-title">Mobile <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Enter mobile number" name="mobile" 
                           value="{{ old('mobile', $user->mobile) }}" tabindex="0" aria-required="true" required>
                </fieldset>

                <fieldset class="utype">
                    <div class="body-title">User Type <span class="tf-color-1">*</span></div>
                    <div class="select flex-grow">
                        <select name="utype" required>
                            <option value="">Select User Type</option>
                            <option value="USR" {{ old('utype', $user->utype) == 'USR' ? 'selected' : '' }}>User</option>
                            <option value="ADM" {{ old('utype', $user->utype) == 'ADM' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="password">
                    <div class="body-title">New Password <span class="text-tiny">(Leave blank to keep current password)</span></div>
                    <input class="flex-grow" type="password" placeholder="Enter new password" name="password" 
                           tabindex="0">
                </fieldset>

                <fieldset class="password-confirmation">
                    <div class="body-title">Confirm Password</div>
                    <input class="flex-grow" type="password" placeholder="Confirm new password" name="password_confirmation" 
                           tabindex="0">
                </fieldset>

                <div class="flex items-center justify-between gap20 mb-27">
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Update User</button>
                    </div>
                    <div class="bot">
                        <a href="{{ route('admin.get.users') }}" class="tf-button style-1 w208">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validation côté client pour les mots de passe
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirmation = document.querySelector('input[name="password_confirmation"]').value;
        
        if (password && password !== passwordConfirmation) {
            e.preventDefault();
            alert('Password confirmation does not match.');
            return false;
        }
    });
</script>
@endpush