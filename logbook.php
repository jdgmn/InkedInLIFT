<?php
include 'dbcon.php';
include 'functions.php'; // for searchTable()

// fetch table, sorted by checkout time DESC
$searchTerm = $_GET['search'] ?? '';
$past_checkins = searchTable($pdo, 'logbook', ['name'], 'checkout_time IS NOT NULL', 'checkout_time DESC', $searchTerm);
ob_start();
?>

<h3>Logbook - Previous Check-ins</h3>

<!-- search bar -->
<?php
$action = 'logbook.php';
$placeholder = 'Search by name';
include 'components/search.php';
?>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($past_checkins)): ?>
            <tr>
                <td colspan="5">No records found</td>
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
    </tbody>
</table>
<?php
$content = ob_get_clean();
$title = 'LIFT - Logbook';
include 'components/layout.php';
?>
