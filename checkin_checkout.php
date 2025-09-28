<?php
include 'includes/dbcon.php';

// check-in handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = trim($_POST['name']);
    $stmt = $pdo->prepare("INSERT INTO logbook (name, checkin_time) VALUES (?, NOW())");
    $stmt->execute([$name]);

    // check-out handler
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // check for checkout_time
    $stmt = $pdo->prepare("SELECT checkout_time FROM logbook WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result && is_null($result['checkout_time'])) {
        $stmt = $pdo->prepare("UPDATE logbook SET checkout_time = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// index redirect
header("Location: index.php");
exit();
