const drawerToggle = document.getElementById('drawer-toggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

let touchStartX = 0;
let touchEndX = 0;

drawerToggle.addEventListener('change', (e) => {
    if (e.target.checked) {
        openSidebar();
    } else {
        closeSidebar();
    }
});
// // Function to toggle sidebar for mobile
// const toggleSidebar = () => {
//     if (window.innerWidth <= 768) { // Mobile view
//         if(sidebar.classList.contains('open')) {
//             closeSidebar();
//         }
//         else {
//             openSidebar();
//         }
//     }else { // Desktop view
//         sidebar.classList.add('open');
//         overlay.classList.add('active');
//     }
// };

const openSidebar = () => {
    sidebar.classList.add('open');
    if (window.innerWidth > 768) {
        overlay.classList.remove('active');
    } else {
        overlay.classList.add('active');
    }
};
const closeSidebar = () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
}

// Add event listener for the drawer toggle button
// drawerToggle.addEventListener('click', toggleSidebar);

// Close sidebar when clicking on the overlay (mobile only)
overlay.addEventListener('click', () => {
    drawerToggle.checked = false; // Uncheck the toggle
    drawerToggle.dispatchEvent(new Event('change')); // Trigger listener
});

// Ensure sidebar is always visible on desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        openSidebar();
    } else {
        closeSidebar();
    }
});

// // Detect swipe gesture to open sidebar
// document.addEventListener('touchstart', (e) => {
//     touchStartX = e.touches[0].clientX;
// });

// document.addEventListener('touchmove', (e) => {
//     touchEndX = e.touches[0].clientX;
// });

// document.addEventListener('touchend', () => {
//     // Swipe right to open sidebar
//     if (touchStartX < 50 && touchEndX > touchStartX + 50 && window.innerWidth <= 768) {
//         drawerToggle.checked = true; // Uncheck the toggle
//     }

//     // Swipe left to close sidebar
//     if (touchStartX > 200 && touchEndX < touchStartX - 50 && window.innerWidth <= 768) {
//         drawerToggle.checked = false;
//     }
    
//         drawerToggle.dispatchEvent(new Event('change')); // Trigger listener
// });

// Section switching functionality
// document.querySelectorAll('#sidebar .menu-item').forEach(item => {
//     item.addEventListener('click', () => {
//         // Remove active class from all sidebar items
//         document.querySelectorAll('#sidebar .menu-item').forEach(div => {
//             div.classList.remove('active');
//         });

//         // Add active class to the clicked item
//         item.classList.add('active');

     

//         // Close the sidebar and overlay (mobile only)
//         if (window.innerWidth <= 768) {
//             closeSidebar();
//         } else {
//             openSidebar();
//         }
//     });
// });
