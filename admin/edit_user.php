<?php
// Use the centralized admin authentication check.
require_once '../php/admin_auth_check.php';

// If the admin is not logged in, redirect them away.
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Include necessary classes.
require_once '../php/database.php';
require_once '../php/admin.php';

// Get the user ID from the URL and validate it.
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header('Location: view_users.php'); // Redirect if no ID is provided.
    exit;
}

// Initialize objects.
$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

$message = '';
$message_type = 'success';

// --- Handle Form Submission for Updating User ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $dataToUpdate = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'nric' => $_POST['nric']
    ];

    if ($admin->updateUser($user_id, $dataToUpdate)) {
        $message = "User updated successfully!";
        $message_type = 'success';
    } else {
        $message = "Failed to update user. The email or NRIC may already be in use.";
        $message_type = 'error';
    }
}

// --- Fetch the Current User Data for Display ---
$userData = $admin->getUserById($user_id);
if (!$userData) {
    // If user not found, redirect back to the user list.
    header('Location: view_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f3f4f6;
            --card-bg-color: #ffffff;
            --text-color: #1f2937;
            --subtle-text-color: #6b7280;
            --border-color: #e5e7eb;
            --primary-color: #2563eb;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --font-family: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-color);
            margin: 0;
            padding: 2rem;
        }

        .dashboard-wrapper {
            max-width: 800px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.25rem;
            color: var(--text-color);
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .card {
            background-color: var(--card-bg-color);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--subtle-text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <header class="header">
            <h1>Edit User: <?php echo htmlspecialchars($userData['username']); ?></h1>
            <a href="view_users.php" class="btn btn-secondary">&larr; Back to User List</a>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nric">NRIC</label>
                    <input type="text" name="nric" id="nric" class="form-control" value="<?php echo htmlspecialchars($userData['nric']); ?>">
                </div>
                <p style="font-size: 0.875rem; color: var(--subtle-text-color);">Note: Password cannot be changed from this panel.</p>
                <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>