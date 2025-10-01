// Dark Mode JavaScript Module
(function() {
    'use strict';
    
    // Initialize dark mode functionality
    function initDarkMode(toggleButtonId = 'toggle-dark-mode', iconId = 'dark-mode-icon') {
        const body = document.body;
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = document.getElementById(iconId);

        if (!toggleButton || !icon) {
            console.warn('Dark Mode: Required elements not found');
            return;
        }

        const isDarkModeEnabled = localStorage.getItem("dark-mode") === "enabled";

        const updateDarkMode = (enable) => {
            body.classList.toggle("dark-mode", enable);
            localStorage.setItem("dark-mode", enable ? "enabled" : "disabled");

            // swap icon based on mode
            if (enable) {
                icon.classList.replace("bi-moon", "bi-sun");
            } else {
                icon.classList.replace("bi-sun", "bi-moon");
            }
        };

        // initialize based on saved preference
        updateDarkMode(isDarkModeEnabled);

        // toggle dark mode on click
        toggleButton.addEventListener("click", () => {
            updateDarkMode(!body.classList.contains("dark-mode"));
        });
    }

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initDarkMode();
    });

    // Export for manual initialization if needed
    window.DarkMode = {
        init: initDarkMode
    };
})();
