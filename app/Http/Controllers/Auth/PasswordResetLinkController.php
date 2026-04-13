<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Throwable;
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

        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', __($status));
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        } catch (Throwable $e) {
            \Log::error('Password reset email failed.', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Failed to send password reset email. Check your mail settings and try again.',
                ]);
        }
    }
}
