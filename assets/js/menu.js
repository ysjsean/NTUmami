// Show or hide the "Back to Top" button based on scroll position
window.addEventListener('scroll', function() {
    const backToTopButton = document.getElementById('backToTop');
    if (window.scrollY > 200) { // Show button after scrolling down 200px
        backToTopButton.style.display = 'block';
    } else {
        backToTopButton.style.display = 'none';
    }
});

// Function to scroll back to the top smoothly
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

 // Save the current scroll position to localStorage before form submission
 function saveScrollPosition() {
    localStorage.setItem('scrollPosition', window.scrollY);
}

// Restore the scroll position after page reload
window.addEventListener('load', function() {
    const scrollPosition = localStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, scrollPosition);
        localStorage.removeItem('scrollPosition'); // Clear it after restoring
    }
});

