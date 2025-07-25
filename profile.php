<?php
// Include the new configuration file first.
require_once 'php/config.php';

// Use the centralized user authentication check with an absolute path.
require_once ROOT_PATH . '/php/user_auth_check.php';

// If a user is not logged in, redirect them to the login page.
if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Include necessary classes with absolute paths.
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/user.php';

// Initialize objects and fetch the user's profile data.
$database = new Database();
$db = $database->connect();
$user = new User($db);
$user_id = $_SESSION['user_id'];
$profileData = $user->getProfile($user_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9fafb;
            --text-color: #111827;
            --subtle-text-color: #6b7280;
            --border-color: #e5e7eb;
            --card-bg-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --primary-color: #2563eb;
            --danger-color: #dc2626;
            --danger-hover-color: #b91c1c;
            --font-sans: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
        }

        .navbar {
            background-color: var(--card-bg-color);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px var(--shadow-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            align-items: flex-start;
        }

        .card {
            background-color: var(--card-bg-color);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-top: 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .profile-pic-container {
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
            margin-bottom: 1rem;
        }

        .profile-pic-container .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn:disabled,
        .btn.loading {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        .tab-link {
            background: none;
            border: none;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            color: var(--subtle-text-color);
            border-bottom: 3px solid transparent;
        }

        .tab-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {

            .profile-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 1rem;
            }

            .page-wrapper {
                margin-top: 1rem;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab-link {
                flex-grow: 1;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>/index.php">My Website</a>
        <div class="nav-links">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="<?php echo BASE_URL; ?>/profile.php">My Profile</a>
            <a href="<?php echo BASE_URL; ?>/api/user_logout.php">Logout</a>
        </div>
    </nav>

    <main class="page-wrapper">
        <div id="feedback-message" class="message" style="display: none;"></div>
        <div class="profile-grid">
            <!-- Left Column: Profile Picture -->
            <div class="card profile-pic-container">
                <img id="profile-pic-img" src="<?php echo empty($profileData['profile_pic_url']) ?  'https://placehold.co/150x150/e0e7ff/3730a3?text=User' : BASE_URL . '/' . htmlspecialchars($profileData['profile_pic_url']); ?>" alt="Profile Picture" class="profile-pic">
                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($profileData['username']); ?></h3>
                <p style="color: var(--subtle-text-color); margin-top: 0;"><?php echo htmlspecialchars($profileData['email']); ?></p>
                <form id="profile-pic-form">
                    <input type="file" name="profile_pic" id="profile-pic-input" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-secondary" id="change-pic-btn" onclick="document.getElementById('profile-pic-input').click();">Change Picture</button>
                </form>
                <p style="font-size: 0.8rem; color: #999; margin-top: 1rem;">
                    Last updated: <?php echo date('F j, Y', strtotime($profileData['updated_at'])); ?>
                </p>
            </div>

            <!-- Right Column: Profile Details -->
            <div class="card">
                <div class="tabs">
                    <button class="tab-link active" data-tab="personal">Personal</button>
                    <button class="tab-link" data-tab="employment">Employment</button>
                    <button class="tab-link" data-tab="security">Security</button>
                </div>

                <div id="personal" class="tab-content active">
                    <form id="personal-details-form">
                        <h2 class="card-title">Personal Information</h2>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?>">
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="m" <?php echo ($profileData['gender'] ?? '') === 'm' ? 'selected' : ''; ?>>Male</option>
                                    <option value="f" <?php echo ($profileData['gender'] ?? '') === 'f' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ethnic">Ethnic</label>
                                <input type="text" id="ethnic" name="ethnic" class="form-control" value="<?php echo htmlspecialchars($profileData['ethnic'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" id="birthday" name="birthday" class="form-control" value="<?php echo htmlspecialchars($profileData['birthday'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address1">Address 1</label>
                            <input type="text" id="address1" name="address1" class="form-control" value="<?php echo htmlspecialchars($profileData['address1'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="address2">Address 2</label>
                            <input type="text" id="address2" name="address2" class="form-control" value="<?php echo htmlspecialchars($profileData['address2'] ?? ''); ?>">
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" id="area" name="area" class="form-control" value="<?php echo htmlspecialchars($profileData['area'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($profileData['postal_code'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($profileData['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($profileData['state'] ?? ''); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Personal Info</button>
                    </form>
                </div>

                <div id="employment" class="tab-content">
                    <form id="employment-details-form">
                        <h2 class="card-title">Employment Details</h2>
                        <div class="form-group">
                            <label for="company">Company</label>
                            <input type="text" id="company" name="company" class="form-control" value="<?php echo htmlspecialchars($profileData['company'] ?? ''); ?>">
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="job_title">Job Title</label>
                                <input type="text" id="job_title" name="job_title" class="form-control" value="<?php echo htmlspecialchars($profileData['job_title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" id="department" name="department" class="form-control" value="<?php echo htmlspecialchars($profileData['department'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($profileData['start_date'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" id="is_current" name="is_current" <?php echo ($profileData['is_current'] ?? 0) ? 'checked' : ''; ?>> I currently work here</label>
                        </div>
                        <div class="form-group" id="end-date-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($profileData['end_date'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="responsibilities">Responsibilities</label>
                            <textarea id="responsibilities" name="responsibilities" rows="4" class="form-control"><?php echo htmlspecialchars($profileData['responsibilities'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Employment Info</button>
                    </form>
                </div>

                <div id="security" class="tab-content">
                    <h2 class="card-title">Security Settings</h2>
                    <form id="password-change-form">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>

                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h3 style="font-size: 1.1rem; color: var(--danger-color);">Delete Account</h3>
                        <p style="font-size: 0.9rem; color: var(--subtle-text-color);">Once you delete your account, there is no going back. Please be certain.</p>
                        <button id="delete-account-btn" class="btn btn-danger">Delete My Account</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Account Modal -->
    <div id="delete-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">Confirm Account Deletion</h3>
            <p>Please enter your password to confirm you want to permanently delete your account.</p>
            <form id="delete-account-form">
                <div class="form-group">
                    <label for="delete-password">Password</label>
                    <input type="password" id="delete-password" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancel-delete-btn" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Element Selections ---
            const personalDetailsForm = document.getElementById('personal-details-form');
            const employmentDetailsForm = document.getElementById('employment-details-form');
            const profilePicInput = document.getElementById('profile-pic-input');
            const changePicButton = document.getElementById('change-pic-btn');
            const feedbackMessage = document.getElementById('feedback-message');
            const isCurrentCheckbox = document.getElementById('is_current');
            const endDateGroup = document.getElementById('end-date-group');
            const passwordChangeForm = document.getElementById('password-change-form');
            const deleteAccountBtn = document.getElementById('delete-account-btn');
            const deleteModal = document.getElementById('delete-modal');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
            const deleteAccountForm = document.getElementById('delete-account-form');

            const tabs = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            const API_ENDPOINT = '<?php echo BASE_URL; ?>/api/profile_actions.php';
            const USER_API_ENDPOINT = '<?php echo BASE_URL; ?>/api/user_actions.php';

            // --- Tab Handling ---
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(item => item.classList.remove('active'));
                    tab.classList.add('active');

                    const target = document.getElementById(tab.dataset.tab);
                    tabContents.forEach(content => content.classList.remove('active'));
                    target.classList.add('active');
                });
            });

            function showMessage(message, type) {
                feedbackMessage.textContent = message;
                feedbackMessage.className = `message ${type}`;
                feedbackMessage.style.display = 'block';
                window.scrollTo(0, 0);
                setTimeout(() => {
                    feedbackMessage.style.display = 'none';
                }, 5000);
            }

            // --- Handle Personal Info Update ---
            personalDetailsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Saving...';

                const formData = new FormData(this);
                formData.append('action', 'update_personal_info');

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });

            // --- Handle Employment Info Update ---
            employmentDetailsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Saving...';

                const formData = new FormData(this);
                formData.append('action', 'update_employment_info');

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });

            // --- Handle Profile Picture Update ---
            profilePicInput.addEventListener('change', async function() {
                if (this.files.length === 0) return;

                changePicButton.disabled = true;
                changePicButton.textContent = 'Uploading...';

                const formData = new FormData();
                formData.append('action', 'update_profile_picture');
                formData.append('profile_pic', this.files[0]);

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success' && result.new_pic_url) {
                        const newUrl = '<?php echo BASE_URL; ?>/' + result.new_pic_url + '?' + new Date().getTime();
                        document.getElementById('profile-pic-img').src = newUrl;
                    }
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    changePicButton.disabled = false;
                    changePicButton.textContent = 'Change Picture';
                }
            });

            // --- Handle Password Change ---
            passwordChangeForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    showMessage('New passwords do not match.', 'error');
                    return;
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Updating...';

                const formData = new FormData(this);
                formData.append('action', 'change_password');

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        this.reset();
                    }
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });

            // --- Delete Account Modal Handling ---
            deleteAccountBtn.addEventListener('click', () => {
                deleteModal.style.display = 'flex';
            });
            cancelDeleteBtn.addEventListener('click', () => {
                deleteModal.style.display = 'none';
            });
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal) {
                    deleteModal.style.display = 'none';
                }
            });

            deleteAccountForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const password = document.getElementById('delete-password').value;
                const deleteBtn = this.querySelector('button[type="submit"]');
                deleteBtn.disabled = true;
                deleteBtn.textContent = 'Deleting...';

                try {
                    const response = await fetch(USER_API_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'delete_account',
                            password: password
                        })
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        alert('Account deleted successfully. You will now be logged out.');
                        window.location.href = '<?php echo BASE_URL; ?>/index.php';
                    } else {
                        showMessage(result.message, 'error');
                    }
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Delete Account';
                }
            });

            // --- Toggle End Date Field ---
            function toggleEndDate() {
                endDateGroup.style.display = isCurrentCheckbox.checked ? 'none' : 'block';
            }
            isCurrentCheckbox.addEventListener('change', toggleEndDate);
            toggleEndDate();
        });
    </script>
</body>

</html>