// js/mkl_script.js
document.addEventListener('DOMContentLoaded', () => {
    const drawerToggle = document.getElementById('drawer-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (drawerToggle) {
        drawerToggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            } else {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });
    }

    if (overlay) {
        // Close sidebar when clicking on the overlay (for mobile)
        overlay.addEventListener('click', () => {
            if (drawerToggle) {
                drawerToggle.checked = false;
                drawerToggle.dispatchEvent(new Event('change'));
            }
        });
    }
});