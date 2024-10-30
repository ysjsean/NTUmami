document.addEventListener("DOMContentLoaded", function() {
    const notificationElement = document.getElementById('notification');

    // If there's a notification message, display it
    if (notificationElement && notificationElement.innerText.trim() !== '') {
        notificationElement.classList.add('show'); // Display the notification
        setTimeout(() => {
            notificationElement.classList.add('fade-out'); // Start fading out after 3 seconds
        }, 3000);

        setTimeout(() => {
            notificationElement.classList.remove('show'); // Hide it completely after fade-out
            notificationElement.style.display = 'none';
        }, 3500); // Allow 0.5 seconds for the fade-out animation
    }
});
