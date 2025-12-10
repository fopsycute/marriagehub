<?php
/**
 * displayBannerSlider
 * Render a Bootstrap carousel for banners for a given placement slug.
 * Expects $con, $siteprefix, $siteurl to be provided by caller.
 */
function displayBannerSlider($con, $siteprefix, $siteurl, $slug)
{
    // defensive
    if (!$con || !$siteprefix || !$siteurl || empty($slug)) return;

    // Prepared query to fetch active banners for placement
    $sql = "
        SELECT aa.banner, aa.redirect_url, aa.alt_text, aa.width, aa.height
        FROM {$siteprefix}active_adverts AS aa
        INNER JOIN {$siteprefix}ad_pacement AS ap
            ON aa.advert_id = ap.id
        WHERE ap.slug = ?
        AND aa.status='active'
        AND aa.start_date <= CURDATE()
        AND aa.end_date >= CURDATE()
        ORDER BY aa.created_at DESC
        LIMIT 10
    ";

    $stmt = $con->prepare($sql);
    if (!$stmt) return; // fail silently
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $res = $stmt->get_result();
    $banners = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    if (empty($banners)) {
        // no banners — indicate nothing was rendered
        return false;
    }

    // default aspect ratio (height/width) fallback
    $defaultAspect = 5 / 16; // ~16:5 wide banner

    $carouselId = 'bannerCarousel_' . uniqid();

    // Carousel: 2 minutes interval, pause on hover
    echo '<div id="' . htmlspecialchars($carouselId) . '" class="carousel slide top-banner-carousel" data-bs-ride="carousel" data-bs-interval="120000" data-bs-pause="hover">';
    echo '<div class="carousel-inner">';

    foreach ($banners as $i => $b) {
        $active = ($i === 0) ? ' active' : '';
        $bannerFile = $b['banner'] ?? '';
        if (empty($bannerFile)) continue;
        $bannerUrl = htmlspecialchars(rtrim($siteurl, '/') . '/uploads/' . ltrim($bannerFile, '/'));

        // validate redirect
        $redirect = '#';
        if (!empty($b['redirect_url'])) {
            $raw = trim($b['redirect_url']);
            if (filter_var($raw, FILTER_VALIDATE_URL) || strpos($raw, '/') === 0) {
                $redirect = htmlspecialchars($raw);
            }
        }

        $alt = !empty($b['alt_text']) ? htmlspecialchars($b['alt_text']) : 'Banner';
        $width = !empty($b['width']) ? (int)$b['width'] : 0;
        $height = !empty($b['height']) ? (int)$b['height'] : 0;
        $aspect = $defaultAspect;
        if ($width > 0 && $height > 0) {
            $aspect = $height / $width;
        }
        $paddingTop = round($aspect * 100, 4);

        echo '<div class="carousel-item' . $active . '">';
        echo '<div class="banner-item-wrap" style="padding-top:' . $paddingTop . '%;">';
        echo '<a href="' . $redirect . '" target="_blank" rel="noopener">';
        echo '<img src="' . $bannerUrl . '" alt="' . $alt . '" loading="lazy" decoding="async" class="banner-img">';
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>'; // .carousel-inner

    // Controls
    echo '<button class="carousel-control-prev" type="button" data-bs-target="#' . htmlspecialchars($carouselId) . '" data-bs-slide="prev">';
    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Previous</span>';
    echo '</button>';

    echo '<button class="carousel-control-next" type="button" data-bs-target="#' . htmlspecialchars($carouselId) . '" data-bs-slide="next">';
    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Next</span>';
    echo '</button>';

    echo '</div>'; // .carousel
    return true;
}

