<?php
include 'includes/dbcon.php';

// Check-in handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate membership_id from POST
    $membership_id = $_POST['membership_id'] ?? null;

    if (!$membership_id || !is_numeric($membership_id)) {
        exit('Error: Membership ID is required and must be numeric.');
    }

    // Verify membership exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE id = ?");
    $stmt->execute([$membership_id]);
    if ($stmt->fetchColumn() == 0) {
        exit('Error: Invalid membership selected.');
    }

    // Insert new check-in record
    $stmt = $pdo->prepare("INSERT INTO logbook (membership_id, checkin_time) VALUES (?, NOW())");
    $stmt->execute([$membership_id]);

    // Check-out handler
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Check current checkout_time
    $stmt = $pdo->prepare("SELECT checkout_time FROM logbook WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result && is_null($result['checkout_time'])) {
        $stmt = $pdo->prepare("UPDATE logbook SET checkout_time = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Redirect back to index
header("Location: index.php");
exit();
