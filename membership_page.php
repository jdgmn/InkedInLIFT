<link rel="stylesheet" href="css/modal.css">
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
<div class="container title">
    <h2>Memberships</h2>
</div>

<!-- adding or renewing membership -->
<div class="modal" id="membership-modal">
    <div class="modal-content">
        <a href="membership_page.php"><span class="close-btn" id="close-modal">&times;</span></a>
        <h3>Add New Member</h3>
        <form method="POST" action="process_membership.php">
        <input type="hidden" name="edit_id" value="<?= $editing ? htmlspecialchars($edit_member['id']) : '' ?>">
            <div id="new-member-fields">
                <input class="modal-input" type="text" name="name" placeholder="Customer Name" required
                        value="<?= $editing ? htmlspecialchars($edit_member['name']) : '' ?>">
                <input class="modal-input" type="email" name="email" placeholder="Email (optional)"
                        value="<?= $editing ? htmlspecialchars($edit_member['email']) : '' ?>">
                <input class="modal-input" type="text" name="phone" placeholder="Phone (optional)"
                        value="<?= $editing ? htmlspecialchars($edit_member['phone']) : '' ?>">
            </div>
            <input class="modal-input" type="number" name="months" placeholder="Number of Months" min="0" required>
            <button type="submit"><?= $editing ? 'Renew Membership' : 'Add Membership' ?></button>
        </form>
    </div>
</div>



<!-- search bar -->
<div class="container actions">
    <div class="comp-container">
        <button class="checkin" id="new-member-btn">New Member</button>
    </div>
    <?php
        $action = 'membership_page.php';
        $placeholder = 'Search';
        include 'components/search.php';
    ?>
</div>

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
            <th colspan="2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($memberships)): ?>
            <tr><td colspan="9" style="text-align: center;">No records found</td></tr>
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
                    <td colspan="2">
                        <a href="membership_page.php?renew=<?= $m['id'] ?>" 
                            class="renew-btn" 
                            data-id="<?= $m['id'] ?>"
                            data-name="<?= htmlspecialchars($m['name']) ?>" 
                            data-email="<?= htmlspecialchars($m['email']) ?>" 
                            data-phone="<?= htmlspecialchars($m['phone']) ?>">
                            <button class="checkout">Renew</button>
                        </a>
                        <button class="delete" onclick="if(confirm('Delete this membership?')) window.location.href='process_membership.php?delete_id=<?= $m['id'] ?>'">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif;?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$title = 'LIFT - Memberships';
include 'components/layout.php';
?>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const newMemberBtn = document.getElementById('new-member-btn');
        const modal = document.getElementById('membership-modal');
        const closeModal = document.getElementById('close-modal');
        const renewButtons = document.querySelectorAll('.renew-btn');
        const form = modal.querySelector('form');
        const newMemberFields = document.getElementById('new-member-fields');
        const modalHeading = modal.querySelector('h3'); 

        newMemberBtn.addEventListener('click', () => {
            form.querySelector('input[name="edit_id"]').value = '';
            modal.style.display = 'flex';
        });
        renewButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault(); 

                const memberId = button.getAttribute('data-id');
                const memberName = button.getAttribute('data-name');
                const memberEmail = button.getAttribute('data-email');
                const memberPhone = button.getAttribute('data-phone');

                
                form.querySelector('input[name="edit_id"]').value = memberId;
                form.querySelector('input[name="name"]').value = memberName;
                form.querySelector('input[name="email"]').value = memberEmail;
                form.querySelector('input[name="phone"]').value = memberPhone;

                newMemberFields.style.display = 'none';

                modalHeading.textContent = 'Add Months';
                
                modal.style.display = 'flex';
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        
    });
</script>