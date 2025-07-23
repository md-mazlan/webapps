<?php
// This is the auth check for ADMINISTRATORS.
// It uses the correct path to the file in the /php/ folder.
require_once '../php/admin_auth_check.php';

// If an admin is already logged in, redirect them to the admin dashboard.
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login & Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover-color: #1d4ed8;
            --bg-color: #f3f4f6;
            --success-color: #16a34a;
            --success-hover-color: #15803d;
            --form-bg-color: #ffffff;
            --text-color: #1f2937;
            --label-color: #374151;
            --placeholder-color: #6b7280;
            --border-color: #d1d5db;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --font-family: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .container {
            width: 100%;
            max-width: 448px;
        }

        .form-container {
            background-color: var(--form-bg-color);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px var(--shadow-color), 0 4px 6px -2px var(--shadow-color);
            animation: fadeIn 0.5s ease-in-out;
        }

        .form-container.hidden {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--text-color);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            color: var(--label-color);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease-in-out;
            font-size: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.4);
        }

        .btn {
            width: 100%;
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: var(--success-hover-color);
        }

        .message {
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .message.hidden {
            display: none;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .remember-me label {
            color: var(--label-color);
            font-size: 0.875rem;
        }

        .remember-me input {
            margin-right: 0.5rem;
        }

        .form-footer {
            text-align: center;
            color: var(--placeholder-color);
            margin-top: 1.5rem;
        }

        .form-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .form-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Login Form -->
        <div id="login-form" class="form-container">
            <h2 class="form-title">Admin Login</h2>
            <div id="login-message" class="message hidden"></div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="login-username" class="form-label">Username</label>
                    <input type="text" id="login-username" name="username" required class="form-input">
                </div>
                <div class="form-group">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" id="login-password" name="password" required class="form-input">
                </div>
                <div class="remember-me">
                    <label>
                        <input type="checkbox" name="remember_me" id="remember-me"> Remember me
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="form-footer">
                Need an account? <a href="#" id="show-register" class="form-link">Register here</a>
            </p>
        </div>

        <!-- Registration Form -->
        <div id="register-form" class="form-container hidden">
            <h2 class="form-title">Admin Register</h2>
            <div id="register-message" class="message hidden"></div>
            <form id="registerForm">
                <div class="form-group">
                    <label for="register-username" class="form-label">Username</label>
                    <input type="text" id="register-username" name="username" required class="form-input">
                </div>
                <div class="form-group">
                    <label for="register-email" class="form-label">Email</label>
                    <input type="email" id="register-email" name="email" required class="form-input">
                </div>
                <div class="form-group">
                    <label for="register-password" class="form-label">Password</label>
                    <input type="password" id="register-password" name="password" required class="form-input">
                </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>
            <p class="form-footer">
                Already have an account? <a href="#" id="show-login" class="form-link">Login here</a>
            </p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginFormContainer = document.getElementById('login-form');
            const registerFormContainer = document.getElementById('register-form');
            const showRegisterLink = document.getElementById('show-register');
            const showLoginLink = document.getElementById('show-login');

            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            const loginMessage = document.getElementById('login-message');
            const registerMessage = document.getElementById('register-message');

            // API endpoints are in the /api/ folder.
            const LOGIN_ENDPOINT = '../api/admin_login.php';
            const REGISTER_ENDPOINT = '../api/admin_register.php';

            // --- Form Toggling ---
            showRegisterLink.addEventListener('click', (e) => {
                e.preventDefault();
                loginFormContainer.classList.add('hidden');
                registerFormContainer.classList.remove('hidden');
            });
            showLoginLink.addEventListener('click', (e) => {
                e.preventDefault();
                registerFormContainer.classList.add('hidden');
                loginFormContainer.classList.remove('hidden');
            });

            // --- Login Form Submission ---
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                data.remember_me = document.getElementById('remember-me').checked;

                try {
                    const response = await fetch(LOGIN_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (response.ok && result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        displayMessage(loginMessage, result.message || 'An error occurred.', 'error');
                    }
                } catch (error) {
                    displayMessage(loginMessage, 'An unexpected error occurred.', 'error');
                }
            });

            // --- Registration Form Submission ---
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
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
                        displayMessage(registerMessage, result.message, 'success');
                        setTimeout(() => {
                            registerForm.reset();
                            showLoginLink.click();
                            loginMessage.classList.add('hidden');
                            displayMessage(loginMessage, 'Registration successful! Please log in.', 'success');
                        }, 2000);
                    } else {
                        displayMessage(registerMessage, result.message || 'An error occurred.', 'error');
                    }
                } catch (error) {
                    displayMessage(registerMessage, 'An unexpected error occurred.', 'error');
                }
            });

            function displayMessage(element, message, type) {
                element.textContent = message;
                element.className = `message ${type}`;
                element.classList.remove('hidden');
                setTimeout(() => {
                    element.classList.add('hidden');
                }, 5000);
            }
        });
    </script>
</body>

</html>