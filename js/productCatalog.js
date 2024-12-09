// productCatalog.js

// Product catalog database
const productCatalog = {
    products: [
        {
            id: 1,
            brand: "STUDIO NICHOLSON",
            name: "Leather Piston Bomber Jacket",
            price: 1095.00,
            image: "Images/menshirt.avif",
            category: "men",
            description: "Luxury leather bomber jacket"
        },
        {
            id: 2,
            brand: "AMIRI",
            name: "Billiards Tapestry Jacket",
            price: 1710.00,
            image: "Images/men2.avif",
            category: "men",
            description: "Designer tapestry jacket"
        },
        {
            id: 3,
            brand: "MOOSE KNUCKLES",
            name: "Down Cloud Puffer Jacket",
            price: 1030.00,
            image: "Images/men5.avif",
            category: "men",
            description: "Warm winter puffer jacket"
        },
        {
            id: 4,
            brand: "Brand Name",
            name: "Leather Jacket",
            price: 1295.00,
            image: "Images/men3.avif",
            category: "men",
            description: "Classic leather jacket"
        },
        {
            id: 5,
            brand: "Brand Name",
            name: "Down Puffer Jacket",
            price: 1150.00,
            image: "Images/men5.avif",
            category: "men",
            description: "Stylish puffer jacket"
        },
        {
            id: 6,
            brand: "Brand Name",
            name: "Winter Jacket",
            price: 1350.00,
            image: "Images/mr.avif",
            category: "men",
            description: "Premium winter jacket"
        },

        {
            id: 7,
            brand: "ELIE SAAB",
            name: "Elegant Evening Gown",
            price: 1250.00,
            image: "Images/womenpuffer2.avif",
            category: "women",
            description: "Luxurious evening gown"
        },
        {
            id: 8,
            brand: "CHRISTIAN LOUBOUTIN",
            name: "Designer Heels",
            price: 895.00,
            image: "Images/womendress1.avif",
            category: "women",
            description: "High-end designer heels"
        },
        {
            id: 9,
            brand: "BURBERRY",
            name: "Classic Trench Coat",
            price: 1695.00,
            image: "Images/womensweater1.avif",
            category: "women",
            description: "Timeless trench coat for any season"
        },
        {
            id: 10,
            brand: "GUCCI",
            name: "Luxury Handbag",
            price: 2200.00,
            image: "Images/womencoat2.avif",
            category: "women",
            description: "Premium luxury handbag"
        },
        {
            id: 11,
            brand: "MONCLER",
            name: "Down Jacket",
            price: 1350.00,
            image: "Images/womensweater3.avif",
            category: "women",
            description: "Stylish and warm down jacket"
        },
        {
            id: 12,
            brand: "MIU MIU",
            name: "Pleated Midi Skirt",
            price: 695.00,
            image: "Images/womenpuffer3.avif",
            category: "women",
            description: "Elegant pleated midi skirt"
        },

        {
            id: 13,
            brand: "TUDOR",
            name: "Black Bay Harrods",
            price: 3550.00,
            image: "Images/girls.avif",
            category: "kids",
            description: "Luxury watch designed for young adventurers"
        },
        {
            id: 14,
            brand: "CARTIER",
            name: "Santos Steel",
            price: 7400.00,
            image: "Images/girls3.avif",
            category: "kids",
            description: "Timeless design, perfect for kids with a taste for elegance"
        },
        {
            id: 15,
            brand: "CARTIER",
            name: "Santos Rose Gold",
            price: 5800.00,
            image: "Images/boys2.webp",
            category: "kids",
            description: "Sophisticated rose gold watch for young fashionistas"
        },
        {
            id: 16,
            brand: "Brand Name",
            name: "New Brand Watch",
            price: 2500.00,
            image: "Images/boys1.avif",
            category: "kids",
            description: "Stylish kids' watch with modern appeal"
        },
        {
            id: 17,
            brand: "Brand Name",
            name: "Classic Chronograph",
            price: 4200.00,
            image: "Images/girls.avif",
            category: "kids",
            description: "Classic chronograph watch suitable for any young adventurer"
        },

        {
            id: 18,
            brand: "TUDOR",
            name: "Black Bay Harrods",
            price: 3550.00,
            image: "Images/menwatch8.avif",
            category: "accessories",
            description: "Luxury watch with exclusive design for Harrods"
        },
        {
            id: 19,
            brand: "CARTIER",
            name: "Santos Steel",
            price: 7400.00,
            image: "Images/menwatch!",
            category: "accessories",
            description: "Timeless steel watch with iconic design"
        },
        {
            id: 20,
            brand: "CARTIER",
            name: "Santos Rose Gold",
            price: 5800.00,
            image: "Images/santos.avif",
            category: "accessories",
            description: "Sophisticated rose gold watch, perfect for luxury enthusiasts"
        },
        {
            id: 21,
            brand: "Brand Name",
            name: "New Brand Watch",
            price: 2500.00,
            image: "Images/menwatch copy.avif",
            category: "accessories",
            description: "A modern and stylish watch with advanced features"
        },
        {
            id: 22,
            brand: "Brand Name",
            name: "Classic Chronograph",
            price: 4200.00,
            image: "Images/menwatch7.avif",
            category: "accessories",
            description: "Classic chronograph watch with a timeless look"
        },
        {
            id: 23,
            brand: "Brand Name",
            name: "Cuddly Wear",
            price: 25.00,
            image: "Images/babies2.avif",
            category: "babies",
            description: "Soft Cotton Onesie"
        },
        {
            id: 24,
            brand: "Brand Name",
            name: "Little Knits",
            price: 35.00,
            image: "Images/babies3.avif",
            category: "babies",
            description: "Knitted Baby Sweater"
        },
        {
            id: 25,
            brand: "Brand Name",
            name: "Snug Bug",
            price: 30.00,
            image: "Images/babies4.avif",
            category: "babies",
            description: "Cozy Baby Romper"
        },
        {
            id: 26,
            brand: "Brand Name",
            name: "Tootsies",
            price: 20.00,
            image: "Images/babies5.avif",
            category: "babies",
            description: "Soft Sole Baby Shoes"
        },
        {
            id: 27,
            brand: "Brand Name",
            name: "Cozy Wrap",
            price: 40.00,
            image: "Images/babies6.avif",
            category: "babies",
            description: "Fluffy Baby Blanket"
        },
        {
            id: 28,
            brand: "Brand Name",
            name: "Warm Heads",
            price: 15.00,
            image: "Images/babies7.avif",
            category: "babies",
            description: "Knitted Baby Hat"
        }

        




    ],

    // Search functionality
    search(query) {
        query = query.toLowerCase().trim();
        
        if (!query) return this.products;

        return this.products.filter(product => {
            const searchText = `
                ${product.brand.toLowerCase()} 
                ${product.name.toLowerCase()} 
                ${product.description.toLowerCase()}
            `;
            return searchText.includes(query);
        });
    },

    // Get all products in a category
    getByCategory(category) {
        return this.products.filter(product => product.category === category);
    },

    // Get a single product by ID
    getById(id) {
        return this.products.find(product => product.id === id);
    }
};

// Search history functionality
const searchHistory = {
    history: [],
    maxItems: 5,

    add(query) {
        if (!query) return;
        
        // Remove duplicate if exists
        this.history = this.history.filter(item => item !== query);
        
        // Add to beginning of array
        this.history.unshift(query);
        
        // Keep only maxItems number of searches
        if (this.history.length > this.maxItems) {
            this.history.pop();
        }

        // Save to localStorage
        this.save();
    },

    save() {
        localStorage.setItem('searchHistory', JSON.stringify(this.history));
    },

    load() {
        const saved = localStorage.getItem('searchHistory');
        if (saved) {
            this.history = JSON.parse(saved);
        }
    },

    clear() {
        this.history = [];
        localStorage.removeItem('searchHistory');
    }
};

// Helper function to create product HTML
function createProductCard(product) {
    return `
        <div class="product" data-id="${product.id}">
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.brand}</h3>
            <p>${product.name}</p>
            <p>£${product.price.toFixed(2)}</p>
            <div class="product-actions">
                <button class="add-to-cart" 
                        data-id="${product.id}"
                        data-name="${product.name}"
                        data-price="${product.price}"
                        data-image="${product.image}">Add to Cart</button>
            </div>
        </div>
    `;
}

document.getElementById('search-bar').querySelector('input').addEventListener('input', function(event) {
    const query = event.target.value.trim();
    // Update search history with each search query
    searchHistory.add(query);

    // Get filtered products based on search query
    const results = productCatalog.search(query);
    
    // Display the results
    displayProducts(results);
});

// Function to display products
function displayProducts(products) {
    const productGrid = document.querySelector('.product-grid');
    productGrid.innerHTML = '';  // Clear current products

    if (products.length > 0) {
        products.forEach(product => {
            const productCard = createProductCard(product);
            productGrid.innerHTML += productCard;
        });
    } else {
        productGrid.innerHTML = '<p>No products found</p>';  // No products match
    }
}

// Load previous search history and display any results on page load
window.addEventListener('DOMContentLoaded', function() {
    searchHistory.load();
    const query = searchHistory.history[0] || '';  // Start with the last search query
    const results = productCatalog.search(query);
    displayProducts(results);
});