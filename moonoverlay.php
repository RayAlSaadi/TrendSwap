<?php
// moonoverlay.php - Dark Mode Toggle Component
?>
<!-- Dark Mode Toggle -->
<button id="dark-mode-toggle" aria-label="Toggle dark mode">
    <img src="Images/moon-4-512.png" alt="Dark mode" class="toggle-image">
</button>

<style>
    #dark-mode-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background-color: white;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1000;
        padding: 12px;
    }

    .toggle-image {
        width: 100%;
        height: 100%;
        transition: filter 0.3s ease;
    }

    #dark-mode-toggle.dark {
        background-color: black;
    }

    #dark-mode-toggle.dark .toggle-image {
        filter: invert(1);
    }

    #dark-mode-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.5);
    }

    /* Dark mode styles for the body */
    body.darkmode {
        background-color: #121212;
        color: #f0f0f0;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
</style>

<script>
    let darkmode = localStorage.getItem('darkmode');
    const themeSwitch = document.getElementById('dark-mode-toggle');

    const enableDarkmode = () => {
        document.body.classList.add('darkmode');
        themeSwitch.classList.add('dark');
        localStorage.setItem('darkmode', 'active');
    }

    const disableDarkmode = () => {
        document.body.classList.remove('darkmode');
        themeSwitch.classList.remove('dark');
        localStorage.setItem('darkmode', null);
    }

    if (darkmode === "active") {
        enableDarkmode();
    }

    themeSwitch.addEventListener("click", () => {
        darkmode = localStorage.getItem('darkmode');
        darkmode !== "active" ? enableDarkmode() : disableDarkmode();
    });
</script>

