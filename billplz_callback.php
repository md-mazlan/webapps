<?php
// FILE 1: billplz_callback.php
// This script is the secure backend endpoint that Billplz will call.
// Its sole purpose is to verify the request and handle the payment status update.

// IMPORTANT: This is your Billplz secret key.
define('BILLPLZ_SECRET', '79381b68-4676-440f-9613-05e30b109d7c');

// Initialize a session to store data and pass it to the user-facing page.
session_start();

// Get the raw request body and signature for verification.
$rawBody = file_get_contents('php://input');
$billplzSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
$computedSignature = hash_hmac('sha256', $rawBody, BILLPLZ_SECRET);

// Check if the signature is valid.
if (!$billplzSignature || !hash_equals($billplzSignature, $computedSignature)) {
    // If the signature is invalid, we do not trust the request.
    http_response_code(400);
    error_log('Error: Signature verification failed.');
    die('Bad Request: Signature verification failed.');
}

// If we've reached this point, the request is secure.
$billplzData = json_decode($rawBody, true);
$billId = $billplzData['billplz']['id'] ?? 'N/A';
$paidStatus = ($billplzData['paid'] === true) ? 'success' : 'failed';

// Store the important data in the session to be used by the next page.
$_SESSION['payment_status'] = $paidStatus;
$_SESSION['bill_id'] = $billId;
$_SESSION['payment_amount'] = $billplzData['billplz']['amount'] ?? 0;
$_SESSION['message'] = ($paidStatus === 'success') ? 'Thank you! Your payment was successful.' : 'Your payment failed or was not completed.';

// TODO: Add your database logic here.
// Example:
// $db = new PDO(...);
// $stmt = $db->prepare("UPDATE orders SET status = ?, bill_id = ? WHERE bill_id = ?");
// $stmt->execute([$paidStatus, $billId, $billId]);

// Respond to Billplz that the callback was received.
http_response_code(200);
echo 'Callback received.';

// The following part is what the user is redirected to.
?>

<!-- FILE 2: payment_status.php -->
<!-- This file would be served to the user after the Billplz redirection. -->
<?php
// Initialize a session to retrieve data passed from the callback script.
session_start();

// Retrieve payment data from the session.
$paymentStatus = $_SESSION['payment_status'] ?? 'failed';
$message = $_SESSION['message'] ?? 'Payment status could not be determined.';
$billId = $_SESSION['bill_id'] ?? 'N/A';
$paymentAmount = $_SESSION['payment_amount'] ?? 0;

// Clear the session data after use to prevent stale information.
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 480px;
            width: 100%;
            background-color: #ffffff;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
            margin: 0 16px;
        }

        .icon {
            width: 96px;
            height: 96px;
            margin: 0 auto 16px;
        }

        .icon-success path {
            stroke: #10b981;
        }

        .icon-failed path {
            stroke: #ef4444;
        }

        h1 {
            font-size: 30px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        p {
            color: #4b5563;
            margin-bottom: 16px;
        }

        .details-box-success {
            background-color: #ecfdf5;
            border-radius: 8px;
            padding: 16px;
            text-align: left;
        }

        .details-box-failed {
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 16px;
            text-align: left;
        }

        .details-box-success p,
        .details-box-failed p {
            font-size: 14px;
            color: #374151;
            margin-bottom: 4px;
        }

        .details-box-success p strong,
        .details-box-failed p strong {
            font-weight: 600;
            color: #1f2937;
        }

        .btn {
            display: inline-block;
            margin-top: 24px;
            font-weight: 600;
            padding: 8px 24px;
            border-radius: 8px;
            color: #ffffff;
            text-decoration: none;
            transition: background-color 0.15s ease-in-out;
        }

        .btn-success {
            background-color: #10b981;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-failed {
            background-color: #ef4444;
        }

        .btn-failed:hover {
            background-color: #dc2626;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php if ($paymentStatus === 'success'): ?>
            <div class="icon icon-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1>Payment Successful!</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <div class="details-box-success">
                <p><strong>Details:</strong></p>
                <p><strong>Bill ID:</strong> <?php echo htmlspecialchars($billId); ?></p>
                <p><strong>Amount:</strong> RM <?php echo number_format($paymentAmount / 100, 2); ?></p>
            </div>
            <a href="/" class="btn btn-success">Go to Homepage</a>

        <?php else: ?>
            <div class="icon icon-failed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1>Payment Failed</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="/checkout" class="btn btn-failed">Try Again</a>
        <?php endif; ?>
    </div>

</body>

</html>