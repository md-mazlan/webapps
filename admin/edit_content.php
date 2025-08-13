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
                    <?php break;
                    case 'event': ?>
                        <div class="form-group">
                            <label for="event_description">Description</label>
                            <textarea name="event_description" id="event_description" rows="5" class="form-control"><?php echo htmlspecialchars($contentData['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Event Date & Time</label>
                            <input type="datetime-local" name="event_date" id="event_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($contentData['event_date'])); ?>">
                        </div>
                    <?php break;
                    case 'gallery': ?>
                        <div class="form-group">
                            <label for="gallery_description">Description</label>
                            <textarea name="gallery_description" id="gallery_description" rows="5" class="form-control"><?php echo htmlspecialchars($contentData['description']); ?></textarea>
                        </div>
                    <?php break;
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
                <?php break;
                endswitch; ?>

                <button type="submit" name="update_content" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>