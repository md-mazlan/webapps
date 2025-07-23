<?php // api/user_register.php
header("Content-Type: application/json; charset=UTF-8");

// Note the updated path to the class files
include_once '../php/database.php';
include_once '../php/user.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->username) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Unable to create user. Data is incomplete.']);
    exit();
}

$user->username = $data->username;
$user->email = $data->email;
$user->password = $data->password;

if ($user->register()) {
    http_response_code(201);
    echo json_encode(['message' => 'User was successfully created.']);
} else {
    http_response_code(409);
    echo json_encode(['message' => 'Unable to create user. Username or email may already exist.']);
}
