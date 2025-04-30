<?php
$host = 'localhost';
$dbname = 'lift_db';
$username = 'root';
$password = '';

try {
    // dsn + user and pass
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>
