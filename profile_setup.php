<?php
// 1. SETUP: Include all necessary configuration and class files.
require_once 'php/config.php';
require_once ROOT_PATH . '/php/user_auth_check.php';

// Redirect user if not logged in.
if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Include all required classes.
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/user.php';
require_once ROOT_PATH . '/app/models/EthnicGroupManager.php';
require_once ROOT_PATH . '/app/models/StateManager.php';

// 2. INITIALIZATION: Create the database connection and manager objects.
$database = new Database();
$db = $database->connect();

if (!$db) {
    // Display a user-friendly error if the database connection fails.
    echo "A database error occurred. Please try again later.";
    exit;
}

// Initialize User and fetch their profile data.
$user = new User($db);
$user_id = $_SESSION['user_id'];
$profileData = $user->getProfile($user_id);

// Initialize managers for dropdown data.
$ethnicManager = new EthnicGroupManager($db);
$stateManager = new StateManager($db);

// 3. DATA FETCHING: Get the lists for the dropdowns.
$ethnic_groups_by_category = $ethnicManager->getAll(true);
$states = $stateManager->getAll(); // Use the refactored getAll() method
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
                        <label for="ethnic">Ethnic</label>
                        <select id="ethnic" name="ethnic" class="form-control">
                            <option value="">-- Please select --</option>
                            <?php
                            foreach ($ethnic_groups_by_category as $category => $ethnics) {
                                echo '<optgroup label="' . htmlspecialchars($category) . '">';
                                foreach ($ethnics as $ethnic_group) {
                                    $selected = (isset($profileData['ethnic']) && $profileData['ethnic'] == $ethnic_group->name) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($ethnic_group->name) . '" ' . $selected . '>' . htmlspecialchars($ethnic_group->name) . '</option>';
                                }
                                echo '</optgroup>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday" class="form-control" value="<?php echo htmlspecialchars($profileData['birthday'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address1">Address 1</label>
                    <input type="text" id="address1" name="address1" class="form-control" value="<?php echo htmlspecialchars($profileData['address1'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address2">Address 2</label>
                    <input type="text" id="address2" name="address2" class="form-control" value="<?php echo htmlspecialchars($profileData['address2'] ?? ''); ?>">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="area">Area</label>
                        <input type="text" id="area" name="area" class="form-control" value="<?php echo htmlspecialchars($profileData['area'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($profileData['postal_code'] ?? ''); ?>">
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
                formData.append('action', 'update_personal_info');

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
                formData.append('action', 'update_employment_info');

                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert('Profile setup complete!');
                    window.location.href = 'dashboard'; // Redirect to homepage
                } else {
                    alert(result.message || 'An error occurred.');
                }
            });
        });
    </script>
</body>

</html>