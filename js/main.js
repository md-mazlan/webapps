var home = "dashboard";
// Wait for the DOM to be fully loaded before running the script
document.addEventListener('DOMContentLoaded', () => {

    const contentContainer = document.getElementById('content');
    const scriptContainer = document.getElementById('page-script-container');
    const navLinks = document.querySelectorAll('#main-nav .nav-link');

    // Variable to keep track of the currently active script tags
    let currentPageScripts = [];

    /**
     * Fetches and loads the content of a page.
     * @param {string} pageUrl - The URL of the PHP file to load.
     */
    const loadPage = async (pageUrl) => {
        // Show a loading indicator
        if (window.innerWidth <= 768) {
            drawerToggle.checked = false; // Uncheck the toggle
            drawerToggle.dispatchEvent(new Event('change')); // Trigger listener
        }
        contentContainer.innerHTML = '<p class="text-center text-gray-500">Loading...</p>';

        try {
            const response = await fetch("pages/" + pageUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const html = await response.text();

            // Create a temporary container to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Find the main element of the newly loaded content
            const newContentElement = tempDiv.firstElementChild;

            // Handle multiple styles before injecting content
            if (newContentElement && newContentElement.dataset.style) {
                const styleHrefs = newContentElement.dataset.style.split(',').map(s => s.trim());
                loadStyles(styleHrefs);
            } else {
                removeCurrentStyles();
            }

            // Inject the new content after styles are loaded
            contentContainer.innerHTML = html;

            // Handle multiple scripts
            if (newContentElement && newContentElement.dataset.script) {
                const scriptSrcs = newContentElement.dataset.script.split(',').map(s => s.trim());
                loadScripts(scriptSrcs);
            } else {
                removeCurrentScripts();
            }

        } catch (error) {
            console.error("Failed to load page:", error);
            contentContainer.innerHTML = `<p class="text-center text-red-500">Sorry, we couldn't load this page. Please try again later.</p>`;
        }
    };

    /**
     * Removes the old scripts and loads new ones.
     * @param {string[]} scriptSrcs - The srcs for the new scripts to load.
     */
    const loadScripts = (scriptSrcs) => {
        // First, remove the previously loaded scripts to avoid conflicts
        removeCurrentScripts();

        scriptSrcs.forEach(scriptSrc => {
            if (scriptSrc) {
                const script = document.createElement('script');
                script.src = scriptSrc;
                script.className = 'dynamic-page-script'; // Use a class for multiple scripts
                scriptContainer.appendChild(script);
                currentPageScripts.push(script);
                console.log(`Loaded script: ${scriptSrc}`);
            }
        });
    };

    /**
     * Removes all script tags currently in the script container.
     */
    const removeCurrentScripts = () => {
        currentPageScripts.forEach(script => {
            if (script.parentNode) {
                script.parentNode.removeChild(script);
            }
            console.log(`Unloaded script: ${script.src}`);
        });
        currentPageScripts = [];
    };

    /**
     * Removes old styles and loads new ones.
     * @param {string[]} styleHrefs - The hrefs for the new CSS to load.
     */
    const loadStyles = (styleHrefs) => {
        // Remove any previously loaded dynamic styles
        removeCurrentStyles();

        // Create and append new style links
        styleHrefs.forEach(styleHref => {
            if (styleHref) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = styleHref;
                link.className = 'dynamic-page-style'; // Use a class for multiple styles
                document.head.appendChild(link);
                console.log(`Loaded style: ${styleHref}`);
            }
        });
    };

    /**
     * Removes all dynamically loaded style links.
     */
    const removeCurrentStyles = () => {
        const oldStyles = document.querySelectorAll('.dynamic-page-style');
        oldStyles.forEach(style => {
            style.remove();
            console.log(`Unloaded style: ${style.href}`);
        });
    };

    // Add click event listeners to all navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            // Get the page name from the href (e.g., "home", "about")
            const url = new URL(link.href, window.location.origin);
            const page = url.pathname.split('/').pop() || home;
            loadPage(`${page}.php`);

            // Update active link styling
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            // Update browser history with clean URL
            history.pushState({
                page: page
            }, '', page);
        });
    });

    // --- Initial Page Load ---
    const path = window.location.pathname.split('/').pop() || home;
    const initialPage = path === '' ? home : path;
    console.log(`Initial page: ${initialPage}`);
    loadPage(`${initialPage}.php`);
    const activeLink = document.querySelector(`a[href="${initialPage}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
        if (activeLink.parentNode.parentNode.children[0].classList.contains('dropdown-checkbox')) {
            activeLink.parentNode.parentNode.children[0].checked = true;
        }
    }

    // Handle browser back/forward navigation
    window.addEventListener('popstate', (event) => {
        const path = window.location.pathname.split('/').pop() || home;
        const page = path === '' ? home : path;
        loadPage(`${page}.php`);

        // Update active link styling
        navLinks.forEach(l => l.classList.remove('active'));
        const activeLink = document.querySelector(`a[href="${page}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    });

    const loadingOverlay = document.getElementById('loading-overlay');
    loadingOverlay.style.display = 'none'; // Hide the loading overlay
    if (window.innerWidth > 768) {
        openSidebar();
    } else {
        closeSidebar();
    }
});