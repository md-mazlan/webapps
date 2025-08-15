<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../app/models/BillplzPayment.php';
require_once __DIR__ . '/../app/controllers/BillplzPaymentController.php';

$db = new Database();
$conn = $db->connect();
$payments = [];
if ($conn && $user_id) {
    $controller = new BillplzPaymentController($conn);
    $payments = $controller->getByUserId($user_id);
}
?>
<div class="container" data-style="css/payment_history.css">
    <h2>Payment history</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Subscription package</th>
                <th>Payment method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td data-label="Date "><?php echo htmlspecialchars($payment->paid_at); ?></td>
                        <td data-label="Subscription package">
                            <?php echo htmlspecialchars($payment->description ?? 'N/A'); ?><br>
                            <span class="sub-amount">
                                <?php echo $payment->amount ? 'MYR ' . number_format($payment->amount / 100, 2) : ''; ?>
                            </span>
                        </td>
                        <td data-label="Payment method"><?php echo htmlspecialchars($payment->name ?? ''); ?></td>
                        <td data-label="Amount">RM <?php echo $payment->paid_amount ? number_format($payment->paid_amount / 100, 2) : '0.00'; ?></td>
                        <td data-label="Status">
                            <?php if ($payment->paid): ?>
                                <span class="status success">● Success</span>
                            <?php else: ?>
                                <span class="status failed">● Failed</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Action">
                            <a href="receipt.php?id=<?php echo urlencode($payment->id); ?>" target="_blank" title="Download Receipt" style="background:#2563eb;border:none;padding:0.3rem 0.7rem;border-radius:5px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                                    <path fill="#fff" d="M12 16.5a1 1 0 0 1-1-1V5a1 1 0 1 1 2 0v10.5a1 1 0 0 1-1 1Z" />
                                    <path fill="#fff" d="M7.21 13.79a1 1 0 0 1 1.42-1.42l2.29 2.3 2.3-2.3a1 1 0 1 1 1.41 1.42l-3 3a1 1 0 0 1-1.42 0l-3-3ZM5 19a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1Z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No payment history found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>