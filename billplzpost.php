
<?php
$user_id = null;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// Include the new configuration file first.
require_once 'lib/API.php';
require_once 'lib/Connect.php';
require_once 'configuration.php';

use Billplz\Minisite\API;
use Billplz\Minisite\Connect;


// Always use logged-in user's full name for Billplz 'name' parameter
require_once __DIR__ . '/php/database.php';
require_once __DIR__ . '/php/user.php';
$full_name = '';
if ($user_id) {
    $database = new Database();
    $db = $database->connect();
    if ($db) {
        $user = new User($db);
        $profile = $user->getProfile($user_id);
        $full_name = $profile['full_name'] ?? '';
    }
}
$parameter = array(
    'collection_id' => !empty($collection_id) ? $collection_id : $_REQUEST['collection_id'],
    'email' => ($profile['email'] ?? (isset($_REQUEST['email']) ? $_REQUEST['email'] : '')),
    'mobile' => ($profile['phone'] ?? (isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : '')),
    'name' => $full_name ?: (isset($_REQUEST['name']) ? $_REQUEST['name'] : 'No Name'),
    'amount' => !empty($amount) ? $amount : $_REQUEST['amount'],
    'callback_url' => $websiteurl . '/callback.php',
    'description' => !empty($description) ? $description : $_REQUEST['description']
);

$optional = array(
    'redirect_url' => $websiteurl . '/redirect.php',
    'reference_1_label' => 'User ID',
    'reference_1' => $user_id,
    'reference_2_label' => !empty($reference_2_label) ? $reference_2_label : $_REQUEST['reference_2_label'] ?? '',
    'reference_2' => isset($_REQUEST['reference_2']) ? $_REQUEST['reference_2'] : '',
    'deliver' => 'false'
);

if (empty($parameter['mobile']) && empty($parameter['email'])) {
    $parameter['email'] = 'noreply@billplz.com';
}

if (!filter_var($parameter['email'], FILTER_VALIDATE_EMAIL)) {
    $parameter['email'] = 'noreply@billplz.com';
}

$connect = new Connect($api_key);
$connect->setStaging($is_sandbox);
$billplz = new API($connect);
list($rheader, $rbody) = $billplz->toArray($billplz->createBill($parameter, $optional));
/***********************************************/
// Include tracking code here
/***********************************************/

$is_debug = defined('DEBUG') || (bool) $debug;

if ($rheader !== 200) {
    if ($is_debug) {
        echo '<pre>' . print_r($rbody, true) . '</pre>';
    } elseif (!empty($fallbackurl)) {
        header('Location: ' . $fallbackurl);
        exit;
    }
}
if (isset($rbody['url'])) {
    header('Location: ' . $rbody['url']);
    exit;
}
