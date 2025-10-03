// Membership Modal JavaScript Module
(function() {
    'use strict';
    
    // Initialize the membership modal
    function initMembershipModal(modalId = 'membership-modal') {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const newMemberBtn = document.getElementById('new-member-btn');
        const closeModal = document.getElementById('close-modal');
        const renewButtons = document.querySelectorAll('.renew-btn');
        const editButtons = document.querySelectorAll('.edit-btn');
        const form = modal.querySelector('form');
        const newMemberFields = document.getElementById('new-member-fields');
        const monthsInput = form.querySelector('input[name="months"]');
        const modalHeading = modal.querySelector('h3');
        const submitBtn = document.getElementById('submit-btn');

        // member type radios
        const membershipTypeRadios = form.querySelectorAll('input[name="membership_type"]');
        const monthsContainer = document.getElementById('months-container');

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
        if (newMemberBtn) {
            newMemberBtn.addEventListener('click', () => {
                form.reset();
                form.querySelector('input[name="edit_id"]').value = '';
                newMemberFields.style.display = 'block';
                monthsContainer.style.display = 'block';
                monthsInput.required = true; // Ensure required for new member
                modalHeading.textContent = 'New Account';
                submitBtn.textContent = 'Add Account';
                modal.style.display = 'flex';
                setMembershipTypeDefault('member');

                // show member type buttons
                document.getElementById('membership-type-group').style.display = 'block';
            });
        }

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
                monthsInput.required = true; // Ensure required for renew mode
                modalHeading.textContent = 'Add Months';
                submitBtn.textContent = 'Submit';
                modal.style.display = 'flex';

                // for renew, assume member
                setMembershipTypeDefault('member');

                // hide member type buttons
                document.getElementById('membership-type-group').style.display = 'none';
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
                monthsInput.required = false; // Remove required for edit mode
                modalHeading.textContent = 'Edit Member Info';
                submitBtn.textContent = 'Update';
                modal.style.display = 'flex';
            });
        });

        // close modal and reset form
        if (closeModal) {
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
                form.reset();
                form.querySelector('input[name="edit_id"]').value = '';
                newMemberFields.style.display = 'block';
                monthsContainer.style.display = 'block';
                monthsInput.required = true; // Ensure required for default state
                modalHeading.textContent = 'Add Account';
                submitBtn.textContent = 'Add Account';

                setMembershipTypeDefault('member');

                // clear URL params (solution to an encountered bug)
                const url = new URL(window.location);
                url.searchParams.delete('renew');
                url.searchParams.delete('edit_id');
                history.replaceState(null, '', url.toString());
            });
        }

        // Initialize on page load
        setMembershipTypeDefault('member');
    }

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMembershipModal();
    });

    // Export for manual initialization if needed
    window.MembershipModal = {
        init: initMembershipModal
    };
})();