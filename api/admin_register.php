<?php // api/admin_register.php
// This script is for manually creating new admin accounts.
// For security, it should be protected or used only by authorized personnel.

header("Content-Type: application/json; charset=UTF-8");

// Note the updated path to the class files using '../' to go up one directory.
include_once '../php/database.php';
include_once '../php/admin.php';

$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

$data = json_decode(file_get_contents("php://input"));

// Basic validation
if (empty($data->username) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Unable to create admin. Data is incomplete.']);
    exit();
}

// Set admin property values
$admin->username = $data->username;
$admin->email = $data->email;
$admin->password = $data->password;

// Attempt to register the admin
if ($admin->register()) {
    http_response_code(201); // Created
    echo json_encode(['message' => 'Admin account was successfully created.']);
} else {
    http_response_code(409); // Conflict
    echo json_encode(['message' => 'Unable to create admin. Username or email may already exist.']);
}
