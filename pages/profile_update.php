<?php
// Include the new configuration file first.
require_once '../php/config.php';

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
<div data-script="js/profile_update.js" data-style="css/profile_update.css">
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
                                <select id="ethnic" name="ethnic" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="m" <?php echo ($profileData['ethnic'] ?? '') === 'm' ? 'selected' : ''; ?>>Male</option>
                                    <option value="f" <?php echo ($profileData['ethnic'] ?? '') === 'f' ? 'selected' : ''; ?>>Female</option>
                                </select>
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

</div>