<?php
// Billplz callback handler: save payment record to DB and redirect to payment history

require_once 'lib/API.php';
require_once 'lib/Connect.php';
require_once 'configuration.php';
require_once 'php/database.php';
require_once 'app/models/BillplzPayment.php';
require_once 'app/controllers/BillplzPaymentController.php';

use Billplz\Minisite\API;
use Billplz\Minisite\Connect;


// --- TEST MODE: allows local testing without Billplz POST ---
if (isset($_GET['test'])) {
    // Simulate Billplz response
    $rbody = [
        'id' => 'dummy_bill_id',
        'collection_id' => 'dummy_collection',
        'paid' => 1,
        'state' => 'paid',
        'amount' => 5000,
        'paid_amount' => 5000,
        'due_at' => date('Y-m-d'),
        'email' => 'test@example.com',
        'mobile' => '0123456789',
        'name' => 'Test User',
        'url' => 'http://localhost/dummy',
        'reference_1_label' => 'User ID',
        'reference_1' => 1,
        'reference_2_label' => 'Order',
        'reference_2' => 'ORD123',
        'redirect_url' => 'http://localhost/redirect',
        'callback_url' => 'http://localhost/callback',
        'description' => 'Test payment',
        'paid_at' => date('Y-m-d H:i:s'),
    ];
} else {
    // Get Billplz payment status (real callback)
    $data = Connect::getXSignature($x_signature, 'bill_callback');
    $connect = new Connect($api_key);
    $connect->setStaging($is_sandbox);
    $billplz = new API($connect);
    list($rheader, $rbody) = $billplz->toArray($billplz->getBill($data['id']));
}

// Debug logging to check if callback is triggered and what data is received
file_put_contents(__DIR__ . '/callback_debug.txt', date('c') . ' ' . json_encode([
    'request' => $_REQUEST,
    'rbody' => $rbody
]) . PHP_EOL, FILE_APPEND);


// Map user_id from Billplz reference_1 if available
$user_id = isset($rbody['reference_1']) ? $rbody['reference_1'] : null;

$db = new Database();
$conn = $db->connect();
if ($conn) {
    $controller = new BillplzPaymentController($conn);
    $payment = new BillplzPayment([
        'id' => $rbody['id'] ?? null,
        'user_id' => $user_id,
        'collection_id' => $rbody['collection_id'] ?? null,
        'paid' => $rbody['paid'] ?? null,
        'state' => $rbody['state'] ?? null,
        'amount' => $rbody['amount'] ?? null,
        'paid_amount' => $rbody['paid_amount'] ?? null,
        'due_at' => $rbody['due_at'] ?? null,
        'email' => $rbody['email'] ?? null,
        'mobile' => $rbody['mobile'] ?? null,
        'name' => $rbody['name'] ?? null,
        'url' => $rbody['url'] ?? null,
        'reference_1_label' => $rbody['reference_1_label'] ?? null,
        'reference_1' => $rbody['reference_1'] ?? null,
        'reference_2_label' => $rbody['reference_2_label'] ?? null,
        'reference_2' => $rbody['reference_2'] ?? null,
        'redirect_url' => $rbody['redirect_url'] ?? null,
        'callback_url' => $rbody['callback_url'] ?? null,
        'description' => $rbody['description'] ?? null,
        'paid_at' => $rbody['paid_at'] ?? date('Y-m-d H:i:s'),
    ]);
    $controller->create($payment);
}

file_put_contents('callback_debug.txt', date('c') . ' ' . json_encode($_REQUEST) . PHP_EOL, FILE_APPEND);

/*
 * In variable (array) $moreData you may get this information:
 * 1. reference_1
 * 2. reference_1_label
 * 3. reference_2
 * 4. reference_2_label
 * 5. amount
 * 6. description
 * 7. id // bill_id
 * 8. name
 * 9. email
 * 10. paid
 * 11. collection_id
 * 12. due_at
 * 13. mobile
 * 14. url
 * 15. callback_url
 * 16. redirect_url
 */
