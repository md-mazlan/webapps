<?php
// receipt.php: View and download a payment receipt as PDF or HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    die('Unauthorized');
}

require_once 'php/database.php';
require_once 'app/models/BillplzPayment.php';
require_once 'app/controllers/BillplzPaymentController.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die('No receipt ID provided.');
}

$db = new Database();
$conn = $db->connect();
$controller = new BillplzPaymentController($conn);
$payment = $controller->getById($id);
if (!$payment || $payment->user_id != $user_id) {
    die('Receipt not found or access denied.');
}

// PDF output if requested
if (isset($_GET['pdf'])) {
    require_once __DIR__ . '/../lib/fpdf/fpdf.php';
    $pdf = new FPDF('P', 'mm', 'A5');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 12, 'Payment Receipt', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(4);
    $pdf->Cell(40, 8, 'Date:', 0, 0); $pdf->Cell(0, 8, $payment->paid_at, 0, 1);
    $pdf->Cell(40, 8, 'Package:', 0, 0); $pdf->Cell(0, 8, $payment->description, 0, 1);
    $pdf->Cell(40, 8, 'Amount:', 0, 0); $pdf->Cell(0, 8, 'RM ' . number_format($payment->paid_amount/100, 2), 0, 1);
    $pdf->Cell(40, 8, 'Payment Method:', 0, 0); $pdf->Cell(0, 8, $payment->name, 0, 1);
    $pdf->Cell(40, 8, 'Status:', 0, 0); $pdf->Cell(0, 8, $payment->paid ? 'Success' : 'Failed', 0, 1);
    $transaction_id = $payment->id;
    $pdf->Cell(40, 8, 'Transaction ID:', 0, 0); $pdf->Cell(0, 8, $transaction_id, 0, 1);
    $pdf->Output('D', 'receipt.pdf');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .receipt-container { max-width: 400px; margin: 2.5rem auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 2rem; }
        h2 { margin-top: 0; text-align: center; }
        .receipt-row { margin-bottom: 1rem; }
        .label { font-weight: bold; display: inline-block; width: 120px; }
        .status-success { color: #1a9b4b; font-weight: bold; }
        .status-failed { color: #d64545; font-weight: bold; }
        .actions { text-align: right; margin-top: 2rem; }
        .btn { background: #2563eb; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 6px; font-weight: 500; cursor: pointer; text-decoration: none; }
        .btn-outline { background: #fff; color: #2563eb; border: 1px solid #2563eb; }
    </style>
</head>
<body>
<div class="receipt-container">
    <h2>Payment Receipt</h2>
    <div class="receipt-row"><span class="label">Date:</span> <?php echo htmlspecialchars($payment->paid_at); ?></div>
    <div class="receipt-row"><span class="label">Package:</span> <?php echo htmlspecialchars($payment->description); ?></div>
    <div class="receipt-row"><span class="label">Amount:</span> RM <?php echo number_format($payment->paid_amount/100, 2); ?></div>
    <div class="receipt-row"><span class="label">Payment Method:</span> <?php echo htmlspecialchars($payment->name); ?></div>
    <div class="receipt-row"><span class="label">Status:</span> <span class="status-<?php echo $payment->paid ? 'success' : 'failed'; ?>"><?php echo $payment->paid ? 'Success' : 'Failed'; ?></span></div>
    <div class="receipt-row"><span class="label">Transaction ID:</span> <?php echo htmlspecialchars($payment->id); ?></div>
    <div class="actions">
        <a href="?id=<?php echo urlencode($id); ?>&pdf=1" class="btn btn-outline">Download PDF</a>
        <button onclick="window.print()" class="btn">Print</button>
    </div>
</div>
</body>
</html>
