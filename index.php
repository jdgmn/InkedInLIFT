<!-- CSS LINKS -->
<link rel="stylesheet" href="css/modal.css">
<link rel="stylesheet" href="css/autocom.css">

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
// defining $editing
$editing = isset($_GET['edit']) ? true : false;
?>

<!-- member modal -->
<div class="modal" id="membership-modal">
    <div class="modal-content">
        <span class="close-btn" id="close-modal">&times;</span>
        <h3 id="modal-heading"><?= $editing ? 'Renew Membership' : 'Add New Member' ?></h3>
        <form method="POST" action="process_membership.php" id="membership-form">
            <input type="hidden" name="edit_id" value="<?= $editing ? htmlspecialchars($edit_member['id']) : '' ?>">

            <div id="new-member-fields" <?= $editing ? 'style="display:none;"' : '' ?>>
                <input class="modal-input" type="text" name="name" placeholder="Customer Name" required
                    value="<?= $editing ? htmlspecialchars($edit_member['name']) : '' ?>">
                <input class="modal-input" type="email" name="email" placeholder="Email"
                    value="<?= $editing ? htmlspecialchars($edit_member['email']) : '' ?>">
                <input class="modal-input" type="text" name="phone" placeholder="Phone"
                    value="<?= $editing ? htmlspecialchars($edit_member['phone']) : '' ?>">
            </div>

            <div id="membership-type-group" style="margin: 10px 0;">
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

<!-- actions section -->
<div class="container actions">
    <div class="comp-container">
        <button class="checkin" id="new-member-btn">New Member</button>
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
                <td colspan="5">No records found</td>
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

<!-- JAVASCRIPT STARTS HERE -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- AUTOCOMPLETE FOR CHECK-IN ---
        const input = document.getElementById('checkin-name');
        const hiddenInput = document.getElementById('checkin-membership-id');
        const list = document.getElementById('autocomplete-list');
        const form = document.getElementById('checkin-form');

        input.addEventListener('input', () => {
            const val = input.value.trim();
            hiddenInput.value = ''; // reset membership ID

            if (!val) {
                list.innerHTML = '';
                return;
            }

            fetch(`search_members.php?term=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item.name;
                        div.classList.add('autocomplete-item');
                        div.addEventListener('click', () => {
                            input.value = item.name;
                            hiddenInput.value = item.id;
                            list.innerHTML = '';
                        });
                        list.appendChild(div);
                    });
                })
                .catch(console.error);
        });

        document.addEventListener('click', e => {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.innerHTML = '';
            }
        });

        form.addEventListener('submit', e => {
            if (!hiddenInput.value) {
                e.preventDefault();
                alert('Please select a valid member from the list.');
            }
        });

        // --- MODAL SCRIPT FOR MEMBERSHIP ---
        const newMemberBtn = document.getElementById('new-member-btn');
        const modal = document.getElementById('membership-modal');
        const closeModal = document.getElementById('close-modal');
        const memberForm = document.getElementById('membership-form');
        const newMemberFields = document.getElementById('new-member-fields');
        const modalHeading = document.getElementById('modal-heading');
        const monthsInput = memberForm.querySelector('input[name="months"]');
        const membershipTypeRadios = memberForm.querySelectorAll('input[name="membership_type"]');
        const monthsContainer = document.getElementById('months-container');

        function getSelectedMembershipType() {
            for (const radio of membershipTypeRadios) {
                if (radio.checked) return radio.value;
            }
            return null;
        }

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

        function setMembershipTypeDefault(type = 'member') {
            membershipTypeRadios.forEach(radio => {
                radio.checked = (radio.value === type);
            });
            toggleMonthsInput();
        }

        membershipTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleMonthsInput);
        });

        newMemberBtn.addEventListener('click', () => {
            memberForm.reset();
            memberForm.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';
            modal.style.display = 'flex';
            setMembershipTypeDefault('member');
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            memberForm.reset();
            memberForm.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';
            setMembershipTypeDefault('member');
        });
    });
</script>