<?php
// File: /admin/ethnic_groups.php

// 1. SETUP: Initialize session and include necessary files using absolute paths.
session_start();
require_once '../php/config.php';

// Authenticate admin user. You should have a similar file for admin checks.
// require_once ROOT_PATH . '/php/admin_auth_check.php';
// if (!isAdminLoggedIn()) { header('Location: ' . BASE_URL . '/admin/login.php'); exit; }

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/app/models/EthnicGroupManager.php';
require_once ROOT_PATH . '/app/models/EthnicGroup.php';

// 2. INITIALIZATION: Connect to the database and instantiate the manager.
$database = new Database();
$db = $database->connect();

if (!$db) {
    die("Database connection failed. Please check your configuration.");
}

$ethnicManager = new EthnicGroupManager($db);

$message = '';
$error = '';
$editing_ethnic = null;

// 3. HANDLE FORM SUBMISSIONS (CRUD LOGIC)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($action === 'delete' && $id) {
        if ($ethnicManager->delete($id)) {
            $message = "Ethnic group deleted successfully.";
        } else {
            $error = "Error: Could not delete the ethnic group.";
        }
    } elseif ($action === 'create' || $action === 'update') {
        if (empty($name) || empty($category)) {
            $error = "Error: Name and Category fields cannot be empty.";
        } else {
            $ethnicGroup = new EthnicGroup($id, $name, $category);
            $success = ($action === 'update') ? $ethnicManager->update($ethnicGroup) : $ethnicManager->create($ethnicGroup);

            if ($success) {
                $message = "Ethnic group has been " . ($action === 'update' ? 'updated' : 'created') . " successfully.";
            } else {
                $error = "Error: Could not save the ethnic group. It might already exist.";
            }
        }
    }
}

// Handle Edit Request (from GET parameter)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'edit') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $editing_ethnic = $ethnicManager->getById($id);
    }
}

// 4. FETCH ALL DATA FOR DISPLAY
$all_ethnics = $ethnicManager->getAll();

include_once 'admin_header.php';
?>
<div class="dashboard-wrapper">
    <div class="header">
        <h1>Manage Ethnic Groups</h1>
        <!-- <a href="index.php" class="btn btn-secondary">Back to Dashboard</a> -->
    </div>

    <?php if ($message): ?><div class="message success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Add/Edit Form Card -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><?= $editing_ethnic ? 'Edit Ethnic Group' : 'Add New Ethnic Group' ?></span>
        </div>
        <div class="card-body">
            <form action="ethnic_groups.php" method="POST">
                <input type="hidden" name="action" value="<?= $editing_ethnic ? 'update' : 'create' ?>">
                <?php if ($editing_ethnic): ?>
                    <input type="hidden" name="id" value="<?= $editing_ethnic->id ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($editing_ethnic->name ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($editing_ethnic->category ?? '') ?>" placeholder="e.g., Major, Non-Indigenous" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%"><?= $editing_ethnic ? 'Update Group' : 'Add Group' ?></button>
            </form>
        </div>
    </div>

    <!-- List of Existing Ethnic Groups Card -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Existing Ethnic Groups</span>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_ethnics)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No ethnic groups found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_ethnics as $ethnic): ?>
                            <tr>
                                <td><?= $ethnic->id ?></td>
                                <td><?= htmlspecialchars($ethnic->name) ?></td>
                                <td><?= htmlspecialchars($ethnic->category) ?></td>
                                <td>
                                    <a href="ethnic_groups.php?action=edit&id=<?= $ethnic->id ?>" class="btn btn-secondary">Edit</a>
                                    <form action="ethnic_groups.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $ethnic->id ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once 'admin_footer.php'; ?>