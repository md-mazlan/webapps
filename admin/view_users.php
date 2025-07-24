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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users - Admin Dashboard</title>
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
            --danger-color: #dc2626;
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
            max-width: 1200px;
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

        .header-actions {
            display: flex;
            gap: 1rem;
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
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
        }

        .search-form {
            display: flex;
            gap: 0.5rem;
        }

        .search-input {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        th {
            color: var(--subtle-text-color);
            font-weight: 500;
        }

        td {
            color: var(--text-color);
        }

        .action-link-delete {
            color: var(--danger-color);
            cursor: pointer;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 400px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close-modal {
            cursor: pointer;
            font-size: 1.5rem;
            background: none;
            border: none;
        }

        .modal-body .form-group {
            margin-bottom: 1rem;
        }

        .modal-body label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .modal-footer {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .page-link {
            text-decoration: none;
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: #dbeafe;
            border-color: var(--primary-color);
        }

        .page-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            cursor: default;
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <header class="header">
            <h1>Registered Public Users</h1>
            <div class="header-actions">
                <button id="export-btn" class="btn btn-primary">Export to Excel</button>
                <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
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
                    <button type="submit" class="btn btn-primary">Search</button>
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
                                    <img src="../<?php echo htmlspecialchars($user['profile_pic_url'] ?? 'https://placehold.co/40x40/e0e7ff/3730a3?text=U'); ?>" alt="Profile Pic" class="profile-avatar">
                                </td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['nric']); ?></td>
                                <td><?php echo $user['like_count']; ?></td>
                                <td><?php echo $user['comment_count']; ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                                    <a href="#" class="action-link-delete" data-id="<?php echo $user['id']; ?>">Delete</a>
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
</body>

</html>