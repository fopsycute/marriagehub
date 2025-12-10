
<?php
// Load banners for inline placement
// $placementSlug can be 'listing-inline-ad' or 'blog-inline-ad'
if (!isset($placementSlug)) {
    $placementSlug = 'within-review-content-inline-ad'; // default
}

$banners = loadBanners($con, $siteprefix, $siteurl, $placementSlug);

// Stop if no ads
if (empty($banners)) return;

// Pick a random ad from available banners
$banner = $banners[array_rand($banners)];

$img = htmlspecialchars($banner['banner']);
$redirect = (!empty($banner['redirect']) && $banner['redirect'] !== '#')
    ? htmlspecialchars($banner['redirect'])
    : '';
?>
<style>
/* Inline Ad Styling */
.inline-ad {
    width: 468px;
    height: 60px;
    max-width: 100%;
    margin: 20px auto; /* space between listings */
    text-align: center;
}

.inline-ad img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 1px solid #ddd;
    border-radius: 4px;
}

@media (max-width: 576px) {
    .inline-ad {
        width: 100%;
        height: auto;
    }
    .inline-ad img {
        height: auto;
    }
}
</style>

<div class="inline-ad">
    <?php if ($redirect): ?>
        <a href="<?= $redirect ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= $img ?>" alt="Sponsored Ad">
        </a>
    <?php else: ?>
        <img src="<?= $img ?>" alt="Sponsored Ad">
    <?php endif; ?>
</div>
