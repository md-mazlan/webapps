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
require_once ROOT_PATH . '/app/controllers/EthnicGroupController.php';
require_once ROOT_PATH . '/app/controllers/StateController.php';
require_once ROOT_PATH . '/app/controllers/DunSeatController.php';

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
$ethnicManager = new EthnicGroupController($db);
$stateManager = new StateController($db);
$dunSeatManager = new DunSeatController($db);

// 3. DATA FETCHING: Get the lists for the dropdowns.
$ethnic_groups_by_category = $ethnicManager->getAll(true);
$states = $stateManager->getAll(); // Use the refactored getAll() method
$dunSeats = $dunSeatManager->getAll(); // Fetch all DUN seats
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
                    <input type="text" id="full_name" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nric">NRIC</label>
                    <input type="text" id="nric" name="nric" class="form-control" value="<?php echo $profileData['nric'] ?>" readonly disabled>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="m" <?php echo (isset($profileData['gender']) && $profileData['gender'] == 'm') ? 'selected' : ''; ?>>Male</option>
                            <option value="f" <?php echo (isset($profileData['gender']) && $profileData['gender'] == 'f') ? 'selected' : ''; ?>>Female</option>
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
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday" class="form-control" value="<?php echo htmlspecialchars($profileData['birthday'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">No Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($profileData['email'] ?? ''); ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label for="address1">Latest Address</label>
                    <textarea type="text" id="address1" name="address1" class="form-control" style="resize: vertical;"><?php echo htmlspecialchars($profileData['address1'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="voting_area">Voting Area</label>
                    <select id="voting_area" name="voting_area" class="form-control">
                        <option value="">-- Please select --</option>
                        <?php foreach ($dunSeats as $dunSeat): ?>
                            <option value="<?php echo htmlspecialchars($dunSeat->code); ?>" <?php echo (isset($profileData['voting_area']) && $profileData['voting_area'] == $dunSeat->code) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dunSeat->code . " " . $dunSeat->seat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h2>Membership</h2>
                <div class="form-group">
                    <label for="service_area">Service Area</label>
                    <select id="service_area" name="service_area" class="form-control">
                        <option value="">-- Please select --</option>
                        <?php foreach ($dunSeats as $dunSeat): ?>
                            <option value="<?php echo htmlspecialchars($dunSeat->code); ?>" <?php echo (isset($profileData['service_area']) && $profileData['service_area'] == $dunSeat->code) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dunSeat->code . " " . $dunSeat->seat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vest_size">Vest Size</label>
                    <select id="vest_size" name="vest_size" class="form-control">
                        <option value="">-- Please select --</option>
                        <option value="M" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'M') ? 'selected' : ''; ?>>M</option>
                        <option value="L" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'L') ? 'selected' : ''; ?>>L</option>
                        <option value="XL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XL') ? 'selected' : ''; ?>>XL</option>
                        <option value="XXL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XXL') ? 'selected' : ''; ?>>XXL</option>
                        <option value="XXXL" <?php echo (isset($profileData['vest_size']) && $profileData['vest_size'] == 'XXXL') ? 'selected' : ''; ?>>XXXL</option>
                    </select>
                </div>
                <div class="wizard-footer" style="justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">Next &rarr;</button>
                </div>
            </form>
        </div>

        <div id="step2" class="wizard-step">
            <h2>Step 2: Employment Details</h2>
            <form id="employment-form">
                <div class="form-group">
                    <label for="employment">Employment</label>
                    <select id="employment" name="employment" class="form-control">
                        <option value="">-- Please select --</option>
                        <option value="Public" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Public') ? 'selected' : ''; ?>>Public</option>
                        <option value="Private" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Private') ? 'selected' : ''; ?>>Private</option>
                        <option value="Business" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                        <option value="Unemployment" <?php echo (isset($profileData['employment']) && $profileData['employment'] == 'Unemployment') ? 'selected' : ''; ?>>Unemployment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" id="position" name="position" class="form-control" value="<?php echo htmlspecialchars($profileData['position'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="employer_name">Employer Name</label>
                    <input type="text" id="employer_name" name="employer_name" class="form-control" value="<?php echo htmlspecialchars($profileData['employer_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="company_address">Company Address</label>
                    <input type="text" id="company_address" name="company_address" class="form-control" value="<?php echo htmlspecialchars($profileData['company_address'] ?? ''); ?>">
                </div>
                <div class="wizard-footer">
                    <button type="button" class="btn btn-secondary" data-prev-step="1">&larr; Previous</button>
                    <button type="submit" class="btn btn-primary">Next &rarr;</button>
                </div>
            </form>
        </div>
        <div id="step3" class="wizard-step">
            <h2>Step 3: EKYC Verification</h2>
            <form id="ekyc-form">
                <div class="form-group">
                    <label for="profile_pic">Upload Profile Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="ic_front">Upload IC (Front)</label>
                    <input type="file" id="ic_front" name="ic_front" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="ic_back">Upload IC (Back)</label>
                    <input type="file" id="ic_back" name="ic_back" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>EKYC Verification</label>
                    <input type="checkbox" id="ekyc_verified" name="ekyc_verified"> I have completed EKYC verification
                </div>
                <div class="wizard-footer">
                    <button type="button" class="btn btn-secondary" data-prev-step="2">&larr; Previous</button>
                    <button type="submit" class="btn btn-primary">Next &rarr;</button>
                </div>
            </form>
        </div>
        <div id="step4" class="wizard-step">
            <h2>Step 4: Complete</h2>
            <div class="form-group">
                <label>All steps are complete. Please proceed to payment to finish your registration.</label>
            </div>
            <div class="form-group" style="display: flex; justify-content: center;">
                <form method="post" action="billplzpost.php" style="margin: 0;">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($profileData['email'] ?? ''); ?>">
                    <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>">
                    <input type="hidden" name="amount" value="100"> <!-- RM1.00 in cents -->
                    <input type="hidden" name="description" value="Membership Payment">
                    <input type="submit" class="btn btn-primary" value="Pay with Billplz">
                </form>
            </div>
            <div class="wizard-footer">
                <button type="button" class="btn btn-secondary" data-prev-step="3" style="margin-right:auto;">&larr; Previous</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.wizard-step');
            const personalForm = document.getElementById('personal-form');
            const employmentForm = document.getElementById('employment-form');
            const ekycForm = document.getElementById('ekyc-form');
            const prevButtons = document.querySelectorAll('[data-prev-step]');

            const API_ENDPOINT = '<?php echo BASE_URL; ?>/api/profile_actions.php';

            function goToStep(stepNumber) {
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step${stepNumber}`).classList.add('active');
            }

            // Handle all previous buttons
            prevButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const prevStep = this.getAttribute('data-prev-step');
                    goToStep(prevStep);
                });
            });

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
                    goToStep(3);
                } else {
                    alert(result.message || 'An error occurred.');
                }
            });

            if (ekycForm) {
                ekycForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    formData.append('action', 'update_ekyc_info');

                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        goToStep(4);
                    } else {
                        alert(result.message || 'An error occurred.');
                    }
                });
            }
        });
    </script>
</body>

</html>