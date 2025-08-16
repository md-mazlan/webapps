<?php

// Also check if a normal user is logged in and block access
require_once 'config.php';
require_once 'user_auth_check.php';

// If a normal user is logged in, redirect to user dashboard (or homepage)
if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/dashboard'); // Adjust path if needed
    exit;
}
/**
 * Checks if an administrator is currently logged in, either through an active
 * session or a valid "Remember Me" token.
 *
 * This function should be included at the top of any page in the /admin/ folder.
 *
 * @return bool Returns true if the admin is logged in, false otherwise.
 */
function isAdminLoggedIn()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Check for an active admin session.
    if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
        return true;
    }

    // 2. If no session, check for an admin "Remember Me" cookie.
    if (isset($_COOKIE['admin_remember_token'])) {
        // Use require_once to prevent re-declaration errors.
        require_once 'database.php';
        require_once 'admin.php'; // Use the Admin class

        $database = new Database();
        $db = $database->connect();

        $admin = new Admin($db);
        if ($admin->validateRememberToken($_COOKIE['admin_remember_token'])) {
            // Token is valid. Re-establish the admin session.
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            $_SESSION['admin_loggedin'] = true;

            return true;
        } else {
            // The token was invalid or expired. Clean up the bad cookie.
            setcookie('admin_remember_token', '', time() - 3600, '/');
        }
    }

    return false;
}
