<?php

/**
 * Checks if a public user is currently logged in, either through an active
 * session or a valid "Remember Me" token.
 *
 * This is used for public-facing pages where interactions are allowed.
 *
 * @return bool Returns true if the user is logged in, false otherwise.
 */
function isUserLoggedIn()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Check for an active user session.
    if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true) {
        return true;
    }

    // 2. If no session, check for a user "Remember Me" cookie.
    if (isset($_COOKIE['user_remember_token'])) {
        // Use require_once to prevent re-declaration errors.
        require_once 'database.php';
        require_once 'user.php'; // Use the User class

        $database = new Database();
        $db = $database->connect();

        $user = new User($db);
        if ($user->validateRememberToken($_COOKIE['user_remember_token'])) {
            // Token is valid. Re-establish the user session.
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user_loggedin'] = true;

            return true;
        } else {
            // The token was invalid or expired. Clean up the bad cookie.
            setcookie('user_remember_token', '', time() - 3600, '/');
        }
    }

    return false;
}
