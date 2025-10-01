<!-- CSS LINKS -->

<!-- PHP STARTS HERE -->
<?php
include 'includes/dbcon.php';
include 'includes/functions.php'; // searchTable()

// fetch past check-ins (sorted by checkout_time DESC)
$searchTerm = $_GET['search'] ?? '';
$params = [];
$sql = "
    SELECT logbook.*, memberships.name AS member_name
    FROM logbook
    JOIN memberships ON logbook.membership_id = memberships.id
    WHERE logbook.checkout_time IS NOT NULL
";

if (!empty($searchTerm)) {
    $sql .= " AND memberships.name LIKE ?";
    $params[] = "%$searchTerm%";
}

$sql .= " ORDER BY logbook.checkout_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$past_checkins = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start(); // start output buffer
?>

<!-- page title -->
<div class="container title">
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

<!-- past check-ins table -->
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Duration</th>
            <th colspan="2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($past_checkins)): ?>
            <tr>
                <td colspan="6">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($past_checkins as $c):
                $checkin = new DateTimeImmutable($c['checkin_time']);
                $checkout = new DateTimeImmutable($c['checkout_time']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['member_name']) ?></td>
                    <td><?= $checkin->format('m-d-Y') ?></td> <!-- mm-dd-yyyy -->
                    <td><?= $checkin->format('h:i A') ?></td> <!-- hh:mm AM/PM -->
                    <td><?= $checkout->format('h:i A') ?></td>
                    <td>
                        <?php
                        $duration = $checkin->diff($checkout);
                        echo $duration->format('%h hr %i min');
                        ?>
                    </td>
                    <td>
                        <a href="membership_page.php?search=<?= urlencode($c['member_name']) ?>">
                            <button class="view">View</button>
                        </a>
                    </td>
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
$content = ob_get_clean(); // buffer clear
$title = 'LIFT - Logbook';
include 'components/layout.php';
?>