document.addEventListener("DOMContentLoaded", () => {
    fetchCanteens();
});

async function fetchCanteens() {
    try {
        const response = await fetch('/api/canteens');
        const canteens = await response.json();
        
        const locationsContainer = document.getElementById("locations");
        
        canteens.forEach(canteen => {
            const card = document.createElement("div");
            card.classList.add("location-card");
            
            card.innerHTML = `
                <img src="${canteen.image_url}" alt="${canteen.names}">
                <h3>${canteen.names}</h3>
                <p class="location-address">${canteen.address}</p>
                <p>${canteen.description}</p>
            `;
            
            locationsContainer.appendChild(card);
        });
    } catch (error) {
        console.error("Error fetching canteens:", error);
    }
}
