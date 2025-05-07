<link rel="stylesheet" type="text/css" href="css/search.css">
<form method="GET" action="<?= htmlspecialchars($action ?? '') ?>" class="search-form" style="position: relative; margin-bottom: 1em;">
   <div class="search-container">
        <input type="text" name="search" id="search-input" placeholder="<?= htmlspecialchars($placeholder ?? 'Search...') ?>"
           value="<?= htmlspecialchars($searchTerm ?? '') ?>" class="search-input">
           <a href="<?= htmlspecialchars($action ?? '') ?>"><button type="button" id="reset-button" class="reset-button" style="<?= !empty($searchTerm) ? '' : 'display: none;' ?>">&times;</button></a>
        <button type="submit" class="search-button">Search</button>
   </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const resetButton = document.getElementById('reset-button');

    searchInput.addEventListener('input', () => {
        if (searchInput.value.trim() !== '') {
            resetButton.style.display = 'block';
        } else {
            resetButton.style.display = 'none';
        }
    });

    resetButton.addEventListener('click', () => {
        searchInput.value = '';
        resetButton.style.display = 'none';
        searchInput.focus();
    });
});
</script>