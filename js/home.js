console.log("Home page script is now active!");

// This code only runs when home.php is loaded
const homeButton = document.getElementById('home-button');

if (homeButton) {
    homeButton.addEventListener('click', () => {
        alert("Hello from the Home Page script!");
    });
}

// You can define functions and variables here that are specific to the home page.
// They will be garbage collected when the script is unloaded.
