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

require_once ROOT_PATH . '/app/controllers/InboxController.php';
?>
<style>
    /* Base Color Variables (Light Mode) */
    :root {
        --primary-color: #007BFF;
        --primary-dark: #0056b3;
        --secondary-color: #6c757d;
        --background-color: #f8f9fa;
        --border-color: #dee2e6;
        --text-color: #343a40;
        --unread-bg: #e2f2ff;
        --element-bg: #fff;
    }

    /* Dark Mode Styles */
    body.dark-mode {
        --primary-color: #4CAF50;
        --primary-dark: #388E3C;
        --secondary-color: #adb5bd;
        --background-color: #39394b;
        --border-color: #495057;
        --text-color: #f8f9fa;
        --unread-bg: #2f2f3d;
        --element-bg: #23243a !important;
    }

    /* Basic Reset and Box-Sizing */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .inbox-container {
        max-width: 700px;
        display: flex;
        margin-left: auto;
        margin-right: auto;
        background: var(--element-bg);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* ----- Sidebar Styles ----- */
    .inbox-sidebar {
        width: 250px;
        background-color: var(--element-bg);
        border-right: 1px solid var(--border-color);
        padding: 20px;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .sidebar-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .new-message-btn {
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 50px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .new-message-btn:hover {
        background-color: var(--primary-dark);
    }

    .sidebar-nav ul {
        list-style: none;
    }

    .sidebar-nav li a {
        display: block;
        padding: 12px 15px;
        margin-bottom: 5px;
        border-radius: 5px;
        text-decoration: none;
        color: var(--text-color);
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sidebar-nav li a.active,
    .sidebar-nav li a:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    .sidebar-nav li a span {
        margin-right: 10px;
    }

    /* ----- Main Content Styles ----- */
    .inbox-main-content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
    }

    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
    }

    .main-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .search-bar input {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        width: 250px;
        font-size: 0.9rem;
        background-color: var(--element-bg);
        color: var(--text-color);
    }

    /* ----- Message List Styles ----- */
    .message-list {
        list-style: none;
    }

    .message-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .message-item:hover {
        background-color: var(--background-color);
    }

    .message-item.unread {
        background-color: var(--unread-bg);
        border-left: 4px solid var(--primary-color);
        padding-left: 11px;
    }

    .message-item.unread:hover {
        background-color: var(--unread-bg);
    }

    .message-content {
        flex-grow: 1;
        flex: 1;
        overflow-wrap: break-word;
    }

    .message-header {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .sender-name {
        font-weight: 600;
        margin-right: 10px;
    }

    .message-subject {
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-color);
    }

    .message-excerpt {
        color: var(--secondary-color);
        font-size: 0.9rem;
    }

    .message-date {
        font-size: 0.8rem;
        color: var(--secondary-color);
        flex-shrink: 0;
        margin-left: 15px;
    }

    /* Dark Mode Toggle Button */
    .toggle-btn-container {
        text-align: right;
        margin-bottom: 20px;
    }

    #dark-mode-toggle {
        padding: 8px 12px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
        background-color: var(--element-bg);
        color: var(--text-color);
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
    }

    #dark-mode-toggle:hover {
        background-color: var(--background-color);
    }


    /* ----- Responsive Design (Media Queries) ----- */
    @media (max-width: 768px) {
        .inbox-container {
            flex-direction: column;
            box-shadow: none;
            border-radius: 0;
        }

        .inbox-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
        }

        .inbox-main-content {
            padding: 15px;
        }

        .main-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .main-header h1 {
            margin-bottom: 10px;
        }

        .search-bar {
            width: 100%;
        }

        .search-bar input {
            width: 100%;
        }

        .message-item {
            flex-direction: column;
            align-items: flex-start;
            padding: 12px;
        }

        .message-header {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .sender-name {
            margin-right: 0;
            margin-bottom: 3px;
        }

        .message-date {
            margin-left: 0;
            margin-top: 5px;
        }
    }
</style>
<div class="inbox-container">
    <!-- <aside class="inbox-sidebar">
        <div class="sidebar-header">
            <h2>Inbox</h2>
            <button class="new-message-btn">+ Compose</button>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" class="active"><span>&#9993;</span> Inbox</a></li>
                <li><a href="#"><span>&#9998;</span> Drafts</a></li>
                <li><a href="#"><span>&#10003;</span> Sent</a></li>
                <li><a href="#"><span>&#128465;</span> Trash</a></li>
            </ul>
        </nav>
    </aside> -->

    <main class="inbox-main-content">
        <header class="main-header">
            <h1>Inbox</h1>
            <!-- <div class="search-bar">
                <input type="text" placeholder="Search messages...">
            </div> -->
        </header>

        <?php
        if (!isUserLoggedIn()) {
        ?>
            <ul class="message-list">
                <li class="message-item">
                    <div class="message-content">Please log in to view your inbox.</div>
                </li>
            </ul>
        <?php
        } else {
            $user_id = $_SESSION['user_id'];
            $inboxController = new InboxController();
            $messages = $inboxController->getUserMessages($user_id);
        ?>
            <ul class="message-list">
                <?php if (!$messages): ?>
                    <li class="message-item">
                        <div class="message-content">No messages found.</div>
                    </li>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php $unread = $msg['is_read'] ? '' : 'unread'; ?>
                        <?php $date = date('M d, Y H:i', strtotime($msg['created_at'])); ?>
                        <li class="message-item <?php echo $unread; ?>">
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="sender-name"><?php echo htmlspecialchars($msg['sender']); ?></span>
                                    <span class="message-subject"><?php echo htmlspecialchars($msg['subject']); ?></span>
                                </div>
                                <div class="message-excerpt"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                            </div>
                            <span class="message-date"><?php echo $date; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        <?php } ?>
    </main>
</div>