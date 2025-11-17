<?php
$requireLogin = true;
include "header.php";

// Ensure user is logged in
if (empty($buyerId)) {
    header("Location: " . $siteurl . "login.php");
    exit;
}

// Pagination setup
$limit = 12; // items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch wishlist items via your API
$url = $siteurl . "script/admin.php?action=listinglists&user_id=" . urlencode($buyerId);
$data = curl_get_contents($url);
$wishlist = [];
$totalItems = 0;

if ($data !== false) {
    $listings = json_decode($data, true);
    if (!empty($listings)) {
        // Filter only active listings
        foreach ($listings as $listing) {
            if (isset($listing['status']) && strtolower($listing['status']) === 'active') {
                $wishlist[] = $listing;
            }
        }
        $totalItems = count($wishlist);
    }
}

$totalPages = ceil($totalItems / $limit);
$currentItems = array_slice($wishlist, $offset, $limit);
?>

<section id="best-sellers" class="best-sellers section">

  <div class="container section-title" data-aos="fade-up">
    <h2>My Wishlist</h2>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-5">

      <?php if (!empty($currentItems)): ?>
        <?php foreach ($currentItems as $listing):
          $listingId   = $listing['id'];
          $title       = htmlspecialchars($listing['title']);
          $slug        = htmlspecialchars($listing['slug'] ?? '');
          $pricingType = $listing['pricing_type'] ?? '';
          $price       = $listing['price'] ?? '';
          $priceMin    = $listing['price_min'] ?? '';
          $priceMax    = $listing['price_max'] ?? '';
          $categoryNames = !empty($listing['category_names']) ? explode(',', $listing['category_names']) : ['General'];
          $category    = htmlspecialchars(trim($categoryNames[0]));
          $featuredImg = !empty($listing['featured_image']) ? $siteurl . $imagePath . $listing['featured_image'] : $siteurl . "assets/img/default-product.jpg";
          $listingUrl  = $siteurl . "products/" . $slug;

          $sellerName  = htmlspecialchars(trim(($listing['first_name'] ?? '') . ' ' . ($listing['last_name'] ?? '')));
          $sellerPhoto = !empty($listing['photo']) ? $siteurl . $imagePath . $listing['photo'] : $siteurl . "assets/img/user.jpg";

          // Compute display price
          $displayPrice = 'Contact for price';
          if ($pricingType === 'Starting Price' && !empty($price)) {
              $displayPrice = $sitecurrency . number_format($price, 2);
          } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
              $displayPrice = $sitecurrency . number_format($priceMin, 2) . $sitecurrency . '-' . number_format($priceMax, 2);
          }

          // Check wishlist status
          $isWishlisted = false;
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
        ?>

        <!-- Product Card -->
        <div class="col-lg-3 col-md-6 col-6">
          <div class="product-item">
            <div class="product-image">
              <div class="product-badge trending-badge"><?php echo $category; ?></div>
              <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
              <div class="product-actions">
                <button class="action-btn wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>"
                        title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>"
                        data-product-id="<?php echo $listingId; ?>">
                  <?php if ($isWishlisted): ?>
                    <i class="bi bi-heart-fill text-red-500"></i>
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
              <div class="mt-3 d-flex align-items-center">
                <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                <span class="small text-muted"><?php echo $sellerName; ?></span>
              </div>
            </div>
          </div>
        </div>

        <?php endforeach; ?>

      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            Your wishlist is empty.
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <section id="category-pagination" class="category-pagination section mt-4">
        <div class="container">
          <nav class="d-flex justify-content-center" aria-label="Page navigation">
            <ul class="pagination">
              <!-- Previous -->
              <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>" aria-label="Previous">
                  <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Previous</span>
                </a>
              </li>

              <?php
              // Display page numbers
              for ($i = 1; $i <= $totalPages; $i++):
                  if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)):
              ?>
                <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                  <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
              <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
              <?php endif; endfor; ?>

              <!-- Next -->
              <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>" aria-label="Next">
                  <span class="d-none d-sm-inline">Next</span> <i class="bi bi-arrow-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </section>
    <?php endif; ?>

  </div>

</section>

<?php include "footer.php"; ?>
