<?php
// Default values for modal configuration
$modal_id = $modal_id ?? 'membership-modal';
$form_action = $form_action ?? 'process_membership.php';
$editing = $editing ?? false;
$edit_member = $edit_member ?? null;
?>

<!-- member modal -->
<div class="modal" id="<?= $modal_id ?>">
    <div class="modal-content">
        <span class="close-btn" id="close-modal">&times;</span>
        <h3 id="modal-heading"><?= $editing ? 'Renew Membership' : 'Add' ?></h3>
        <form method="POST" action="<?= $form_action ?>" id="membership-form">
            <input type="hidden" name="edit_id" value="<?= $editing ? htmlspecialchars($edit_member['id']) : '' ?>">

            <div id="new-member-fields" <?= $editing ? 'style="display:none;"' : '' ?>>
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
                <label>
                    <input type="radio" name="membership_type" value="non-member"> Non-member
                </label>
            </div>

            <div id="months-container">
                <input class="modal-input" type="number" name="months" placeholder="Number of Months" min="0" required>
            </div>

            <button type="submit" id="submit-btn"><?= $editing ? 'Renew Membership' : 'Add' ?></button>
        </form>
    </div>
</div>

<!-- Include membership modal script -->
<script src="/InkedInLIFT/scripts/membership-modal.js"></script>