<?php // api/user_login.php

// --- Robust Error Handling & API Setup ---
ini_set('display_errors', 0);
error_reporting(0);
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        echo json_encode([
            'status' => 'error',
            'message' => 'A critical server error occurred. Please check the server logs.',
        ]);
    }
});

session_start();
header("Content-Type: application/json; charset=UTF-8");

include_once '../php/database.php';
include_once '../php/user.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->login_identifier) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Login failed. Email/NRIC and password are required.']);
    exit();
}

// This property will be used in the User class to search both email and nric columns
$user->login_identifier = $data->login_identifier;
$user->password = $data->password;

if ($user->login()) {
    // Set user-specific session variables
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['user_loggedin'] = true;

    // The profile pic URL is now available directly from the user object after login
    $_SESSION['profile_pic_url'] = $user->profile_pic_url;

    // Handle the "Remember Me" option (if provided by the form)
    if (!empty($data->remember_me) && $data->remember_me == true) {
        $token = $user->setRememberToken();
        if ($token) {
            $cookie_expiry = time() + (86400 * 30); // 30 days
            setcookie('user_remember_token', $token, [
                'expires' => $cookie_expiry,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Set to true if using HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }

    http_response_code(200);
    echo json_encode(['message' => 'Login successful.', 'redirect' => 'index.php']);
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Login failed. Invalid credentials.']);
}
