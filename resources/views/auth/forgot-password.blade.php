<x-guest-layout>
    <div class="login-logo">
        <div class="logo-title">
            <span class="red">PICTO</span><span class="blue"> - RTS</span>
        </div>
        <div class="logo-sub">PICTO - RECORDS AND TRACKING SYSTEM</div>
    </div>

    <div style="text-align: center; margin-bottom: 24px;">
        <h2 style="color: #333; font-size: 20px; font-weight: 600; margin-bottom: 8px;">Forgot Password?</h2>
        <p style="color: #666; font-size: 14px; line-height: 1.5;">
            No problem. Just let us know your email address and we will email you a password reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="login-form-group">
            <label style="display: block; margin-bottom: 6px; color: #555; font-size: 14px; font-weight: 500;">
                Email Address
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                placeholder="Enter your email address"
                style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none; background: #fff; color: #444;"
            >
            @error('email')
                <div style="color: #dc3545; font-size: 12px; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-login">
                <i class="fas fa-envelope"></i>
                Email Password Reset Link
            </button>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('login') }}" style="color: #1a1a6c; text-decoration: none; font-size: 14px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
