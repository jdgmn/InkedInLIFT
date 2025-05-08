<?php
include 'includes/dbcon.php';
include 'includes/functions.php'; // for searchTable()

// fetch table, sorted by checkout time DESC
$searchTerm = $_GET['search'] ?? '';
$past_checkins = searchTable($pdo, 'logbook', ['name'], 'checkout_time IS NOT NULL', 'checkout_time DESC', $searchTerm);
ob_start();
?>

<div class="conatainer title">
    <h2>Logbook - Previous Check-ins</h2>
</div>

<!-- search bar -->
<div class="container actions single">
    <?php
        $action = 'logbook.php';
        $placeholder = 'Search by name';
        include 'components/search.php';
    ?>
</div>

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
                            class="delete"
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
