document.addEventListener("DOMContentLoaded", function () {
    // Select all add and edit food forms
    const addFoodForm = document.getElementById("add-food-form");
    const editFoodForms = document.querySelectorAll(".food-items-card form");

    // Attach validation for the add food form
    if (addFoodForm) {
        addFoodForm.addEventListener("input", validateField);
        addFoodForm.addEventListener("submit", validateFoodFormOnSubmit);
    }

    // Attach validation for each edit food form
    editFoodForms.forEach(form => {
        form.addEventListener("input", validateField);
        form.addEventListener("submit", validateFoodFormOnSubmit);
    });

    // Function to validate a single field
    function validateField(event) {
        const field = event.target;
        const errorMessage = validateSingleFoodField(field);
        displayError(field, errorMessage);
    }

    // Function to validate individual fields based on ID patterns
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
            // For add form, image is required; for edit form, validate only if provided
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

    // Function to display or hide the error message
    function displayError(field, errorMessage) {
        const errorDivId = field.getAttribute("id") + "-error";
        const errorDiv = document.getElementById(errorDivId);

        if (errorDiv) {
            errorDiv.innerHTML = errorMessage;
            errorDiv.style.display = errorMessage ? "block" : "none";
        }
    }

    // Function to validate the entire form on submit
    function validateFoodFormOnSubmit(event) {
        event.preventDefault();
        let isValid = true;

        // Define fields based on prefix (for both add and edit)
        const formIdPrefix = event.target.getAttribute("id").includes("add") ? "food-" : "food-edit-";
        const foodId = event.target.getAttribute("id").split("-")[2] || ""; // Extract the food ID for edit forms

        // Validate each required field in the form
        ["name", "price", "image"].forEach(field => {
            const fieldElement = document.getElementById(`${formIdPrefix}${foodId}-${field}`);
            const errorMessage = validateSingleFoodField(fieldElement);
            displayError(fieldElement, errorMessage);

            if (errorMessage) isValid = false;
        });

        // If all fields are valid, submit the form
        if (isValid) {
            event.target.submit();
        }
    }
});
