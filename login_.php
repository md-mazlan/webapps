<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF4B2B;
            --secondary-color: #FF416C;
            --form-bg-color: #FFFFFF;
            --bg-color: #f6f5f7;
            --text-color: #333;
            --subtle-text-color: #555;
            --input-bg-color: #eee;
            --font-family: 'Montserrat', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: var(--text-color);
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        .container {
            background-color: var(--form-bg-color);
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        form {
            background-color: var(--form-bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: var(--input-bg-color);
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        button {
            border-radius: 20px;
            border: 1px solid var(--primary-color);
            background-color: var(--primary-color);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .overlay {
            background: #FF416C;
            background: -webkit-linear-gradient(to right, var(--secondary-color), var(--primary-color));
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        /* Animation */
        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {

            0%,
            49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%,
            100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .message {
            font-weight: 500;
            font-size: 12px;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .message:empty {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <!-- Sign Up -->
        <div class="form-container sign-up-container">
            <form id="registerForm">
                <h1>Create Account</h1>
                <div id="register-message" class="message"></div>
                <input type="text" id="register-nric" name="nric" placeholder="NRIC" required pattern="[0-9]*" title="NRIC must contain only numbers." />
                <input type="email" id="register-email" name="email" placeholder="Email" required />
                <input type="text" id="register-username" name="username" placeholder="Username (Optional)" />
                <input type="password" id="register-password" name="password" placeholder="Password" required />
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <!-- Sign In -->
        <div class="form-container sign-in-container">
            <form id="loginForm">
                <h1>Sign In</h1>
                <div id="login-message" class="message"></div>
                <input type="text" id="login-identifier" name="login_identifier" placeholder="Email or NRIC" required />
                <input type="password" id="login-password" name="password" placeholder="Password" required />
                <a href="#">Forgot your password?</a>
                <button type="submit">Sign In</button>
            </form>
        </div>
        <!-- Overlay -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const signUpButton = document.getElementById('signUp');
            const signInButton = document.getElementById('signIn');
            const container = document.getElementById('container');

            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const loginMessage = document.getElementById('login-message');
            const registerMessage = document.getElementById('register-message');

            const LOGIN_ENDPOINT = 'api/user_login.php';
            const REGISTER_ENDPOINT = 'api/user_register.php';

            // --- Animation Toggling ---
            signUpButton.addEventListener('click', () => container.classList.add('right-panel-active'));
            signInButton.addEventListener('click', () => container.classList.remove('right-panel-active'));

            // --- Form Submission Logic ---
            function displayMessage(element, message, type) {
                element.textContent = message;
                element.className = `message ${type}`;
            }

            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                // Note: "Remember Me" is not in this UI, so it's omitted.

                try {
                    const response = await fetch(LOGIN_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    console.log(response);
                    const result = await response.json();
                    if (response.ok && result.redirect) {
                        displayMessage(loginMessage, result.message, 'success');
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1000);
                    } else {
                        displayMessage(loginMessage, result.message || 'An error occurred.', 'error');
                    }
                } catch (error) {
                    displayMessage(loginMessage, error.message || 'An unexpected error occurred.', 'error');
                }
            });

            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                const nricInput = document.getElementById('register-nric');
                if (!/^[0-9]+$/.test(data.nric)) {
                    displayMessage(registerMessage, 'NRIC must contain only numbers.', 'error');
                    nricInput.focus();
                    return;
                }

                if (data.password.length < 6) {
                    displayMessage(registerMessage, 'Password must be at least 6 characters long.', 'error');
                    return;
                }

                try {
                    const response = await fetch(REGISTER_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (response.status === 201) {
                        displayMessage(registerMessage, 'Success! Please sign in.', 'success');
                        setTimeout(() => {
                            signInButton.click(); // Switch back to the sign-in panel
                            document.getElementById('login-identifier').value = data.email;
                            displayMessage(loginMessage, 'Registration successful! Please log in.', 'success');
                        }, 2000);
                    } else {
                        displayMessage(registerMessage, result.message || 'An error occurred.', 'error');
                    }
                } catch (error) {
                    displayMessage(registerMessage, 'An unexpected error occurred.', 'error');
                }
            });
        });
    </script>
</body>

</html>