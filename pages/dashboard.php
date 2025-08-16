<div class="container" data-script="js/dashboard.js" data-style="css/dashboard.css">

    <div class="tab-bar">
        <button class="active" data-type="news">News</button>
        <button data-type="event">Events</button>
        <button data-type="gallery">Gallery</button>
        <button data-type="vendor">Vendor</button>
    </div>
    <div class="tab-content" id="tab-content">
        <div id="dashboard-loading" style="display:flex;align-items:center;justify-content:center;height:180px;">
            <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg" style="animation:spin 1s linear infinite;">
                <circle cx="27" cy="27" r="22" stroke="#6373ff" stroke-width="6" opacity="0.2" />
                <path d="M49 27a22 22 0 1 1-44 0" stroke="#6373ff" stroke-width="6" stroke-linecap="round" />
            </svg>
        </div>
        <style>
            @keyframes spin {
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </div>

</div>