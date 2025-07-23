<?php // register.php

// Set headers for JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include database and user objects
include_once 'database.php';
include_once 'user.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Invalid request method.']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->connect();

// Instantiate user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Basic validation
if (empty($data->username) || empty($data->email) || empty($data->password)) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Unable to create user. Data is incomplete.']);
    exit();
}

// Set user property values
$user->username = $data->username;
$user->email = $data->email;
$user->password = $data->password;

// Attempt to register the user
if ($user->register()) {
    http_response_code(201); // Created
    echo json_encode(['message' => 'User was successfully created.']);
} else {
    http_response_code(409); // Conflict
    echo json_encode(['message' => 'Unable to create user. Username or email may already exist.']);
}

?>