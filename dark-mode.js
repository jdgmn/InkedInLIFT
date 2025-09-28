const body = document.body;
const toggleButton = document.getElementById("toggle-dark-mode");
const icon = document.getElementById("dark-mode-icon");

if (toggleButton && icon) {
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
