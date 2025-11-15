<?php
/**
 * Vendor Services page
 * Reuses marketplace filters and live-fetch but restricts results to a single vendor.
 * Expects ?slug=<vendor-slug>
 */
include "header.php";

// ensure we have a vendor slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    echo "<div class='alert alert-info text-center mt-5'>No vendor specified.</div>";
    include "footer.php";
    exit;
}

$vendorSlug = trim($_GET['slug']);
$vendorApi = $siteurl . "script/admin.php?action=vendorslug&slug=" . urlencode($vendorSlug);
$vendorData = curl_get_contents($vendorApi);
if ($vendorData === false) {
    echo "<div class='alert alert-danger text-center mt-5'>Unable to load vendor information. Try again later.</div>";
    include "footer.php";
    exit;
}

$vendorResp = json_decode($vendorData, true);
if (empty($vendorResp)) {
    echo "<div class='alert alert-warning text-center mt-5'>Vendor not found.</div>";
    include "footer.php";
    exit;
}

$vendor = isset($vendorResp[0]) ? $vendorResp[0] : $vendorResp;
$vendorId = intval($vendor['id'] ?? 0);
$vendorName = htmlspecialchars(trim(($vendor['first_name'] ?? '') . ' ' . ($vendor['last_name'] ?? '')) ?: ($vendor['business_name'] ?? 'Vendor'));

// Render filters and listing area (based on marketplace.php)
?>

<section id="category-header" class="category-header section">
    <div class="container" data-aos="fade-up">
        <form method="get" id="marketFilter">
            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($vendorSlug); ?>">
            <div class="filter-container mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-item search-form">
                            <label for="productSearch" class="form-label">Search Services</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" id="productSearch" placeholder="Search for services..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button class="btn search-btn" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="filter-item">
                            <label for="priceRange" class="form-label">Price Range</label>
                            <?php
                            $priceBounds = null;
                            $boundsData = curl_get_contents($siteurl . "script/admin.php?action=listing_price_bounds");
                            if ($boundsData !== false) $priceBounds = json_decode($boundsData, true);

                            $priceOptions = [];
                            if (!empty($priceBounds) && isset($priceBounds['min']) && isset($priceBounds['max'])) {
                                $pmin = floatval($priceBounds['min']);
                                $pmax = floatval($priceBounds['max']);
                                if ($pmax > $pmin) {
                                    $buckets = 5;
                                    $step = ($pmax - $pmin) / $buckets;
                                    $low = $pmin;
                                    for ($i = 0; $i < $buckets; $i++) {
                                        $high = ($i == $buckets - 1) ? $pmax : floor($low + $step);
                                        $label = $sitecurrency . number_format($low, 0) . ' - ' . $sitecurrency . number_format($high, 0);
                                        $value = floor($low) . '-' . floor($high);
                                        $priceOptions[] = ['value' => $value, 'label' => $label];
                                        $low = $high + 1;
                                    }
                                    $priceOptions[] = ['value' => floor($pmax) . '+', 'label' => $sitecurrency . number_format($pmax, 0) . ' &+' ];
                                }
                            }
                            ?>
                            <select class="form-select" id="priceRange" name="price_range">
                                <option value="">All Prices</option>
                                <?php foreach ($priceOptions as $opt): ?>
                                    <option value="<?php echo htmlspecialchars($opt['value']); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == $opt['value']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="filter-item">
                            <label for="sortBy" class="form-label">Sort By</label>
                            <select class="form-select" id="sortBy" name="sort">
                                <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo (isset($_GET['sort']) && $_GET['sort']=='rating') ? 'selected' : ''; ?>>Customer Rating</option>
                                <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort']=='newest') ? 'selected' : ''; ?>>Newest Arrivals</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-item">
                            <label class="form-label">View</label>
                            <div class="d-flex align-items-center">
                                <div class="items-per-page">
                                    <select class="form-select" id="itemsPerPage" name="items_per_page" aria-label="Items per page">
                                        <option value="12" <?php echo (isset($_GET['items_per_page']) && $_GET['items_per_page']==12) ? 'selected' : ''; ?>>12 per page</option>
                                        <option value="24" <?php echo (isset($_GET['items_per_page']) && $_GET['items_per_page']==24) ? 'selected' : ''; ?>>24 per page</option>
                                        <option value="48" <?php echo (isset($_GET['items_per_page']) && $_GET['items_per_page']==48) ? 'selected' : ''; ?>>48 per page</option>
                                        <option value="96" <?php echo (isset($_GET['items_per_page']) && $_GET['items_per_page']==96) ? 'selected' : ''; ?>>96 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12" data-aos="fade-up" data-aos-delay="200">
                        <div class="active-filters">
                            <span class="active-filter-label">Active Filters:</span>
                            <div class="filter-tags">
                                <?php if (!empty($_GET['search'])): ?>
                                    <span class="filter-tag"><?php echo htmlspecialchars($_GET['search']); ?> <button class="filter-remove" type="button" onclick="location.href='vendor-services.php?slug=<?php echo urlencode($vendorSlug); ?>' "><i class="bi bi-x"></i></button></span>
                                <?php endif; ?>
                                <?php if (!empty($_GET['price_range'])): ?>
                                    <span class="filter-tag"><?php echo htmlspecialchars($_GET['price_range']); ?> <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=price_range]').value=''; document.getElementById('marketFilter').submit();"><i class="bi bi-x"></i></button></span>
                                <?php endif; ?>
                                <button class="clear-all-btn" type="button" onclick="location.href='vendor-services.php?slug=<?php echo urlencode($vendorSlug); ?>'">Clear All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="best-sellers" class="best-sellers section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="section-title" data-aos="fade-up">
            <h2><?php echo $vendorName; ?> &mdash; Services</h2>
            <p>Services offered by <?php echo $vendorName; ?></p>
        </div>

        <div class="row g-5">
            <?php
            // Build params for initial server-side render
            $params = [];
            if (!empty($_GET['search'])) $params['search'] = trim($_GET['search']);
            if (!empty($_GET['price_range'])) $params['price_range'] = trim($_GET['price_range']);
            if (!empty($_GET['sort'])) $params['sort'] = trim($_GET['sort']);
            $itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 12;
            $params['items_per_page'] = $itemsPerPage;
            $currentPage = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $params['page'] = $currentPage;
            $params['ajax'] = 1;

            // Vendor filter - add both user_id and vendor_id to be tolerant of backend param name
            if ($vendorId > 0) {
                $params['user_id'] = $vendorId;
                $params['vendor_id'] = $vendorId;
            }

            $query = http_build_query($params);
            $url = $siteurl . "script/admin.php?action=listinglists" . ($query ? "&$query" : "");
            $data = curl_get_contents($url);

            $totalItems = 0;
            $listings = [];
            if ($data !== false) {
                $resp = json_decode($data, true);
                if (is_array($resp) && isset($resp['data'])) {
                    $listings = json_decode(json_encode($resp['data']));
                    $totalItems = intval($resp['total'] ?? 0);
                } else {
                    $decoded = json_decode($data);
                    if ($decoded) $listings = $decoded;
                }

                if (!empty($listings)) {
                    foreach ($listings as $listing) {
                        if (isset($listing->status) && strtolower($listing->status) === 'active' && (strtolower($listing->type) === 'service' || $listing->type === 'Service')) {
                            $listingId   = $listing->id;
                            $title       = htmlspecialchars($listing->title);
                            $slug        = htmlspecialchars($listing->slug ?? '');
                            $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                            $price       = htmlspecialchars($listing->price ?? '');
                            $priceMin    = htmlspecialchars($listing->price_min ?? '');
                            $priceMax    = htmlspecialchars($listing->price_max ?? '');
                            $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                            $category    = htmlspecialchars(trim($categoryNames[0]));
                            $featuredImg = !empty($listing->featured_image) ? $siteurl . $imagePath . $listing->featured_image : $siteurl . "assets/img/default-product.jpg";
                            $listingUrl  = $siteurl . "products.php?slug=" . $slug;

                            $sellerName = htmlspecialchars(trim(($listing->first_name ?? '') . ' ' . ($listing->last_name ?? '')));
                            $sellerPhoto = !empty($listing->photo) ? $siteurl . $imagePath . $listing->photo : $siteurl . "assets/img/user.jpg";

                            $displayPrice = 'Contact for price';
                            if ($pricingType === 'Starting Price' && !empty($price)) {
                                $displayPrice = $sitecurrency  . number_format($price, 2);
                            } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
                                $displayPrice = $sitecurrency . number_format($priceMin, 2) . ' - ' . $sitecurrency . number_format($priceMax, 2);
                            }

                            // wishlist check (best-effort)
                            $isWishlisted = false;
                            if (!empty($buyerId)) {
                                $apiCheckUrl = $siteurl . "script/user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
                                $wishlistData = curl_get_contents($apiCheckUrl);
                                if ($wishlistData !== false) {
                                    $wishlistResult = json_decode($wishlistData, true);
                                    if (is_array($wishlistResult)) {
                                        if (isset($wishlistResult['isWishlisted'])) $isWishlisted = (bool)$wishlistResult['isWishlisted'];
                                        elseif (isset($wishlistResult['data']['isWishlisted'])) $isWishlisted = (bool)$wishlistResult['data']['isWishlisted'];
                                    }
                                }
                            }
                            ?>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="product-item">
                                    <div class="product-image">
                                        <div class="product-badge trending-badge"><?php echo $category; ?></div>
                                        <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                                        <div class="product-actions">
                                            <button class="action-btn wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>" title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                                <?php if ($isWishlisted): ?><i class="bi bi-heart-fill text-danger"></i><?php else: ?><i class="bi bi-heart"></i><?php endif; ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-category"><?php echo $category; ?></div>
                                        <h4 class="product-name"><a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a></h4>
                                        <div class="product-price"><?php echo $displayPrice; ?></div>
                                        <div class="mt-3 d-flex align-items-center">
                                            <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                                            <span class="small text-muted"><?php echo $sellerName; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Pagination placeholder -->
<section id="category-pagination" class="category-pagination section">
    <div class="container">
        <nav class="d-flex justify-content-center" aria-label="Page navigation">
            <ul id="marketplace-pagination-list"></ul>
        </nav>
    </div>
</section>

<script>
// Inject PHP variables into JS
const VENDOR_ID = <?php echo intval($vendorId); ?>;
const VENDOR_SLUG = '<?php echo addslashes($vendorSlug); ?>';
const API_BASE = '<?php echo $siteurl; ?>script/admin.php?action=listinglists&user_id=<?php echo intval($vendorId); ?>';
const SITEURL = '<?php echo $siteurl; ?>';
const IMAGE_PATH = '<?php echo $imagePath ?? "uploads/"; ?>';
const SITE_CURRENCY = '<?php echo $sitecurrency ?? ""; ?>';
let currentPage = <?php echo isset($currentPage) ? intval($currentPage) : 1; ?>;
let itemsPerPage = <?php echo isset($itemsPerPage) ? intval($itemsPerPage) : 12; ?>;
let totalItems = <?php echo isset($totalItems) ? intval($totalItems) : 0; ?>;

function buildQuery(params) {
  return Object.keys(params).filter(k => params[k] !== undefined && params[k] !== '' && params[k] !== null).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k])).join('&');
}

function renderListings(listings) {
  const container = document.querySelector('.best-sellers .container .row.g-5');
  if (!container) return;
  if (!Array.isArray(listings) || listings.length === 0) {
    container.innerHTML = '<div class="col-12"><p>No services found.</p></div>';
    return;
  }
  let html = '';
  listings.forEach(listing => {
    if (!listing.status || listing.status.toLowerCase() !== 'active') return;
    const type = (listing.type || '').toString().toLowerCase();
    if (type !== 'service') return;
    const listingId = listing.id;
    const title = listing.title ? listing.title.replace(/</g,'&lt;') : '';
    const slug = listing.slug || '';
    const pricingType = listing.pricing_type || '';
    const price = listing.price || '';
    const priceMin = listing.price_min || '';
    const priceMax = listing.price_max || '';
    const categoryNames = listing.category_names ? listing.category_names.split(',') : ['General'];
    const category = categoryNames[0] ? categoryNames[0].trim() : 'General';
    const featuredImg = listing.featured_image ? (SITEURL + IMAGE_PATH + listing.featured_image) : (SITEURL + 'assets/img/default-product.jpg');
    const listingUrl = SITEURL + 'products.php?slug=' + encodeURIComponent(slug);
    const sellerName = ((listing.first_name || '') + ' ' + (listing.last_name || '')).trim();
    const sellerPhoto = listing.photo ? (SITEURL + IMAGE_PATH + listing.photo) : (SITEURL + 'assets/img/user.jpg');
    let displayPrice = 'Contact for price';
    if (pricingType === 'Starting Price' && price) displayPrice = SITE_CURRENCY + parseFloat(price).toFixed(2);
    else if (pricingType === 'Price Range' && priceMin && priceMax) displayPrice = SITE_CURRENCY + parseFloat(priceMin).toFixed(2) + '-' + SITE_CURRENCY + parseFloat(priceMax).toFixed(2);

    html += `
      <div class="col-lg-3 col-md-6 col-6">
        <div class="product-item">
          <div class="product-image">
            <div class="product-badge trending-badge">${category}</div>
            <img src="${featuredImg}" alt="${title}" class="img-fluid" loading="lazy">
            <div class="product-actions">
              <button class="action-btn wishlist-btn" data-product-id="${listingId}" title="Add to Wishlist">
                <i class="bi bi-heart"></i>
              </button>
            </div>
          </div>
          <div class="product-info">
            <div class="product-category">${category}</div>
            <h4 class="product-name"><a href="${listingUrl}">${title}</a></h4>
            <div class="product-price">${displayPrice}</div>
            <div class="mt-3 d-flex align-items-center">
              <img src="${sellerPhoto}" alt="${sellerName}" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
              <span class="small text-muted">${sellerName}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  });
  container.innerHTML = html;
}

let debounceTimer = null;
function liveFetch() {
  const form = document.getElementById('marketFilter');
  if (!form) return;
  const formData = new FormData(form);
  const params = {};
  for (const [k, v] of formData.entries()) params[k] = v;
  const query = buildQuery(params);
  const url = API_BASE + (query ? '&' + query : '');
  fetch(url)
    .then(r => r.json())
    .then(data => {
      if (data && data.data && Array.isArray(data.data)) {
        renderListings(data.data);
        renderPagination(data.total || 0, currentPage, itemsPerPage);
      } else if (Array.isArray(data)) {
        renderListings(data);
        renderPagination(0, 1, itemsPerPage);
      } else {
        console.error('Unexpected response format', data);
      }
      updateActiveFilters();
      try {
        const urlParams = new URLSearchParams(buildQuery(params));
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        history.replaceState(null, '', newUrl);
      } catch (e) {}
    })
    .catch(err => console.error('Live fetch error', err));
}

function scheduleLiveFetch() { clearTimeout(debounceTimer); currentPage = 1; debounceTimer = setTimeout(liveFetch, 300); }

document.addEventListener('DOMContentLoaded', function(){
  const searchInput = document.querySelector('#marketFilter [name=search]');
  const selects = document.querySelectorAll('#marketFilter select');
  if (searchInput) searchInput.addEventListener('input', scheduleLiveFetch);
  selects.forEach(s => s.addEventListener('change', scheduleLiveFetch));
  document.addEventListener('click', function(e){
    const t = e.target.closest && e.target.closest('[data-market-page]');
    if (t) { e.preventDefault(); const p = parseInt(t.getAttribute('data-market-page')) || 1; if (p === currentPage) return; currentPage = p; liveFetch(); }
  });
  updateActiveFilters();
  try { if (typeof totalItems !== 'undefined' && totalItems > 0) renderPagination(totalItems, currentPage, itemsPerPage); } catch (e) {}
});

function updateActiveFilters() {
  const container = document.querySelector('.filter-tags'); if (!container) return; const form = document.getElementById('marketFilter'); const fd = new FormData(form); let html = ''; const search = (fd.get('search') || '').trim(); const price = (fd.get('price_range') || '').trim(); if (search) html += `<span class="filter-tag">${escapeHtml(search)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=search]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`; if (price) html += `<span class="filter-tag">${escapeHtml(price)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=price_range]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`; html += `<button class="clear-all-btn" type="button" onclick="document.getElementById('marketFilter').reset(); scheduleLiveFetch();">Clear All</button>`; container.innerHTML = html; }

function escapeHtml(unsafe) { return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'\/','`':'&#96;','=':'&#61;'})[s]; }); }

function renderPagination(total, page, perPage) {
  const container = document.getElementById('marketplace-pagination-list'); if (!container) return; perPage = parseInt(perPage, 10) || 1; total = parseInt(total, 10) || 0; const totalPages = Math.max(1, Math.ceil(total / perPage)); if (total <= perPage || totalPages <= 1) { container.innerHTML = ''; return; }
  let html = '';
  const prevDisabled = page <= 1;
  html += `<li class="page-item ${prevDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.max(1, page-1)}" aria-label="Previous"><span aria-hidden="true"><i class="bi bi-arrow-left"></i></span><span class="d-none d-sm-inline"> Previous</span></a></li>`;
  const showRange = 2;
  for (let p = 1; p <= totalPages; p++) {
    if (p === 1 || p === totalPages || (p >= page - showRange && p <= page + showRange)) {
      const active = p === page ? ' active' : '';
      const aria = p === page ? ' aria-current="page"' : '';
      html += `<li class="page-item${active}"><a class="page-link" href="#" data-market-page="${p}"${aria}>${p}</a></li>`;
    } else if (p === 2 && page - showRange > 2) {
      html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
    } else if (p === totalPages - 1 && page + showRange < totalPages - 1) {
      html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
    }
  }
  const nextDisabled = page >= totalPages;
  html += `<li class="page-item ${nextDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.min(totalPages, page+1)}" aria-label="Next"><span class="d-none d-sm-inline">Next </span><span aria-hidden="true"><i class="bi bi-arrow-right"></i></span></a></li>`;
  container.innerHTML = html;
}
</script>

<?php include "footer.php"; ?>

