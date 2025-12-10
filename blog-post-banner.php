
<div class="sidebar-card sidebar-sticky" data-aos="fade-up" data-aos-delay="80">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="mb-0">Sponsored</h5>
    <small class="text-muted">Advert</small>
  </div>

<?php
// Sidebar banner widget — compact card for sidebar
$banners = [];
if (function_exists('loadBanners') && isset($con, $siteprefix, $siteurl)) {
  $banners = loadBanners($con, $siteprefix, $siteurl, 'blog-posts-in-between-blog-posts');
}

// Debug: output banner URLs for quick inspection in page source
if (!empty($banners)) {
  $urls = array_map(function($b){ return $b['banner']; }, $banners);
  echo "<!-- SIDEBAR_BANNER_URLS: " . implode(' | ', $urls) . " -->\n";
}

if (!empty($banners)):
?>


  <div class="swiper init-swiper sidebar-swiper">
    <script type="application/json" class="swiper-config">
      {
        "loop": true,
        "speed": 600,
        "autoplay": { "delay": 5000 },
        "slidesPerView": 1,
        "spaceBetween": 10,
        "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
        "navigation": { "nextEl": ".swiper-button-next", "prevEl": ".swiper-button-prev" }
      }
    </script>

    <div class="swiper-wrapper">
      <?php foreach ($banners as $banner):
        $img = htmlspecialchars($banner['banner']);
        $redirect = (!empty($banner['redirect']) && $banner['redirect'] !== '#') ? htmlspecialchars($banner['redirect']) : '#';
      ?>
        <div class="swiper-slide">
          <a href="<?= $redirect ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= $img ?>" alt="Advert" class="sidebar-banner-img img-fluid rounded">
          </a>
        </div>
      <?php endforeach; ?>
    </div>
<!---
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
--->

  </div>
    <div class="mt-3 text-center">
      <a href="<?= $siteurl ?>advertise.php" class="btn btn-sm btn-outline-primary">Advertise with us</a>
    </div>
</div>

<?php endif; ?>
