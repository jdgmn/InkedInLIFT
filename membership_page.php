<!-- CSS LINKS -->
<link rel="stylesheet" href="css/modal.css">

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
// defining $editing
$editing = isset($_GET['edit']) ? true : false;
?>

<!-- modal for add / renew membership -->
<div class="modal" id="membership-modal">
    <div class="modal-content">
        <span class="close-btn" id="close-modal">&times;</span>
        <h3 id="modal-heading"><?= $editing ? 'Renew Membership' : 'Add New Member' ?></h3>
        <form method="POST" action="process_membership.php">
            <input type="hidden" name="edit_id" value="<?= $editing ? htmlspecialchars($edit_member['id']) : '' ?>">

            <div id="new-member-fields" style="<?= $editing ? 'display:none;' : 'display:block;' ?>">
                <input class="modal-input" type="text" name="name" placeholder="Customer Name" required
                    value="<?= $editing ? htmlspecialchars($edit_member['name']) : '' ?>">
                <input class="modal-input" type="email" name="email" placeholder="Email"
                    value="<?= $editing ? htmlspecialchars($edit_member['email']) : '' ?>">
                <input class="modal-input" type="text" name="phone" placeholder="Phone"
                    value="<?= $editing ? htmlspecialchars($edit_member['phone']) : '' ?>">
            </div>

            <div id="membership-type-group">
                <label>
                    <input type="radio" name="membership_type" value="member" checked> Member
                </label>
                <label style="margin-left: 15px;">
                    <input type="radio" name="membership_type" value="non-member"> Non-member
                </label>
            </div>

            <div id="months-container">
                <input class="modal-input" type="number" name="months" placeholder="Number of Months" min="0" required>
            </div>

            <button type="submit"><?= $editing ? 'Renew Membership' : 'Add Membership' ?></button>
        </form>
    </div>
</div>

<!-- actions & search -->
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

<!-- JAVASCRIPT STARTS HERE-->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const newMemberBtn = document.getElementById('new-member-btn');
        const modal = document.getElementById('membership-modal');
        const closeModal = document.getElementById('close-modal');
        const renewButtons = document.querySelectorAll('.renew-btn');
        const editButtons = document.querySelectorAll('.edit-btn');
        const form = modal.querySelector('form');
        const newMemberFields = document.getElementById('new-member-fields');
        const monthsInput = form.querySelector('input[name="months"]');
        const modalHeading = modal.querySelector('h3');

        // member type radios
        const membershipTypeRadios = form.querySelectorAll('input[name="membership_type"]');
        const monthsContainer = monthsInput.parentElement;

        function getSelectedMembershipType() {
            for (const radio of membershipTypeRadios) {
                if (radio.checked) return radio.value;
            }
            return null;
        }

        // show months input based on member type
        function toggleMonthsInput() {
            const selected = getSelectedMembershipType();
            if (selected === 'member') {
                monthsContainer.style.display = 'block';
                monthsInput.required = true;
            } else {
                monthsContainer.style.display = 'none';
                monthsInput.required = false;
                monthsInput.value = 0;
            }
        }

        // set default member type radio (and months input)
        function setMembershipTypeDefault(type = 'member') {
            membershipTypeRadios.forEach(radio => {
                radio.checked = (radio.value === type);
            });
            toggleMonthsInput();
        }

        // listen for member type radio changes
        membershipTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleMonthsInput);
        });

        // show new member modal
        newMemberBtn.addEventListener('click', () => {
            form.reset();
            form.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';
            modal.style.display = 'flex';

            setMembershipTypeDefault('member');
        });

        // show renew modal
        renewButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();

                form.reset();
                form.querySelector('input[name="edit_id"]').value = button.dataset.id;
                form.querySelector('input[name="name"]').value = button.dataset.name;
                form.querySelector('input[name="email"]').value = button.dataset.email;
                form.querySelector('input[name="phone"]').value = button.dataset.phone;

                newMemberFields.style.display = 'none';
                monthsContainer.style.display = 'block';
                modalHeading.textContent = 'Add Months';
                modal.style.display = 'flex';

                // for renew, assume member
                setMembershipTypeDefault('member');
            });
        });

        // show edit member modal
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                form.reset();
                form.querySelector('input[name="edit_id"]').value = button.dataset.id;
                form.querySelector('input[name="name"]').value = button.dataset.name;
                form.querySelector('input[name="email"]').value = button.dataset.email;
                form.querySelector('input[name="phone"]').value = button.dataset.phone;

                newMemberFields.style.display = 'block';
                monthsContainer.style.display = 'none';
                modalHeading.textContent = 'Edit Member Info';
                modal.style.display = 'flex';
            });
        });

        // close modal and reset form
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            form.reset();
            form.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';

            setMembershipTypeDefault('member');

            // clear URL params (solution to an encountered bug)
            const url = new URL(window.location);
            url.searchParams.delete('renew');
            url.searchParams.delete('edit_id');
            history.replaceState(null, '', url.toString());
        });
    });
</script>