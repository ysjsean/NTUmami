// Cleave.js formatting
new Cleave('#card_number', { creditCard: true });
new Cleave('#expiry_date', { date: true, datePattern: ['m', 'y'] });

// Luhn algorithm for card number validation
function isLuhnValid(cardNumber) {
    let sum = 0;
    let shouldDouble = false;

    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber.charAt(i));
        if (shouldDouble) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
        shouldDouble = !shouldDouble;
    }

    return sum % 10 === 0;
}

// Validation functions for each field
function validateCardNumber() {
    const cardNumber = document.getElementById("card_number").value.replace(/\s+/g, '');
    const cardNumberError = document.getElementById("card_number_error");

    if (cardNumber.length < 13 || cardNumber.length > 16 || !isLuhnValid(cardNumber)) {
        cardNumberError.textContent = "Please enter a valid card number.";
        return false;
    } else {
        cardNumberError.textContent = "";
        return true;
    }
}

function validateExpiryDate() {
    const expiryDateInput = document.getElementById("expiry_date").value;
    const expiryDateError = document.getElementById("expiry_date_error");

    // Ensure correct format MM/YY
    const match = expiryDateInput.match(/^(0[1-9]|1[0-2])\/(\d{2})$/);
    if (!match) {
        expiryDateError.textContent = "Invalid expiry date (MM/YY).";
        return false;
    }

    // Extract month and year
    const month = parseInt(match[1], 10);
    const year = parseInt("20" + match[2], 10); // Convert YY to 20YY

    // Get the current month and year
    const today = new Date();
    const currentMonth = today.getMonth() + 1; // JavaScript months are 0-based
    const currentYear = today.getFullYear();

    // Check if the expiry date is in the past
    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        expiryDateError.textContent = "Card has expired.";
        return false;
    }

    expiryDateError.textContent = ""; // Clear any previous error
    return true;
}


function validateCVV() {
    const cvv = document.getElementById("cvv").value;
    const cvvError = document.getElementById("cvv_error");

    if (!/^\d{3}$/.test(cvv)) {
        cvvError.textContent = "Please enter a valid 3-digit CVV.";
        return false;
    } else {
        cvvError.textContent = "";
        return true;
    }
}

function validateCardholderName() {
    const cardholderName = document.getElementById("cardholder_name").value.trim();
    const cardholderNameError = document.getElementById("cardholder_name_error");

    if (cardholderName === "") {
        cardholderNameError.textContent = "Please enter the cardholder's name.";
        return false;
    } else {
        cardholderNameError.textContent = "";
        return true;
    }
}


function validateCVVPerCard(element) {
    const id = element.id;
    const cvv = element.value;
    const cvvError = document.getElementById(`${id}_error`);

    if (!/^\d{3}$/.test(cvv)) {
        cvvError.textContent = "Please enter a valid 3-digit CVV.";
        return false;
    } else {
        cvvError.textContent = "";
        return true;
    }
}

// Real-time validation
document.getElementById("card_number").addEventListener("input", validateCardNumber);
document.getElementById("expiry_date").addEventListener("input", validateExpiryDate);
document.getElementById("cvv").addEventListener("input", validateCVV);
document.getElementById("cardholder_name").addEventListener("input", validateCardholderName);

// Validate on form submission
document.querySelector(".secure-payment-form").addEventListener("submit", function (event) {
    let isError = false;
    const paymentTypes = document.querySelectorAll(".payment-method-toggle input[name=payment_method_type]");
    
    for (i = 0; i < paymentTypes.length; i++) {
        let paymentType = paymentTypes[i];
        if (paymentType.checked) {
            switch (paymentType.id) {
                case "use_saved_payment":
                    let isSavedCardCvvValid = false;
                    const cards = document.querySelectorAll("#saved_payment_methods .saved-card-option");

                    for (j = 0; j < cards.length; j++) {
                        let card = cards[j];
                        let radio = card.querySelector("input[type=radio]");
                        if (radio.checked) {
                            let cvv = card.querySelector(".cvv-input input[type=password]");
                            isSavedCardCvvValid = validateCVVPerCard(cvv);
                        }
                        
                    }

                    if (!isSavedCardCvvValid)
                        isError = true;

                    break;
                case "use_new_payment":
                    const isCardValid = validateCardNumber();
                    const isExpiryValid = validateExpiryDate();
                    const isCvvValid = validateCVV();
                    const isNameValid = validateCardholderName();

                    if (!isCardValid || !isExpiryValid || !isCvvValid || !isNameValid) {
                        isError = true;
                    }
                    
                    break;
                default:
                    isError = true;
            }
        }
    }
    
    if (isError)
        event.preventDefault(); // Prevent submission if validation fails
});
