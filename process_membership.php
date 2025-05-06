<?php
include 'dbcon.php';
include 'functions.php';  // for getRemaningTime()

// process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email'] ?? '') ?: null;
    $phone = trim($_POST['phone'] ?? '') ?: null;
    $months = (int) ($_POST['months'] ?? 0);
    $edit_id = $_POST['edit_id'] ?? null;

    if ($months < 0)
        die("Invalid months value.");

    if ($edit_id) {
        // previous member
        $current_end = new DateTimeImmutable($pdo->query("SELECT end_date FROM memberships WHERE id = $edit_id")->fetchColumn());
        $new_end = $current_end->modify("+$months months");
        $stmt = $pdo->prepare("UPDATE memberships SET name=?, email=?, phone=?, end_date=?, status='active' WHERE id=?");
        $stmt->execute([$name, $email, $phone, $new_end->format('Y-m-d H:i:s'), $edit_id]);
    } else {
        // new member
        $start = new DateTimeImmutable();
        $end = $start->modify("+$months months");
        $stmt = $pdo->prepare("INSERT INTO memberships (name, email, phone, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$name, $email, $phone, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]);
    }

    header("Location: membership_page.php");
    exit();
}

// membership deletion
if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM memberships WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: membership_page.php");
    exit();
}
?>