<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css"> 
<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

<?php include 'phpLogic/header.php'; ?>

<!-- Link to CSS -->


<!-- Main Content -->
<main>
    <section id="contact">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Whether you have a question, feedback, or just want to say hello, feel free to reach out. Fill out the form below and we'll get back to you as soon as possible.</p>
        <div class="form-container">
            <form id="contactForm" action="https://formspree.io/f/mldekele" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
                
                <button type="submit">Submit</button>
            </form>
            <div id="form-response"></div>
        </div>
    </section>
</main>

<?php include 'phpLogic/footer.php'; ?>

<script>
    // Toggle Search Bar
    const searchIcon = document.getElementById('search-icon');
    const searchBar = document.getElementById('search-bar');
    const personIcon = document.getElementById('person-icon'); // Login button
    const loginBox = document.getElementById('login-box');
    const overlay = document.getElementById('overlay');
    const closeLogin = document.getElementById('close-login');
    let isSearchOpen = false;

    searchIcon.addEventListener('click', (event) => {
        event.stopPropagation(); // Prevent this click from triggering document's click handler
        if (isSearchOpen) {
            searchBar.style.width = '0';
            searchBar.classList.remove('open'); // Remove expanded border color
        } else {
            searchBar.style.width = '200px';
            searchBar.classList.add('open'); // Add expanded border color
        }
        isSearchOpen = !isSearchOpen;
    });

    document.addEventListener('click', (event) => {
        // Check if the click is outside the search bar and search icon
        if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
            searchBar.style.width = '0';
            searchBar.classList.remove('open'); // Remove expanded border color
            isSearchOpen = false; // Reset search open state
        }
    });

    // Show Login Box
    personIcon.addEventListener('click', () => {
        loginBox.style.display = 'block';
        overlay.style.display = 'block';
    });

    // Close Login Box
    closeLogin.addEventListener('click', () => {
        loginBox.style.display = 'none';
        overlay.style.display = 'none';
    });

    overlay.addEventListener('click', () => {
        loginBox.style.display = 'none';
        overlay.style.display = 'none';
    });

    // Change button appearance on input
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const loginButton = document.getElementById('login-button');

    function toggleButtonState() {
        if (username.value && password.value) {
            loginButton.classList.add('active');
        } else {
            loginButton.classList.remove('active');
        }
    }

    username.addEventListener('input', toggleButtonState);
    password.addEventListener('input', toggleButtonState);
</script>
