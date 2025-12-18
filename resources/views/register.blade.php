@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <form method="POST" action="/register">
            @csrf

            <div class="form-group">
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="name" required>

                @error('name')
                    <div class="label -mt-4 mb-2">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </div>
                @enderror

            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="email" required>

                @error('email')
                    <div class="label -mt-4 mb-2">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </div>
                @enderror

            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="*******" required>
                    <span class="eye-icon" onclick="togglePassword('password')">👁️</span>

                    @error('password')
                        <div class="label -mt-4 mb-2">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror

                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="*******" required>
                    <span class="eye-icon" onclick="togglePassword('password_confirmation')">👁️</span>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-green">Register</button>
                <span class="or-divider">Or</span>
            </div>
        </form>
        <a href="{{ route('login') }}" class="btn-outline">Login</a>
    </div>
</div>
@endsection