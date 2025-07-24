<?php // api/user_actions.php

// This API handles admin actions related to public users, like deletion.

header('Content-Type: application/json');

// Use the centralized admin authentication check.
require_once '../php/admin_auth_check.php';

if (!isAdminLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in as an admin to perform this action.']);
    exit;
}

// Include necessary classes.
require_once '../php/database.php';
require_once '../php/admin.php';

$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;

    if (!$user_id) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
        exit;
    }

    if ($action === 'delete_user') {
        if ($admin->deleteUser($user_id)) {
            $response = ['status' => 'success', 'message' => 'User deleted successfully.'];
        } else {
            $response['message'] = 'Failed to delete user.';
        }
    } else {
        http_response_code(400);
        $response['message'] = 'Invalid action specified.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
