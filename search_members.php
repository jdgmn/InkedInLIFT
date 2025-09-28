<?php
// search_members.php

include 'includes/dbcon.php';

$term = trim($_GET['term'] ?? '');

if ($term === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT name FROM memberships WHERE name LIKE ? ORDER BY name ASC LIMIT 10");
$stmt->execute(["%{$term}%"]);

$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($results);
