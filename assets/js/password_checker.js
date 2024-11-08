document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrength = document.getElementById('password-strength');
    const passwordMatch = document.getElementById('password-match');

    // Password strength checker
    passwordInput.addEventListener('input', function () {
        const strength = checkPasswordStrength(passwordInput.value);
        passwordStrength.textContent = `Password Strength: ${strength}`;
        passwordStrength.style.color = getStrengthColor(strength);
    });

    // Password match checker
    confirmPasswordInput.addEventListener('input', function () {
        if (passwordInput.value === confirmPasswordInput.value) {
            passwordMatch.textContent = 'Passwords match!';
            passwordMatch.style.color = 'green';
        } else {
            passwordMatch.textContent = 'Passwords do not match!';
            passwordMatch.style.color = 'red';
        }
    });

    function checkPasswordStrength(password) {
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (strength === 0) return 'Weak';
        if (strength <= 2) return 'Moderate';
        if (strength === 3) return 'Strong';
        return 'Very Strong';
    }

    function getStrengthColor(strength) {
        switch (strength) {
            case 'Weak':
                return 'red';
            case 'Moderate':
                return 'orange';
            case 'Strong':
                return 'blue';
            case 'Very Strong':
                return 'green';
            default:
                return 'black';
        }
    }
});
