
<?php
// Load banners for sidebar placement
$banners = loadBanners($con, $siteprefix, $siteurl, 'sidebar-right-or-left-scrolling-ad');

// Stop if no ads found
if (empty($banners)) return;
?>
<style>
/*** Sticky Sidebar Ad (300x600) ***/
.sidebar-sticky-ad {
  
    position: fixed;
    right: 10px; /* change to left:10px for left side */
    top: 120px;
    z-index: 9999;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 25px rgba(0,0,0,0.25);
    transition: opacity 0.4s ease-in-out;
}

/* Ad Image */
.sidebar-sticky-ad img {
 
    object-fit: cover;
    display: block;
}

/* Close button */
.sidebar-ad-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(255,255,255,0.95);
    border: none;
    font-size: 16px;
    padding: 4px 9px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1000;
    font-weight: bold;
}

/* Hide on screens < 992px */
@media (max-width: 992px) {
    .sidebar-sticky-ad {
        display: none !important;
    }
}
</style>

<div class="sidebar-sticky-ad" id="sidebarAdBox">
    <button class="sidebar-ad-close" id="sidebarAdClose">✕</button>

    <a id="sidebarAdLink" href="#" target="_blank" rel="noopener noreferrer">
        <img id="sidebarAdImg" src="" alt="Sponsored Ad">
    </a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ads = <?php echo json_encode($banners); ?>;
    let current = 0;

    const adImg = document.getElementById("sidebarAdImg");
    const adLink = document.getElementById("sidebarAdLink");
    const adBox  = document.getElementById("sidebarAdBox");
    const closeBtn = document.getElementById("sidebarAdClose");

    function showAd() {
        let ad = ads[current];
        adImg.src = ad.banner;

        if (ad.redirect && ad.redirect !== "#") {
            adLink.href = ad.redirect;
            adLink.style.pointerEvents = "auto";
        } else {
            adLink.href = "#";
            adLink.style.pointerEvents = "none";
        }
    }

    // Rotate ads every 8 seconds
    function rotateAd() {
        current = (current + 1) % ads.length;
        showAd();
    }

    // Start with first ad
    showAd();

    // Only rotate if more than one ad exists
    if (ads.length > 1) {
        setInterval(rotateAd, 8000);
    }

    // Close button functionality
    closeBtn.addEventListener("click", function() {
        adBox.style.opacity = "0";
        setTimeout(() => adBox.style.display = "none", 400);
    });
});
</script>


