document.addEventListener('DOMContentLoaded', function() {
    const canteenContainer = document.getElementById('canteenContainer');
    let canteens = [], stalls = [], foods = [];

    // Fetch data from the PHP script
    fetch('menu.php?data=json')
        .then(response => response.json())
        .then(data => {
            canteens = data.canteens;
            stalls = data.stalls;
            foods = data.foods;
            displayMenu();
        })
        .catch(error => console.error('Error fetching menu data:', error));

    // Toggle dropdowns on label click
    document.querySelectorAll('.dropdown label').forEach(label => {
        label.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent the event from bubbling up
            const dropdown = this.parentElement;
            dropdown.classList.toggle('active');
        });
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', function(event) {
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    });

    // Apply filters when checkboxes are changed
    document.querySelectorAll('.filter-option').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilter);
    });

    // Gather selected filter values
    function getSelectedValues(className) {
        return Array.from(document.querySelectorAll(`.${className}:checked`)).map(checkbox => checkbox.value);
    }

    // Apply filtering logic
    function applyFilter() {
        const selectedCanteens = getSelectedValues('canteen-option');
        const selectedCuisines = getSelectedValues('cuisine-option');
        const selectedDietary = getSelectedValues('dietary-option');
    
        console.log('Selected Canteens:', selectedCanteens);
        console.log('Selected Cuisines:', selectedCuisines);
        console.log('Selected Dietary:', selectedDietary);
    
        const filteredStalls = stalls.filter(stall =>
            (selectedCanteens.length === 0 || selectedCanteens.includes(stall.canteen_id.toString())) &&
            (selectedCuisines.length === 0 || selectedCuisines.includes(stall.cuisine_type))
        );
    
        const filteredFoods = foods.filter(food => {
            const isHalal = food.is_halal === 1;
            const isVegetarian = food.is_vegetarian === 1;
    
            const matchesDietary = selectedDietary.length === 0 ||
                (selectedDietary.includes('halal') && isHalal) ||
                (selectedDietary.includes('vegetarian') && isVegetarian);
    
            return matchesDietary && filteredStalls.some(stall => stall.id === food.stall_id);
        });
    
        console.log('Filtered Stalls:', filteredStalls);
        console.log('Filtered Foods:', filteredFoods);
    
        displayFilteredMenu(filteredStalls, filteredFoods);
    }
    

    // Display menu based on filter results
    function displayFilteredMenu(filteredStalls, filteredFoods) {
        canteenContainer.innerHTML = '';

        canteens.forEach(canteen => {
            const canteenDiv = document.createElement('div');
            canteenDiv.classList.add('canteen');
            canteenDiv.innerHTML = `<h2>${canteen.name}</h2>`;

            filteredStalls.filter(stall => stall.canteen_id === canteen.id).forEach(stall => {
                const stallDiv = document.createElement('div');
                stallDiv.classList.add('stall');
                stallDiv.innerHTML = `<h3>${stall.name}</h3>`;

                const foodContainer = document.createElement('div');
                foodContainer.classList.add('food-container');

                filteredFoods.filter(food => food.stall_id === stall.id).forEach(food => {
                    const foodItem = document.createElement('div');
                    foodItem.classList.add('food-item');

                    const halalIcon = food.is_halal === 1 ? '<span class="icon halal">🕌 Halal</span>' : '';
                    const vegetarianIcon = food.is_vegetarian === 1 ? '<span class="icon vegetarian">🌱 Vegetarian</span>' : '';

                    foodItem.innerHTML = `
                        <img src="${food.image_url}" alt="${food.name}">
                        <p>${food.name}</p>
                        <p class="description">${food.description}</p>
                        <div class="dietary-icons">
                            ${halalIcon} ${vegetarianIcon}
                        </div>
                        <p class="price">$${parseFloat(food.price).toFixed(2)}</p>
                    `;
                    foodContainer.appendChild(foodItem);
                });

                stallDiv.appendChild(foodContainer);
                canteenDiv.appendChild(stallDiv);
            });

            if (canteenDiv.querySelector('.stall')) {
                canteenContainer.appendChild(canteenDiv);
            }
        });
    }

    // Initial menu display without any filters applied
    function displayMenu() {
        canteenContainer.innerHTML = '';
        
        canteens.forEach(canteen => {
            const canteenDiv = document.createElement('div');
            canteenDiv.classList.add('canteen');
            canteenDiv.innerHTML = `<h2>${canteen.name}</h2>`;

            stalls.filter(stall => stall.canteen_id === canteen.id).forEach(stall => {
                const stallDiv = document.createElement('div');
                stallDiv.classList.add('stall');
                stallDiv.innerHTML = `<h3>${stall.name}</h3>`;

                const foodContainer = document.createElement('div');
                foodContainer.classList.add('food-container');

                foods.filter(food => food.stall_id === stall.id).forEach(food => {
                    const foodItem = document.createElement('div');
                    foodItem.classList.add('food-item');

                    const halalIcon = food.is_halal === 1 ? '<span class="icon halal">🕌 Halal</span>' : '';
                    const vegetarianIcon = food.is_vegetarian === 1 ? '<span class="icon vegetarian">🌱 Vegetarian</span>' : '';

                    foodItem.innerHTML = `
                        <img src="${food.image_url}" alt="${food.name}">
                        <p>${food.name}</p>
                        <p class="description">${food.description}</p>
                        <div class="dietary-icons">
                            ${halalIcon} ${vegetarianIcon}
                        </div>
                        <p class="price">$${parseFloat(food.price).toFixed(2)}</p>
                    `;
                    foodContainer.appendChild(foodItem);
                });

                stallDiv.appendChild(foodContainer);
                canteenDiv.appendChild(stallDiv);
            });

            canteenContainer.appendChild(canteenDiv);
        });
    }
});
