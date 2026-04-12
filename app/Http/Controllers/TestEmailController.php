<?php

namespace App\Http\Controllers;

use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestEmailController extends Controller
{
    /**
     * Test email configuration
     */
    public function test(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Send test notification
            $user->notify(new SystemNotification(
                'Email Configuration Test',
                'Email Test',
                'This is a test email to verify your email configuration is working correctly.',
                route('dashboard'),
                'Go to Dashboard',
                ['Test Time: ' . now()->format('Y-m-d H:i:s')],
                'EMAIL-TEST-' . time()
            ));

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'user' => $user->email,
                'time' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send test email: ' . $e->getMessage(),
                'details' => [
                    'Error: ' . $e->getMessage(),
                    'File: ' . $e->getFile(),
                    'Line: ' . $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Test email configuration without auth (for debugging)
     */
    public function testWithoutAuth(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            // Create a test user object
            $testUser = new class {
                public $email = 'test@example.com';
                public $name = 'Test User';
            };
            $testUser->email = $request->email;
            $testUser->name = 'Test User';

            // Send test notification
            $testUser->notify(new SystemNotification(
                'Email Configuration Test',
                'Email Test',
                'This is a test email to verify your email configuration is working correctly.',
                route('dashboard'),
                'Go to Dashboard',
                ['Test Time: ' . now()->format('Y-m-d H:i:s')],
                'EMAIL-TEST-' . time()
            ));

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'email' => $request->email,
                'time' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send test email: ' . $e->getMessage(),
                'details' => [
                    'Error: ' . $e->getMessage(),
                    'File: ' . $e->getFile(),
                    'Line: ' . $e->getLine()
                ]
            ], 500);
        }
    }
}
