<?php
require_once '/var/www/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test the updateStatus method directly
Route::patch('/test-update-status', function (Request $request) {
    try {
        $todo = \App\Models\Todo::find(1);
        
        if (!$todo) {
            return response()->json(['error' => 'Todo not found'], 404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,on-going,done,cancelled',
        ]);

        $todo->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true, 
            'status' => $validated['status']
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});
