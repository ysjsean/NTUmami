const moreButton = document.querySelector('.more-button');
const morePanel = document.querySelector('.more-panel');
const closeMore = document.querySelector('.close-more');
const darkOverlay = document.querySelector('.dark-overlay');

moreButton.addEventListener('click', () => {
    morePanel.classList.toggle('show');
    darkOverlay.classList.toggle('show');
});

closeMore.addEventListener('click', () => {
    morePanel.classList.remove('show');
    darkOverlay.classList.remove('show');
});

darkOverlay.addEventListener('click', () => {
    morePanel.classList.remove('show');
    darkOverlay.classList.remove('show');
});
