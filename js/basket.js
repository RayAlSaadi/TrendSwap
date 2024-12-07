
let basket = [
    { name: "Product Name", price: 50.00, quantity: 1 }
];

function renderBasket() {
    const basketContainer = document.getElementById("basket-container");
    const totalPriceElement = document.getElementById("total-price");

    basketContainer.innerHTML = ""; 
    let totalPrice = 0;

    basket.forEach((item, index) => {
        const itemElement = document.createElement("div");
        itemElement.className = "basket-item";
        itemElement.innerHTML = `
            <p>Item ${index + 1}: <span class="item-name">${item.name}</span></p>
            <p>Price: $<span class="item-price">${item.price.toFixed(2)}</span></p>
            <p>Quantity: <input type="number" class="item-quantity" value="${item.quantity}" min="1" data-index="${index}"></p>
            <button class="remove-item" data-index="${index}">Remove</button>
        `;

        basketContainer.appendChild(itemElement);
        totalPrice += item.price * item.quantity;
    });

    totalPriceElement.textContent = totalPrice.toFixed(2);
}

document.addEventListener("change", (e) => {
    if (e.target.classList.contains("item-quantity")) {
        const index = e.target.getAttribute("data-index");
        const newQuantity = parseInt(e.target.value);
        basket[index].quantity = newQuantity;
        renderBasket();
    }
});

basketContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-item')) {
        const basket = getBasket(); 
        const productName = e.target.dataset.name; 
        
        const updatedBasket = basket.filter(item => item.name !== productName);
        
        saveBasket(updatedBasket); 
        displayBasket(); 
    }
});


function getBasket() {
    return JSON.parse(localStorage.getItem('basket')) || [];
}

function saveBasket(basket) {
    localStorage.setItem('basket', JSON.stringify(basket));
}

function addToBasket(product) {
    const basket = getBasket();
    const existingProduct = basket.find(item => item.name === product.name);

    if (existingProduct) {
        existingProduct.quantity += 1; 
    } else {
        basket.push({ ...product, quantity: 1 }); 
    }

    saveBasket(basket);
    const notification = document.getElementById('notification');
    notification.style.display = 'block';
    notification.textContent = `${product.name} added to the basket`;

    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    const addToBasketButtons = document.querySelectorAll('.add-to-basket');

    addToBasketButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                name: button.dataset.name,
                price: parseFloat(button.dataset.price),
                image: button.dataset.image,
            };

            addToBasket(product);
        });
    });
});


renderBasket();

