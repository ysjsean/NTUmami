// assets/js/signupValidation.js

function validateName() {
    const name = document.getElementById('name');
    const nameError = document.getElementById('nameError');

    if (!/^[a-zA-Z\s]+$/.test(name.value)) {
        nameError.textContent = "Name should contain only alphabetic characters.";
        return false;
    } else {
        nameError.textContent = "";
        return true;
    }
}

function validateUsername() {
    const username = document.getElementById('username');
    const usernameError = document.getElementById('usernameError');

    if (!/^[a-zA-Z0-9]+$/.test(username.value)) {
        usernameError.textContent = "Username should contain only alphanumeric characters.";
        return false;
    } else {
        usernameError.textContent = "";
        return true;
    }
}

function validateEmail() {
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');

    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.value)) {
        emailError.textContent = "Please enter a valid email address.";
        return false;
    } else {
        emailError.textContent = "";
        return true;
    }
}

function validatePassword() {
    const password = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');

    if (password.value.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters long.";
        return false;
    } else {
        passwordError.textContent = "";
        return true;
    }
}

function validateConfirmPassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('cpassword');
    const confirmPasswordError = document.getElementById('cpasswordError');

    if (confirmPassword.value !== password) {
        confirmPasswordError.textContent = "Passwords do not match.";
        return false;
    } else {
        confirmPasswordError.textContent = "";
        return true;
    }
}

// Final form validation before submission
function finalValidateForm() {
    // Trigger all individual validations
    const isNameValid = validateName();
    const isUsernameValid = validateUsername();
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();
    const isConfirmPasswordValid = validateConfirmPassword();

    // Allow form submission only if all fields are valid
    return isNameValid && isUsernameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid;
}
