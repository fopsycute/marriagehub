
<?php include "header.php"; ?>

<!-- Top Banner Advert -->
<?php
$placementSlug = 'marketplace-page-top-banner';
include "listing-banner.php";
?>

<section id="category-header" class="category-header section">

            <div class="container aos-init aos-animate" data-aos="fade-up">
              
              <!-- Header with Register Button -->
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Marketplace</h2>
                <?php if(isset($buyerId) && $buyerId > 0): ?>
                  <a href="<?php echo $siteurl; ?>vendor/" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> List My Product
                  </a>
                <?php else: ?>
                  <a href="<?php echo $siteurl; ?>register/vendor" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Register as Vendor
                  </a>
                <?php endif; ?>
              </div>

              <!-- Filter and Sort Options -->
              <form method="get" id="marketFilter">
              <div class="filter-container mb-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-3">
                  <div class="col-12 col-md-6 col-lg-4">
                    <div class="filter-item search-form">
                      <label for="productSearch" class="form-label">Search Products</label>
                      <div class="input-group">
                        <input type="text" name="search" class="form-control" id="productSearch" placeholder="Search for products..." aria-label="Search for products" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn search-btn" type="submit">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 col-md-6 col-lg-2">
                    <div class="filter-item">
                      <label for="priceRange" class="form-label">Price Range</label>
                      <?php
                      // Fetch price bounds from the API and generate ranges dynamically
                      $priceBounds = null;
                      $boundsData = curl_get_contents($siteurl . "script/admin.php?action=listing_price_bounds");
                      if ($boundsData !== false) {
                          $priceBounds = json_decode($boundsData, true);
                      }

                      $priceOptions = [];
                      if (!empty($priceBounds) && isset($priceBounds['min']) && isset($priceBounds['max'])) {
                          $pmin = floatval($priceBounds['min']);
                          $pmax = floatval($priceBounds['max']);
                          if ($pmax > $pmin) {
                              // create 5 buckets
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
                              // add open-ended top bucket
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
                      <label for="stateFilter" class="form-label">Location</label>
                      <select class="form-select" id="stateFilter" name="state">
                        <option value="">All States</option>
                        <option value="Lagos" <?php echo (isset($_GET['state']) && $_GET['state']=='Lagos') ? 'selected' : ''; ?>>Lagos</option>
                        <option value="Federal Capital Territory" <?php echo (isset($_GET['state']) && $_GET['state']=='Federal Capital Territory') ? 'selected' : ''; ?>>FCT Abuja</option>
                        <option value="Rivers" <?php echo (isset($_GET['state']) && $_GET['state']=='Rivers') ? 'selected' : ''; ?>>Rivers</option>
                        <option value="Oyo" <?php echo (isset($_GET['state']) && $_GET['state']=='Oyo') ? 'selected' : ''; ?>>Oyo</option>
                        <option value="Anambra" <?php echo (isset($_GET['state']) && $_GET['state']=='Anambra') ? 'selected' : ''; ?>>Anambra</option>
                        <option value="Delta" <?php echo (isset($_GET['state']) && $_GET['state']=='Delta') ? 'selected' : ''; ?>>Delta</option>
                        <option value="Enugu" <?php echo (isset($_GET['state']) && $_GET['state']=='Enugu') ? 'selected' : ''; ?>>Enugu</option>
                        <option value="Kano" <?php echo (isset($_GET['state']) && $_GET['state']=='Kano') ? 'selected' : ''; ?>>Kano</option>
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
                  <div class="col-12 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="active-filters">
                      <span class="active-filter-label">Active Filters:</span>
                      <div class="filter-tags">
                        <?php if (!empty($_GET['search'])): ?>
                        <span class="filter-tag"><?php echo htmlspecialchars($_GET['search']); ?> <button class="filter-remove" type="button" onclick="location.href='marketplace.php' "><i class="bi bi-x"></i></button></span>
                        <?php endif; ?>
                        <?php if (!empty($_GET['price_range'])): ?>
                        <span class="filter-tag"><?php echo htmlspecialchars($_GET['price_range']); ?> <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=price_range]').value=''; document.getElementById('marketFilter').submit();"><i class="bi bi-x"></i></button></span>
                        <?php endif; ?>
                        <button class="clear-all-btn btn-primary" type="button" onclick="location.href='<?php echo $siteurl . 'marketplace'; ?>'">Clear All</button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              </form>
              <?php
              // Show quick action: List product if signed-in, otherwise link to vendor registration
              // If a vendor token cookie exists, treat the user as logged-in for the CTA
              $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
              if (!empty($vendorAuth) && empty($activeLog)) {
               echo '<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="' .$siteurl . 'vendor/add-listing.php">List My Product</a></div>';
              }
           
          else {
                echo '<div class="d-flex justify-content-end mb-3"><a class="btn btn-outline-primary" href="' . $siteurl . 'register/vendor' . '">Register as a Vendor</a></div>';
              }
              ?>
            </div>

          </section>

            <section id="best-sellers" class="best-sellers section">
                
      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
<?php
$params = [];
if (!empty($_GET['search'])) $params['search'] = trim($_GET['search']);
if (!empty($_GET['price_range'])) $params['price_range'] = trim($_GET['price_range']);
if (!empty($_GET['sort'])) $params['sort'] = trim($_GET['sort']);
$itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 12;
// Always include items_per_page in params so backend receives the intended page size
$params['items_per_page'] = $itemsPerPage;
$currentPage = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$params['page'] = $currentPage;
// Request structured response (total + data) for server-side pagination
$params['ajax'] = 1;
$query = http_build_query($params);
$url = $siteurl . "script/admin.php?action=listinglists" . ($query ? "&$query" : "");
$data = curl_get_contents($url);

$totalItems = 0;
$listings = [];
if ($data !== false) {
  $resp = json_decode($data, true);
  if (is_array($resp) && isset($resp['data'])) {
    // convert associative arrays to objects to keep existing template accessors ($listing->field)
    $listings = json_decode(json_encode($resp['data']));
    $totalItems = intval($resp['total'] ?? 0);
  } else {
    // fallback for legacy responses
    $decoded = json_decode($data);
    if ($decoded) $listings = $decoded;
  }

  if (!empty($listings)) {
    // Compute visible product count (active + type Product) for display
    $productCount = 0;
    $itemCounter = 0; // counter for inserting in-between banners
    foreach ($listings as $lcheck) {
      if (isset($lcheck->status) && strtolower($lcheck->status) === 'active' && ($lcheck->type ?? '') == 'Product') {
        $productCount++;
      }
    }
    echo '<div class="col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><h5 class="mb-0">Products (' . intval($productCount) . ')</h5></div></div>';
   
    foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active' && $listing->type == 'Product') {

                // 🧩 Extract data
                $listingId   = $listing->id;
                $title       = htmlspecialchars($listing->title);
                $slug        = htmlspecialchars($listing->slug ?? '');
                $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                $price       = htmlspecialchars($listing->price ?? '');
                $priceMin    = htmlspecialchars($listing->price_min ?? '');
                $priceMax    = htmlspecialchars($listing->price_max ?? '');
                $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                $category    = htmlspecialchars(trim($categoryNames[0]));
                $featuredImg = !empty($listing->featured_image)
                    ? $siteurl . $imagePath . $listing->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                $listingUrl  = $siteurl . "products/" . $slug;

                // 🧩 Seller Info
                $sellerName = htmlspecialchars(trim(($listing->first_name ?? '') . ' ' . ($listing->last_name ?? '')));
                $sellerPhoto = !empty($listing->photo)
                    ? $siteurl . $imagePath . $listing->photo
                    : $siteurl . "assets/img/user.jpg";

                // 🧩 Compute Display Price
                $displayPrice = 'Contact for price';
                if ($pricingType === 'Starting Price' && !empty($price)) {
                    $displayPrice = $sitecurrency  . number_format($price, 2);
                } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
                    $displayPrice = $sitecurrency . number_format($priceMin, 2) . $sitecurrency .'-'. number_format($priceMax, 2);
                }

                // ✅ Check wishlist status
                $isWishlisted = false;

                if (!empty($buyerId)) {
                    $apiCheckUrl = $siteurl . "script/user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
                    $wishlistData = curl_get_contents($apiCheckUrl);

                    if ($wishlistData !== false) {
                        $wishlistResult = json_decode($wishlistData, true);
                        if (is_array($wishlistResult)) {
                            if (isset($wishlistResult['isWishlisted'])) {
                                $isWishlisted = (bool)$wishlistResult['isWishlisted'];
                            } elseif (isset($wishlistResult['data']['isWishlisted'])) {
                                $isWishlisted = (bool)$wishlistResult['data']['isWishlisted'];
                            }
                        }
                    }
                }
                ?>

                <!-- 🛍️ Product Card -->
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="product-item">
                        <div class="product-image">
                            <div class="product-badge trending-badge"><?php echo $category; ?></div>
                            <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                            <div class="product-actions">

            

                                <button 
                                    class="action-btn wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>" 
                                    title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                    <?php if ($isWishlisted): ?>
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    <?php else: ?>
                                        <i class="bi bi-heart"></i>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo $category; ?></div>
                            <h4 class="product-name">
                                <a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a>
                            </h4>
                            <div class="product-price"><?php echo $displayPrice; ?></div>

                            <!--Seller Info -->
                            <div class="mt-3 d-flex align-items-center">
                                <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                                <span class="small text-muted"><?php echo $sellerName; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

<?php
// increment visible item counter
$itemCounter++;

// ⭐ Insert banner after every 4 products
if ($itemCounter % 4 == 0) {
?>
    <!-- 🔥 In-between Listing Banner -->
    <div class="col-12 my-4">
        <?php include __DIR__ . '/listing-banner.php'; ?>
    </div>
<?php
}
?>
<?php
            }
        }
    }
}
?>



</div>
        </div>
        
    
        </section>
    <!-- Pagination -->
                <section id="category-pagination" class="category-pagination section">
                    <div class="container">
                      <nav class="d-flex justify-content-center" aria-label="Page navigation">
                        <ul id="marketplace-pagination-list">
                          <!-- JS will populate pagination links -->
                        </ul>
                      </nav>
                    </div>
                </section>
<script>
// Inject PHP variables into JS
const API_BASE = '<?php echo $siteurl; ?>script/admin.php?action=listinglists';
const SITEURL = '<?php echo $siteurl; ?>';
const IMAGE_PATH = '<?php echo $imagePath ?? "uploads/"; ?>';
const SITE_CURRENCY = '<?php echo $sitecurrency ?? ""; ?>';
let currentPage = <?php echo isset($currentPage) ? intval($currentPage) : 1; ?>;
let itemsPerPage = <?php echo isset($itemsPerPage) ? intval($itemsPerPage) : 12; ?>;
let totalItems = <?php echo isset($totalItems) ? intval($totalItems) : 0; ?>;
let LISTING_BANNER_HTML = '';

function buildQuery(params) {
  return Object.keys(params).filter(k => params[k] !== undefined && params[k] !== '' && params[k] !== null).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k])).join('&');
}

function renderListings(listings) {
  const container = document.querySelector('.best-sellers .container .row.g-5');
  if (!container) return;
  if (!Array.isArray(listings) || listings.length === 0) {
    container.innerHTML = '<div class="col-12"><p>No products found.</p></div>';
    return;
  }
  let html = '';
  listings.forEach(listing => {
    if (!listing.status || listing.status.toLowerCase() !== 'active' || listing.type !== 'Product') return;
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
    const listingUrl = SITEURL + 'products/' + encodeURIComponent(slug);
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
  // Insert listing banner HTML after every 4 items when available
  if (LISTING_BANNER_HTML && LISTING_BANNER_HTML.trim() !== '') {
    let out = '';
    const parser = new DOMParser();
    // we already have html string pieces; iterate by splitting by product wrapper
    // simpler: rebuild by iterating the listings array again
    let counter = 0;
    listings.forEach(listing => {
      if (!listing.status || listing.status.toLowerCase() !== 'active' || listing.type !== 'Product') return;
      // extract next product block by finding first occurrence of '<div class="col-lg-3' from current html
      // Instead of parsing the string, reuse the single-item template by calling renderListings for each; but to keep simple, we will append and insert banners based on counter
      counter++;
    });
    // Since we already constructed html for visible items sequentially, just insert banner blocks into the HTML string
    // Split html into array of product blocks
    const productBlocks = html.split(/(?=<div class="col-lg-3)/g).filter(Boolean);
    let merged = '';
    for (let i = 0; i < productBlocks.length; i++) {
      merged += productBlocks[i];
      const visibleIndex = i + 1;
      if (visibleIndex % 4 === 0) {
        merged += `<div class="col-12 mb-4">${LISTING_BANNER_HTML}</div>`;
      }
    }
    container.innerHTML = merged;
  } else {
    container.innerHTML = html;
  }
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
      // If structured response { total, data }
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
      // update browser URL to reflect current filters and page
      try {
        const urlParams = new URLSearchParams(buildQuery(params));
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        history.replaceState(null, '', newUrl);
      } catch (e) {}
    })
    .catch(err => console.error('Live fetch error', err));
}

// debounce helper
function scheduleLiveFetch() {
  clearTimeout(debounceTimer);
  // reset to first page when filters change
  currentPage = 1;
  debounceTimer = setTimeout(liveFetch, 300);
}

document.addEventListener('DOMContentLoaded', function(){
  const searchInput = document.querySelector('#marketFilter [name=search]');
  const selects = document.querySelectorAll('#marketFilter select');
  if (searchInput) {
    searchInput.addEventListener('input', scheduleLiveFetch);
  }
  selects.forEach(s => s.addEventListener('change', function(){ scheduleLiveFetch(); }));

  // Fetch banner HTML for client-side injections (used when live-updating listings)
  try {
    fetch('listing-banner.php')
      .then(r => r.text())
      .then(t => { LISTING_BANNER_HTML = t; })
      .catch(e => { console.warn('Could not load listing banner HTML:', e); });
  } catch (e) { console.warn('Banner fetch failed', e); }

  // Category/subcategory filters removed from UI — search still matches category/subcategory names server-side
  
  // Pagination clicks (delegated)
  document.addEventListener('click', function(e){
    const t = e.target.closest && e.target.closest('[data-market-page]');
    if (t) {
      e.preventDefault();
      const p = parseInt(t.getAttribute('data-market-page')) || 1;
      if (p === currentPage) return;
      currentPage = p;
      liveFetch();
    }
  });

  // Initial update of active filters and pagination on page load
  updateActiveFilters();
  // If server provided total (rendered), allow JS to render pagination correctly after DOM load
  try {
    // if global totalItems injected by server
    if (typeof totalItems !== 'undefined' && totalItems > 0) {
      renderPagination(totalItems, currentPage, itemsPerPage);
    }
  } catch (e) {}
});

// Update the active filters UI tags based on current form values
function updateActiveFilters() {
  const container = document.querySelector('.filter-tags');
  if (!container) return;
  const form = document.getElementById('marketFilter');
  const fd = new FormData(form);
  let html = '';
  const search = (fd.get('search') || '').trim();
  const price = (fd.get('price_range') || '').trim();
  if (search) {
    html += `<span class="filter-tag">${escapeHtml(search)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=search]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`;
  }
  if (price) {
    html += `<span class="filter-tag">${escapeHtml(price)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=price_range]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`;
  }
  // category/subcategory filters removed from UI; search covers these fields
  html += `<button class="clear-all-btn btn-primary" type="button" onclick="document.getElementById('marketFilter').reset(); scheduleLiveFetch();">Clear All</button>`;
  container.innerHTML = html;
}

function escapeHtml(unsafe) {
  return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'\/','`':'&#96;','=':'&#61;'})[s]; });
}

// Render pagination controls
function renderPagination(total, page, perPage) {
  const container = document.getElementById('marketplace-pagination-list');
  if (!container) return;
  perPage = parseInt(perPage, 10) || 1;
  total = parseInt(total, 10) || 0;
  const totalPages = Math.max(1, Math.ceil(total / perPage));

  // hide pagination when there is only one page or no items
  if (total <= perPage || totalPages <= 1) {
    container.innerHTML = '';
    return;
  }

  let html = '';
  // previous
  const prevDisabled = page <= 1;
  html += `<li class="page-item ${prevDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.max(1, page-1)}" aria-label="Previous"><span aria-hidden="true"><i class="bi bi-arrow-left"></i></span><span class="d-none d-sm-inline"> Previous</span></a></li>`;

  // page numbers (show first, last, current +-2)
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

  // next
  const nextDisabled = page >= totalPages;
  html += `<li class="page-item ${nextDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.min(totalPages, page+1)}" aria-label="Next"><span class="d-none d-sm-inline">Next </span><span aria-hidden="true"><i class="bi bi-arrow-right"></i></span></a></li>`;
  container.innerHTML = html;
}
</script>




    <!-- Community Group -->
    <?php
    // Ensure we have a context (category / subcategory) determined from first active product
    if (!isset($contextCategory) || !isset($contextSubcategory)) {
      $contextCategory = '';
      $contextSubcategory = '';
      if (!empty($listings) && is_array($listings)) {
        foreach ($listings as $l) {
          if (isset($l->status) && strtolower($l->status) === 'active' && ($l->type ?? '') == 'Product') {
            $names = !empty($l->category_names) ? explode(',', $l->category_names) : [];
            $contextCategory = trim($names[0] ?? '');
            $subnames = !empty($l->subcategory_names) ? explode(',', $l->subcategory_names) : [];
            $contextSubcategory = trim($subnames[0] ?? '');
            break;
          }
        }
      }
    }
    ?>
  <section id="featured-courses" class="featured-courses section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Tribes & Groups</h2>
      <p><a href="<?php echo $siteurl; ?>all-groups.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <?php
        // Fetch groups and select related ones based on current product context (subcat -> cat -> fallback)
        $url = $siteurl . "script/admin.php?action=grouplists";
        $data = curl_get_contents($url);

        $groupsToShow = [];
        $contextCats = array_filter(array_map('trim', explode(',', strtolower($contextCategory ?? ''))));
        $contextSubs = array_filter(array_map('trim', explode(',', strtolower($contextSubcategory ?? ''))));

        if ($data !== false) {
          $groups = json_decode($data);
          $bySub = [];
          $byCat = [];
          $fallback = [];

          if (!empty($groups) && is_array($groups)) {
            foreach ($groups as $group) {
              if (!isset($group->status) || strtolower($group->status) !== 'active') continue;
              $gid = $group->id ?? null; if (!$gid) continue;
              $gCats = array_filter(array_map('trim', explode(',', strtolower($group->category_names ?? ''))));
              $gSubs = array_filter(array_map('trim', explode(',', strtolower($group->subcategory_names ?? ''))));
              $matchedSub = (!empty($contextSubs) && array_intersect($contextSubs, $gSubs));
              $matchedCat = (!$matchedSub && !empty($contextCats) && array_intersect($contextCats, $gCats));
              if ($matchedSub) $bySub[$gid] = $group;
              elseif ($matchedCat) $byCat[$gid] = $group;
              else $fallback[$gid] = $group;
            }

            $merged = [];
            foreach ([$bySub, $byCat, $fallback] as $pool) {
              foreach ($pool as $id => $item) {
                if (count($merged) >= 3) break 2;
                if (!isset($merged[$id])) $merged[$id] = $item;
              }
            }
            $groupsToShow = array_values($merged);
          }
        }

        if (!empty($groupsToShow)):
          foreach ($groupsToShow as $group):
              $groupId = $group->id;
              $title = $group->group_name;
              $author = $group->first_name . ' ' . $group->last_name;
              $group_access = $group->group_access;
              $group_type = $group->group_type;
              $date = date('M d, Y', strtotime($group->created_at));
              $banner = $group->banner ?? '';
              $slug = $group->slug ?? '';
              $content = limitWords(strip_tags($group->group_description), 10);
              $photo = !empty($group->photo)
                          ? $siteurl . $imagePath . $group->photo
                          : $siteurl . "assets/img/user.jpg";
              $bannerimage = $siteurl . $imagePath . $banner;

              // Category & Subcategory — only first item
              $category = !empty($group->category_names) ? trim(explode(',', $group->category_names)[0]) : 'Uncategorized';
              $subcategory = !empty($group->subcategory_names) ? trim(explode(',', $group->subcategory_names)[0]) : 'General';

              // Price logic
        if (strtolower($group_access) === 'free') {
          $price = 'Free';
      } else {
          $fees = [
              floatval($group->fee_1m ?? 0),
              floatval($group->fee_3m ?? 0),
              floatval($group->fee_6m ?? 0),
              floatval($group->fee_12m ?? 0)
          ];

          // ✅ Use regular anonymous function for broader compatibility
          $fees = array_filter($fees, function ($f) {
              return $f > 0;
          });

          if (!empty($fees)) {
              $minFee = min($fees);
              $maxFee = max($fees);
              $price = ($minFee === $maxFee)
                  ? '₦' . number_format($minFee)
                  : '₦' . number_format($minFee) . ' - ₦' . number_format($maxFee);
          } else {
              $price = 'Paid';
          }
      }
      ?>
        <div class="col-lg-4 col-md-6 col-12 mb-4">
          <div class="course-card">
            <div class="course-image">
              <img src="<?php echo $bannerimage; ?>" alt="Course" class="img-fluid">
              <div class="badge featured"><?php echo $group_type; ?></div>
              <div class="price-badge"><?php echo $price; ?></div>
            </div>
            <div class="course-content">
              <div class="course-meta">
                <span class="level"><?php echo $category; ?></span>
                <span class="duration"><?php echo $subcategory; ?></span>
              </div>
              <h3><a href="group/<?php echo $slug; ?>"><?php echo $title; ?></a></h3>
              <p><?php echo $content; ?>...</p>
              <div class="instructor">
                <img src="<?php echo $photo; ?>" alt="Instructor" class="instructor-img">
                <div class="instructor-info">
                  <h6><?php echo $author; ?></h6>
                  <span>Admin</span>
                </div>
              </div>
              <a href="<?php echo $siteurl; ?>group/<?php echo $slug; ?>" class="btn-course">Join Group</a>
            </div>
          </div>
        </div>
      <?php
          endforeach;
      else:
          echo '<p>No active groups found.</p>';
      endif;
      ?>
    </div>
  </div>

</section><!-- /Community Group -->


<!-- Questions Slider Section -->
<section id="questions-slider" class="section">
   <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Question and Answer</h2>
      <p><a href="<?php echo $siteurl; ?>questions-answers.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <?php
    // Fetch questions and select related ones based on current product context
    $url = $siteurl . "script/admin.php?action=questionlists";
    $data = curl_get_contents($url);
    $questionsList = [];

    $contextCats = array_filter(array_map('trim', explode(',', strtolower($contextCategory ?? ''))));
    $contextSubs = array_filter(array_map('trim', explode(',', strtolower($contextSubcategory ?? ''))));

    $bySub = [];
    $byCat = [];
    $fallback = [];

    if ($data !== false) {
      $questions = json_decode($data);
      if (!empty($questions) && is_array($questions)) {
        foreach ($questions as $question) {
          $qid = $question->id ?? null;
          if (!$qid) continue;

          $qCats = array_filter(array_map('trim', explode(',', strtolower($question->category_names ?? ''))));
          $qSubs = array_filter(array_map('trim', explode(',', strtolower($question->subcategory_names ?? ''))));

          $matchedSub = (!empty($contextSubs) && array_intersect($contextSubs, $qSubs));
          $matchedCat = (!$matchedSub && !empty($contextCats) && array_intersect($contextCats, $qCats));

          $article = $question->article ?? '';
          $words = explode(' ', strip_tags($article));
          $shortText = implode(' ', array_slice($words, 0, 6));
          $hasMore = count($words) > 6;

          $entry = [
            'id' => $qid,
            'title' => $question->title ?? '',
            'slug' => $question->slug ?? '',
            'author' => (intval($question->anonymous ?? 0) === 1) ? 'Anonymous' : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: 'Unknown User'),
            'category' => !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : '',
            'date' => date('M d, Y', strtotime($question->created_at ?? '')),
            'shortText' => $shortText,
            'hasMore' => $hasMore,
            'answers' => intval($question->total_answers ?? 0)
          ];

          if ($matchedSub) $bySub[$qid] = $entry;
          elseif ($matchedCat) $byCat[$qid] = $entry;
          else $fallback[$qid] = $entry;
        }

        // merge priority-wise and limit to 6
        $merged = [];
        foreach ([$bySub, $byCat, $fallback] as $pool) {
          foreach ($pool as $id => $item) {
            if (count($merged) >= 6) break 2;
            if (!isset($merged[$id])) $merged[$id] = $item;
          }
        }

        $questionsList = array_values($merged);
      }
    }

    
    ?>

    <?php if (!empty($questionsList)): ?>
      <!-- Swiper Container -->
      <div class="swiper init-swiper">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "autoplay": {
              "delay": 4000,
              "disableOnInteraction": false
            },
            "grabCursor": true,
            "speed": 600,
            "slidesPerView": "auto",
            "spaceBetween": 20,
            "navigation": {
              "nextEl": ".questions-swiper-button-next",
              "prevEl": ".questions-swiper-button-prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 1,
                "spaceBetween": 10
              },
              "576": {
                "slidesPerView": 2,
                "spaceBetween": 15
              },
              "768": {
                "slidesPerView": 3,
                "spaceBetween": 20
              },
              "992": {
                "slidesPerView": 3,
                "spaceBetween": 20
              }
            }
          }
        </script>

   <div class="swiper-wrapper">
  <?php foreach ($questionsList as $q): ?>
    <div class="swiper-slide">
      <div class="post-list border-bottom p-3 bg-white rounded shadow-sm">
        <div class="post-meta mb-2">
          <span class="date fw-bold text-primary"><?php echo $q['category']; ?></span>
          <span class="mx-1">•</span>
          <span class="text-muted small"><?php echo $q['date']; ?></span>
        </div>

        <h2 class="mb-2 fs-5 d-flex align-items-center justify-content-between">
          <a href="<?php echo $siteurl; ?>single-questions/<?php echo $q['slug']; ?>" 
             class="text-dark text-decoration-none hover:text-primary flex-grow-1">
            <?php echo $q['title']; ?>
          </a>
          <a href="<?php echo $siteurl; ?>single-questions/<?php echo $q['slug']; ?>" 
             class="text-primary ms-2">
            <i class="bi bi-arrow-right fs-5"></i>
          </a>
        </h2>

        <p class="mb-1 text-muted">
          <?php echo $q['shortText']; ?><?php echo $q['hasMore'] ? '...' : ''; ?>
        </p>

        <span class="author d-block text-secondary small">
          <?php echo $q['author']; ?>
        </span>
      </div>
    </div>
  <?php endforeach; ?>
</div>


        <!-- Swiper Navigation Buttons -->
        <div class="questions-swiper-button-prev swiper-button-prev"></div>
        <div class="questions-swiper-button-next swiper-button-next"></div>
      </div>
    <?php else: ?>
      <p>No questions available.</p>
    <?php endif; ?>

  </div>
</section>


 <!-- Trending Category Section -->
<section id="trending-category" class="trending-category section">
  <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>More Articles</h2>
          <p><a href="<?php echo $siteurl; ?>blog.php">View All</a></p>
        </div>
      </div><!-- End Section Title -->

  <!-- Blog Grid Container -->
  <div class="container my-5">
    <div class="row g-4">
      <?php
        $url = $siteurl . "script/admin.php?action=bloglists";
        $data = curl_get_contents($url);
        $limit = 3; // Number of blogs to show
        $relatedBlogs = [];

        // Prepare current marketplace context (category/subcategory from first active product)
        $contextCats = array_filter(array_map('trim', explode(',', strtolower($contextCategory ?? ''))));
        $contextSubs = array_filter(array_map('trim', explode(',', strtolower($contextSubcategory ?? ''))));

        if ($data !== false) {
          $blogs = json_decode($data);
          $bySub = [];
          $byCat = [];
          $fallback = [];

          if (!empty($blogs) && is_array($blogs)) {
            foreach ($blogs as $blog) {
              if (!isset($blog->status) || strtolower($blog->status) !== 'active' || (!empty($blog->group_id))) continue;
              $bid = $blog->id ?? null; if (!$bid) continue;

              $bCats = array_filter(array_map('trim', explode(',', strtolower($blog->category_names ?? ''))));
              $bSubs = array_filter(array_map('trim', explode(',', strtolower($blog->subcategory_names ?? ''))));

              $matchedSub = (!empty($contextSubs) && array_intersect($contextSubs, $bSubs));
              $matchedCat = (!$matchedSub && !empty($contextCats) && array_intersect($contextCats, $bCats));

              if ($matchedSub) $bySub[$bid] = $blog;
              elseif ($matchedCat) $byCat[$bid] = $blog;
              else $fallback[$bid] = $blog;
            }

            // merge bySub -> byCat -> fallback and limit to $limit
            $merged = [];
            foreach ([$bySub, $byCat, $fallback] as $pool) {
              foreach ($pool as $id => $item) {
                if (count($merged) >= $limit) break 2;
                if (!isset($merged[$id])) $merged[$id] = $item;
              }
            }

            $relatedBlogs = array_values($merged);
          }
        }

        if (!empty($relatedBlogs)) {
          foreach ($relatedBlogs as $blog) {
            $blogId = $blog->id;
            $title = htmlspecialchars($blog->title);
            $slug = htmlspecialchars($blog->slug);
            $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
            $content = limitWords(strip_tags($blog->article), 10);
            $date = date('F d, Y', strtotime($blog->created_at));
            $views = htmlspecialchars($blog->views ?? 0);
            $photo = !empty($blog->photo)
                  ? $siteurl . $imagePath . $blog->photo
                  : $siteurl . "assets/img/user.jpg";
            $blogimage = !empty($blog->featured_image)
                  ? $siteurl . $imagePath . $blog->featured_image
                  : $siteurl . "assets/img/default-blog.jpg";
            $blogUrl = $siteurl . "blog-details/" . $slug;
            $categoryNames = !empty($blog->category_names) ? explode(',', $blog->category_names) : ['General'];
            $category = htmlspecialchars(trim($categoryNames[0]));

                      ?>
                      
   <div class="col-lg-4 col-md-6 col-12">
  <div class="contentBox p-3 h-100">
    
    <!-- Category Badge -->
    <span class="category-outline-badge mb-2 d-inline-block">
      <?php echo $category; ?>
    </span>

    <!-- Date + Views -->
    <small class="text-muted d-block mb-2"><?php echo $date; ?> • <?php echo $views; ?> views</small>

    <!-- Blog Title -->
    <h5 class="card-title mb-2">
      <a href="<?php echo $blogUrl; ?>" class="text-dark text-decoration-none">
        <?php echo $title; ?>
      </a>
    </h5>

    <!-- Short Excerpt -->
    <p class="mb-3"><?php echo $content; ?>...</p>

    <!-- Author -->
    <div class="d-flex align-items-center mt-auto">
      <img src="<?php echo $photo; ?>" 
           alt="<?php echo $author; ?>" 
           class="rounded-circle me-2" 
           style="width:40px;height:40px;">
      <span><?php echo $author; ?></span>
    </div>

  </div>
</div>

                  <?php
                  }
              }
          
      
      ?>
    </div>
  </div>

</section><!-- /Trending Category Section -->
<?php include "footer.php"; ?>