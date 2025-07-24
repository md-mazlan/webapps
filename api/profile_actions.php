<?php // api/profile_actions.php

// --- Robust Error Handling & API Setup ---
// This ensures that even if a fatal PHP error occurs, a valid JSON response is sent.
ini_set('display_errors', 0);
error_reporting(0);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500); // Internal Server Error
        }
        // For debugging, you can send back the error details.
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

// Include the main configuration file to define absolute paths.
require_once '../php/config.php';

// Now use the ROOT_PATH constant for all other includes.
require_once ROOT_PATH . '/php/user_auth_check.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/user.php';

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
        case 'update_personal_info':
            $data = [
                'full_name' => htmlspecialchars(strip_tags($_POST['full_name'] ?? '')),
                'gender' => htmlspecialchars(strip_tags($_POST['gender'] ?? '')),
                'ethnic' => htmlspecialchars(strip_tags($_POST['ethnic'] ?? '')),
                'phone' => htmlspecialchars(strip_tags($_POST['phone'] ?? '')),
                'birthday' => !empty($_POST['birthday']) ? $_POST['birthday'] : null,
                'address1' => htmlspecialchars(strip_tags($_POST['address1'] ?? '')),
                'address2' => htmlspecialchars(strip_tags($_POST['address2'] ?? '')),
                'area' => htmlspecialchars(strip_tags($_POST['area'] ?? '')),
                'postal_code' => htmlspecialchars(strip_tags($_POST['postal_code'] ?? '')),
                'city' => htmlspecialchars(strip_tags($_POST['city'] ?? '')),
                'state' => htmlspecialchars(strip_tags($_POST['state'] ?? ''))
            ];
            if ($user->updatePersonalInfo($user_id, $data)) {
                $response = ['status' => 'success', 'message' => 'Personal info updated successfully!'];
            } else {
                $response['message'] = 'Failed to update personal information.';
            }
            break;

        case 'update_employment_info':
            $data = [
                'company' => htmlspecialchars(strip_tags($_POST['company'] ?? '')),
                'job_title' => htmlspecialchars(strip_tags($_POST['job_title'] ?? '')),
                'department' => htmlspecialchars(strip_tags($_POST['department'] ?? '')),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'is_current' => isset($_POST['is_current']) ? 1 : 0,
                'responsibilities' => htmlspecialchars(strip_tags($_POST['responsibilities'] ?? ''))
            ];
            if ($user->updateEmploymentInfo($user_id, $data)) {
                $response = ['status' => 'success', 'message' => 'Employment details updated successfully!'];
            } else {
                $response['message'] = 'Failed to update employment details.';
            }
            break;

        case 'update_profile_picture':
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                $updateResult = $user->updateProfilePicture($user_id, $_FILES['profile_pic']);
                if ($updateResult['status'] === 'success') {
                    $profileData = $user->getProfile($user_id);
                    $response = [
                        'status' => 'success',
                        'message' => 'Profile picture updated!',
                        'new_pic_url' => $profileData['profile_pic_url'] ?? ''
                    ];
                } else {
                    $response['message'] = $updateResult['message'];
                }
            } else {
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

        case 'change_password':
            $old_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';

            if (empty($old_password) || empty($new_password)) {
                $response['message'] = 'All password fields are required.';
            } elseif (strlen($new_password) < 6) {
                $response['message'] = 'New password must be at least 6 characters long.';
            } else {
                $result = $user->changePassword($user_id, $old_password, $new_password);
                $response = $result;
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
