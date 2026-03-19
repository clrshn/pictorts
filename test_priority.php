<?php

require_once 'vendor/autoload.php';

use App\Models\Todo;

try {
    $todo = Todo::create([
        'title' => 'Test Priority Dropdown',
        'description' => 'Testing TOP priority functionality',
        'priority' => 'top',
        'assigned_to' => 'CLYDE',
        'status' => 'pending',
        'due_date' => now()->addDays(7),
        'remarks' => 'Testing the new dropdown functionality',
        'user_id' => 1
    ]);

    echo "Todo created with ID: " . $todo->id;
    echo "Priority: " . $todo->priority;
    echo "Assigned to: " . $todo->assigned_to;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
