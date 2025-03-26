# TrendSwap E-commerce Website

TrendSwap is an e-commerce website that was designed to provide users with a worthwhile shopping experience , with a focus on both new and second-hand designer clothing that encourages sustainable fashion. The main aim of the project is to design a unique and efficient platform that allows our users to have access to a safe and reliable shopping experience that also caters to their needs.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation](#installation)
3. [Database Setup](#database-setup)
4. [Admin Access](#admin-access)
5. [Features](#features)
6. [File Structure](#file-structure)
7. [Recent Enhancements](#recent-enhancements)
8. [Troubleshooting](#troubleshooting)

## System Requirements

- PHP 8.0+
- MySQL 8.0+
- Apache/Nginx web server
- Web browser (Chrome, Firefox, Safari, Edge)

## Installation

1. **Clone or download the project files**
   - Place all files in your web server's document root or a subdirectory

2. **Configure database connection**
   - Open `db_connect.php` and update the database credentials:
   ```php
   <?php
   // Database connection parameters
   $servername = "localhost";
   $username = "your_db_username";
   $password = "your_db_password";
   $dbname = "cs2team2_db";
   
   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   
   // Check connection
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   

## Database Setup

1. **Create database**
   ```sql
   CREATE DATABASE cs2team2_db;

2. **Import schema and data**
   - Using phpMyAdmin, MySQL Workbench, or command line:
   ```
   mysql -u username -p cs2team2_db < cs2team2_db-2.sql
   ```
   - Alternatively, use the provided SQL file via phpMyAdmin import feature

3. **Database structure**
   The database has the following important  tables:
   - `users` - Customer accounts
   - `admins` - Admin users
   - `products` - Product information
   - `product_sizes` - Size variants and stock levels
   - `product_images` - Product images
   - `categories` - Product categories
   - `orders` - Customer orders
   - `order_items` - Items within orders
   - `carts` - Shopping carts
   - `cart_items` - Items in shopping carts
   - `wishlists` - Customer wishlists
   - `reviews` - Product reviews

## Admin Access

1. **Admin Login**
   - URL: `/adminLogin.php`
   - Default admin credentials:
     - Username: `admin_user`
     - Password: [///////]

2. **Admin Dashboard**
   - The dashboard provides access to:
     - Sales overview and statistics
     - Product management
     - Customer management
     - Order processing
     - Stock levels management
     - Sales analytics

## Features

### Customer Features

1. **User Authentication**
   - Registration: `/signup.php`
   - Login: `/login.php`
   - Account management: `/userPage.php`

2. **Product Browsing**
   - Homepage with featured products: `/index.php`
   - Category browsing:
     - Men: `/men.php`
     - Women: `/women.php`
     - Kids: `/kids.php`
     - Accessories: `/accessories.php`
     - Babies: `/babies.php`

3. **Shopping Features**
   - Product details: `/product_details.php?product_id=[id]`
   - Shopping cart: `/cart.php`
   - Wishlist: `/wishlist.php`
   - Checkout: `/checkout.php`
   - Order confirmation: `/order_confirmation.php`
   - Order history: `/previous_orders.php`

4. **Users**
   
   - Order returns: `/process_return.php`

### Admin Features

1. **Dashboard & Analytics**
   - Admin dashboard: `/admin_dashboard.php`
   - Sales analytics: `/sales_analytics.php`

2. **Product Management**
   - Product listing: `/product_management.php`
   - Add new products: `/add_product.php`
   - Edit products: `/edit_product.php`
   - Delete products: via `/product_management.php`

3. **Inventory Management**
   - Stock levels: `/stock_management.php`
   - Low stock alerts (shown on dashboard)

4. **Customer Management**
   - Customer listing: `/customer_management.php`
   - Customer details: `/customer_management.php?view_customer=[id]`
   - Edit customer: `/edit_customer.php`

5. **Order Management**
   - Order listing: `/order_management.php`
   - Order details: `/order_management.php?view_order=[id]`
   - Update order status (processing, shipping, etc.)
   - Handle returns: Available from order details



