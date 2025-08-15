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
include_once 'admin_header.php';
?>

<style>
</style>
<div class="dashboard-wrapper">
    <header class="header">
        <h1>Admin Dashboard</h1>
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
            <div class="info-item">
                <p>Account Deletion Requests</p>
                <a href="account_deletion_requests.php">View Requests &rarr;</a>
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
<?php include_once 'admin_footer.php'; ?>