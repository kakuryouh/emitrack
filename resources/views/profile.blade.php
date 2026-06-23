@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card wide-card">
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password <small>(Leave blank to keep current)</small></label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control">
                    <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    <span class="eye-icon" onclick="togglePassword('password_confirmation')">👁️</span>
                </div>
            </div>

            <div class="stacked-actions">
                <button style="align-items: center" type="submit" class="btn-green full-btn">Save Changes</button>
                <button style="align-items: center" onclick="{{ url('/') }}" class="btn-outline full-btn">Cancel</button>
            </div>
        </form>

        <div class="settings-section">
            <h3>Settings</h3>
            
            <div class="stacked-actions-settings">

                <form action="{{ route('logout') }}" method="POST" style="width: 100%; display: flex; justify-content: left;">
                    @csrf
                    <button type="submit" class="btn-outline full-btn">Logout</button>
                </form>
                
                <span style="justify-content: left; display: flex;">Warning this action is unreverseable!</span>
                <form action="{{ route('profile.delete') }}" method="POST" style="width: 100%; display: flex; justify-content: left;" onsubmit="return confirmDelete(this)">
                    @csrf

                    <button type="submit" class="btn-red full-btn">Delete Account</button>
                </form>

            </div>
        </div>

    </div>
</div>

<script>
    function togglePassword(id) {
        var x = document.getElementById(id);
        x.type = x.type === "password" ? "text" : "password";
    }

    function confirmDelete(form) {
        return confirm('Do you really want to delete your account?');
    }
</script>

<form id="delete-account-form" action="{{ route('profile.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection