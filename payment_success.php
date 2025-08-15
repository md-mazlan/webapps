
<?php
require_once __DIR__ . '/php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <meta http-equiv="refresh" content="10;url=<?php echo BASE_URL; ?>/payment_history">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f8fafc;
            color: #222;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 4px 32px rgba(37,99,235,0.10);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            text-align: center;
            min-width: 320px;
            max-width: 95vw;
        }
        .success-icon {
            width: 70px;
            height: 70px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 2px 12px rgba(37,99,235,0.12);
        }
        .success-icon svg {
            width: 38px;
            height: 38px;
            color: #fff;
        }
        .success-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }
        .success-message {
            font-size: 1.1rem;
            color: #222;
            margin-bottom: 1.5rem;
        }
        .redirect {
            margin-top: 1.5rem;
            color: #6b7280;
            font-size: 0.98rem;
        }
        .redirect a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
    <script>
        var seconds = 10;
        function updateTimer() {
            var timerElem = document.getElementById('timer');
            if (timerElem) timerElem.textContent = seconds;
            if (seconds <= 0) {
                window.location.href = '<?php echo BASE_URL; ?>/payment_history';
            } else {
                seconds--;
                setTimeout(updateTimer, 1000);
            }
        }
        window.onload = updateTimer;
    </script>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.08"/>
                <path d="M7 13.5L11 17L17 9.5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="success-title">Payment Successful</div>
        <div class="success-message">Your payment was successful. Thank you!</div>
        <div class="redirect">
            You will be redirected to your payment history in <span id="timer">10</span> seconds.<br>
            <a href="<?php echo BASE_URL; ?>/payment_history">Click here if you are not redirected.</a>
        </div>
    </div>
</body>
</html>
