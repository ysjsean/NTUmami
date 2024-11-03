// Keep separate counters for 'add' and each 'edit' section
let hoursBlockIndices = {
    add: 1,
};

function openTab(event, tabId) {
    // Hide all tab contents and reset their forms
    const tabContents = document.querySelectorAll(".tab-content");
    tabContents.forEach(content => {
        content.style.display = "none";
        resetFormsInContent(content); // Reset forms and clear error messages
    });

    // Show the selected tab
    document.getElementById(tabId).style.display = "block";

    // Set the clicked tab as active
    const tabLinks = document.querySelectorAll(".tab-link");
    tabLinks.forEach(link => link.classList.remove("active"));
    if (event) {
        event.currentTarget.classList.add("active");
    } else {
        document.querySelector(`[onclick="openTab(event, '${tabId}')"]`).classList.add("active");
    }
}

function toggleEdit(itemId) {
    const item = document.getElementById(itemId);
    const viewMode = item.querySelector(".view-mode");
    const editMode = item.querySelector(".edit-mode");

    // Toggle display and reset edit mode if canceling
    if (editMode.style.display === "flex") {
        resetForm(editMode); // Reset the form content and error messages on cancel
    }

    viewMode.style.display = viewMode.style.display === "none" ? "flex" : "none";
    editMode.style.display = editMode.style.display === "none" ? "flex" : "none";
}

function resetFormsInContent(content) {
    // Reset all forms and clear error messages within the specified tab content
    const forms = content.querySelectorAll("form");
    forms.forEach(form => {
        form.reset(); // Reset form fields

        // Hide any error messages
        const errorMessages = form.querySelectorAll(".error-message");
        errorMessages.forEach(error => {
            error.textContent = "";
            error.style.display = "none";
        });

        // Hide edit mode and show view mode
        const editModes = form.parentElement.querySelectorAll(".edit-mode");
        const viewModes = form.parentElement.querySelectorAll(".view-mode");
        editModes.forEach(mode => mode.style.display = "none");
        viewModes.forEach(mode => mode.style.display = "flex");
    });
}

function resetForm(form) {
    // Reset a specific form's fields and error messages
    form.reset();

    // Hide error messages within the form
    const errorMessages = form.querySelectorAll(".error-message");
    errorMessages.forEach(error => {
        error.textContent = "";
        error.style.display = "none";
    });
}


// On page load, check the URL for the 'tab' parameter and set the active tab accordingly
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    let activeTab = urlParams.get('tab'); // Default
    if (!activeTab)
        activeTab = document.getElementById('tab-canteens') ? 'tab-canteens' : 'tab-stalls'; 
    openTab(null, activeTab)
});


// Function to initialize an index for editing if not already set
function initializeEditHoursIndex(canteenId) {
    if (!(canteenId in hoursBlockIndices)) {
        hoursBlockIndices[canteenId] = document.querySelectorAll(`#business-hours-section-${canteenId} .hours-block`).length;
    }
}

// Function to add an hours block in the specified section
function addHoursBlock(sectionId, id = 'add') {
    const template = document.getElementById('hours-block-template');
    const clone = template.content.cloneNode(true);

    const index = hoursBlockIndices[id];
    clone.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
        checkbox.name = `days[${index}][]`;
    });

    // Update the index for the current section
    hoursBlockIndices[id]++;
    document.getElementById(sectionId).appendChild(clone);
}

// Function to remove an hours block
function removeHoursBlock(button) {
    const section = button.closest(".business-hours-section");
    if (section.querySelectorAll(".hours-block").length > 1) {
        button.closest(".hours-block").remove();
    } else {
        alert("At least one business hours block is required.");
    }
}

function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to delete this ${name}?`)) {
        window.location.href = `../controllers/${name}_handler.php?action=delete&id=${id}&tab=tab-${name}s`;
    }
}