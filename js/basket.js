// Initialize basket from localStorage or with an empty array if none exists
let basket = getBasket();

// Function to get basket from localStorage
function getBasket() {
    return JSON.parse(localStorage.getItem('basket')) || [];
}

// Function to save basket to localStorage
function saveBasket(basket) {
    localStorage.setItem('basket', JSON.stringify(basket));
}


function showNotification(message) {
    const notification = document.getElementById('notification');
    notification.textContent = message; // Set message content
    notification.classList.add('show'); // Add the 'show' class to trigger animation
    notification.style.display = 'block'; // Ensure it's visible

    console.log('Notofocation should show:', message);

    setTimeout(() => {
        notification.classList.remove('show'); // Remove 'show' class after 3 seconds
    }, 3000);
}



// Function to render basket items and update total price
function renderBasket() {
    const basketContainer = document.getElementById("basket-container");
    const totalPriceElement = document.getElementById("total-price");

    basketContainer.innerHTML = ""; // Clear current basket content
    let totalPrice = 0;

    basket.forEach((item, index) => {
        const itemElement = document.createElement("div");
        itemElement.className = "basket-item";
        itemElement.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="item-image">  <!-- Display item image -->
            <p>Product: <span class="item-name">${item.name}</span></p>  <!-- Display item name -->
            <p>Price: £<span class="item-price">${item.price.toFixed(2)}</span></p>
            <p>Quantity: <input type="number" class="item-quantity" value="${item.quantity}" min="1" data-index="${index}"></p>
            <button class="remove-item" data-index="${index}">Remove</button>
        `;

        basketContainer.appendChild(itemElement);
        totalPrice += item.price * item.quantity; // Update total price based on quantity
    });

    totalPriceElement.textContent = totalPrice.toFixed(2); // Update total price on the page
}

// Function to add a product to the basket
function addToBasket(product) {
    const existingProduct = basket.find(item => item.name === product.name);

    if (existingProduct) {
        existingProduct.quantity += 1; // Increase quantity if product already in basket
    } else {
        basket.push({ ...product, quantity: 1 }); // Add new product
    }

    saveBasket(basket); // Save updated basket to localStorage
    console.log('Product added to basket:', product); 
    showNotification(`${product.name} added to the basket!`);
}

// Event listener for changing item quantities
document.addEventListener("change", (e) => {
    if (e.target.classList.contains("item-quantity")) {
        const index = parseInt(e.target.dataset.index, 10);
        let newQuantity = Math.max(1, parseInt(e.target.value, 10)); // Ensure positive quantity
        basket[index].quantity = newQuantity;
        saveBasket(basket);
        renderBasket();
    }
});

// Event listener for removing items from the basket
document.addEventListener("click", (e) => {
    if (e.target.classList.contains("remove-item")) {
        const index = parseInt(e.target.dataset.index, 10);
        basket.splice(index, 1); // Remove item at specified index
        saveBasket(basket); // Save updated basket
        renderBasket(); // Update basket UI
        showNotification(`Item removed from the basket.`);
    }
});

// Event listener for adding items to the basket (triggered by buttons)
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded fired');
    const addToBasketButtons = document.querySelectorAll('.add-to-cart');

    addToBasketButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                image: button.dataset.image,
            };

            addToBasket(product); // Add product to the basket
        });
    });

});