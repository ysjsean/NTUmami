// Display error for individual input fields with specific message
function displayError(input, message) {
    const errorElement = document.getElementById(`${input.id}-error`);
    if (errorElement) {
        errorElement.innerHTML = message;
        errorElement.style.display = message ? "block" : "none";
    }
}

// Validation function for text fields
function validateText(input, fieldName) {
    const value = input.value.trim();
    const message = value === "" ? `${fieldName} cannot be empty.` : "";
    displayError(input, message);
    return !message;
}

function validateEmail(input) {
    const value = input.value.trim();
    const message = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? "" : "Please enter a valid email address.";
    displayError(input, message);
    return !message;
}

function validatePassword(input) {
    const value = input.value;
    const hasLength = value.length >= 8;
    const hasUpperCase = /[A-Z]/.test(value);
    const hasNumber = /[0-9]/.test(value);
    const hasSpecialChar = /[^a-zA-Z0-9]/.test(value);
    
    let message = "";
    if (!hasLength) {
        message = "Password must be at least 8 characters long.";
    } 
    
    if (!hasUpperCase) {
        if (message)
            message += "<br>";
        message += "Password must contain at least one uppercase letter.";
    } 
    
    if (!hasNumber) {
        if (message)
            message += "<br>";
        message += "Password must contain at least one number.";
    } 
    
    if (!hasSpecialChar) {
        if (message)
            message += "<br>";
        message += "Password must contain at least one special character.";
    }

    displayError(input, message);
    return !message;
}

function validateConfirmPassword(confirmInput, passwordInput) {
    const message = confirmInput.value === passwordInput.value ? "" : "Passwords do not match.";
    displayError(confirmInput, message);
    return !message;
}


function validatePhoneNumber(input) {
    const value = input.value.trim();
    const phonePattern = /^[689]\d{7}$/; // Adjust pattern based on preferred format
    const message = !phonePattern.test(value) ? "Please enter a valid Singapore phone number." : "";
    displayError(input, message);
    return !message;
}

// Validation function for dropdowns with optional invalid default value
function validateDropdown(input, fieldName, invalidValue = "") {
    const message = input.value === invalidValue ? `${fieldName} is required.` : "";
    displayError(input, message);
    return !message;
}

// Validation function for image input with file type and size check
function validateImage(input) {
    const file = input.files[0];
    let message = "";

    if (file) {
        const validTypes = ["image/jpeg", "image/png"];
        if (!validTypes.includes(file.type)) {
            message = "Only JPEG and PNG formats are allowed.";
        } else if (file.size > 2 * 1024 * 1024) {
            message = "File size should not exceed 2MB.";
        }
    } else {
        message = "Canteen image is required.";
    }
    console.log(message)
    displayError(input, message);
    return !message;
}

// Validate business hours (open and close times) and ensure days are selected
function validateBusinessHours(form) {
    const hoursBlocks = form.querySelectorAll(".hours-block");
    let valid = true;

    hoursBlocks.forEach((block, index) => {
        const openTimeInput = block.querySelector("input[name='open_time[]']");
        const closeTimeInput = block.querySelector("input[name='close_time[]']");
        const daysInputs = block.querySelectorAll("input[name^='days']:checked");

        let errorMessage = "";
        
        // Validate Open and Close Time
        if (!openTimeInput.value || !closeTimeInput.value) {
            errorMessage = "Both open and close times are required.";
            valid = false;
        } else if (new Date(`1970-01-01T${openTimeInput.value}`) >= new Date(`1970-01-01T${closeTimeInput.value}`)) {
            errorMessage = "Open time must be earlier than close time.";
            valid = false;
        } else if (daysInputs.length === 0) {
            errorMessage = "At least one day must be selected for each time block.";
            valid = false;
        }

        // Display or hide the error message for this hours block
        let errorElement = block.querySelector(".business-hours-error");
        if (!errorElement) {
            // Create a new error message element if it doesn't exist
            errorElement = document.createElement("div");
            errorElement.classList.add("error-message", "business-hours-error");
            block.appendChild(errorElement);
        }
        errorElement.textContent = errorMessage;
        errorElement.style.display = errorMessage ? "block" : "none";
    });

    return valid;
}


// Canteen form validation
function validateCanteenForm(form) {
    let valid = true;
    if (!validateText(form.querySelector("#canteen-name"), "Canteen Name")) valid = false;
    if (!validateText(form.querySelector("#canteen-address"), "Address")) valid = false;
    if (!validateImage(form.querySelector("#canteen-image"))) valid = false;
    if (!validateBusinessHours(form)) valid = false;
    return valid;
}

// Stall form validation
function validateStallForm(form) {
    let valid = true;
    if (!validateText(form.querySelector("#stall-name"), "Stall Name")) valid = false;
    if (!validateDropdown(form.querySelector("#stall-cuisine-type"), "Cuisine Type", "Choose a cuisine")) valid = false;
    if (!validateDropdown(form.querySelector("#vendor-id"), "Vendor", "Choose a vendor")) valid = false;
    if (!validateDropdown(form.querySelector("#canteen-id"), "Canteen", "Choose a canteen")) valid = false;
    return valid;
}

// Vendor form validation
function validateVendorForm(form) {
    let isValid = true;

    // Validate text fields
    if (!validateText(form.querySelector("#vendor-username"), "Username")) isValid = false;
    if (!validateEmail(form.querySelector("#vendor-email"))) isValid = false;
    if (!validatePassword(form.querySelector("#vendor-password"))) isValid = false;
    if (!validateConfirmPassword(form.querySelector("#vendor-cpassword"), form.querySelector("#vendor-password"))) isValid = false;
    if (!validateText(form.querySelector("#vendor-name"), "Name")) isValid = false;
    if (!validateText(form.querySelector("#vendor-business-name"), "Business Name")) isValid = false;
    if (!validateText(form.querySelector("#vendor-contact-number"), "Contact Number")) isValid = false;

    return isValid;
}

// Edit Canteen form validation
function validateEditCanteenForm(form) {
    let valid = true;
    if (!validateText(form.querySelector("input[name='name']"), "Canteen Name")) valid = false;
    if (!validateText(form.querySelector("input[name='address']"), "Address")) valid = false;
    if (form.querySelector("input[name='image']").files.length > 0) {
        if (!validateImage(form.querySelector("input[name='image']"))) valid = false;
    }
    if (!validateBusinessHours(form)) valid = false;
    return valid;
}

// Edit Stall form validation
function validateEditStallForm(form) {
    let valid = true;
    if (!validateText(form.querySelector("input[name='name']"), "Stall Name")) valid = false;
    return valid;
}

function validateEditVendorForm(form) {
    let isValid = true;

    // Validate each field
    if (!validateText(form.username, "Username")) isValid = false;
    if (!validateEmail(form.email)) isValid = false;
    if (!validateText(form.vendor_name, "Name")) isValid = false;
    if (!validateText(form.business_name, "Business Name")) isValid = false;
    if (!validatePhoneNumber(form.contact_number)) isValid = false;

    return isValid;
}
