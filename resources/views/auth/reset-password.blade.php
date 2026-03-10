<x-guest-layout>
    <div class="login-logo">
        <div class="logo-title">
            <span class="red">PICTO</span><span class="blue">-RTS</span>
        </div>
        <div class="logo-sub">DOCUMENT & FINANCIAL TRACKING SYSTEM</div>
    </div>

    <div style="text-align: center; margin-bottom: 24px;">
        <h2 style="color: #333; font-size: 20px; font-weight: 600; margin-bottom: 8px;">Reset Password</h2>
        <p style="color: #666; font-size: 14px; line-height: 1.5;">
            Enter your new password below to reset your account.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="login-form-group">
            <label style="display: block; margin-bottom: 6px; color: #555; font-size: 14px; font-weight: 500;">
                Email Address
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email', $request->email) }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="Enter your email address"
                style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none; background: #fff; color: #444;"
            >
            @error('email')
                <div style="color: #dc3545; font-size: 12px; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="login-form-group">
            <label style="display: block; margin-bottom: 6px; color: #555; font-size: 14px; font-weight: 500;">
                New Password
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="Enter your new password"
                style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none; background: #fff; color: #444;"
            >
            @error('password')
                <div style="color: #dc3545; font-size: 12px; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="login-form-group">
            <label style="display: block; margin-bottom: 6px; color: #555; font-size: 14px; font-weight: 500;">
                Confirm New Password
            </label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Confirm your new password"
                style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none; background: #fff; color: #444;"
            >
            @error('password_confirmation')
                <div style="color: #dc3545; font-size: 12px; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-login">
                <i class="fas fa-key"></i>
                Reset Password
            </button>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('login') }}" style="color: #1a1a6c; text-decoration: none; font-size: 14px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
