<?php
// Use the centralized admin authentication check.
require_once '../php/admin_auth_check.php';

// If the admin is not logged in, redirect them away.
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Include necessary classes for database and admin management.
require_once '../php/database.php';
require_once '../php/admin.php';

// Initialize objects and fetch data.
$database = new Database();
$db = $database->connect();
$admin = new Admin($db);
$admin->id = $_SESSION['admin_id']; // Set admin ID from the active session.

// Fetch active sessions.
$currentToken = $_COOKIE['admin_remember_token'] ?? '';
$active_sessions = $admin->getActiveSessions($currentToken);

// Fetch site statistics.
$total_users = $admin->getTotalUsersCount();
$total_content = $admin->getTotalContentCount();
$total_likes = $admin->getTotalLikesCount();
$total_comments = $admin->getTotalCommentsCount();

// Dynamic contextual information.
$currentTime = "Thursday, July 24, 2025 at 9:43 AM";
$location = "Kota Kinabalu, Sabah, Malaysia";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            --primary-hover-color: #1d4ed8;
            --danger-color: #dc2626;
            --danger-hover-color: #b91c1c;
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
            max-width: 900px;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: #ffffff;
        }

        .btn-danger:hover {
            background-color: var(--danger-hover-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }

        .card {
            background-color: var(--card-bg-color);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .info-item p {
            margin: 0;
            color: var(--subtle-text-color);
            font-size: 0.875rem;
        }

        .info-item span,
        .info-item a {
            display: block;
            color: var(--text-color);
            font-weight: 500;
            font-size: 1rem;
            text-decoration: none;
        }

        .info-item a:hover {
            color: var(--primary-color);
        }

        .session-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .session-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .session-item:last-child {
            border-bottom: none;
        }

        .session-icon {
            margin-right: 1rem;
            color: var(--primary-color);
        }

        .session-details p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--subtle-text-color);
        }

        .session-details span {
            font-weight: 500;
            color: var(--text-color);
        }

        .current-session-tag {
            font-size: 0.75rem;
            font-weight: 600;
            color: #059669;
            background-color: #d1fae5;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <header class="header">
            <h1>Admin Dashboard</h1>
            <a href="../api/admin_logout.php" class="btn btn-danger">Logout</a>
        </header>

        <div class="card">
            <h2 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
            <div class="info-grid">
                <div class="info-item">
                    <p>Current Time</p>
                    <span><?php echo htmlspecialchars($currentTime); ?></span>
                </div>
                <div class="info-item">
                    <p>Your Location</p>
                    <span><?php echo htmlspecialchars($location); ?></span>
                </div>
                <div class="info-item">
                    <p>Content Management</p>
                    <a href="content_dashboard.php">Manage Content &rarr;</a>
                </div>
                <div class="info-item">
                    <p>User Management</p>
                    <a href="view_users.php">View All Users &rarr;</a>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Site Statistics</h2>
            <div class="info-grid">
                <div class="info-item">
                    <p>Total Public Users</p>
                    <span><?php echo $total_users; ?></span>
                </div>
                <div class="info-item">
                    <p>Total Content Posts</p>
                    <span><?php echo $total_content; ?></span>
                </div>
                <div class="info-item">
                    <p>Total Likes</p>
                    <span><?php echo $total_likes; ?></span>
                </div>
                <div class="info-item">
                    <p>Total Comments</p>
                    <span><?php echo $total_comments; ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Active Admin Sessions</h2>
            <div class="session-list">
                <?php if (empty($active_sessions)): ?>
                    <p>No active "Remember Me" sessions found.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($active_sessions as $session): ?>
                            <li class="session-item">
                                <div class="session-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                    </svg>
                                </div>
                                <div class="session-details">
                                    <span>
                                        <?php echo htmlspecialchars($session['device_info']); ?>
                                        <?php if ($session['is_current_session']): ?>
                                            <span class="current-session-tag">This Device</span>
                                        <?php endif; ?>
                                    </span>
                                    <p>
                                        IP: <?php echo htmlspecialchars($session['ip_address']); ?> |
                                        Last used: <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($session['last_used_at']))); ?>
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>