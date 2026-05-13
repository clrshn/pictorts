<?php
// Test script to check if GD extension is loaded in web context
echo "GD Extension Status: " . (extension_loaded('gd') ? 'LOADED' : 'NOT LOADED') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded Configuration File: " . php_ini_loaded_file() . "\n";

if (extension_loaded('gd')) {
    echo "GD Info: " . print_r(gd_info(), true) . "\n";
}
?>
