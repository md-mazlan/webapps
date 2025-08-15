<?php
require_once __DIR__ . '/php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <meta http-equiv="refresh" content="10;url=<?php echo BASE_URL; ?>/payment_history">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8d7da;
            color: #721c24;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 2rem 3rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }
        h1 {
            color: #c82333;
        }
        .redirect {
            margin-top: 1.5rem;
            color: #856404;
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = '<?php echo BASE_URL; ?>/payment_history';
        }, 10000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Payment Failed</h1>
        <p>Your payment was not successful or was cancelled.</p>
        <div class="redirect">
            You will be redirected to your payment history in <span id="timer">10</span> seconds.<br>
            <a href="<?php echo BASE_URL; ?>/payment_history">Click here if you are not redirected.</a>
            </div>
            <script>
                var seconds = 10;
                var timerElem = document.getElementById('timer');
                var countdown = setInterval(function() {
                    seconds--;
                    if (timerElem) timerElem.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        window.location.href = '<?php echo BASE_URL; ?>/payment_history';
                    }
                }, 1000);
            </script>
        </div>
    </div>
</body>
</html>
