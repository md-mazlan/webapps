<?php
// api/news_data.php
require_once '../php/config.php';
require_once ROOT_PATH . '/app/controllers/NewsController.php';
require_once ROOT_PATH . '/php/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->connect();
$newsController = new NewsController($db);

$newsList = $newsController->getAll();
$data = [];
foreach ($newsList as $news) {
    $data[] = [
        'id' => $news->id,
        'title' => $news->title,
        'body' => $news->body,
        'image_url' => $news->image_url,
        'published_at' => $news->published_at,
        'created_at' => $news->created_at,
        'updated_at' => $news->updated_at
    ];
}
echo json_encode(['status' => 'success', 'data' => $data]);
