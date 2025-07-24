<?php // api/export_users.php

// Use the centralized admin authentication check.
require_once '../php/admin_auth_check.php';

// Ensure an admin is logged in to access this feature.
if (!isAdminLoggedIn()) {
    http_response_code(401); // Unauthorized
    exit('You do not have permission to access this resource.');
}

// Include necessary classes.
require_once '../php/database.php';
require_once '../php/admin.php';

$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

// Get the selected columns from the URL query parameters.
$selectedColumns = isset($_GET['columns']) ? explode(',', $_GET['columns']) : ['id', 'username', 'email', 'created_at'];

// Fetch the user data with only the selected columns.
$users = $admin->getUsersForExport($selectedColumns);

if (empty($users)) {
    // Provide a graceful exit if there are no users to export.
    echo "No user data available for export with the selected columns.";
    exit;
}

// Set the filename for the download.
$filename = "users_export_" . date('Y-m-d') . ".csv";

// Set the HTTP headers to trigger a file download.
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Create a file pointer connected to the output stream.
$output = fopen('php://output', 'w');

// Write the column headers to the CSV file.
fputcsv($output, array_keys($users[0]));

// Loop through the user data and write each row to the CSV file.
foreach ($users as $user) {
    fputcsv($output, $user);
}

// Close the file pointer.
fclose($output);
exit;
