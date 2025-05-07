<?php
include 'dbcon.php';
include 'functions.php'; // for searchTable()

// fetch table, sorted by checkout time DESC
$searchTerm = $_GET['search'] ?? '';
$past_checkins = searchTable($pdo, 'logbook', ['name'], 'checkout_time IS NOT NULL', 'checkout_time DESC', $searchTerm);
?>

<!DOCTYPE html>
<html>
<head>
    <title>LIFT - Logbook</title>
</head>
<body>
    <h1>Logbook - Previous Check-ins</h1>

    <!-- search bar -->
    <form method="GET" action="logbook.php" style="margin-bottom: 1em;">
        <input type="text" name="search" placeholder="Search by name"
               value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
        <a href="logbook.php"><button type="button">Reset</button></a>
    </form>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Delete</th>
        </tr>
        <?php if (empty($past_checkins)): ?>
            <tr>
                <td colspan="8" style="text-align: center;">No records found</td>
            </tr>
        <?php else: ?>
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
        <?php endif; ?>
    </table>

    <br><a href="index.php">Back to Main Page</a>
</body>
</html>
