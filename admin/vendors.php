<?php
// admin/vendors.php
require_once '../php/admin_auth_check.php';
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}
require_once '../php/config.php';
require_once ROOT_PATH . '/app/controllers/VendorController.php';
$vendorController = new VendorController();
$vendors = $vendorController->getAll();
$message = '';
$message_type = 'success';
// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_vendor'])) {
    $data = [
        'name' => $_POST['name'],
        'location' => $_POST['location'],
        'discount' => $_POST['discount'],
        'offering' => $_POST['offering']
    ];
    if ($vendorController->create($data)) {
        $message = 'Vendor created successfully!';
        $vendors = $vendorController->getAll();
    } else {
        $message = 'Failed to create vendor.';
        $message_type = 'error';
    }
}
// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($vendorController->delete($id)) {
        $message = 'Vendor deleted.';
        $vendors = $vendorController->getAll();
    } else {
        $message = 'Failed to delete vendor.';
        $message_type = 'error';
    }
}
include_once 'admin_header.php';
?>
<div class="dashboard-wrapper">
    <h2>Vendor Management</h2>
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div class="card" style="margin-bottom:2rem;">
        <h3>Add New Vendor</h3>
        <form method="POST" action="vendors.php">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="form-group">
                <label>Discount</label>
                <textarea name="discount" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Offering</label>
                <textarea name="offering" class="form-control"></textarea>
            </div>
            <button type="submit" name="create_vendor" class="btn btn-primary">Add Vendor</button>
        </form>
    </div>
    <div class="card">
        <h3>All Vendors</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Discount</th>
                    <th>Offering</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vendor['name']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['location']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['discount']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['offering']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['created_at']); ?></td>
                        <td>
                            <a href="vendors.php?delete=<?php echo $vendor['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this vendor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once 'admin_footer.php'; ?>
