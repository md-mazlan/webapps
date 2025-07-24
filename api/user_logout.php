<?php // api/user_logout.php

// Start the session to access session variables and the remember me cookie.
session_start();

// Include the necessary class files using the correct relative paths.
require_once '../php/database.php';
require_once '../php/user.php'; // Use the User class for public users

// --- Clear the "Remember Me" Token and Cookie ---

// Check if a user "Remember Me" cookie is set.
if (isset($_COOKIE['user_remember_token'])) {
    // Connect to the database and instantiate the User class.
    $database = new Database();
    $db = $database->connect();
    $user = new User($db);

    // Call the method to delete the specific token from the `auth_tokens` table.
    $user->clearRememberToken($_COOKIE['user_remember_token']);

    // Clear the cookie from the user's browser by setting its expiration to the past.
    setcookie('user_remember_token', '', [
        'expires' => time() - 3600, // A time in the past.
        'path' => '/',
        'domain' => '', // Should match the domain used in user_login.php
        'secure' => false, // Set to true if using HTTPS.
        'httponly' => true, // Prevents client-side script access.
        'samesite' => 'Lax' // CSRF protection.
    ]);
}

// --- Destroy the PHP Session ---

// Unset all session variables to clean up the user's state.
$_SESSION = array();

// Destroy the session itself.
session_destroy();

// --- Redirect to the Public Homepage ---

// Redirect the user back to the main public-facing index page.
header("location: ../index.php");
exit;
