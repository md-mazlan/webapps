<?php

// Include the new configuration file first.
require_once '../php/config.php';


if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang_code = $_SESSION['lang'] ?? 'en';

require_once ROOT_PATH . "/lang/lang_{$lang_code}.php";

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
<div data-style="css/profile.css">
    <div id="profile-content">
        <div class="container">
            <div class="profile-header">
                <div class="profile-dot"></div>
                <span class="profile-title"><?php echo $lang['profile'] ?></span>
            </div>
            <div class="profile-content">
                <div class="profile-image-section">
                    <div class="profile-image-box">
                        <!-- Placeholder SVG avatar -->
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_pic_url'] ?? 'https://placehold.co/32x32/e0e7ff/3730a3?text=U'); ?>" alt="Profile Picture">
                        <div class="profile-camera" title="Change photo">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="13" r="3" />
                                <path d="M5 7h2l2-3h6l2 3h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                            </svg>
                        </div>
                    </div>
                    <!-- <div class="profile-actions">
                        <div class="profile-action">LOGO</div>
                        <div class="profile-action">VENDOR DOCUMENTS</div>
                    </div> -->
                </div>
                <div class="profile-details">
                    <div>
                        <label>Membership ID</label>
                        <span><?php echo $profileData['skuad_id'] ?? ''; ?></span>
                    </div>
                    <div>
                        <label>User ID / NRIC:</label>
                        <span><?php echo $profileData['nric'] ?? ''; ?></span>
                    </div>
                    <div>
                        <label><?php echo $lang['full_name']; ?>:</label>
                        <span><?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?></span>
                    </div>
                    <div>
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($profileData['email'] ?? ''); ?></span>
                    </div>
                </div>
            </div>
            <div class="profile-content">
                <?php if ($user->isProfileComplete($user_id)) { ?>
                    <div class="profile-details">
                        <h3><?php echo $lang['personal_information']; ?></h3>
                        <div>
                            <label><?php echo $lang['full_name']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['gender']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['gender'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['ethnic']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['ethnic'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['phone_number']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['phone'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['address1']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['address1'] ?? ''); ?></span>
                            <br>
                            <span><?php echo htmlspecialchars($profileData['address2'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['area']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['area'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['postcode']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['postcode'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['city']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['city'] ?? ''); ?></span>
                        </div>
                        <div>
                            <label><?php echo $lang['state']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['state'] ?? ''); ?></span>
                        </div>


                        <h2><?php echo $lang['employment_details']; ?></h2>



                    </div>
                <?php } else { ?>
                    <div class="profile-details">
                        <h3><?php echo $lang['personal_information']; ?> : NOT UPDATED</h3>
                        <div>
                            <label><?php echo $lang['full_name']; ?>:</label>
                            <span><?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div style="margin-left: auto;  margin-right:auto"><button class="edit-profile-btn" onclick="loadSubPage('update.php')"><?php echo strtoupper($lang['update']); ?></button></div>
        </div>
    </div>

</div>