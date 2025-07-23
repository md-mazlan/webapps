<?php // api/admin_login.php
session_start();
header("Content-Type: application/json; charset=UTF-8");

// Note the updated path to the class files using '../' to go up one directory.
include_once '../php/database.php';
include_once '../php/admin.php';

$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->username) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Login failed. Username and password are required.']);
    exit();
}

$admin->username = $data->username;
$admin->password = $data->password;

if ($admin->login()) {
    // Set admin-specific session variables to avoid conflicts with user sessions.
    $_SESSION['admin_id'] = $admin->id;
    $_SESSION['admin_username'] = $admin->username;
    $_SESSION['admin_loggedin'] = true;

    if (!empty($data->remember_me) && $data->remember_me == true) {
        $token = $admin->setRememberToken();
        if ($token) {
            $cookie_expiry = time() + (86400 * 30);
            // Use a unique cookie name for admins.
            setcookie('admin_remember_token', $token, $cookie_expiry, "/", "", false, true);
        }
    }

    http_response_code(200);
    // Redirect to the dashboard inside the /admin/ folder.
    echo json_encode(['message' => 'Login successful.', 'redirect' => 'dashboard.php']);
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Login failed. Invalid credentials.']);
}
