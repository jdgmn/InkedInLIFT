<form method="GET" action="<?= htmlspecialchars($action ?? '') ?>" style="margin-bottom: 1em;">
    <input type="text" name="search" placeholder="<?= htmlspecialchars($placeholder ?? 'Search...') ?>"
           value="<?= htmlspecialchars($searchTerm ?? '') ?>">
    <button type="submit">Search</button>
    <a href="<?= htmlspecialchars($action ?? '') ?>"><button type="button">Reset</button></a>
</form>