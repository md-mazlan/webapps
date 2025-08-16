<?php
// admin/news.php
require_once '../php/config.php';
require_once ROOT_PATH . '/php/admin_auth_check.php';



require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/app/controllers/NewsController.php';
$database = new Database();
$db = $database->connect();
$newsController = new NewsController($db);
$action = $_GET['action'] ?? '';

// Handle create, update, delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        // Handle image upload
        $image_url = $_POST['image_url'];
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/uploads/news/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('news_', true) . '.' . $ext;
            $targetFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFile)) {
                $image_url = '/uploads/news/' . $filename;
            }
        }
        $newsController->create([
            'title' => $_POST['title'],
            'body' => $_POST['body'],
            'image_url' => $image_url,
            'published_at' => $_POST['published_at']
        ]);
        header('Location: news.php');
        exit;
    } elseif ($action === 'update' && isset($_POST['id'])) {
        // Handle image upload for update
        $image_url = $_POST['image_url'];
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/uploads/news/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('news_', true) . '.' . $ext;
            $targetFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFile)) {
                $image_url = 'uploads/news/' . $filename;
            }
        }
        $newsController->update($_POST['id'], [
            'title' => $_POST['title'],
            'body' => $_POST['body'],
            'image_url' => $image_url,
            'published_at' => $_POST['published_at']
        ]);
        header('Location: news.php');
        exit;
    }
}
if ($action === 'delete' && isset($_GET['id'])) {
    $newsController->delete($_GET['id']);
    header('Location: news.php');
    exit;
}

// Fetch all news for listing
$newsList = $newsController->getAll();
$editNews = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editNews = $newsController->getById($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - News Management</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="dashboard-wrapper">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">News Management</h1>
        </div>
        <div class="news-form">
            <h2><?= $editNews ? 'Edit News' : 'Add News' ?></h2>
        <form method="post" action="news.php?action=<?= $editNews ? 'update' : 'create' ?>" enctype="multipart/form-data">
                <?php if ($editNews): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editNews->id) ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Title:
                        <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($editNews->title ?? '') ?>" required>
                    </label>
                </div>
                <div class="form-group">
                    <label>Body:
                        <textarea class="form-control" name="body" rows="5" required><?= htmlspecialchars($editNews->body ?? '') ?></textarea>
                    </label>
                </div>
                <div class="form-group">
                    <label>Image URL:
                        <input class="form-control" type="text" name="image_url" value="<?= htmlspecialchars($editNews->image_url ?? '') ?>">
                    </label>
                </div>
                <div class="form-group">
                    <label>Or Upload Image:
                        <input class="form-control" type="file" name="image_file" accept="image/*">
                    </label>
                </div>
                <div class="form-group">
                    <label>Published At:
                        <input class="form-control" type="datetime-local" name="published_at" value="<?= $editNews && $editNews->published_at ? date('Y-m-d\\TH:i', strtotime($editNews->published_at)) : '' ?>" required>
                    </label>
                </div>
                <button class="btn btn-primary" type="submit">Save</button>
                <?php if ($editNews): ?>
                    <a class="btn btn-secondary" href="news.php">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All News</h2>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Published At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($newsList as $news): ?>
                <tr>
                    <td><?= htmlspecialchars($news->id) ?></td>
                    <td><?= htmlspecialchars($news->title) ?></td>
                    <td><?= htmlspecialchars($news->published_at) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="news.php?action=edit&id=<?= $news->id ?>">Edit</a>
                        <a class="btn btn-danger" href="news.php?action=delete&id=<?= $news->id ?>" onclick="return confirm('Delete this news item?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include 'admin_footer.php'; ?>
</body>
</html>
