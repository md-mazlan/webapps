<?php // api/user_register.php
header("Content-Type: application/json; charset=UTF-8");

include_once '../php/database.php';
include_once '../php/user.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

// Validation to make NRIC mandatory
if (empty($data->nric) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Unable to create user. NRIC, Email, and Password are required.']);
    exit();
}

$user->username = $data->username ?? ''; // Username is optional
$user->email = $data->email;
$user->nric = $data->nric;
$user->password = $data->password;

if ($user->register()) {
    http_response_code(201);
    echo json_encode(['message' => 'User was successfully created.']);
} else {
    http_response_code(409);
    echo json_encode(['message' => 'Unable to create user. Email or NRIC may already exist, or NRIC was not a valid number.']);
}
