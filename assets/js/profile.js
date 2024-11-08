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

