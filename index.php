<?php
include 'dbcon.php';
include 'functions.php'; // for searchTable()

// fetch table
$searchTerm = $_GET['search'] ?? '';
$current_checkins = searchTable($pdo, 'logbook', ['name'], 'checkout_time IS NULL', 'checkin_time DESC', $searchTerm);

ob_start();
?>

<form action="checkin_checkout.php" method="POST">
    <input type="text" name="name" placeholder="Customer Name" required>
    <button type="submit">Check In</button>
</form>

<h3>Check-Ins</h3>

<!-- search bar -->
<?php
$action = 'index.php';
$placeholder = 'Search by name';
include 'components/search.php';
?>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in Time</th>
            <th>Checkout</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
    <?php if(empty($current_checkins)): ?>
        <tr>
            <td colspan="8" style="text-align: center;">No records found</td>
        </tr>
    <?php else: ?>
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
    <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$title = 'LIFT - Main';

include 'components/layout.php';
?>