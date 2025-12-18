<?php
// Debug script to test session behavior
session_start();

echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . "\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Current session data: " . print_r($_SESSION, true) . "\n";

// Simulate what LoginController does
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'test@test.com',
    'role' => 'ADMIN'
];

echo "\nAfter setting admin session:\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";

// Save session
session_write_close();

echo "Session written. Check if it persists.\n";
echo "\nTo test, run this script twice - the second time should show the admin data.\n";
