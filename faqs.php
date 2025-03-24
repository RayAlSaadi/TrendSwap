<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

<body>
<?php include 'phpLogic/header.php'; ?>

<!-- Link to CSS -->
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="css/faq.css">
<link rel="stylesheet" href="css/footer.css">

<!-- Notification for adding to basket -->
<div id="notification"></div>
<div id="basket-container"></div>



<section>
    <h2 class="title">Frequently Asked Questions</h2>

    <div class="faq">
        <div class="question">
            <h3> What is TrendSwap?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p>TrendSwap is your go-to online store for trendy and affordable clothing and accessories for men, women, and kids. We offer a wide variety of stylish options for all occasions.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3>How can I contact customer support?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p>You can reach us via email at support@trendswap.com or through our contact form feature available on the website during business hours.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3>What payment methods do you accept?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p> We accept all major credit and debit cards, PayPal, Apple Pay, Google Pay, and other secure payment options.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3> Can I modify or cancel my order?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p> Orders can be modified or canceled within 24 hours of placing them. Please contact our customer support team as soon as possible.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3>Do you offer international shipping?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p> Yes, TrendSwap ships worldwide. Shipping rates and delivery times vary depending on your location.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3>How long will it take to receive my order?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p>Orders within the UK typically arrive within 5-7 business days. International orders may take 7-14 business days, depending on the destination.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3> What is your return policy?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p>We accept returns within 30 days of delivery for unused, unworn items with original tags. Visit our Returns & Refunds page for detailed instructions.</p>
        </div>
    </div>

    <div class="faq">
        <div class="question">
            <h3> How long does it take to process my refund?</h3>
            <svg width="15" height="10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p>Refunds are processed within 7-10 business days after receiving your returned items. You can get the refund back in the original payment method.</p>
        </div>
    </div>
</section>

<script src="js/faq.js"></script>

<?php include 'phpLogic/footer.php'; ?>
</body>
<script>
    const searchIcon = document.getElementById('search-icon');
    const searchBar = document.getElementById('search-bar');
    let isSearchOpen = false;

    searchIcon.addEventListener('click', (event) => {
        event.stopPropagation();
        if (isSearchOpen) {
            searchBar.style.width = '0';
            searchBar.classList.remove('open');
        } else {
            searchBar.style.width = '200px';
            searchBar.classList.add('open');
        }
        isSearchOpen = !isSearchOpen;
    });

    document.addEventListener('click', (event) => {
        if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
            searchBar.style.width = '0';
            searchBar.classList.remove('open');
            isSearchOpen = false;
        }
    });
</script>

<script src="js/basket.js"></script>
