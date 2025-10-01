// Autocomplete JavaScript Module
(function() {
    'use strict';
    
    // Initialize autocomplete for member search
    function initAutocomplete(inputId = 'checkin-name', hiddenInputId = 'checkin-membership-id', formId = 'checkin-form', listId = 'autocomplete-list') {
        const input = document.getElementById(inputId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const list = document.getElementById(listId);
        const form = document.getElementById(formId);

        if (!input || !hiddenInput || !list || !form) {
            console.warn('Autocomplete: Required elements not found');
            return;
        }

        input.addEventListener('input', () => {
            const val = input.value.trim();
            hiddenInput.value = ''; // reset membership ID

            if (!val) {
                list.innerHTML = '';
                return;
            }

            fetch(`/InkedInLIFT/search_members.php?term=${encodeURIComponent(val)}`)
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
    }

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initAutocomplete();
    });

    // Export for manual initialization if needed
    window.Autocomplete = {
        init: initAutocomplete
    };
})();