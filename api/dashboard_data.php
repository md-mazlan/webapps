<?php
// api/dashboard_data.php
require_once '../php/config.php';
require_once ROOT_PATH . '/app/controllers/ContentController.php';
require_once ROOT_PATH . '/app/controllers/VendorController.php';

$type = $_GET['type'] ?? 'news';

header('Content-Type: application/json');

if ($type === 'vendor') {
    $vendorController = new VendorController();
    $vendors = $vendorController->getAll();
    echo json_encode(['status' => 'success', 'data' => $vendors]);
    exit;
}


if ($type === 'gallery') {
    $contentController = new ContentController();
    $limit = 20;
    $offset = 0;
    // Get both gallery and video content
    $gallery = $contentController->getAll($limit, $offset, 'gallery');
    $videos = $contentController->getAll($limit, $offset, 'video');
    $data = array_merge($gallery, $videos);
    // Sort by created_at descending
    usort($data, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

$contentController = new ContentController();
$limit = 20;
$offset = 0;
$data = $contentController->getAll($limit, $offset, $type);
echo json_encode(['status' => 'success', 'data' => $data]);
