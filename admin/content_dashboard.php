<?php
// Use the centralized admin authentication check from the 'php' folder.
require_once '../php/admin_auth_check.php';

// If the admin is not logged in, redirect them away.
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Include necessary classes for database and content management from the 'php' folder.
require_once '../php/database.php';
require_once '../php/content.php';

// Initialize database and content objects.
$database = new Database();
$db = $database->connect();
$content = new Content($db);

$message = ''; // To store feedback for the user.
$message_type = 'success'; // To control message styling (success or error)

// --- Handle Form Submission for Creating New Content ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_content'])) {
    // Populate the content object from the form data.
    $content->content_type = $_POST['content_type'];
    $content->title = $_POST['title'];

    // Collect type-specific details into the $details array.
    $details = [];
    switch ($content->content_type) {
        case 'article':
            $details['author'] = $_POST['article_author'];
            $details['body'] = $_POST['article_body'];
            break;
        case 'event':
            $details['description'] = $_POST['event_description'];
            $details['event_date'] = $_POST['event_date'];
            if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] === UPLOAD_ERR_OK) {
                $details['banner_file'] = $_FILES['event_banner'];
            }
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

    // Attempt to create the content.
    if ($content->create()) {
        $message = "Content created successfully!";
        $message_type = 'success';
    } else {
        $message = "Failed to create content. Please check your input and try again.";
        $message_type = 'error';
    }
}

// --- Fetch All Existing Content for Display ---
$allContent = $content->getAll();

include_once 'admin_header.php';
?>
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
        --danger-hover-color: #b91c1c;
        --success-color: #16a34a;
        --error-color: #f87171;
        --shadow-color: rgba(0, 0, 0, 0.05);
        --font-family: 'Inter', sans-serif;
    }

    body {
        font-family: var(--font-family);
        background-color: var(--bg-color);
        margin: 0;
        padding-top: 80px;
        /* Add padding to prevent content from being hidden by the fixed header */
    }

    .dashboard-wrapper {
        max-width: 1000px;
        margin: auto;
        padding: 2rem;
    }

    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: var(--card-bg-color);
        box-shadow: 0 2px 4px var(--shadow-color);
        z-index: 1000;
        padding: 1rem 2rem;
        box-sizing: border-box;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1000px;
        margin: auto;
    }

    .header h1 {
        font-size: 1.5rem;
        color: var(--text-color);
        margin: 0;
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

    .btn-danger {
        background-color: var(--danger-color);
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: var(--danger-hover-color);
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover-color);
    }

    .btn-secondary {
        background-color: var(--border-color);
        color: var(--text-color);
    }

    .card {
        background-color: var(--card-bg-color);
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
        margin-bottom: 1.5rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1rem;
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

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.4);
    }

    .dynamic-fields {
        display: none;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--border-color);
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

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        text-align: left;
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    th {
        color: var(--subtle-text-color);
        font-weight: 500;
    }

    td {
        color: var(--text-color);
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-article {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-event {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-gallery {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-video {
        background-color: #e0e7ff;
        color: #3730a3;
    }

    .action-link-delete {
        color: var(--danger-color);
        cursor: pointer;
    }
</style>
<div class="dashboard-wrapper">
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Create Content Form -->
    <div class="card">
        <h2 class="card-title">Create New Content</h2>
        <form action="content_dashboard.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="content_type">Content Type</label>
                <select name="content_type" id="content_type" class="form-control" required>
                    <option value="">-- Select Type --</option>
                    <option value="article">Article</option>
                    <option value="event">Event</option>
                    <option value="gallery">Gallery</option>
                    <option value="video">Video</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div id="article_fields" class="dynamic-fields">
                <div class="form-group">
                    <label for="article_author">Author</label>
                    <input type="text" name="article_author" id="article_author" class="form-control">
                </div>
                <div class="form-group">
                    <label for="article_body">Body</label>
                    <textarea name="article_body" id="article_body" rows="5" class="form-control"></textarea>
                </div>
            </div>

            <div id="event_fields" class="dynamic-fields">
                <div class="form-group">
                    <label for="event_description">Description</label>
                    <textarea name="event_description" id="event_description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="event_date">Event Date & Time</label>
                    <input type="datetime-local" name="event_date" id="event_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="event_banner">Banner Image (Max 5MB)</label>
                    <input type="file" name="event_banner" id="event_banner" class="form-control" accept="image/png, image/jpeg, image/gif, image/webp">
                </div>
            </div>

            <div id="gallery_fields" class="dynamic-fields">
                <div class="form-group">
                    <label for="gallery_description">Description</label>
                    <textarea name="gallery_description" id="gallery_description" rows="3" class="form-control"></textarea>
                </div>
            </div>

            <div id="video_fields" class="dynamic-fields">
                <div class="form-group">
                    <label for="video_embedded_src">Embedded Video URL (e.g., YouTube)</label>
                    <input type="text" name="video_embedded_src" id="video_embedded_src" class="form-control" placeholder="https://www.youtube.com/embed/...">
                </div>
                <div class="form-group">
                    <label for="video_embedded_description">Embedded Video Description</label>
                    <textarea name="video_embedded_description" id="video_embedded_description" rows="2" class="form-control"></textarea>
                </div>
                <hr style="margin: 1.5rem 0; border-color: var(--border-color);">
                <div class="form-group">
                    <label for="video_uploaded_src">Uploaded Video Path</label>
                    <input type="text" name="video_uploaded_src" id="video_uploaded_src" class="form-control" placeholder="/uploads/video.mp4">
                </div>
                <div class="form-group">
                    <label for="video_uploaded_description">Uploaded Video Description</label>
                    <textarea name="video_uploaded_description" id="video_uploaded_description" rows="2" class="form-control"></textarea>
                </div>
            </div>

            <button type="submit" name="create_content" class="btn btn-primary" style="font-weight:700;padding:0.85rem 2rem;font-size:1.1rem;border-radius:0.6rem;box-shadow:0 4px 16px rgba(37,99,235,0.12);transition:background 0.2s;">Create Content</button>
        </form>
    </div>

    <!-- List of Existing Content -->
    <div class="card">
        <h2 class="card-title">Existing Content</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Created On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allContent)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No content found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allContent as $item): ?>
                        <tr id="content-row-<?php echo $item['id']; ?>">
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($item['content_type']); ?>">
                                    <?php echo htmlspecialchars($item['content_type']); ?>
                                </span>
                            </td>
                            <td><?php echo date('F j, Y', strtotime($item['created_at'])); ?></td>
                            <td>
                                <a href="../view.php?id=<?php echo $item['id']; ?>" target="_blank" class="btn btn-secondary" style="margin-right:4px;font-weight:600;padding:0.5rem 1rem;border-radius:0.4rem;">View</a>
                                <a href="edit_content.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="margin-right:4px;font-weight:600;padding:0.5rem 1rem;border-radius:0.4rem;">Edit</a>
                                <a href="#" class="btn btn-danger" data-id="<?php echo $item['id']; ?>" style="font-weight:600;padding:0.5rem 1rem;border-radius:0.4rem;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('content_type').addEventListener('change', function() {
        document.querySelectorAll('.dynamic-fields').forEach(div => {
            div.style.display = 'none';
        });
        const selectedType = this.value;
        if (selectedType) {
            const selectedFields = document.getElementById(selectedType + '_fields');
            if (selectedFields) {
                selectedFields.style.display = 'block';
            }
        }
    });

    // --- Delete Content Handler ---
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('action-link-delete')) {
            e.preventDefault();
            const contentId = e.target.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
                deleteContent(contentId);
            }
        }
    });

    async function deleteContent(id) {
        try {
            const response = await fetch('../api/content_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'delete_content',
                    content_id: id
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                // Remove the table row from the page
                const row = document.getElementById('content-row-' + id);
                if (row) {
                    row.style.transition = 'opacity 0.5s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 500);
                }
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('An unexpected error occurred.');
        }
    }
</script>
<?php include_once 'admin_footer.php'; ?>