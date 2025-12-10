<?php
// Load banners
$banners = loadBanners($con, $siteprefix, $siteurl, 'home-page-top-banner');

// Debug: output banner URLs as an HTML comment so you can inspect them in page source
if (!empty($banners)) {
  $urls = array_map(function($b){ return $b['banner']; }, $banners);
  echo "<!-- BANNER_URLS: " . implode(' | ', $urls) . " -->\n";
}

// Only render the section if there are banners
if (!empty($banners)):
?>

<!-- Top Banner Section -->
<section id="top-banner" class="top-banner-section">
  <div class="top-banner-inner aos-init aos-animate" data-aos="fade-down" data-aos-delay="50">
      <div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="mb-0">Sponsored</h5>
    <small class="text-muted">Advert</small>
  </div>
    <div class="container">
      <div class="swiper init-swiper top-banner-swiper">

        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 700,
            "autoplay": { "delay": 7000 },
            "slidesPerView": 1,
            "spaceBetween": 10,
            "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
            "navigation": { "nextEl": ".swiper-button-next", "prevEl": ".swiper-button-prev" }
          }
        </script>

        <div class="swiper-wrapper">

        <?php foreach ($banners as $banner):
          $img = htmlspecialchars($banner['banner']);
          $redirect = (!empty($banner['redirect']) && $banner['redirect'] !== '#') ? htmlspecialchars($banner['redirect']) : '';
        ?>
          <div class="swiper-slide">
            <div class="top-banner-card">
              <?php if (!empty($redirect)): ?>
                <a href="<?= $redirect ?>" target="_blank" rel="noopener noreferrer">
                  <img src="<?= $img ?>" alt="Advert" class="top-banner-img img-fluid">
                </a>
              <?php else: ?>
                <img src="<?= $img ?>" alt="Advert" class="top-banner-img img-fluid">
              <?php endif; ?>

              <span class="top-banner-badge">Advert</span>

              <?php if (!empty($redirect)): ?>
                <div class="top-banner-cta">
                  <a href="<?= $redirect ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">Learn more</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
<!----
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
--->
    </div>

  </div>
</section>

<?php endif; ?>
