<?php
// logout.php

// Start the session to access session variables.
session_start();

// Include the necessary class files to interact with the database.
require_once 'database.php';
require_once 'user.php';

// --- Clear the "Remember Me" Token and Cookie ---

// Check if a remember_token cookie is set.
if (isset($_COOKIE['remember_token'])) {
    // Connect to the database and instantiate the User class.
    $database = new Database();
    $db = $database->connect();
    $user = new User($db);
    
    // Call the method to delete the specific token from the `auth_tokens` table.
    // This is crucial for security, as it invalidates the token.
    $user->clearRememberToken($_COOKIE['remember_token']);
    
    // Clear the cookie from the user's browser by setting its expiration to the past.
    // Use the same secure options as when the cookie was set.
    setcookie('remember_token', '', [
        'expires' => time() - 3600, // A time in the past.
        'path' => '/',
        'domain' => '', // Should match the domain used in login.php
        'secure' => false, // Set to true if using HTTPS.
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// --- Destroy the PHP Session ---

// Unset all session variables to clean up the user's state.
$_SESSION = array();

// Destroy the session itself.
session_destroy();

// --- Redirect to Login Page ---

// Redirect the user back to the main login page.
header("location: ../index.php");
exit;
?>
