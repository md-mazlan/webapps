<?php // api/user_login.php
session_start();
header("Content-Type: application/json; charset=UTF-8");

// Note the updated path to the class files
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

$user->login_identifier = $data->login_identifier;
$user->password = $data->password;

if ($user->login()) {
    // Set user-specific session variables
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['user_loggedin'] = true;

    // Fetch the user's profile to get the picture URL
    $profileData = $user->getProfile($user->id);
    $_SESSION['profile_pic_url'] = $profileData['profile_pic_url'] ?? null;

    if (!empty($data->remember_me) && $data->remember_me == true) {
        $token = $user->setRememberToken();
        if ($token) {
            $cookie_expiry = time() + (86400 * 30);
            setcookie('user_remember_token', $token, $cookie_expiry, "/", "", false, true);
        }
    }

    http_response_code(200);
    echo json_encode(['message' => 'Login successful.', 'redirect' => 'index.php']); // Redirect to homepage
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Login failed. Invalid credentials.']);
}
