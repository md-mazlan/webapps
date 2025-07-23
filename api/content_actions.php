<?php // api/content_actions.php

// This API handles actions like deleting content from the admin dashboard.

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
require_once '../php/content.php';

$database = new Database();
$db = $database->connect();
$content = new Content($db);

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- Process the requested action ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $content_id = isset($data['content_id']) ? (int)$data['content_id'] : 0;

    if (!$content_id) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid content ID.']);
        exit;
    }

    if ($action === 'delete_content') {
        if ($content->delete($content_id)) {
            $response = ['status' => 'success', 'message' => 'Content deleted successfully.'];
        } else {
            $response['message'] = 'Failed to delete content.';
        }
    } else {
        http_response_code(400);
        $response['message'] = 'Invalid action specified.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Invalid request method.';
}

// Return the final JSON response.
echo json_encode($response);
