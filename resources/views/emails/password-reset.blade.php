@component('emails.layouts.app', ['title' => 'Password Reset Request - PICTO - RMS'])

    <h2 style="color: #1a1a2e; margin-bottom: 20px;">Password Reset Request</h2>
    
    <p>Hello {{ $notifiable->name }},</p>
    
    <p>You have requested to reset your password for your PICTO - RMS account. Click the button below to reset your password:</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" class="btn">Reset Password</a>
    </div>
    
    <p>If you did not request a password reset, no further action is required. This link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</p>
    
    <div class="alert">
        <strong>Security Notice:</strong> Never share this link with anyone. If you didn't request this reset, please contact support immediately.
    </div>
    
    <p>Best regards,<br>The PICTO - RMS Team</p>
@endcomponent
