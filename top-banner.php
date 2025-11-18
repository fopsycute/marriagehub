<?php
// Load banners
$banners = loadBanners($con, $siteprefix, $siteurl, 'home-page-top-banner');

// Only render the section if there are banners
if (!empty($banners)):
?>

<!-- Slider Section -->
<section id="slider" class="slider dark-background">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="swiper init-swiper">

      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": {
            "delay": 5000
          },
          "slidesPerView": "auto",
          "centeredSlides": true,
          "pagination": {
            "el": ".swiper-pagination",
            "type": "bullets",
            "clickable": true
          },
          "navigation": {
            "nextEl": ".swiper-button-next",
            "prevEl": ".swiper-button-prev"
          }
        }
      </script>

      <div class="swiper-wrapper">

        <?php foreach ($banners as $banner): ?>
          <div class="swiper-slide" style="background-image: url('<?= htmlspecialchars($banner['banner']) ?>');">
            <div class="content">
              <!-- Optional: add badge -->
              <span class="badge bg-warning position-absolute top-0 start-0 m-2">Advert</span>
              
              <!-- Only show button if there is a redirect URL -->
              <?php if (!empty($banner['redirect']) && $banner['redirect'] !== '#'): ?>
                <h2><a href="<?= htmlspecialchars($banner['redirect']) ?>" class="btn btn-primary">Click Here</a></h2>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>

    </div>

  </div>
</section>

<?php endif; ?>
