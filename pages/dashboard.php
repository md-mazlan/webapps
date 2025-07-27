<?php
// Include necessary classes
require_once '../php/database.php';
require_once '../php/content.php';

// Fetch all content data from the database.
$database = new Database();
$db = $database->connect();
$content = new Content($db);
$allContent = $content->getAll();
?>

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
        margin-bottom: 3rem;
    }

    .page-header h1 {
        font-family: var(--font-serif);
        font-size: 3rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    .content-card {
        background-color: var(--card-bg-color);
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease-in-out;
    }

    .content-card:hover {
        transform: translateY(-5px);
    }

    .card-body {
        padding: 1.5rem;
        flex-grow: 1;
    }

    .card-title {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        margin: 0 0 0.5rem 0;
    }

    .card-title a {
        text-decoration: none;
        color: var(--text-color);
    }

    .card-title a:hover {
        color: var(--primary-color);
    }

    .card-meta {
        font-size: 0.875rem;
        color: var(--subtle-text-color);
        margin-bottom: 1rem;
    }

    .card-footer {
        padding: 1rem 1.5rem;
        background-color: #f9fafb;
        border-top: 1px solid var(--border-color);
    }

    .card-footer a {
        text-decoration: none;
        font-weight: 500;
        color: var(--primary-color);
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
        margin-bottom: 1rem;
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
</style>

<main class="page-wrapper">
    <header class="page-header">
        <h1>Our Latest Content</h1>
    </header>

    <?php if (empty($allContent)): ?>
        <p style="text-align: center;">No content has been published yet.</p>
    <?php else: ?>
        <div class="content-grid">
            <?php foreach ($allContent as $item): ?>
                <div class="content-card">
                    <div class="card-body">
                        <span class="badge badge-<?php echo htmlspecialchars($item['content_type']); ?>">
                            <?php echo htmlspecialchars($item['content_type']); ?>
                        </span>
                        <h2 class="card-title">
                            <a href="view.php?id=<?php echo $item['id']; ?>">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </h2>
                        <p class="card-meta">
                            Published on <?php echo date('F j, Y', strtotime($item['created_at'])); ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="view.php?id=<?php echo $item['id']; ?>">Read More &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>