<?php

// Include the new configuration file first.
require_once 'php/config.php';


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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <meta id="Viewport" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/mkl_style.css">
    <style>
        /* Simple transition for content fade-in */
        #content>* {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Loading overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>
    <div id="toolbar">
        <label class="menu-toggle" for="drawer-toggle">
            <input type="checkbox" id="drawer-toggle" style="display: none;" />
            <div></div>
        </label>
        <div id="toolbar-title"><img src="images/logo-200.png" alt="Logo" style="margin: 10px;" height="30px" /></div>
        <div id="toolbar-actions" style="min-width: 45px; display: flex; justify-content: space-between; align-items: center;">
            <a href="?lang=en" class="icon-link" title="English" style="color:#FFF; text-decoration: none;">
                EN
            </a>
            <span class="toolbar-separator">|</span>
            <a href="?lang=my" class="icon-link" title="Malay" style="color:#FFF; text-decoration: none;">
                MY
            </a>
            <a href="javascript:void(0);" class="icon-link" title="Notifications">
                <svg fill="none" stroke="#b0b0b0" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 22a2 2 0 1 1-4 0h4zm6-6V9a6 6 0 1 0-12 0v7l-2 2v1h16v-1l-2-2z" />
                </svg>
            </a>
        </div>
    </div>

    <div id="sidebar">
        <div class="sidebar-profile">
            <img src="<?php echo htmlspecialchars($_SESSION['profile_pic_url'] ?? 'https://placehold.co/32x32/e0e7ff/3730a3?text=U'); ?>" alt="Profile Picture" class="sidebar-profile-img" />
            <div class="sidebar-profile-name"><?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?></div>
            <div class="sidebar-profile-email"><?php echo htmlspecialchars("SKUAD".($profileData['id'] ?? '00')); ?></div>
        </div>
        <div id="main-nav" class="sidebar-menu">
            <a href="dashboard" class="menu-item nav-link"> <i class="fa fa-home"></i> <?php echo $lang['dashboard']; ?></a>
            <!-- <div href="profile" class="menu-item nav-link">  <i class="fa fa-user"></i></i> Profile</div> -->
            <label class="dropdown-toggle" for="admin-dropdown-toggle" style="padding: 0; margin: 0;">
                <input type="checkbox" class='dropdown-checkbox' id="admin-dropdown-toggle" style="display: none;" />
                <div class="dropdown-btn">
                    <i class="fa fa-user"></i> <?php echo $lang["profile"] ?> <i class="fa fa-caret-down"></i>
                </div>
                <div class="dropdown-container">
                    <a href="profile_view" class="menu-item nav-link" data-section="am-users"><?php echo $lang['view'] ?></a>
                    <a href="profile_update" class="menu-item nav-link" data-section="am-roles"><?php echo $lang['update'] ?></a>
                </div>
            </label>
            <a href="payment_history" class="menu-item nav-link"> <i class="fa fa-inbox"></i> Inbox</a>
            <a href="payment_history" class="menu-item nav-link"> <i class="fa fa-money"></i> <?php echo $lang['payment_history'] ?></a>
        </div>

        <div style="bottom: 0; position: absolute; width: 100%; text-align: center; padding: 10px; font-size: 0.9em; color: #666;">
            <div class="sidebar-menu">
                <a href="php/logout.php" class="menu-item"> <i class="fa fa-sign-out"></i> <?php echo $lang['signout'] ?></a>
                <!-- <div href="profile" class="menu-item nav-link">  <i class="fa fa-user"></i></i> Profile</div> -->

            </div>
            <div class="sidebar-toggle-row">
                <label for="darkModeToggle"><i class="fa fa-moon-o"></i> <?php echo $lang['dark_mode'] ?></label>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="slider"></span>
                </label>
            </div>

            <footer>
                <p>&copy; 2025 Skuad Anak Sabah</p>
            </footer>
        </div>

    </div>

    <div id="content" class="bg-white p-6 md:p-8 rounded-lg shadow-md min-h-[300px]">
        <!-- Dynamic content will be loaded here -->
    </div>


    <!-- This container is where page-specific scripts will be loaded and unloaded -->
    <div id="page-script-container"></div>

    <!-- The core script that handles all the logic -->
    <div id="overlay"></div>
    <script src="js/main.js"></script>
    <script src="js/mkl_script.js"></script>
    <script>
        // Simple dark mode toggle
        const toggle = document.getElementById('darkModeToggle');
        if (toggle) {
            // Load mode from localStorage
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                toggle.checked = true;
            }
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('darkMode', 'true');
                } else {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('darkMode', 'false');
                }
            });
        }
    </script>

</body>

</html>