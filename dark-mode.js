const body = document.body;
const toggleButton = document.getElementById("toggle-dark-mode");
const icon = document.getElementById("dark-mode-icon");

if (toggleButton && icon) {
  const savedDarkMode = localStorage.getItem("dark-mode") === "enabled";
  const updateDarkMode = (isDarkMode) => {
    body.classList.toggle("dark-mode", isDarkMode);
    localStorage.setItem("dark-mode", isDarkMode ? "enabled" : "disabled");
    icon.classList.replace(isDarkMode ? "bi-moon" : "bi-sun", isDarkMode ? "bi-sun" : "bi-moon");
  };

  updateDarkMode(savedDarkMode);

  toggleButton.addEventListener("click", () => updateDarkMode(!body.classList.contains("dark-mode")));
}
