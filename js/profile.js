
console.log("Home page script loaded");
if (typeof cPage === "undefined") {
    var cPage = null;
}

/**
 * Fetches and loads the content of a page.
 * @param {string} pageUrl - The URL of the PHP file to load.
 */

if (typeof loadSubPage === "undefined") {
    var loadSubPage = async (pageUrl) => {
        // Show a loading indicator

        let pcontentContainer = document.getElementById('profile-content');
        let pLinks = document.querySelectorAll('.olink .nav-link');
        pcontentContainer.innerHTML = '<p class="text-center text-gray-500">Loading...</p>';

        try {
            const response = await fetch("pages/profile_pages/" + pageUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const html = await response.text();

            // Inject the new content
            pcontentContainer.innerHTML = html;



        } catch (error) {
            console.error("Failed to load page:", error);
            pcontentContainer.innerHTML = `<p class="text-center text-red-500">Sorry, we couldn't load this page. Please try again later.</p>`;
        }
    };
}

if (cPage === null) {
    cPage = "view";
}
loadSubPage(cPage + ".php");

