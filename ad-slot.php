<?php
// Reusable ad slot renderer
// Usage examples:
// <?php $placement = 'blog-in-between-posts'; include __DIR__.'/ad-slot.php'; ?>
// <?php $placement = 'sidebar-right'; $size = '300x600'; include __DIR__.'/ad-slot.php'; ?>

if (!isset($placement) || empty($placement)) {
  // default placement used previously
  $placement = 'listing-pages-in-between-listings';
}

// optional manual size override (e.g., '468x60', '300x600', '336x280', '728x90', '800x600')
$size = isset($size) ? $size : null;

$banners = [];
if (function_exists('loadBanners') && isset($con, $siteprefix, $siteurl)) {
  $banners = loadBanners($con, $siteprefix, $siteurl, $placement);
}

// Debug comment to inspect what the server returned
if (!empty($banners)) {
  $urls = array_map(function($b){ return $b['banner']; }, $banners);
  echo "<!-- AD_SLOT: placement={$placement}; size=".($size?:'auto')."; count=".count($banners)." -->\n";
  echo "<!-- AD_URLS: " . implode(' | ', $urls) . " -->\n";
} else {
  echo "<!-- AD_SLOT: placement={$placement}; size=".($size?:'auto')."; count=0 -->\n";
}

if (empty($banners)) {
  // nothing to render
  return;
}

// Map placements to default sizes when size not provided
$defaultSizes = [
  'blog-in-between-posts' => '468x60',
  'sidebar-right' => '300x600',
  'sidebar-left' => '300x600',
  'inline-review' => '336x280',
  'search-results-top' => '728x90',
  'interstitial' => '800x600',
  'listing-pages-in-between-listings' => '468x60',
];

if (!$size && isset($defaultSizes[$placement])) {
  $size = $defaultSizes[$placement];
}

// small helper to output the first banner for a placement
$renderAd = function($ad, $sizeClass, $attrs = []) use ($siteurl) {
  $img = htmlspecialchars($ad['banner']);
  $redirect = (!empty($ad['redirect']) && $ad['redirect'] !== '#') ? htmlspecialchars($ad['redirect']) : '#';
  $attrStr = '';
  foreach ($attrs as $k=>$v) { $attrStr .= " $k=\"".htmlspecialchars($v).'\"'; }
  return "<a href=\"{$redirect}\" target=\"_blank\" rel=\"noopener noreferrer\"><img src=\"{$img}\" alt=\"Advert\" class=\"ad-img {$sizeClass}\"{$attrStr}></a>";
};

// Inline CSS for ad sizes — small, safe addition; we can move this to main.css later
?>
<style>
/* Ad slot helpers (move to assets/css/main.css for production) */
.ad-img { display:block; max-width:100%; height:auto; border-radius:6px; }
.ad-468x60 { width:468px; height:60px; object-fit:cover; }
.ad-300x600 { width:300px; height:600px; object-fit:cover; }
.ad-336x280 { width:336px; height:280px; object-fit:cover; }
.ad-728x90 { width:728px; height:90px; object-fit:cover; }
.ad-800x600 { width:800px; height:600px; object-fit:cover; }
.ad-responsive { max-width:100%; height:auto; }
.ad-sticky { position:sticky; top:100px; }
.ad-interstitial-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; z-index:1050; }
.ad-interstitial { background:#fff; padding:12px; border-radius:6px; box-shadow:0 6px 24px rgba(0,0,0,0.3); }
.ad-interstitial .ad-close { position:absolute; right:12px; top:8px; background:transparent; border:0; font-size:20px; cursor:pointer; }
@media (max-width:900px) {
  .ad-468x60, .ad-728x90 { width:100%; height:auto; }
  .ad-300x600 { width:160px; height:auto; }
}
</style>

<?php
// Render logic per placement/size
switch ($size) {
  case '300x600':
    // render as a sticky sidebar card (show first banner)
    $ad = $banners[0];
    ?>
    <div class="sidebar-card ad-sticky" data-placement="<?= htmlspecialchars($placement) ?>">
      <div class="mb-2"><small class="text-muted">Sponsored</small></div>
      <div class="text-center">
        <?= $renderAd($ad,'ad-300x600') ?>
      </div>
    </div>
    <?php
    break;

  case '336x280':
    // inline ad for reviews/content — use a responsive container
    $ad = $banners[0];
    ?>
    <div class="my-3 text-center" data-placement="<?= htmlspecialchars($placement) ?>">
      <?= $renderAd($ad,'ad-336x280 ad-responsive') ?>
    </div>
    <?php
    break;

  case '728x90':
    // Top of search results or wide header ad
    $ad = $banners[0];
    ?>
    <div class="my-3 text-center" data-placement="<?= htmlspecialchars($placement) ?>">
      <?= $renderAd($ad,'ad-728x90 ad-responsive') ?>
    </div>
    <?php
    break;

  case '800x600':
    // Interstitial / popup — render overlay + JS to show once per session
    $ad = $banners[0];
    $modalId = 'mh_interstitial_' . md5($placement);
    ?>
    <div id="<?= $modalId ?>" class="ad-interstitial-overlay" style="display:none;" data-placement="<?= htmlspecialchars($placement) ?>">
      <div class="ad-interstitial position-relative">
        <button class="ad-close" aria-label="Close">&times;</button>
        <?= $renderAd($ad,'ad-800x600') ?>
      </div>
    </div>
    <script>
      (function(){
        try {
          var key = 'mh_interstitial_shown_<?= md5($placement) ?>';
          if (!sessionStorage.getItem(key)) {
            setTimeout(function(){
              var el = document.getElementById('<?= $modalId ?>');
              if (!el) return;
              el.style.display = 'flex';
              el.querySelector('.ad-close').addEventListener('click', function(){ el.style.display='none'; });
              sessionStorage.setItem(key, '1');
            }, 800);
          }
        } catch (e) { console && console.warn && console.warn(e); }
      })();
    </script>
    <?php
    break;

  default:
    // fallback: render a small banner or a carousel if multiple banners exist
    if (count($banners) === 1) {
      $ad = $banners[0];
      // choose class based on size token
      $cls = 'ad-468x60';
      if ($size) {
        $cls = 'ad-' . str_replace('x','x',$size);
      }
      ?>
      <div class="my-3 text-center" data-placement="<?= htmlspecialchars($placement) ?>">
        <?= $renderAd($ad, $cls . ' ad-responsive') ?>
      </div>
      <?php
    } else {
      // multiple banners — render simple horizontal carousel (no swiper dependency)
      ?>
      <div class="mh-ad-carousel" data-placement="<?= htmlspecialchars($placement) ?>" style="display:flex;gap:10px;overflow:hidden;align-items:center;">
      <?php foreach ($banners as $ad): ?>
        <div style="flex:0 0 auto;">
          <?= $renderAd($ad, 'ad-468x60 ad-responsive') ?>
        </div>
      <?php endforeach; ?>
      </div>
      <?php
    }
    break;
}

?>
