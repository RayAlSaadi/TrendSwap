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