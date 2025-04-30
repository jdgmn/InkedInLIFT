<?php
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    // check-ins
    $stmt = $pdo->prepare("INSERT INTO logbook (name, checkin_time) VALUES (?, NOW())");
    $stmt->execute([trim($_POST['name'])]);

} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) { // numeric check for edge cases (?id=abc)
    // check-out
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT checkout_time FROM logbook WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result && is_null($result['checkout_time'])) {
        $stmt = $pdo->prepare("UPDATE logbook SET checkout_time = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: index.php");
exit();
?>