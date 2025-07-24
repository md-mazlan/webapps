<?php
// Use the centralized user authentication check.
require_once 'php/user_auth_check.php';

// If a user is not logged in, redirect them to the login page.
if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Include necessary classes.
require_once 'php/database.php';
require_once 'php/user.php';

// Initialize objects and fetch the user's profile data.
$database = new Database();
$db = $database->connect();
$user = new User($db);
$user_id = $_SESSION['user_id'];
$profileData = $user->getProfile($user_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
        }

        .navbar {
            background-color: var(--card-bg-color);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px var(--shadow-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            align-items: flex-start;
        }

        .card {
            background-color: var(--card-bg-color);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-top: 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .profile-pic-container {
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
            margin-bottom: 1rem;
        }

        .profile-pic-container .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--subtle-text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .btn {
            padding: 0.65rem 1.25rem;
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

        .btn:disabled,
        .btn.loading {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="index.php">My Website</a>
        <div class="nav-links">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="profile.php">My Profile</a>
            <a href="api/user_logout.php">Logout</a>
        </div>
    </nav>

    <main class="page-wrapper">
        <div id="feedback-message" class="message" style="display: none;"></div>
        <div class="profile-grid">
            <!-- Left Column: Profile Picture -->
            <div class="card profile-pic-container">
                <img id="profile-pic-img" src="<?php echo htmlspecialchars($profileData['profile_pic_url'] ?? 'https://placehold.co/150x150/e0e7ff/3730a3?text=User'); ?>" alt="Profile Picture" class="profile-pic">
                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($profileData['username']); ?></h3>
                <p style="color: var(--subtle-text-color); margin-top: 0;"><?php echo htmlspecialchars($profileData['email']); ?></p>
                <form id="profile-pic-form">
                    <input type="file" name="profile_pic" id="profile-pic-input" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-secondary" id="change-pic-btn" onclick="document.getElementById('profile-pic-input').click();">Change Picture</button>
                </form>
            </div>

            <!-- Right Column: Profile Details -->
            <div class="card">
                <h2 class="card-title">Edit Profile</h2>
                <form id="profile-details-form">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($profileData['full_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4" class="form-control"><?php echo htmlspecialchars($profileData['bio'] ?? ''); ?></textarea>
                    </div>

                    <h3 class="card-title" style="margin-top: 2rem;">Employment Details</h3>
                    <div class="form-group">
                        <label for="job_title">Job Title</label>
                        <input type="text" id="job_title" name="job_title" class="form-control" value="<?php echo htmlspecialchars($profileData['job_title'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" id="company" name="company" class="form-control" value="<?php echo htmlspecialchars($profileData['company'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($profileData['start_date'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" id="is_current" name="is_current" <?php echo ($profileData['is_current'] ?? 0) ? 'checked' : ''; ?>> I currently work here</label>
                    </div>
                    <div class="form-group" id="end-date-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($profileData['end_date'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileDetailsForm = document.getElementById('profile-details-form');
            const saveButton = profileDetailsForm.querySelector('button[type="submit"]');
            const profilePicInput = document.getElementById('profile-pic-input');
            const changePicButton = document.getElementById('change-pic-btn');
            const feedbackMessage = document.getElementById('feedback-message');
            const isCurrentCheckbox = document.getElementById('is_current');
            const endDateGroup = document.getElementById('end-date-group');

            const API_ENDPOINT = 'api/profile_actions.php';

            function showMessage(message, type) {
                feedbackMessage.textContent = message;
                feedbackMessage.className = `message ${type}`;
                feedbackMessage.style.display = 'block';
                window.scrollTo(0, 0); // Scroll to top to make message visible
                setTimeout(() => {
                    feedbackMessage.style.display = 'none';
                }, 5000);
            }

            // --- Handle Text Details Update ---
            profileDetailsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
                saveButton.classList.add('loading');

                const formData = new FormData(this);
                formData.append('action', 'update_profile_text');

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Changes';
                    saveButton.classList.remove('loading');
                }
            });

            // --- Handle Profile Picture Update ---
            profilePicInput.addEventListener('change', async function() {
                if (this.files.length === 0) return;

                changePicButton.disabled = true;
                changePicButton.textContent = 'Uploading...';
                changePicButton.classList.add('loading');

                const formData = new FormData();
                formData.append('action', 'update_profile_picture');
                formData.append('profile_pic', this.files[0]);

                try {
                    const response = await fetch(API_ENDPOINT, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success' && result.new_pic_url) {
                        document.getElementById('profile-pic-img').src = result.new_pic_url + '?' + new Date().getTime(); // Add cache buster
                    }
                    showMessage(result.message, result.status);
                } catch (error) {
                    showMessage('An unexpected error occurred.', 'error');
                } finally {
                    changePicButton.disabled = false;
                    changePicButton.textContent = 'Change Picture';
                    changePicButton.classList.remove('loading');
                }
            });

            // --- Toggle End Date Field ---
            function toggleEndDate() {
                endDateGroup.style.display = isCurrentCheckbox.checked ? 'none' : 'block';
            }
            isCurrentCheckbox.addEventListener('change', toggleEndDate);
            toggleEndDate(); // Initial check on page load
        });
    </script>
</body>

</html>