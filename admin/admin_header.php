<?php
require_once '../php/admin_auth_check.php';

if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta id="Viewport" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/mkl_style.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body class="bg-gray-100">
    <div id="toolbar">
        <label class="menu-toggle" for="drawer-toggle">
            <input type="checkbox" id="drawer-toggle" style="display: none;" />
            <div></div>
        </label>
        <div id="toolbar-title"><img src="../images/logo-200.png" alt="Logo" style="margin: 10px;" height="30px" /></div>
        <div id="toolbar-actions">
            <a href="../api/admin_logout.php" class="icon-link" title="Logout" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>
    </div>

    <div id="sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-profile-name">Admin</div>
            <div class="sidebar-profile-email"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
        </div>
        <div id="main-nav" class="sidebar-menu">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $nav_items = [
                ['dashboard.php', 'fa-home', 'Dashboard'],
                ['view_users.php', 'fa-users', 'Users'],
                ['content_dashboard.php', 'fa-pencil-square-o', 'Content'],
                ['states.php', 'fa-map', 'States'],
                ['ethnic_groups.php', 'fa-globe', 'Ethnic Groups'],
            ];
            foreach ($nav_items as $item) {
                $active = ($current_page === $item[0]) ? 'active' : '';
                echo '<a href="' . $item[0] . '" class="menu-item nav-link ' . $active . '"><i class="fa ' . $item[1] . '"></i> ' . $item[2] . '</a>';
            }
            ?>
        </div>
    </div>

    <div id="content">