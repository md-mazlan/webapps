<?php
// Include necessary classes from the 'php' folder.
require_once 'php/config.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/content.php';

// --- Filter and Pagination Logic ---
$database = new Database();
$db = $database->connect();
$content = new Content($db);

// Define allowed content types for filtering and validate user input.
$allowed_types = ['article', 'event', 'gallery', 'video'];
$filter_type = isset($_GET['type']) && in_array($_GET['type'], $allowed_types) ? $_GET['type'] : null;

$items_per_page = 9; // Display 9 items per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $items_per_page;

// Fetch total count and calculate total pages based on the filter
$total_items = $content->getTotalCount($filter_type);
$total_pages = ceil($total_items / $items_per_page);

// Fetch the content for the current page with the filter
$allContent = $content->getAll($items_per_page, $offset, $filter_type);


/**
 * Helper function to get a YouTube thumbnail from a URL.
 * @param string $url The YouTube URL.
 * @return string The URL of the thumbnail image.
 */
function getYouTubeThumbnail($url)
{
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $match)) {
        return 'https://img.youtube.com/vi/' . $match[1] . '/hqdefault.jpg';
    }
    return 'https://placehold.co/400x225/111827/FFFFFF?text=Video';
}

/**
 * Creates a short excerpt from a string.
 * @param string $text The full text.
 * @param int $length The desired length of the excerpt.
 * @return string The shortened text.
 */
function create_excerpt($text, $length = 150)
{
    if (empty($text)) {
        return '';
    }
    $text = strip_tags($text);
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Content</title>
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
        }

        .navbar a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
        }

        .page-wrapper {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-family: var(--font-serif);
            font-size: 3rem;
        }

        .filter-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .filter-nav a {
            text-decoration: none;
            color: var(--subtle-text-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .filter-nav a:hover {
            color: var(--primary-color);
            background-color: #eef2ff;
        }

        .filter-nav a.active {
            color: var(--primary-color);
            background-color: #dbeafe;
            font-weight: 600;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        .card {
            background-color: var(--card-bg-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card a {
            text-decoration: none;
        }

        .card-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .card-title {
            font-family: var(--font-serif);
            font-size: 1.25rem;
            margin: 0.5rem 0;
            color: var(--text-color);
        }

        .card-title:hover {
            color: var(--primary-color);
        }

        .card-meta {
            font-size: 0.875rem;
            color: var(--subtle-text-color);
        }

        .card-stats {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--subtle-text-color);
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .card-stats svg {
            width: 16px;
            height: 16px;
            margin-right: 0.25rem;
            vertical-align: middle;
        }

        .card-excerpt {
            font-size: 0.9rem;
            color: var(--subtle-text-color);
            line-height: 1.6;
            margin-top: 0.75rem;
        }

        .card-article .card-footer {
            padding: 1rem 1.5rem;
            background-color: #f9fafb;
            border-top: 1px solid var(--border-color);
        }

        .card-article .card-footer a {
            font-weight: 500;
            color: var(--primary-color);
        }

        .card-event .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background-color: var(--border-color);
        }

        .card-event .event-date {
            font-weight: 600;
            color: var(--primary-color);
        }

        .card-gallery {
            position: relative;
            aspect-ratio: 1 / 1;
            justify-content: center;
            align-items: center;
        }

        .card-gallery .card-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .card-gallery:hover .card-background {
            transform: scale(1.05);
        }

        .card-gallery .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 2;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            color: white;
        }

        .card-gallery:hover .card-overlay {
            opacity: 1;
        }

        .card-gallery .card-stats {
            color: white;
            font-size: 1.125rem;
            font-weight: 500;
        }

        .card-video .card-thumbnail {
            position: relative;
        }

        .card-video .card-thumbnail img {
            width: 100%;
            display: block;
            background-color: #000;
        }

        .card-video .card-body {
            display: flex;
        }

        .card-video .card-title {
            font-family: var(--font-sans);
            font-size: 1rem;
            font-weight: 600;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 3rem;
            gap: 0.5rem;
        }

        .page-link {
            text-decoration: none;
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: #dbeafe;
            border-color: var(--primary-color);
        }

        .page-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            cursor: default;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>/index.php">My Website</a>
    </nav>

    <main class="page-wrapper">
        <header class="page-header">
            <h1>Our Latest Content</h1>
        </header>

        <!-- Filter Navigation -->
        <div class="filter-nav">
            <a href="contents.php" class="<?php echo !$filter_type ? 'active' : ''; ?>">All</a>
            <a href="contents.php?type=article" class="<?php echo $filter_type == 'article' ? 'active' : ''; ?>">Articles</a>
            <a href="contents.php?type=event" class="<?php echo $filter_type == 'event' ? 'active' : ''; ?>">Events</a>
            <a href="contents.php?type=gallery" class="<?php echo $filter_type == 'gallery' ? 'active' : ''; ?>">Galleries</a>
            <a href="contents.php?type=video" class="<?php echo $filter_type == 'video' ? 'active' : ''; ?>">Videos</a>
        </div>

        <?php if (empty($allContent)): ?>
            <p style="text-align: center;">No content found for this category.</p>
        <?php else: ?>
            <div class="content-grid">
                <?php foreach ($allContent as $item): ?>
                    <?php switch ($item['content_type']):
                        case 'article': ?>
                            <div class="card card-article">
                                <div class="card-body">
                                    <p class="card-meta">Published on <?php echo date('F j, Y', strtotime($item['created_at'])); ?></p>
                                    <a href="view.php?id=<?php echo $item['id']; ?>">
                                        <h2 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                                    </a>
                                    <p class="card-excerpt"><?php echo htmlspecialchars(create_excerpt($item['article_body'])); ?></p>
                                    <div class="card-stats">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                            </svg> <?php echo $item['like_count']; ?></span>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"></path>
                                            </svg> <?php echo $item['comment_count']; ?></span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="view.php?id=<?php echo $item['id']; ?>">Read More &rarr;</a>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php
                        case 'event': ?>
                            <div class="card card-event">
                                <a href="view.php?id=<?php echo $item['id']; ?>">
                                    <img src="<?php echo $item['banner_url'] ? BASE_URL . '/' . htmlspecialchars($item['banner_url']) : htmlspecialchars('https://placehold.co/400x225/e0e7ff/3730a3?text=Event'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-img-top">
                                </a>
                                <div class="card-body">
                                    <p class="card-meta event-date"><?php echo date('D, M j, Y', strtotime($item['event_date'])); ?></p>
                                    <a href="view.php?id=<?php echo $item['id']; ?>">
                                        <h2 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                                    </a>
                                    <div class="card-stats">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                            </svg> <?php echo $item['like_count']; ?></span>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"></path>
                                            </svg> <?php echo $item['comment_count']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php
                        case 'gallery': ?>
                            <a href="view.php?id=<?php echo $item['id']; ?>" class="card card-gallery">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($item['gallery_thumbnail'] ?: 'https://placehold.co/400x400/fef3c7/92400e?text=Gallery'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-background">
                                <div class="card-overlay">
                                    <div class="card-stats">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                            </svg> <?php echo $item['like_count']; ?></span>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"></path>
                                            </svg> <?php echo $item['comment_count']; ?></span>
                                    </div>
                                </div>
                            </a>
                            <?php break; ?>

                        <?php
                        case 'video': ?>
                            <div class="card card-video">
                                <a href="view.php?id=<?php echo $item['id']; ?>" class="card-thumbnail">
                                    <img src="<?php echo getYouTubeThumbnail($item['embedded_src']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </a>
                                <div class="card-body">
                                    <div>
                                        <a href="view.php?id=<?php echo $item['id']; ?>">
                                            <h2 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                                        </a>
                                        <div class="card-stats">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                                </svg> <?php echo $item['like_count']; ?></span>
                                            <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"></path>
                                                </svg> <?php echo $item['comment_count']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php break; ?>

                    <?php endswitch; ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php
                $base_url = "contents.php?";
                if ($filter_type) {
                    $base_url .= "type=" . urlencode($filter_type) . "&";
                }
                ?>
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" class="page-link <?php if ($i == $current_page) echo 'active'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </main>
</body>

</html>