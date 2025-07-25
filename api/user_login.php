<?php // api/user_login.php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once '../php/config.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/user.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->login_identifier) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Login failed. Email/NRIC and password are required.']);
    exit();
}

$user->login_identifier = $data->login_identifier;
$user->password = $data->password;

if ($user->login()) {
    // Set user-specific session variables
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['user_loggedin'] = true;
    $_SESSION['profile_pic_url'] = $user->profile_pic_url;

    // Check if the user's profile is complete to determine the redirect URL
    if ($user->isProfileComplete($user->id)) {
        $redirect_url = 'index.php'; // Profile is complete, go to homepage
    } else {
        $redirect_url = 'profile_setup.php'; // Profile is incomplete, go to the setup page
    }

    if (!empty($data->remember_me) && $data->remember_me == true) {
        $token = $user->setRememberToken();
        if ($token) {
            $cookie_expiry = time() + (86400 * 30);
            setcookie('user_remember_token', $token, [
                'expires' => $cookie_expiry,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }

    http_response_code(200);
    echo json_encode(['message' => 'Login successful.', 'redirect' => $redirect_url]);
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Login failed. Invalid credentials.']);
}
