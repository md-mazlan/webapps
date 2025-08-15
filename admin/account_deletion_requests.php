<?php
// Admin page to view and manage account deletion requests
require_once '../php/admin_auth_check.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}
require_once '../php/database.php';
require_once '../php/admin.php';

$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

// Fetch all pending account deletion requests
$stmt = $db->prepare('SELECT r.*, u.username, u.email FROM account_deletion_requests r JOIN users u ON r.user_id = u.id ORDER BY r.requested_at DESC');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once 'admin_header.php';
?>
<div class="dashboard-wrapper">
    <header class="header">
        <h1>Account Deletion Requests</h1>
    </header>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Reason</th>
                    <th>Requested At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?php echo htmlspecialchars($req['username']); ?></td>
                    <td><?php echo htmlspecialchars($req['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($req['reason'])); ?></td>
                    <td><?php echo htmlspecialchars($req['requested_at']); ?></td>
                    <td><?php echo $req['reviewed'] ? ucfirst($req['decision']) : 'Pending'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once 'admin_footer.php'; ?>
