<?php // api/admin_logout.php

// Start the session to access session variables and the remember me cookie.
session_start();

// Include the necessary class files using the correct relative paths.
require_once '../php/database.php';
require_once '../php/admin.php';

// --- Clear the "Remember Me" Token and Cookie ---

// Check if an admin "Remember Me" cookie is set.
if (isset($_COOKIE['admin_remember_token'])) {
    // Connect to the database and instantiate the Admin class.
    $database = new Database();
    $db = $database->connect();
    $admin = new Admin($db);

    // Call the method to delete the specific token from the `admin_tokens` table.
    // This invalidates the token on the server side.
    $admin->clearRememberToken($_COOKIE['admin_remember_token']);

    // Clear the cookie from the admin's browser by setting its expiration to the past.
    // Using the options array is the modern, secure way to manage cookies.
    setcookie('admin_remember_token', '', [
        'expires' => time() - 3600, // A time in the past.
        'path' => '/',
        'domain' => '', // Should match the domain used in admin_login.php
        'secure' => false, // Set to true if using HTTPS.
        'httponly' => true, // Prevents client-side script access.
        'samesite' => 'Lax' // CSRF protection.
    ]);
}

// --- Destroy the PHP Session ---

// Unset all session variables to clean up the admin's state.
$_SESSION = array();

// Destroy the session itself.
session_destroy();

// --- Redirect to the Admin Login Page ---

// Redirect the user back to the admin login page in the /admin/ folder.
header("location: ../admin/index.php");
exit;
