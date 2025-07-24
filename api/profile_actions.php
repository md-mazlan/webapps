<?php // api/profile_actions.php

// --- Robust Error Handling & API Setup ---
// This ensures that even if a fatal PHP error occurs, a valid JSON response is sent.
ini_set('display_errors', 0);
error_reporting(0);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // If headers haven't been sent, it means our script failed before sending a response.
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500); // Internal Server Error
        }
        // For debugging, we can send back the error details.
        // In a live production environment, you would log this error instead of displaying it.
        echo json_encode([
            'status' => 'error',
            'message' => 'A critical server error occurred. Please check the server logs.',
            'error_details' => [
                'type' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ]
        ]);
    }
});

header('Content-Type: application/json');

// This API handles updating user profile information and profile picture uploads.
require_once '../php/user_auth_check.php';
require_once '../php/database.php';
require_once '../php/user.php';

// All actions on this page require a logged-in public user.
if (!isUserLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

$database = new Database();
$db = $database->connect();
$user = new User($db);
$user_id = $_SESSION['user_id'];

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_profile_text':
            // Sanitize and prepare data for updating
            $data = [
                'full_name' => htmlspecialchars(strip_tags($_POST['full_name'] ?? '')),
                'bio' => htmlspecialchars(strip_tags($_POST['bio'] ?? '')),
                'company' => htmlspecialchars(strip_tags($_POST['company'] ?? '')),
                'job_title' => htmlspecialchars(strip_tags($_POST['job_title'] ?? '')),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'is_current' => isset($_POST['is_current']) ? 1 : 0,
            ];

            if ($user->updateProfile($user_id, $data)) {
                $response = ['status' => 'success', 'message' => 'Profile updated successfully!'];
            } else {
                $response['message'] = 'Failed to update profile information.';
            }
            break;

        case 'update_profile_picture':
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {

                // Pass the file to the user class to handle the upload and update
                $updateResult = $user->updateProfilePicture($user_id, $_FILES['profile_pic']);

                if ($updateResult['status'] === 'success') {
                    // On success, fetch the new URL to send back to the client
                    $profileData = $user->getProfile($user_id);
                    $response = [
                        'status' => 'success',
                        'message' => 'Profile picture updated!',
                        'new_pic_url' => $profileData['profile_pic_url'] ?? ''
                    ];
                } else {
                    // Pass the specific error message from the user class
                    $response['message'] = $updateResult['message'];
                }
            } else {
                // Handle different upload errors
                $upload_error_messages = [
                    UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server\'s maximum file size limit.',
                    UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the maximum file size specified in the form.',
                    UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE    => 'No file was selected for upload.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error: Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Server error: Failed to write file to disk. Check folder permissions.',
                    UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
                ];
                $error_code = $_FILES['profile_pic']['error'] ?? UPLOAD_ERR_NO_FILE;
                $response['message'] = $upload_error_messages[$error_code] ?? 'An unknown upload error occurred.';
            }
            break;

        default:
            http_response_code(400);
            $response['message'] = 'Invalid action specified.';
            break;
    }
} else {
    http_response_code(405);
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
