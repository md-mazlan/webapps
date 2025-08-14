<?php
// payment_success.php
// Billplz callback for successful payment

// You may log the successful payment or process the callback data here if needed
// Example: $billplz_data = $_GET['billplz'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <meta http-equiv="refresh" content="10;url=payment_history.php">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #d4edda;
            color: #155724;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 2rem 3rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }
        h1 {
            color: #218838;
        }
        .redirect {
            margin-top: 1.5rem;
            color: #856404;
        }
    </style>
    <script>
        var seconds = 10;
        function updateTimer() {
            var timerElem = document.getElementById('timer');
            if (timerElem) timerElem.textContent = seconds;
            if (seconds <= 0) {
                window.location.href = 'payment_history.php';
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
        <h1>Payment Successful</h1>
        <p>Your payment was successful. Thank you!</p>
        <div class="redirect">
            You will be redirected to your payment history in <span id="timer">10</span> seconds.<br>
            <a href="payment_history.php">Click here if you are not redirected.</a>
        </div>
    </div>
</body>
</html>
<?php
// payment_failed.php
// Billplz callback for failed payment

// You may log the failed payment or process the callback data here if needed
// Example: $billplz_data = $_GET['billplz'] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <meta http-equiv="refresh" content="10;url=payment_history.php">
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
            window.location.href = '/payment_history';
        }, 10000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Payment Failed</h1>
        <p>Your payment was not successful or was cancelled.</p>
        <div class="redirect">
            You will be redirected to your payment history in <span id="timer">10</span> seconds.<br>
            <a href="payment_history">Click here if you are not redirected.</a>
            </div>
            <script>
                var seconds = 10;
                var timerElem = document.getElementById('timer');
                var countdown = setInterval(function() {
                    seconds--;
                    if (timerElem) timerElem.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        window.location.href = 'payment_history';
                    }
                }, 1000);
            </script>
        </div>
    </div>
</body>
</html>
