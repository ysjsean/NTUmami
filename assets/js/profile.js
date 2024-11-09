function showTab(tabId) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show the selected tab
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Update active class on the sidebar
    const sidebarLinks = document.querySelectorAll('.profile-sidebar ul li');
    sidebarLinks.forEach(link => link.classList.remove('active'));
    
    // Highlight the clicked tab in the sidebar
    const activeLink = document.querySelector(`[onclick="showTab('${tabId}')"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

document.querySelector('form').addEventListener('submit', function (e) {
    const phoneInput = document.querySelector('input[name="phone"]');
    const birthdateInput = document.querySelector('input[name="birthdate"]');

    // Validate phone number
    const phonePattern = /^[89]\d{7}$/;
    if (!phonePattern.test(phoneInput.value)) {
        alert("Phone number must start with 8 or 9 and be 8 digits long.");
        e.preventDefault();
        return;
    }

    // Validate birthdate (must be at least 12 years old)
    const birthdate = new Date(birthdateInput.value);
    const today = new Date();
    const age = today.getFullYear() - birthdate.getFullYear();
    if (age < 12 || (age === 12 && today < new Date(today.getFullYear(), birthdate.getMonth(), birthdate.getDate()))) {
        alert("You must be at least 12 years old.");
        e.preventDefault();
        return;
    }
});


function toggleCardForm() {
    const form = document.getElementById('add-card-form');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const monthSelect = document.getElementById('card_expiry_month');
    const yearSelect = document.getElementById('card_expiry_year');

    function updateMonthOptions() {
        const currentMonth = new Date().getMonth() + 1; // Months are 0-indexed in JavaScript
        const currentYear = new Date().getFullYear() % 100; // Get last two digits of the current year
        const selectedYear = parseInt(yearSelect.value);

        // Enable all month options first
        for (let i = 0; i < monthSelect.options.length; i++) {
            monthSelect.options[i].disabled = false;
        }

        // Disable past months if the selected year is the current year
        if (selectedYear === currentYear) {
            for (let i = 1; i < currentMonth; i++) {
                monthSelect.options[i].disabled = true;
            }
        }
    }

    function updateYearOptions() {
        const currentMonth = new Date().getMonth() + 1;
        const currentYear = new Date().getFullYear() % 100;
        const selectedMonth = parseInt(monthSelect.value);

        // Enable all year options first
        for (let i = 0; i < yearSelect.options.length; i++) {
            yearSelect.options[i].disabled = false;
        }

        // If the selected month is before the current month, disable the current year
        if (selectedMonth < currentMonth) {
            for (let i = 0; i < yearSelect.options.length; i++) {
                if (parseInt(yearSelect.options[i].value) === currentYear) {
                    yearSelect.options[i].disabled = true;
                }
            }
        }
    }

    function validateExpiryDate() {
        const currentMonth = new Date().getMonth() + 1;
        const currentYear = new Date().getFullYear() % 100;
        const selectedMonth = parseInt(monthSelect.value);
        const selectedYear = parseInt(yearSelect.value);

        if (selectedYear < currentYear || (selectedYear === currentYear && selectedMonth < currentMonth)) {
            alert('The expiry date cannot be in the past.');
            return false;
        }
        return true;
    }

    // Update months when the year changes
    yearSelect.addEventListener('change', updateMonthOptions);

    // Update years when the month changes
    monthSelect.addEventListener('change', updateYearOptions);

    // Attach validation to the form submit event
    const form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        if (!validateExpiryDate()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});


function toggleCardActions(cardId) {
    const actionsForm = document.getElementById(`card-actions-${cardId}`);
    const arrowIcon = document.getElementById(`arrow-${cardId}`);
    
    if (actionsForm.classList.contains('active')) {
        actionsForm.classList.remove('active');
        arrowIcon.style.transform = 'rotate(0deg)';
    } else {
        // Hide all other card actions
        document.querySelectorAll('.card-actions').forEach(form => form.classList.remove('active'));
        document.querySelectorAll('.arrow-icon').forEach(icon => icon.style.transform = 'rotate(0deg)');
        
        actionsForm.classList.add('active');
        arrowIcon.style.transform = 'rotate(180deg)';
    }
}



