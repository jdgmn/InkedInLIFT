<?php
include 'dbcon.php';

// fetch table
$current_checkins = $pdo->query("SELECT * FROM logbook WHERE checkout_time IS NULL")->fetchAll();
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
                <td><?= $dt->format('h:i A') ?></td> <!-- hh-mm AM/PM -->
                <td><a href="checkin_checkout.php?id=<?= $c['id'] ?>">Check-out</a></td>
                <td><button
                        onclick="if(confirm('Delete this record?')) window.location.href='delete_checkin.php?id=<?= $c['id'] ?>&from=index'">
                        Delete
                    </button>
                <?php endforeach; ?>
    </table>

    <br>
    <a href="logbook.php">View Logbook</a><br>
    <a href="membership_page.php">Manage Memberships</a>
</body>

</html>