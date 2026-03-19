<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Check if priority column exists and its type
    $columns = DB::select("SHOW COLUMNS FROM pictorts.todos WHERE Field = 'priority'");
    
    echo "Priority column info:\n";
    print_r($columns);
    
    // Also check table structure
    echo "\nTable columns:\n";
    $tableColumns = Schema::getColumnListing('todos');
    print_r($tableColumns);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
