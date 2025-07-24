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
require_once '../php/content.php';

// Get the content ID from the URL and validate it.
$content_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$content_id) {
    header('Location: content_dashboard.php'); // Redirect if no ID is provided.
    exit;
}

// Initialize database and content objects.
$database = new Database();
$db = $database->connect();
$content = new Content($db);

$message = '';
$message_type = 'success';

// --- Handle Form Submission for Updating Content ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $content->title = $_POST['title'];

    $details = [];
    // The content type is determined from the database, not the form.
    $existingContent = $content->getById($content_id);
    switch ($existingContent['content_type']) {
        case 'article':
            $details['author'] = $_POST['article_author'];
            $details['body'] = $_POST['article_body'];
            break;
        case 'event':
            $details['description'] = $_POST['event_description'];
            $details['event_date'] = $_POST['event_date'];
            // Note: File upload updates are more complex and are omitted here for simplicity.
            break;
        case 'gallery':
            $details['description'] = $_POST['gallery_description'];
            break;
        case 'video':
            $details['uploaded_src'] = $_POST['video_uploaded_src'];
            $details['uploaded_description'] = $_POST['video_uploaded_description'];
            $details['embedded_src'] = $_POST['video_embedded_src'];
            $details['embedded_description'] = $_POST['video_embedded_description'];
            break;
    }
    $content->details = $details;

    if ($content->update($content_id)) {
        $message = "Content updated successfully!";
        $message_type = 'success';
    } else {
        $message = "Failed to update content.";
        $message_type = 'error';
    }
}

// --- Fetch the Current Content for Display ---
$contentData = $content->getById($content_id);
if (!$contentData) {
    // If content not found, redirect back to the dashboard.
    header('Location: content_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content</title>
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
            --primary-hover-color: #1d4ed8;
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
            max-width: 1000px;
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
            color: #ffffff;
        }

        .card {
            background-color: var(--card-bg-color);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
        }

        .form-group {
            margin-bottom: 1rem;
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
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <header class="header">
            <h1>Edit "<?php echo htmlspecialchars($contentData['title']); ?>"</h1>
            <a href="content_dashboard.php" class="btn btn-secondary">Back to Content Dashboard</a>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_content.php?id=<?php echo $content_id; ?>" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($contentData['title']); ?>" required>
                </div>

                <?php switch ($contentData['content_type']):
                    case 'article': ?>
                        <div class="form-group">
                            <label for="article_author">Author</label>
                            <input type="text" name="article_author" id="article_author" class="form-control" value="<?php echo htmlspecialchars($contentData['author']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="article_body">Body</label>
                            <textarea name="article_body" id="article_body" rows="10" class="form-control"><?php echo htmlspecialchars($contentData['body']); ?></textarea>
                        </div>
                        <?php break; ?>
                    <?php
                    case 'event': ?>
                        <div class="form-group">
                            <label for="event_description">Description</label>
                            <textarea name="event_description" id="event_description" rows="5" class="form-control"><?php echo htmlspecialchars($contentData['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Event Date & Time</label>
                            <input type="datetime-local" name="event_date" id="event_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($contentData['event_date'])); ?>">
                        </div>
                        <?php break; ?>
                    <?php
                    case 'gallery': ?>
                        <div class="form-group">
                            <label for="gallery_description">Description</label>
                            <textarea name="gallery_description" id="gallery_description" rows="5" class="form-control"><?php echo htmlspecialchars($contentData['description']); ?></textarea>
                        </div>
                        <?php break; ?>
                    <?php
                    case 'video': ?>
                        <div class="form-group">
                            <label for="video_embedded_src">Embedded Video URL</label>
                            <input type="text" name="video_embedded_src" id="video_embedded_src" class="form-control" value="<?php echo htmlspecialchars($contentData['embedded_src']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="video_embedded_description">Embedded Video Description</label>
                            <textarea name="video_embedded_description" id="video_embedded_description" rows="2" class="form-control"><?php echo htmlspecialchars($contentData['embedded_description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="video_uploaded_src">Uploaded Video Path</label>
                            <input type="text" name="video_uploaded_src" id="video_uploaded_src" class="form-control" value="<?php echo htmlspecialchars($contentData['uploaded_src']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="video_uploaded_description">Uploaded Video Description</label>
                            <textarea name="video_uploaded_description" id="video_uploaded_description" rows="2" class="form-control"><?php echo htmlspecialchars($contentData['uploaded_description']); ?></textarea>
                        </div>
                        <?php break; ?>
                <?php endswitch; ?>

                <button type="submit" name="update_content" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>