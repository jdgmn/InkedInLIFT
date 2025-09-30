<!-- CSS LINKS -->
<link rel="stylesheet" href="css/modal.css">
<link rel="stylesheet" href="css/autocom.css">

<!-- PHP STARTS HERE -->
<?php
include 'includes/dbcon.php';
include 'includes/functions.php'; // searchTable()

$searchTerm = $_GET['search'] ?? '';
$current_checkins = searchTable(
    $pdo,
    'logbook',
    ['name'],
    'checkout_time IS NULL',
    'checkin_time ASC',
    $searchTerm
);

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
        <h3 id="modal-heading"><?= $editing && $editing ? 'Renew Membership' : 'Add New Member' ?></h3>
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

    <form action="checkin_checkout.php" method="POST">
        <div class="comp-container">
            <input type="text" name="name" id="checkin-name" placeholder="Customer Name" autocomplete="off" required>
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
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= $dt->format('m-d-Y') ?></td>
                    <td><?= $dt->format('h:i A') ?></td>
                    <td>
                        <a href="checkin_checkout.php?id=<?= $c['id'] ?>">
                            <button class="checkout">Check-out</button>
                        </a>
                    </td>
                    <td>
                        <a href="membership_page.php?search=<?= urlencode($c['name']) ?>">
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
        // modal script
        const newMemberBtn = document.getElementById('new-member-btn');
        const modal = document.getElementById('membership-modal');
        const closeModal = document.getElementById('close-modal');
        const form = modal.querySelector('#membership-form');
        const newMemberFields = document.getElementById('new-member-fields');
        const modalHeading = document.getElementById('modal-heading');
        const monthsInput = form.querySelector('input[name="months"]');
        const membershipTypeRadios = form.querySelectorAll('input[name="membership_type"]');
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
            form.reset();
            form.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';
            modal.style.display = 'flex';
            setMembershipTypeDefault('member');
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            form.reset();
            form.querySelector('input[name="edit_id"]').value = '';
            newMemberFields.style.display = 'block';
            monthsContainer.style.display = 'block';
            modalHeading.textContent = 'Add New Member';
            setMembershipTypeDefault('member');
        });

        // autocomplete script
        const input = document.getElementById('checkin-name');
        const list = document.getElementById('autocomplete-list');

        input.addEventListener('input', function() {
            const val = this.value.trim();

            list.innerHTML = '';
            if (!val) return;

            fetch(`search_members.php?term=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    data.forEach(name => {
                        const item = document.createElement('div');
                        item.textContent = name;
                        item.classList.add('autocomplete-item');
                        item.addEventListener('click', () => {
                            input.value = name;
                            list.innerHTML = '';
                        });
                        list.appendChild(item);
                    });
                })
                .catch(err => {
                    console.error('Autocomplete fetch error:', err);
                });
        });

        // hide autocomplete on outside click
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.innerHTML = '';
            }
        });
    });
</script>