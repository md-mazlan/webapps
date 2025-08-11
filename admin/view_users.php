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

// --- Pagination and Search Logic ---
$database = new Database();
$db = $database->connect();
$admin = new Admin($db);

$search_term = isset($_GET['search']) ? trim($_GET['search']) : null;

$items_per_page = 10; // Display 10 users per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $items_per_page;

// Fetch total count and calculate total pages based on the search term
$total_items = $admin->getTotalUsersCount($search_term);
$total_pages = ceil($total_items / $items_per_page);

// Fetch the users for the current page with the search term
$allUsers = $admin->getAllUsers($items_per_page, $offset, $search_term);

include_once 'admin_header.php';
?>
<style>
</style>
<div class="dashboard-wrapper">
    <header class="header">
        <h1>Registered Public Users</h1>
        <div class="header-actions">
            <button id="export-btn" class="btn btn-primary" style="font-weight:700;padding:0.85rem 2rem;font-size:1.05rem;border-radius:0.6rem;box-shadow:0 4px 16px rgba(37,99,235,0.12);transition:background 0.2s;display:inline-block;">Export to Excel</button>
        </div>
    </header>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <?php
                $start_item = $offset + 1;
                $end_item = min($offset + $items_per_page, $total_items);
                echo "Showing $start_item-$end_item of $total_items users";
                ?>
            </h2>
            <form action="view_users.php" method="GET" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Search by username, email, or NRIC..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                <button type="submit" class="btn btn-primary" style="font-weight:600;padding:0.7rem 1.5rem;font-size:1rem;border-radius:0.5rem;box-shadow:0 2px 8px rgba(37,99,235,0.08);transition:background 0.2s;display:inline-block;">Search</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>NRIC</th>
                    <th>Likes</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allUsers)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allUsers as $user): ?>
                        <tr id="user-row-<?php echo $user['id']; ?>">
                            <td>
                                <img src="<?php echo $user['profile_pic_url'] ? "../" . htmlspecialchars($user['profile_pic_url']) : "https://placehold.co/40x40/e0e7ff/3730a3?text=".substr(htmlspecialchars($user['username']), 0, 1); ?>" alt="Profile Pic" class="profile-avatar">
                            </td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['nric']); ?></td>
                            <td><?php echo $user['like_count']; ?></td>
                            <td><?php echo $user['comment_count']; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary" style="margin-right:6px;padding:0.5rem 0.7rem;border-radius:50%;display:inline-block;" title="Edit">
                                    <i class="fa fa-pencil" style="font-size:1.1rem;"></i>
                                </a>
                                <a href="#" class="btn btn-danger" data-id="<?php echo $user['id']; ?>" style="padding:0.5rem 0.7rem;border-radius:50%;display:inline-block;" title="Delete">
                                    <i class="fa fa-trash" style="font-size:1.1rem;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                $base_url = "view_users.php?";
                if ($search_term) {
                    $base_url .= "search=" . urlencode($search_term) . "&";
                }
                ?>
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" class="page-link <?php if ($i == $current_page) echo 'active'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Export Modal -->
<div id="export-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Select Columns to Export</h3>
            <button id="close-modal-btn" class="close-modal">&times;</button>
        </div>
        <form id="export-form">
            <div class="modal-body">
                <p><strong>Account Details</strong></p>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="id" checked> User ID</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="username" checked> Username</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="email" checked> Email</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="nric" checked> NRIC</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="created_at" checked> Registration Date</label>
                </div>
                <hr style="margin: 1rem 0;">
                <p><strong>Profile Details</strong></p>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="full_name"> Full Name</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="bio"> Bio</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="profile_pic_url"> Profile Picture URL</label>
                </div>
                <hr style="margin: 1rem 0;">
                <p><strong>Employment Details</strong></p>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="company"> Company</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="job_title"> Job Title</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="start_date"> Start Date</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="end_date"> End Date</label>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="columns[]" value="is_current"> Is Current Job?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancel-export-btn" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Download CSV</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportModal = document.getElementById('export-modal');
        const exportBtn = document.getElementById('export-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelExportBtn = document.getElementById('cancel-export-btn');
        const exportForm = document.getElementById('export-form');

        function showModal() {
            exportModal.classList.add('active');
        }

        function hideModal() {
            exportModal.classList.remove('active');
        }

        exportBtn.addEventListener('click', showModal);
        closeModalBtn.addEventListener('click', hideModal);
        cancelExportBtn.addEventListener('click', hideModal);

        exportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedColumns = Array.from(this.querySelectorAll('input[name="columns[]"]:checked')).map(cb => cb.value);
            if (selectedColumns.length === 0) {
                alert('Please select at least one column to export.');
                return;
            }
            const columnsString = selectedColumns.join(',');
            window.location.href = `../api/export_users.php?columns=${encodeURIComponent(columnsString)}`;
            hideModal();
        });

        // --- Delete User Handler ---
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('action-link-delete')) {
                e.preventDefault();
                const userId = e.target.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this user? This will also remove all their likes and comments.')) {
                    deleteUser(userId);
                }
            }
        });

        async function deleteUser(id) {
            try {
                const response = await fetch('../api/user_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete_user',
                        user_id: id
                    })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    const row = document.getElementById('user-row-' + id);
                    if (row) {
                        row.style.transition = 'opacity 0.5s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An unexpected error occurred.');
            }
        }
    });
</script>
<?php include_once 'admin_footer.php'; ?>