<?php
include 'dbcon.php';
include 'functions.php';  // for getRemainingTime(), searchTable()

// for editing members
$editing = false;
$edit_member = null;
if (!empty($_GET['renew']) && is_numeric($_GET['renew'])) {
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE id = ?");
    $stmt->execute([$_GET['renew']]);
    $edit_member = $stmt->fetch();
    $editing = !!$edit_member;
}

// fetch memberships with optional search
$searchTerm = $_GET['search'] ?? '';
$memberships = searchTable($pdo, 'memberships', ['name', 'email', 'phone'], '', 'status DESC, end_date DESC', $searchTerm);

$now = new DateTimeImmutable();

// expiry check
foreach ($memberships as &$m) {
    $end = new DateTimeImmutable($m['end_date']);
    if ($m['status'] === 'active' && $now > $end) {
        $pdo->prepare("UPDATE memberships SET status='inactive' WHERE id=?")->execute([$m['id']]);
        $m['status'] = 'inactive';
    }
    $m['remaining'] = $m['status'] === 'inactive' ? 'Expired' : getRemainingTime($end);
}
unset($m);

ob_start();
?>

<h3>Membership Management</h3>

<!-- adding or renewing membership -->
<form method="POST" action="process_membership.php">
    <input type="hidden" name="edit_id" value="<?= $editing ? htmlspecialchars($edit_member['id']) : '' ?>">
    <input type="text" name="name" placeholder="Customer Name" required
            value="<?= $editing ? htmlspecialchars($edit_member['name']) : '' ?>">
    <input type="email" name="email" placeholder="Email (optional)"
            value="<?= $editing ? htmlspecialchars($edit_member['email']) : '' ?>">
    <input type="text" name="phone" placeholder="Phone (optional)"
            value="<?= $editing ? htmlspecialchars($edit_member['phone']) : '' ?>">
    <input type="number" name="months" placeholder="Number of Months" min="0" required>
    <button type="submit"><?= $editing ? 'Renew Membership' : 'Add Membership' ?></button>
    <?php if ($editing): ?><a href="membership_page.php">Cancel</a><?php endif; ?>
</form>

<h2>Memberships</h2>

<!-- search bar -->
<?php
$action = 'membership_page.php';
$placeholder = 'Search by name, email, or phone';
include 'components/search.php';
?>

<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Start</th>
        <th>End</th>
        <th>Remaining</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($memberships)): ?>
        <tr><td colspan="8" style="text-align: center;">No records found</td></tr>
    <?php else: ?>
        <?php foreach ($memberships as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= htmlspecialchars($m['phone']) ?></td>
                <td><?= date('m-d-Y', strtotime($m['start_date'])) ?></td>
                <td><?= date('m-d-Y', strtotime($m['end_date'])) ?></td>
                <td><?= $m['remaining'] ?></td>
                <td><?= $m['status'] ?></td>
                <td>
                    <a href="membership_page.php?renew=<?= $m['id'] ?>">Add</a> |
                    <button onclick="if(confirm('Delete this membership?')) window.location.href='process_membership.php?delete_id=<?= $m['id'] ?>'">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif;?>
</table>

<?php
$content = ob_get_clean();
include 'components/layout.php';
$title = 'LIFT - Memberships';
?>
