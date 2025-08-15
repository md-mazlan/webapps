<?php
// File: /webapp/pages/profile_update.php

// 1. SETUP: Include all necessary configuration and class files.
require_once '../php/config.php';
require_once ROOT_PATH . '/php/user_auth_check.php';

// Redirect user if not logged in.
if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Include all required classes.
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/user.php';
require_once ROOT_PATH . '/app/controllers/EthnicGroupController.php';
require_once ROOT_PATH . '/app/controllers/StateController.php';
require_once ROOT_PATH . '/app/controllers/DunSeatController.php';

// 2. INITIALIZATION: Create the database connection and manager objects.
$database = new Database();
$db = $database->connect();

if (!$db) {
    // Display a user-friendly error if the database connection fails.
    echo "A database error occurred. Please try again later.";
    exit;
}

// Initialize User and fetch their profile data.
$user = new User($db);
$user_id = $_SESSION['user_id'];
$profileData = $user->getProfile($user_id);

// Initialize managers for dropdown data.
$ethnicManager = new EthnicGroupController($db);
$stateManager = new StateController($db);
$dunSeatManager = new DunSeatController($db);
$dunSeats = $dunSeatManager->getAll(); // Fetch all DUN seats

// 3. DATA FETCHING: Get the lists for the dropdowns.
$ethnic_groups_by_category = $ethnicManager->getAll();
$states = $stateManager->getAll(); // Use the refactored getAll() method
?>
<div data-script="js/profile_update.js" data-style="css/profile_update.css">
    <main class="page-wrapper">
        <div id="feedback-message" class="message" style="display: none;"></div>
        <div class="profile-grid">
            <!-- Left Column: Profile Picture -->
            <div class="card profile-pic-container">
                <img id="profile-pic-img" src="<?php echo htmlspecialchars($_SESSION['profile_pic_url'] ?? 'https://placehold.co/32x32/e0e7ff/3730a3?text=U'); ?>" alt="Profile Picture" class="profile-pic">
                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($profileData['username']); ?></h3>
                <p style="color: var(--subtle-text-color); margin-top: 0;"><?php echo htmlspecialchars($profileData['skuad_id']); ?></p>
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
                            <input type="text" id="full_name" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="nric">NRIC</label>
                            <input type="text" id="nric" name="nric" class="form-control" value="<?php echo $profileData['nric'] ?>" readonly disabled>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="m" <?php echo (isset($profileData['gender']) && $profileData['gender'] == 'm') ? 'selected' : ''; ?>>Male</option>
                                    <option value="f" <?php echo (isset($profileData['gender']) && $profileData['gender'] == 'f') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ethnic">Ethnic</label>
                                <select id="ethnic" name="ethnic" class="form-control">
                                    <option value="">-- Please select --</option>
                                    <?php
                                    foreach ($ethnic_groups_by_category as $category => $ethnics) {
                                        echo '<optgroup label="' . htmlspecialchars($category) . '">';
                                        foreach ($ethnics as $ethnic_group) {
                                            $selected = (isset($profileData['ethnic']) && $profileData['ethnic'] == $ethnic_group->name) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($ethnic_group->name) . '" ' . $selected . '>' . htmlspecialchars($ethnic_group->name) . '</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" id="birthday" name="birthday" class="form-control" value="<?php echo htmlspecialchars($profileData['birthday'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">No Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($profileData['email'] ?? ''); ?>" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label for="address1">Latest Address</label>
                            <textarea type="text" id="address1" name="address1" class="form-control" style="resize: vertical;"><?php echo htmlspecialchars($profileData['address1'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="voting_area">Voting Area</label>
                            <select id="voting_area" name="voting_area" class="form-control">
                                <option value="">-- Please select --</option>
                                <?php foreach ($dunSeats as $dunSeat): ?>
                                    <option value="<?php echo htmlspecialchars($dunSeat->code); ?>" <?php echo (isset($profileData['voting_area']) && $profileData['voting_area'] == $dunSeat->code) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dunSeat->code . " " . $dunSeat->seat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="service_area">Service Area</label>
                            <select id="service_area" name="service_area" class="form-control">
                                <option value="">-- Please select --</option>
                                <?php foreach ($dunSeats as $dunSeat): ?>
                                    <option value="<?php echo htmlspecialchars($dunSeat->code); ?>" <?php echo (isset($profileData['service_area']) && $profileData['service_area'] == $dunSeat->code) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dunSeat->code . " " . $dunSeat->seat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vest_size">Vest Size</label>
                            <select id="vest_size" name="vest_size" class="form-control">
                                <option value="">-- Please select --</option>
                                <option value="M" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'M') ? 'selected' : ''; ?>>M</option>
                                <option value="L" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'L') ? 'selected' : ''; ?>>L</option>
                                <option value="XL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XL') ? 'selected' : ''; ?>>XL</option>
                                <option value="XXL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XXL') ? 'selected' : ''; ?>>XXL</option>
                                <option value="XXXL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XXXL') ? 'selected' : ''; ?>>XXXL</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Personal Info</button>
                    </form>
                </div>

                <div id="employment" class="tab-content">
                    <form id="employment-details-form">
                        <h2 class="card-title">Employment Details</h2>
                        <div class="form-group">
                            <label for="employment">Employment</label>
                            <select id="employment" name="employment" class="form-control">
                                <option value="">-- Please select --</option>
                                <option value="Public" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Public') ? 'selected' : ''; ?>>Public</option>
                                <option value="Private" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Private') ? 'selected' : ''; ?>>Private</option>
                                <option value="Business" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                                <option value="Unemployment" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Unemployment') ? 'selected' : ''; ?>>Unemployment</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" id="position" name="position" class="form-control" value="<?php echo htmlspecialchars($profileData['position'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="employer_name">Employer Name</label>
                            <input type="text" id="employer_name" name="employer_name" class="form-control" value="<?php echo htmlspecialchars($profileData['employer_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="company_address">Company Address</label>
                            <input type="text" id="company_address" name="company_address" class="form-control" value="<?php echo htmlspecialchars($profileData['company_address'] ?? ''); ?>">
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
                        <h3 style="font-size: 1.1rem; color: var(--danger-color);">Request Account Deletion</h3>
                        <p style="font-size: 0.9rem; color: var(--subtle-text-color);">You may request to delete your account. An admin will review your request. Your account will not be deleted immediately.</p>
                        <button id="delete-account-btn" class="btn btn-danger">Request Account Deletion</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Request Account Deletion Modal -->
    <div id="delete-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">Request Account Deletion</h3>
            <p>Your request to delete your account will be sent to the admin for review. Please provide a reason for your request.</p>
            <form id="delete-account-form">
                <div class="form-group">
                    <label for="delete-reason">Reason for deletion</label>
                    <textarea id="delete-reason" class="form-control" required placeholder="Please provide a reason..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancel-delete-btn" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

</div>