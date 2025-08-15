<?php

// --- Dependencies ---
require_once 'lib/API.php';
require_once 'lib/Connect.php';
require_once 'configuration.php';
require_once 'php/database.php';

use Billplz\Minisite\API;
use Billplz\Minisite\Connect;

// --- Session & User ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// --- Billplz API ---
$data = Connect::getXSignature($x_signature, 'bill_redirect');
$connect = new Connect($api_key);
$connect->setStaging($is_sandbox);
$billplz = new API($connect);
list($rheader, $rbody) = $billplz->toArray($billplz->getBill($data['id']));


// --- Save Payment Record (fallback if callback is not working) ---
$db = new Database();
$conn = $db->connect();
if ($conn) {
    $stmt = $conn->prepare(
        "INSERT INTO billplz_payment (
            id, user_id, collection_id, paid, state, amount, paid_amount, due_at, email, mobile, name, url,
            reference_1_label, reference_1, reference_2_label, reference_2, redirect_url, callback_url, description, paid_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE paid=VALUES(paid), state=VALUES(state), paid_amount=VALUES(paid_amount), paid_at=VALUES(paid_at)"
    );
    $stmt->execute([
        $rbody['id'] ?? null,
        $user_id,
        $rbody['collection_id'] ?? null,
        $rbody['paid'] ?? null,
        $rbody['state'] ?? null,
        $rbody['amount'] ?? null,
        $rbody['paid_amount'] ?? null,
        $rbody['due_at'] ?? null,
        $rbody['email'] ?? null,
        $rbody['mobile'] ?? null,
        $rbody['name'] ?? null,
        $rbody['url'] ?? null,
        $rbody['reference_1_label'] ?? null,
        $rbody['reference_1'] ?? null,
        $rbody['reference_2_label'] ?? null,
        $rbody['reference_2'] ?? null,
        $rbody['redirect_url'] ?? null,
        $rbody['callback_url'] ?? null,
        $rbody['description'] ?? null,
        $rbody['paid_at'] ?? date('Y-m-d H:i:s')
    ]);
}

// --- Redirect ---
if ($rbody['paid']) {
    if (!empty($successpath)) {
        header('Location: ' . $successpath);
    } else {
        header('Location: ' . $rbody['url']);
    }
} else {
    header('Location: ' . $rbody['url']);
}
