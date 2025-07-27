// --- Element Selections ---\
if (typeof personalDetailsForm === 'undefined') {
    const personalDetailsForm = document.getElementById('personal-details-form');
    const employmentDetailsForm = document.getElementById('employment-details-form');
    const profilePicInput = document.getElementById('profile-pic-input');
    const changePicButton = document.getElementById('change-pic-btn');
    const feedbackMessage = document.getElementById('feedback-message');
    const isCurrentCheckbox = document.getElementById('is_current');
    const endDateGroup = document.getElementById('end-date-group');
    const passwordChangeForm = document.getElementById('password-change-form');
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const deleteModal = document.getElementById('delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const deleteAccountForm = document.getElementById('delete-account-form');

    const tabs = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    const API_ENDPOINT = 'api/profile_actions.php';
    const USER_API_ENDPOINT = 'api/user_actions.php';

    // --- Tab Handling ---
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(item => item.classList.remove('active'));
            tab.classList.add('active');

            const target = document.getElementById(tab.dataset.tab);
            tabContents.forEach(content => content.classList.remove('active'));
            target.classList.add('active');
        });
    });

    function showMessage(message, type) {
        feedbackMessage.textContent = message;
        feedbackMessage.className = `message ${type}`;
        feedbackMessage.style.display = 'block';
        window.scrollTo(0, 0);
        setTimeout(() => {
            feedbackMessage.style.display = 'none';
        }, 5000);
    }

    // --- Handle Personal Info Update ---
    personalDetailsForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const formData = new FormData(this);
        formData.append('action', 'update_personal_info');

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
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    // --- Handle Employment Info Update ---
    employmentDetailsForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const formData = new FormData(this);
        formData.append('action', 'update_employment_info');

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
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    // --- Handle Profile Picture Update ---
    profilePicInput.addEventListener('change', async function () {
        if (this.files.length === 0) return;

        changePicButton.disabled = true;
        changePicButton.textContent = 'Uploading...';

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
                const newUrl = '<?php echo BASE_URL; ?>/' + result.new_pic_url + '?' + new Date().getTime();
                document.getElementById('profile-pic-img').src = newUrl;
            }
            showMessage(result.message, result.status);
        } catch (error) {
            showMessage('An unexpected error occurred.', 'error');
        } finally {
            changePicButton.disabled = false;
            changePicButton.textContent = 'Change Picture';
        }
    });

    // --- Handle Password Change ---
    passwordChangeForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            showMessage('New passwords do not match.', 'error');
            return;
        }

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Updating...';

        const formData = new FormData(this);
        formData.append('action', 'change_password');

        try {
            const response = await fetch(API_ENDPOINT, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                this.reset();
            }
            showMessage(result.message, result.status);
        } catch (error) {
            showMessage('An unexpected error occurred.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    // --- Delete Account Modal Handling ---
    deleteAccountBtn.addEventListener('click', () => {
        deleteModal.style.display = 'flex';
    });
    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });
    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });

    deleteAccountForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const password = document.getElementById('delete-password').value;
        const deleteBtn = this.querySelector('button[type="submit"]');
        deleteBtn.disabled = true;
        deleteBtn.textContent = 'Deleting...';

        try {
            const response = await fetch(USER_API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'delete_account',
                    password: password
                })
            });
            const result = await response.json();
            if (result.status === 'success') {
                alert('Account deleted successfully. You will now be logged out.');
                window.location.href = '<?php echo BASE_URL; ?>/index.php';
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('An unexpected error occurred.', 'error');
        } finally {
            deleteBtn.disabled = false;
            deleteBtn.textContent = 'Delete Account';
        }
    });

    // --- Toggle End Date Field ---
    function toggleEndDate() {
        endDateGroup.style.display = isCurrentCheckbox.checked ? 'none' : 'block';
    }
    isCurrentCheckbox.addEventListener('change', toggleEndDate);
    toggleEndDate();
}