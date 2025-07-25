<?php
// This is the new user onboarding page.
require_once 'php/config.php';
require_once ROOT_PATH . '/php/user_auth_check.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9fafb;
            --text-color: #111827;
            --subtle-text-color: #6b7280;
            --border-color: #e5e7eb;
            --card-bg-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --primary-color: #2563eb;
            --font-sans: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .wizard-container {
            width: 100%;
            max-width: 600px;
            background-color: var(--card-bg-color);
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px var(--shadow-color);
            padding: 2rem;
        }

        .wizard-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .wizard-step {
            display: none;
        }

        .wizard-step.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .wizard-footer {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-color);
        }
    </style>
</head>

<body>
    <div class="wizard-container">
        <div class="wizard-header">
            <h1>Welcome! Let's set up your profile.</h1>
            <p>Please complete the following steps to get started.</p>
        </div>

        <div id="step1" class="wizard-step active">
            <h2>Step 1: Personal Information</h2>
            <form id="personal-form">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday" class="form-control">
                    </div>
                </div>
                <div class="wizard-footer">
                    <button type="submit" class="btn btn-primary">Next &rarr;</button>
                </div>
            </form>
        </div>

        <div id="step2" class="wizard-step">
            <h2>Step 2: Employment Details</h2>
            <form id="employment-form">
                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" class="form-control">
                </div>
                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" id="job_title" name="job_title" class="form-control">
                </div>
                <div class="wizard-footer">
                    <button type="button" class="btn btn-secondary" data-prev-step="1">&larr; Previous</button>
                    <button type="submit" class="btn btn-primary">Finish &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.wizard-step');
            const personalForm = document.getElementById('personal-form');
            const employmentForm = document.getElementById('employment-form');
            const prevButton = document.querySelector('[data-prev-step]');

            const API_ENDPOINT = '<?php echo BASE_URL; ?>/api/profile_actions.php';

            function goToStep(stepNumber) {
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step${stepNumber}`).classList.add('active');
            }

            prevButton.addEventListener('click', () => goToStep(1));

            personalForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'insert_personal_info');

                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    goToStep(2);
                } else {
                    alert(result.message || 'An error occurred.');
                }
            });

            employmentForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'insert_employment_info');

                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert('Profile setup complete!');
                    window.location.href = 'index.php'; // Redirect to homepage
                } else {
                    alert(result.message || 'An error occurred.');
                }
            });
        });
    </script>
</body>

</html>