<!-- CSS LINKS -->
<link rel="stylesheet" href="/InkedInLIFT/css/modal.css">
<link rel="stylesheet" href="/InkedInLIFT/css/autocom.css">

<!-- PHP STARTS HERE -->
<?php
include 'includes/dbcon.php';
include 'includes/functions.php'; // searchTable()

$searchTerm = $_GET['search'] ?? '';

$sql = "
    SELECT logbook.*, memberships.name AS member_name
    FROM logbook
    JOIN memberships ON logbook.membership_id = memberships.id
    WHERE logbook.checkout_time IS NULL
";

$params = [];
if ($searchTerm !== '') {
    $sql .= " AND memberships.name LIKE ?";
    $params[] = "%$searchTerm%";
}

$sql .= " ORDER BY logbook.checkin_time ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$current_checkins = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start(); // start output buffer
?>

<!-- page title -->
<div class="container title">
    <h2>Check-Ins</h2>
</div>

<?php
// Include the membership modal component
include 'components/membership_modal.php';
?>

<!-- actions section -->
<div class="container actions">
    <div class="comp-container">
        <button class="checkin" id="new-member-btn">New Account</button>
    </div>

    <form action="checkin_checkout.php" method="POST" id="checkin-form">
        <div class="comp-container">
            <input type="text" id="checkin-name" placeholder="Customer Name" autocomplete="off" required>
            <input type="hidden" name="membership_id" id="checkin-membership-id" required>
            <div id="autocomplete-list" class="autocomplete-items"></div>
            <button type="submit" class="checkin">Check-in</button>
        </div>
    </form>

    <?php
    $action = 'index.php';
    $placeholder = 'Search by name';
    include 'components/search.php';
    ?>
</div>

<!-- check-ins table -->
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Check-in Time</th>
            <th colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($current_checkins)): ?>
            <tr>
                <td colspan="6">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($current_checkins as $c):
                $dt = new DateTimeImmutable($c['checkin_time']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['member_name']) ?></td>
                    <td><?= $dt->format('m-d-Y') ?></td>
                    <td><?= $dt->format('h:i A') ?></td>
                    <td>
                        <a href="checkin_checkout.php?id=<?= $c['id'] ?>">
                            <button class="checkout">Check-out</button>
                        </a>
                    </td>
                    <td>
                        <a href="membership_page.php?id=<?= $c['membership_id'] ?>">
                            <button class="view">View</button>
                        </a>

                    </td>
                    <td>
                        <button class="delete" onclick="if(confirm('Delete this record?')) window.location.href='delete_checkin.php?id=<?= $c['id'] ?>&from=index'">
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
$title = 'LIFT - Main';

include 'components/layout.php';
?>

<!-- Include autocomplete script -->
<script src="/InkedInLIFT/scripts/autocomplete.js"></script>