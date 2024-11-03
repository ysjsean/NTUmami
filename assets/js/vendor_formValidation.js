document.addEventListener("DOMContentLoaded", function () {
    // Select all add and edit food forms and profile form
    const addFoodForm = document.getElementById("add-food-form");
    const editFoodForms = document.querySelectorAll(".food-items-card form");
    const profileForm = document.getElementById("profile-edit");
    const stallEditForm = document.getElementById("stall-edit");

    // Attach validation for the add food form
    if (addFoodForm) {
        addFoodForm.addEventListener("input", validateField);
        addFoodForm.addEventListener("submit", (e) => validateFoodFormOnSubmit(e, "food"));
    }

    // Attach validation for each edit food form
    editFoodForms.forEach(form => {
        form.addEventListener("input", validateField);
        form.addEventListener("submit", (e) => validateFoodFormOnSubmit(e, "food"));
    });

    // Attach validation for the profile form
    if (profileForm) {
        profileForm.addEventListener("input", validateField);
        profileForm.addEventListener("submit", (e) => validateProfileFormOnSubmit(e, "profile"));
    }

    if (stallEditForm) {
        stallEditForm.addEventListener("input", validateStallField);
        stallEditForm.addEventListener("submit", validateStallFormOnSubmit);
    }

    // Function to validate a single field
    function validateField(event) {
        const field = event.target;
        let errorMessage = "";

        if (field.closest("#add-food-form") || field.closest(".food-items-card form")) {
            errorMessage = validateSingleFoodField(field);
        } else if (field.closest("#profile-edit")) {
            errorMessage = validateSingleProfileField(field);
        }

        displayError(field, errorMessage);
    }

    // Food-specific field validation based on ID patterns
    function validateSingleFoodField(field) {
        const fieldId = field.getAttribute("id");
        const fieldValue = field.value.trim();
        let errorMessage = "";

        if (fieldId.endsWith("-name")) {
            if (!fieldValue) {
                errorMessage = "Name is required!";
            }
        } else if (fieldId.endsWith("-price")) {
            if (!fieldValue) {
                errorMessage = "Price is required!";
            } else if (isNaN(fieldValue) || Number(fieldValue) <= 0) {
                errorMessage = "Price must be a positive number!";
            }
        } else if (fieldId.endsWith("-image")) {
            if (field.closest("form").getAttribute("id") === "add-food-form" || field.files.length > 0) {
                const file = field.files[0];
                const validExtensions = ["image/jpeg", "image/png"];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!validExtensions.includes(file.type)) {
                    errorMessage = "Image must be in JPEG or PNG format!";
                } else if (file.size > maxSize) {
                    errorMessage = "Image must be under 2MB!";
                }
            }
        }
        return errorMessage;
    }

    // Profile-specific field validation based on field IDs
    function validateSingleProfileField(field) {
        const fieldId = field.getAttribute("id");
        const fieldValue = field.value.trim();
        let errorMessage = "";

        if (fieldId === "name" || fieldId === "business_name") {
            if (!fieldValue) {
                errorMessage = `${fieldId.replace("_", " ")} is required!`;
            }
        } else if (fieldId === "contact_number") {
            const sgPhonePattern = /^[689]\d{7}$/;
            if (!sgPhonePattern.test(fieldValue)) {
                errorMessage = "Please enter a valid Singapore phone number.";
            }
        } else if (fieldId === "password" || fieldId === "confirm_password") {
            if (fieldValue) {
                const hasLength = fieldValue.length >= 8;
                const hasUpperCase = /[A-Z]/.test(fieldValue);
                const hasNumber = /[0-9]/.test(fieldValue);
                const hasSpecialChar = /[^a-zA-Z0-9]/.test(fieldValue);

                if (fieldId === "password") {
                    if (!hasLength) {
                        errorMessage = "Password must be at least 8 characters long.";
                    } 
                    
                    if (!hasUpperCase) {
                        if (errorMessage)
                            errorMessage += "<br>";
                        errorMessage += "Password must contain at least one uppercase letter.";
                    } 
                    
                    if (!hasNumber) {
                        if (errorMessage)
                            errorMessage += "<br>";
                        errorMessage += "Password must contain at least one number.";
                    } 
                    
                    if (!hasSpecialChar) {
                        if (errorMessage)
                            errorMessage += "<br>";
                        errorMessage += "Password must contain at least one special character.";
                    }
                }

                if (fieldId === "confirm_password") {
                    const passwordField = document.getElementById("password");
                    if (passwordField.value && passwordField.value !== fieldValue) {
                        errorMessage = "Passwords do not match!";
                    }
                }
            }
        }
        return errorMessage;
    }


    // Stall-specific field validation
    function validateStallField(event) {
        const field = event.target;
        const errorMessage = validateSingleStallField(field);
        displayError(field, errorMessage);
    }

    function validateSingleStallField(field) {
        const fieldId = field.getAttribute("id");
        const fieldValue = field.value.trim();
        let errorMessage = "";

        if (fieldId === "stall-name") {
            if (!fieldValue) {
                errorMessage = "Stall name is required!";
            }
        } else if (fieldId === "stall-cuisine-type") {
            if (!fieldValue) {
                errorMessage = "Cuisine type is required!";
            }
        }
        return errorMessage;
    }

    function validateStallFormOnSubmit(event) {
        event.preventDefault();
        let isValid = true;

        const fields = ["stall-name", "stall-cuisine-type"];
        fields.forEach(id => {
            const field = document.getElementById(id);
            const errorMessage = validateSingleStallField(field);
            displayError(field, errorMessage);

            if (errorMessage) isValid = false;
        });

        if (isValid) {
            event.target.submit();
        }
    }

    // Function to display or hide the error message
    function displayError(field, errorMessage) {
        const errorDivId = field.getAttribute("id") + "-error";
        const errorDiv = document.getElementById(errorDivId);

        if (errorDiv) {
            errorDiv.innerHTML = errorMessage;
            errorDiv.style.display = errorMessage ? "block" : "none";
        }
    }

    // Function to validate the entire food form on submit
    function validateFoodFormOnSubmit(event, formType) {
        event.preventDefault();
        let isValid = true;

        const formIdPrefix = formType === "food" ? (event.target.getAttribute("id").includes("add") ? "food-" : "food-edit-") : "";
        const foodId = event.target.getAttribute("id").split("-")[2] || "";

        ["name", "price", "image"].forEach(field => {
            const fieldElement = document.getElementById(`${formIdPrefix}${foodId}-${field}`);
            const errorMessage = validateSingleFoodField(fieldElement);
            displayError(fieldElement, errorMessage);

            if (errorMessage) isValid = false;
        });

        if (isValid) {
            event.target.submit();
        }
    }

    // Function to validate the entire profile form on submit
    function validateProfileFormOnSubmit(event, formType) {
        event.preventDefault();
        let isValid = true;

        ["name", "business_name", "contact_number", "password", "confirm_password"].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const errorMessage = validateSingleProfileField(field);
            displayError(field, errorMessage);

            if (errorMessage) isValid = false;
        });

        if (isValid) {
            event.target.submit();
        }
    }
});
