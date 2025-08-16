<?php
// admin/approve_admins.php - Admin Approval Page
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

// Handle activation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
    $adminId = intval($_POST['admin_id']);
    $admin->activateAdmin($adminId);
    header('Location: approve_admins.php');
    exit;
}

$inactiveAdmins = $admin->getInactiveAdmins();
include_once 'admin_header.php';
?>
<div class="dashboard-wrapper">
    <h2>Pending Admin Approvals</h2>
    <?php if (empty($inactiveAdmins)): ?>
        <p>No pending admin accounts.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            <?php foreach ($inactiveAdmins as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" onclick="return confirm('Activate this admin?')">Activate</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php include_once 'admin_footer.php'; ?>
