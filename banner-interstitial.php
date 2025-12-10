<?php
// banner-interstitial.php

// Load banners for interstitial placement
$banners = loadBanners($con, $siteprefix, $siteurl, 'pop-up-interstitial-ads');

// Stop if no banners
if (empty($banners)) return;
?>
<style>
/* Fullscreen Overlay */
#interstitial-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}

/* Ad Container (800x600) */
#interstitial-ad {
    width: 800px;
    height: 600px;
    max-width: calc(100% - 30px);
    max-height: calc(100% - 30px);
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 10px 50px rgba(0,0,0,0.6);
}

#interstitial-ad img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Close Button */
#interstitial-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.9);
    border: none;
    padding: 7px 12px;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    z-index: 1000;
    font-weight: bold;
}

/* Show Animation */
.show-interstitial {
    display: flex !important;
    animation: fadeIn .2s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<!-- Interstitial HTML -->
<div id="interstitial-overlay">
    <div id="interstitial-ad">
        <button id="interstitial-close">✕</button>

        <!-- Dynamic ad goes here -->
        <a id="interstitial-link" href="#" target="_blank" rel="noopener noreferrer" style="display:block;">
            <img id="interstitial-img" src="" alt="Advert">
        </a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const overlay = document.getElementById("interstitial-overlay");
    const closeBtn = document.getElementById("interstitial-close");
    const imgEl = document.getElementById("interstitial-img");
    const linkEl = document.getElementById("interstitial-link");

    // PHP → JS array (all banners)
    let ads = <?php echo json_encode($banners); ?>;
    let index = 0;

    function showAd() {
        if (index >= ads.length) return; // No more ads

        let ad = ads[index];

        imgEl.src = ad.banner;
        linkEl.href = ad.redirect ? ad.redirect : "#";

        overlay.classList.add("show-interstitial");
        document.body.style.overflow = "hidden";
    }

    function closeAd() {
        overlay.classList.remove("show-interstitial");
        document.body.style.overflow = "";

        index++;

        // Show next ad after closing the previous
        if (index < ads.length) {
            setTimeout(showAd, 300);
        }
    }

    // Open first ad after small delay
    setTimeout(showAd, 400);

    // Close triggers
    closeBtn.addEventListener("click", closeAd);

    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) closeAd();
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") closeAd();
    });
});
</script>
