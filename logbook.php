<?php
include 'dbcon.php';

// fetch table, sorted by checkout time DESC
$past_checkins = $pdo
    ->query("SELECT * FROM logbook WHERE checkout_time IS NOT NULL ORDER BY checkout_time DESC")
    ->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>LIFT - Logbook</title>
</head>

<body>
    <h1>Logbook - Previous Check-ins</h1>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($past_checkins as $c):
            $checkin = new DateTimeImmutable($c['checkin_time']);
            $checkout = new DateTimeImmutable($c['checkout_time']);
            ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $checkin->format('m-d-Y') ?></td> <!-- mm-dd-yyyy -->
                <td><?= $checkin->format('h:i A') ?></td> <!-- hh:mm AM/PM -->
                <td><?= $checkout->format('h:i A') ?></td>
                <td>
                    <button
                        onclick="if(confirm('Delete this record?')) window.location.href='delete_checkin.php?id=<?= $c['id'] ?>&from=logbook'">
                        Delete
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br><a href="index.php">Back to Main Page</a>
</body>

</html>