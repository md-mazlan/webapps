<?php
// Include necessary classes and the separate auth check functions
require_once 'php/admin_auth_check.php'; // For checking admin status
require_once 'php/user_auth_check.php';  // For checking public user status
require_once 'php/database.php';
require_once 'php/content.php';

// Get the content ID from the URL and validate it.
$content_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Check login status for both user types
$is_user_logged_in = isUserLoggedIn();
$is_admin_logged_in = isAdminLoggedIn();
$current_user_id = $is_user_logged_in ? $_SESSION['user_id'] : null;

$contentData = null;
if ($content_id) {
    // Fetch the content data from the database.
    $database = new Database();
    $db = $database->connect();
    $content = new Content($db);
    // Pass the current public user's ID to check if they have liked this content.
    $contentData = $content->getById($content_id, $current_user_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $contentData ? htmlspecialchars($contentData['title']) : 'Content Not Found'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9fafb;
            --text-color: #111827;
            --subtle-text-color: #6b7280;
            --border-color: #e5e7eb;
            --card-bg-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --primary-color: #2563eb;
            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Lora', serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
        }

        .navbar {
            background-color: var(--card-bg-color);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px var(--shadow-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .admin-link {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .content-wrapper {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .content-card {
            background-color: var(--card-bg-color);
            border-radius: 0.5rem;
            padding: 2rem;
        }

        .content-title {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .meta {
            color: var(--subtle-text-color);
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .content-body {
            font-family: var(--font-serif);
            font-size: 1.125rem;
            line-height: 1.8;
        }

        .content-body p,
        .content-body div {
            margin-bottom: 1.5rem;
        }

        .event-banner {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .gallery-grid img {
            width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            max-width: 100%;
            background: #000;
            margin-bottom: 1rem;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .interactions {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .like-button {
            display: inline-flex;
            align-items: center;
            background-color: var(--border-color);
            color: var(--subtle-text-color);
            padding: 0.5rem 1rem;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            border: 1px solid transparent;
        }

        .like-button.liked {
            background-color: #dbeafe;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .like-button svg {
            margin-right: 0.5rem;
        }

        .like-count {
            margin-left: 1rem;
            font-weight: 500;
        }

        .comments-section {
            margin-top: 2rem;
        }

        .comments-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .comment-form button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .comment-list {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }

        .comment-item {
            display: flex;
            padding: 1rem 0;
            border-top: 1px solid var(--border-color);
        }

        .comment-author {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .comment-meta {
            font-size: 0.8rem;
            color: var(--subtle-text-color);
        }

        .comment-body {
            margin-left: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="index.php">My Website</a>
        <div class="nav-links">
            <?php if ($is_admin_logged_in): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                <a href="admin/dashboard.php">Admin Dashboard</a>
                <?php if ($contentData): ?>
                    <a href="admin/edit_content.php?id=<?php echo $content_id; ?>" class="admin-link">Edit Content</a>
                <?php endif; ?>
            <?php elseif ($is_user_logged_in): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="api/user_logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login / Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="content-wrapper">
        <?php if ($contentData): ?>
            <div class="content-card">
                <h1 class="content-title"><?php echo htmlspecialchars($contentData['title']); ?></h1>
                <p class="meta">
                    Published on <?php echo date('F j, Y', strtotime($contentData['created_at'])); ?>
                </p>

                <div class="content-body">
                    <?php switch ($contentData['content_type']):
                        case 'article': ?>
                            <p><strong>By:</strong> <?php echo htmlspecialchars($contentData['author']); ?></p>
                            <div><?php echo nl2br(htmlspecialchars($contentData['body'])); ?></div>
                            <?php break; ?>

                        <?php
                        case 'event': ?>
                            <?php if (!empty($contentData['banner_url'])): ?>
                                <img src="<?php echo htmlspecialchars($contentData['banner_url']); ?>" alt="Event Banner" class="event-banner">
                            <?php endif; ?>
                            <p><strong>Event Date:</strong> <?php echo date('l, F j, Y \a\t g:i A', strtotime($contentData['event_date'])); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($contentData['description'])); ?></p>
                            <?php break; ?>

                        <?php
                        case 'gallery': ?>
                            <p><?php echo nl2br(htmlspecialchars($contentData['description'])); ?></p>
                            <div class="gallery-grid">
                                <?php if (!empty($contentData['images'])): ?>
                                    <?php foreach ($contentData['images'] as $image): ?>
                                        <a href="<?php echo htmlspecialchars($image['image_src']); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($image['image_src']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php break; ?>

                        <?php
                        case 'video': ?>
                            <?php if (!empty($contentData['embedded_src'])): ?>
                                <h4><?php echo nl2br(htmlspecialchars($contentData['embedded_description'])); ?></h4>
                                <div class="video-wrapper">
                                    <iframe src="<?php echo htmlspecialchars($contentData['embedded_src']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($contentData['uploaded_src'])): ?>
                                <h4><?php echo nl2br(htmlspecialchars($contentData['uploaded_description'])); ?></h4>
                                <video controls style="width:100%; border-radius: 0.5rem;">
                                    <source src="<?php echo htmlspecialchars($contentData['uploaded_src']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                            <?php break; ?>

                    <?php endswitch; ?>
                </div>

                <!-- Interactions Section -->
                <div class="interactions">
                    <?php if ($is_user_logged_in): ?>
                        <div id="like-section" class="like-button <?php echo $contentData['user_has_liked'] ? 'liked' : ''; ?>" data-content-id="<?php echo $content_id; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                            </svg>
                            <span class="like-text"><?php echo $contentData['user_has_liked'] ? 'Liked' : 'Like'; ?></span>
                        </div>
                    <?php endif; ?>
                    <span class="like-count" id="like-count"><?php echo $contentData['like_count']; ?> Likes</span>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h3>Comments (<span id="comment-count"><?php echo count($contentData['comments']); ?></span>)</h3>
                    <?php if ($is_user_logged_in): ?>
                        <form id="comment-form" class="comment-form">
                            <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                            <button type="submit">Post Comment</button>
                        </form>
                    <?php else: ?>
                        <p>You must be <a href="login.php">logged in</a> to post a comment.</p>
                    <?php endif; ?>

                    <ul id="comment-list" class="comment-list">
                        <?php if (!empty($contentData['comments'])): ?>
                            <?php foreach ($contentData['comments'] as $comment): ?>
                                <li class="comment-item">
                                    <div class="comment-body">
                                        <p class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></p>
                                        <p class="comment-meta"><?php echo date('F j, Y', strtotime($comment['created_at'])); ?></p>
                                        <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        <?php else: ?>
            <div class="content-card">
                <h1>Content Not Found</h1>
                <p>The content you are looking for does not exist or may have been removed. Please <a href="index.php">return to the homepage</a>.</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const likeButton = document.getElementById('like-section');
            const commentForm = document.getElementById('comment-form');
            const contentId = <?php echo $content_id ?? 0; ?>;

            // The API endpoint is now in the /api/ folder
            const API_ENDPOINT = 'api/interactions.php';

            if (likeButton) {
                likeButton.addEventListener('click', async function() {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'toggle_like',
                            content_id: contentId
                        })
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        const likeCountSpan = document.getElementById('like-count');
                        let currentCount = parseInt(likeCountSpan.textContent);

                        if (result.action === 'liked') {
                            likeButton.classList.add('liked');
                            likeButton.querySelector('.like-text').textContent = 'Liked';
                            likeCountSpan.textContent = (currentCount + 1) + ' Likes';
                        } else {
                            likeButton.classList.remove('liked');
                            likeButton.querySelector('.like-text').textContent = 'Like';
                            likeCountSpan.textContent = (currentCount - 1) + ' Likes';
                        }
                    } else {
                        alert(result.message || 'An error occurred.');
                    }
                });
            }

            if (commentForm) {
                commentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const commentText = this.querySelector('textarea').value;
                    if (!commentText.trim()) return;

                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'add_comment',
                            content_id: contentId,
                            comment_text: commentText
                        })
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        const commentList = document.getElementById('comment-list');
                        const newComment = document.createElement('li');
                        newComment.className = 'comment-item';

                        const newCommentDate = new Date(result.comment.created_at);
                        const formattedDate = newCommentDate.toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric'
                        });

                        newComment.innerHTML = `
                        <div class="comment-body">
                            <p class="comment-author">${result.comment.username}</p>
                            <p class="comment-meta">${formattedDate}</p>
                            <p>${result.comment.comment.replace(/\n/g, '<br>')}</p>
                        </div>`;
                        commentList.prepend(newComment);

                        const commentCountSpan = document.getElementById('comment-count');
                        let currentCount = parseInt(commentCountSpan.textContent);
                        commentCountSpan.textContent = currentCount + 1;

                        this.querySelector('textarea').value = '';
                    } else {
                        alert(result.message || 'Failed to post comment.');
                    }
                });
            }
        });
    </script>
</body>

</html>