<?php
include 'dbcon.php';

if (!empty($_GET['id']) && is_numeric($_GET['id'])) { // handles null, false, and empty + cases such as (?id=abc)
    $pdo->prepare("DELETE FROM logbook WHERE id = ?")->execute([$_GET['id']]);
}

header("Location: " . ($_GET['from'] ?? 'index') . ".php");
exit();
?>
