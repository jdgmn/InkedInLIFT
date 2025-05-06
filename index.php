<?php
include 'dbcon.php';
include 'functions.php'; // for searchTable()

// fetch table
$searchTerm = $_GET['search'] ?? '';
$current_checkins = searchTable($pdo, 'logbook', ['name'], 'checkout_time IS NULL', 'checkin_time DESC', $searchTerm);
?>

<!DOCTYPE html>
<html>
<head>
    <title>LIFT - Main</title>
</head>
<body>
    <h1>Logging Integrated Fitness Tracking</h1>

    <form action="checkin_checkout.php" method="POST">
        <input type="text" name="name" placeholder="Customer Name" required>
        <button type="submit">Check In</button>
    </form>

    <h2>Currently Checked-In</h2>

    <!-- search bar -->
    <form method="GET" action="index.php" style="margin-bottom: 1em;">
        <input type="text" name="search" placeholder="Search by name"
               value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
        <a href="index.php"><button type="button">Reset</button></a>
    </form>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in Time</th>
            <th>Checkout</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($current_checkins as $c):
            $dt = new DateTimeImmutable($c['checkin_time']);
        ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $dt->format('m-d-Y') ?></td> <!-- mm-dd-yyyy -->
                <td><?= $dt->format('h:i A') ?></td> <!-- hh:mm AM/PM -->
                <td><a href="checkin_checkout.php?id=<?= $c['id'] ?>">Check-out</a></td>
                <td>
                    <button onclick="if(confirm('Delete this record?')) window.location.href='delete_checkin.php?id=<?= $c['id'] ?>&from=index'">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="logbook.php">View Logbook</a><br>
    <a href="membership_page.php">Manage Memberships</a>
</body>
</html>