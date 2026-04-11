<?php
require_once '/var/www/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

echo 'Testing direct database update...' . PHP_EOL;
try {
    $result = DB::table('todos')
        ->where('id', 1)
        ->update(['status' => 'done']);
    
    if ($result) {
        echo 'Database update successful' . PHP_EOL;
    } else {
        echo 'Database update failed' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
