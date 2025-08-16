<?php
// Include the new configuration file first.
require_once 'php/config.php';
// Also check if an admin is logged in and block access
require_once 'php/admin_auth_check.php';
if (isAdminLoggedIn()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}


if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang_code = $_SESSION['lang'] ?? 'my';

require_once ROOT_PATH . "/lang/lang_{$lang_code}.php";

// Use the centralized user authentication check with an absolute path.
require_once ROOT_PATH . '/php/user_auth_check.php';

// If a user is not logged in, redirect them to the login page.
if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --body-bg: #f6f5f7;
            --container-bg: #fff;
            --form-bg: #fff;
            --input-bg: #eee;
            --text-color: #000;
            --social-border: #DDDDDD;
            --social-icon-color: #333;
            --link-color: #333;
            --forgot-password-color: #333;
        }

        body.dark {
            --body-bg: #222831;
            --container-bg: #393E46;
            --form-bg: #393E46;
            --input-bg: #4a4e57;
            --text-color: #EEEEEE;
            --social-border: #555;
            --social-icon-color: #EEEEEE;
            --link-color: #8ab4f8;
            --forgot-password-color: #ccc;
        }

        /* Base Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--body-bg);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }

        h1 {
            font-weight: bold;
            margin: 0;
            font-size: 2rem;
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
            color: var(--forgot-password-color);
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid #002b7f;
            background-color: #002b7f;
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

        form {
            background-color: var(--form-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: var(--input-bg);
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 5px;
            color: var(--text-color);
        }

        input::placeholder {
            color: var(--text-color);
            opacity: 0.7;
        }

        /* Main Container */
        .container {
            background-color: var(--container-bg);
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 768px;
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

        /* Overlay Styles */
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
            background: #0052cc;
            background: -webkit-linear-gradient(to right, #002b7f, #0052cc);
            background: linear-gradient(to right, #002b7f, #0052cc);
            background-repeat: no-repeat;
            background-size: 200% 200%;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
            animation: moveGradient 7s ease infinite;
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

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
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

        @keyframes moveGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Social Icons */
        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid var(--social-border);
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
            color: var(--social-icon-color);
        }

        body.dark .social-container a:hover {
            background-color: #4a4e57;
        }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 200;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #002b7f;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Message Box */
        #message-box {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            transition: opacity 0.3s;
        }

        #message-box.hidden {
            display: none;
        }

        .message-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message-error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .overlay-container {
                display: none;
            }

            .form-container {
                width: 100%;
                position: static;
                height: auto;
                padding: 2rem 1rem;
            }

            .sign-in-container {
                display: block;
            }

            .sign-up-container {
                display: none;
            }

            .container.right-panel-active .sign-in-container {
                display: none;
            }

            .container.right-panel-active .sign-up-container {
                display: block;
                opacity: 1;
                transform: none;
                animation: none;
            }

            .mobile-switch {
                display: inline-block;
                margin-top: 1rem;
                color: var(--link-color);
            }
        }

        @media (min-width: 769px) {
            .mobile-switch {
                display: none;
            }

            .container {
                min-height: 580px;
            }
        }
    </style>
</head>

<body>

    <div class="dark-mode-toggle">
        <label class="toggle-switch">
            <input type="checkbox" id="dark-mode-checkbox">
            <span class="slider"></span>
        </label>
    </div>

    <div id="main-container" class="container">
        <!-- Sign Up Container -->
        <div class="form-container sign-up-container">
            <form id="register-form">
                <h1>Create Account</h1>
                <div id="register-message" class="message"></div>
                <input type="text" id="register-nric" name="nric" placeholder="NRIC" required pattern="[0-9]*" title="NRIC must contain only numbers." />
                <input type="email" id="register-email" name="email" placeholder="Email" required />
                <input type="text" id="register-username" name="username" placeholder="Username (Optional)" />
                <input name="password" type="password" placeholder="Password" required />
                <input name="retype-password" type="password" placeholder="Retype Password" required />
                <button type="submit">Sign Up</button>
                <a href="#" id="mobile-signIn" class="mobile-switch">Already have an account? Sign In</a>
            </form>
        </div>
        <!-- Sign In Container -->
        <div class="form-container sign-in-container">
            <form id="login-form">
                <h1>Sign in</h1>
                <div id="login-message" class="message"></div>
                <input type="text" id="login-identifier" name="login_identifier" placeholder="Email or NRIC" required />
                <input type="password" id="login-password" name="password" placeholder="Password" required />
                <a href="#">Forgot your password?</a>
                <button type="submit">Sign In</button>
                <a href="#" id="mobile-signUp" class="mobile-switch">Don't have an account? Sign Up</a>
            </form>
        </div>
        <!-- Overlay Container -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button id="signIn" class="ghost">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button id="signUp" class="ghost">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <div id="message-box" class="hidden">
        <p id="message-text"></p>
    </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const mobileSignUpButton = document.getElementById('mobile-signUp');
        const mobileSignInButton = document.getElementById('mobile-signIn');
        const container = document.getElementById('main-container');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const messageBox = document.getElementById('message-box');
        const messageText = document.getElementById('message-text');
        const darkModeToggle = document.getElementById('dark-mode-checkbox');



        const LOGIN_ENDPOINT = 'api/user_login.php';
        const REGISTER_ENDPOINT = 'api/user_register.php';

        // Dark Mode Logic
        const enableDarkMode = () => {
            document.body.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        };
        const disableDarkMode = () => {
            document.body.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        };
        if (localStorage.getItem('darkMode') === 'true') {
            enableDarkMode();
            darkModeToggle.checked = true;
        }
        darkModeToggle.addEventListener('change', () => {
            if (darkModeToggle.checked) enableDarkMode();
            else disableDarkMode();
        });

        // Panel Switching Logic
        if (signUpButton) {
            signUpButton.addEventListener('click', () => container.classList.add("right-panel-active"));
        }
        if (signInButton) {
            signInButton.addEventListener('click', () => container.classList.remove("right-panel-active"));
        }
        mobileSignUpButton.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add("right-panel-active");
        });
        mobileSignInButton.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove("right-panel-active");
        });

        function handleMobileSwitch() {
            const loginFormEl = document.querySelector('.sign-in-container');
            const registerFormEl = document.querySelector('.sign-up-container');
            if (window.innerWidth < 768) {
                if (container.classList.contains('right-panel-active')) {
                    loginFormEl.style.display = 'none';
                    registerFormEl.style.display = 'block';
                } else {
                    loginFormEl.style.display = 'block';
                    registerFormEl.style.display = 'none';
                }
            } else {
                loginFormEl.style.display = '';
                registerFormEl.style.display = '';
            }
        }

        // Use a MutationObserver to detect class changes for mobile view
        if (container) {
            new MutationObserver(handleMobileSwitch).observe(container, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
        window.addEventListener('resize', handleMobileSwitch);
        handleMobileSwitch(); // Initial check


        // Message Box Logic
        function showMessage(message, isError = false) {
            messageText.textContent = message;
            messageBox.className = isError ? 'message-error' : 'message-success';
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3000);
        }

        // AJAX Form Submission Logic

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
                    showMessage(result.message);
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showMessage(result.message || 'An error occurred.', true);
                }
            } catch (error) {
                showMessage(error.message || 'An unexpected error occurred.', true);
            }
        });
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data['nric'] = data['nric'].replace(/\D/g, ''); // Ensure NRIC contains only numbers
            const nricInput = document.getElementById('register-nric');
            if (!/^[0-9]+$/.test(data.nric)) {
                showMessage('NRIC must contain only numbers.', true);
                nricInput.focus();
                return;
            }

            if (data.password.length < 6) {
                showMessage('Password must be at least 6 characters long.', true);
                return;
            }

            let jsonData = [];
            try {
                jsonData = JSON.stringify(data);
            } catch (error) {
                console.error('Error stringifying data:', error);
            }
            // Use XMLHttpRequest instead of fetch
            try {
                console.log({
                    data: jsonData
                });
                var xhr = new XMLHttpRequest();
                xhr.open('POST', REGISTER_ENDPOINT, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        let result = {};
                        console.log(xhr.responseText);
                        try {
                            result = JSON.parse(xhr.responseText);
                        } catch (err) {
                            result = {
                                message: 'Invalid server response.'
                            };
                        }
                        if (xhr.status === 201) {
                            showMessage('Success! Please sign in.');
                            setTimeout(function() {
                                signInButton.click(); // Switch back to the sign-in panel
                                document.getElementById('login-identifier').value = data.nric;
                                document.getElementById('login-password').focus(); // Focus on password field
                                showMessage('Registration successful! Please log in.');
                            }, 2000);
                        } else {
                            showMessage(result.message || 'An error occurred.', true);
                        }
                    }
                };
                xhr.onerror = function() {
                    showMessage('Network error occurred.', true);
                };
                xhr.send(JSON.stringify(data));
            } catch (error) {
                console.error('Submission error:', error);
                showMessage(error.message || error || 'An unexpected error occurred.', true);
            }
        });
    </script>
</body>

</html>