@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-green">Login</button>
                <span class="or-divider">Or</span>
                <a href="{{ route('register') }}" class="btn-outline">Register Now</a>
            </div>
        </form>
    </div>
</div>
@endsection