<?php
include 'includes/dbcon.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM logbook WHERE id = ?");
    $stmt->execute([$id]);
}

$redirectPage = !empty($_GET['from']) ? $_GET['from'] : 'index';
header("Location: {$redirectPage}.php");
exit();
