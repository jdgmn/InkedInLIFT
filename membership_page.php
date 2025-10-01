<!-- CSS LINKS -->
<link rel="stylesheet" href="/InkedInLIFT/css/modal.css">

<!-- PHP STARTS HERE -->
<?php
include 'includes/dbcon.php';
include 'includes/functions.php';  // getRemainingTime(), searchTable()

// handle member editing
$editing = false;
$edit_member = null;
if (!empty($_GET['renew']) && is_numeric($_GET['renew'])) {
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE id = ?");
    $stmt->execute([$_GET['renew']]);
    $edit_member = $stmt->fetch();
    $editing = !!$edit_member;
}

// fetch memberships with search
$searchTerm = $_GET['search'] ?? '';
$memberships = searchTable(
    $pdo,
    'memberships',
    ['name', 'email', 'phone'],
    '',
    'status ASC, end_date ASC',
    $searchTerm
);

// update membership status
$now = new DateTimeImmutable();
foreach ($memberships as &$m) {
    $end = new DateTimeImmutable($m['end_date']);
    if ($m['status'] === 'active' && $now > $end) {
        $pdo->prepare("UPDATE memberships SET status='inactive' WHERE id=?")->execute([$m['id']]);
        $m['status'] = 'inactive';
    }
    $m['remaining'] = $m['status'] === 'inactive' ? 'Expired' : getRemainingTime($end);
}
unset($m);

ob_start(); // start output buffer
?>

<!-- page title -->
<div class="container title">
    <h2>Client List</h2>
</div>

<?php
// Include the membership modal component
include 'components/membership_modal.php';
?>

<!-- actions & search -->
<div class="container actions">
    <div class="comp-container">
        <button class="checkin" id="new-member-btn">New Account</button>
    </div>
    <?php
    $action = 'membership_page.php';
    $placeholder = 'Search';
    include 'components/search.php';
    ?>
</div>

<!-- membership table-->
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Start</th>
            <th>End</th>
            <th>Remaining</th>
            <th>Status</th>
            <th colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($memberships)): ?>
            <tr>
                <td colspan="9" style="text-align: center;">No records found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($memberships as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['phone']) ?></td>
                    <td><?= date('m-d-Y', strtotime($m['start_date'])) ?></td>
                    <td><?= date('m-d-Y', strtotime($m['end_date'])) ?></td>
                    <td><?= $m['remaining'] ?></td>
                    <td>
                        <?= $m['status'] === 'active' ? 'Member' : 'Non-member' ?>
                    </td>
                    <td>
                        <!-- renew -->
                        <a href="membership_page.php?renew=<?= $m['id'] ?>"
                            class="renew-btn"
                            data-id="<?= $m['id'] ?>"
                            data-name="<?= htmlspecialchars($m['name']) ?>"
                            data-email="<?= htmlspecialchars($m['email']) ?>"
                            data-phone="<?= htmlspecialchars($m['phone']) ?>">
                            <button class="checkout">Renew</button>
                        </a>
                    </td>
                    <td>
                        <!-- edit -->
                        <button class="edit-btn"
                            data-id="<?= $m['id'] ?>"
                            data-name="<?= htmlspecialchars($m['name']) ?>"
                            data-email="<?= htmlspecialchars($m['email']) ?>"
                            data-phone="<?= htmlspecialchars($m['phone']) ?>">
                            Edit
                        </button>
                    </td>
                    <td>
                        <!-- delete -->
                        <button class="delete"
                            onclick="if(confirm('Delete this membership?')) window.location.href='process_membership.php?delete_id=<?= $m['id'] ?>'">
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
$title = 'LIFT - Memberships';
include 'components/layout.php';
?>