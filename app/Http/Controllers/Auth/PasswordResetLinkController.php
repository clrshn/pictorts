<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found with this email address.']);
        }

        // Generate password reset token
        $token = Str::random(60);
        
        // Save token to password_resets table
        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Send password reset notification using our new email system
        try {
            $user->notify(new ResetPasswordNotification($token));
            
            return back()->with('status', 'Password reset link has been sent to your email address.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Failed to send password reset email. Please try again later.']);
        }
    }
}
