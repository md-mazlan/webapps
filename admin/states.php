<?php
// File: /admin/states.php

// 1. SETUP: Initialize session and include necessary files using absolute paths.
session_start();
require_once '../php/config.php';

// It's highly recommended to have an admin-specific authentication check.
// require_once ROOT_PATH . '/php/admin_auth_check.php';
// if (!isAdminLoggedIn()) { header('Location: ' . BASE_URL . '/admin/login.php'); exit; }

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/app/models/StateManager.php';
require_once ROOT_PATH . '/app/models/State.php';

// 2. INITIALIZATION: Connect to the database and instantiate the manager.
$database = new Database();
$db = $database->connect();

if (!$db) {
    die("Database connection failed. Please check your configuration.");
}

$stateManager = new StateManager($db);

$message = '';
$error = '';
$editing_state = null;

// 3. HANDLE FORM SUBMISSIONS (CRUD LOGIC)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = trim($_POST['name'] ?? '');

    if ($action === 'delete' && $id) {
        if ($stateManager->delete($id)) {
            $message = "State deleted successfully.";
        } else {
            $error = "Error: Could not delete the state.";
        }
    } elseif ($action === 'create' || $action === 'update') {
        if (empty($name)) {
            $error = "Error: State name cannot be empty.";
        } else {
            $state = new State($id, $name);
            $success = ($action === 'update') ? $stateManager->update($state) : $stateManager->create($state);

            if ($success) {
                $message = "State has been " . ($action === 'update' ? 'updated' : 'created') . " successfully.";
            } else {
                $error = "Error: Could not save the state. It might already exist.";
            }
        }
    }
}

// Handle Edit Request (from GET parameter)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'edit') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $editing_state = $stateManager->getById($id);
    }
}

// 4. FETCH ALL DATA FOR DISPLAY
$all_states = $stateManager->getAll();

include_once 'admin_header.php';
?>
<div class="dashboard-wrapper">
    <div class="header">
        <h1>Manage States</h1>
        <!-- <a href="index.php" class="btn btn-secondary">Back to Dashboard</a> -->
    </div>

    <?php if ($message): ?><div class="message success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Add/Edit Form Card -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><?= $editing_state ? 'Edit State' : 'Add New State' ?></span>
        </div>
        <div class="card-body">
            <form action="states.php" method="POST">
                <input type="hidden" name="action" value="<?= $editing_state ? 'update' : 'create' ?>">
                <?php if ($editing_state): ?><input type="hidden" name="id" value="<?= $editing_state->id ?>"><?php endif; ?>
                <div class="form-group">
                    <label for="name">State Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($editing_state->name ?? '') ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%"><?= $editing_state ? 'Update State' : 'Add State' ?></button>
            </form>
        </div>
    </div>

    <!-- List of Existing States Card -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Existing States</span>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_states)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center;">No states found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_states as $state): ?>
                            <tr>
                                <td><?= $state->id ?></td>
                                <td><?= htmlspecialchars($state->name) ?></td>
                                <td>
                                    <a href="states.php?action=edit&id=<?= $state->id ?>" class="btn btn-secondary">Edit</a>
                                    <form action="states.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $state->id ?>">
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