
    <?php include "header.php"; ?>
    <?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // ✅ Build URLs
    $shareUrl = $siteurl . 'listing-details/' . urlencode($slug);
    $sitelink = $siteurl . "script/";
    $apiUrl = $sitelink . "admin.php?action=fetchlistingslug&slug=" . $slug;

    // ✅ Fetch listing details from API
    $data = curl_get_contents($apiUrl);

    if ($data !== false) {
        $listings = json_decode($data);
        if (!empty($listings)) {
            $listing = $listings[0];

            // 🧩 Extract listing details
            $listingId    = $listing->listing_id ?? '';
            $title        = htmlspecialchars($listing->title ?? '');
            $description  = $listing->description ?? '';
            $price        = htmlspecialchars($listing->price ?? '');
            $priceMin     = htmlspecialchars($listing->price_min ?? '');
            $priceMax     = htmlspecialchars($listing->price_max ?? '');
            $pricingType  = htmlspecialchars($listing->pricing_type ?? '');
            $category     = htmlspecialchars($listing->category_names ?? '');
            $subcategory  = htmlspecialchars($listing->subcategory_names ?? '');
            $status       = htmlspecialchars($listing->status ?? '');
            $limited_slot = htmlspecialchars($listing->limited_slot ?? '');
            $availability = $listing->availability ?? '';
            $capacity_volume = $listing->capacity ?? '';
            $pricing_notes = $listing->pricing_notes ?? '';
            $delivery = $listing->delivery ?? '';
            $service_coverage = $listing->coverage ?? '';
             $type = strtolower($listing->type); // ✅ product or service


            $created_at   = date('F d, Y \a\t h:i A', strtotime($listing->created_at));
                   
            $shortBio = limitWords(strip_tags($description), 20);
            $isTruncated = (str_word_count(strip_tags($description)) > 20);

            
            // 🧩 Variations
            $variations = $listing->variations ?? [];

            // 🧩 Seller details
            $sellerName   = trim(($listing->first_name ?? '') . ' ' . ($listing->last_name ?? ''));
            $sellerPhoto  = !empty($listing->photo)
                ? $siteurl . $imagePath . $listing->photo
                : $siteurl . "assets/img/default-user.jpg";

            // 🧩 Featured image
           $images = [];
    if (!empty($listing->all_images)) {
        $images = explode(',', $listing->all_images);
    }


    $videos = [];
    if (!empty($listing->all_videos)) {
        $videos = explode(',', $listing->all_videos);
    }

    // Redirect if listing is not active
    if (strtolower($status) !== 'active') {
        header("Location: index.php");
        exit;
    }


         if ($pricingType === 'Starting Price' && !empty($price)) {
    $displayPrice = $sitecurrency . number_format($price, 2);
    } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
        $displayPrice = $sitecurrency . number_format($priceMin, 2) . ' - ' . number_format($priceMax, 2);
    } elseif ($pricingType === 'Custom Quote') {
        $isCustomQuote = true;
        $displayPrice = '<div class="custom-quote">
            <strong>Pricing:</strong><br>
            This product/service uses a custom quote. Customers must contact you directly for pricing details.
        </div>';
    } else {
        $isCustomQuote = true;
        $displayPrice = '<div class="custom-quote">
            <strong>Pricing:</strong><br>
            Please contact the seller for a custom quote.
        </div>';
    }


             } else {
            echo "<div class='alert alert-warning text-center'>No listing found with the given slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center'>Error fetching listing data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

// Prepare product page context for related content (category/subcategory names)
$contextCats = array_filter(array_map('trim', explode(',', strtolower($category ?? ''))));
$contextSubs = array_filter(array_map('trim', explode(',', strtolower($subcategory ?? ''))));
// canonical ids for this listing (internal id and public listing_id)
$currentInternalId = intval($listing->id ?? 0);
$currentExternalListingId = intval($listing->listing_id ?? 0);
            ?>


<?php 
$isWishlisted = false; // ✅ Always define it first

if (!empty($buyerId)) {
    $apiCheckUrl = $sitelink . "user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
    $wishlistData = curl_get_contents($apiCheckUrl);

    if ($wishlistData !== false) {
        $wishlistResult = json_decode($wishlistData, true);

        if (is_array($wishlistResult) && isset($wishlistResult['isWishlisted'])) {
            $isWishlisted = (bool)$wishlistResult['isWishlisted'];
        }
    }
}
?>


   <?php
          $stats_url = $sitelink . "user.php?action=reviewstats&listing_id=" . $listingId;
          $stats_data = curl_get_contents($stats_url);
          $stats = $stats_data ? json_decode($stats_data, true) : [];

          $average = round($stats['average_rating'] ?? 0, 1);
          $total_reviews = intval($stats['total_reviews'] ?? 0);
          $five = $stats['five_star_percent'] ?? 0;
          $four = $stats['four_star_percent'] ?? 0;
          $three = $stats['three_star_percent'] ?? 0;
          $two = $stats['two_star_percent'] ?? 0;
          $one = $stats['one_star_percent'] ?? 0;
          ?>

<section id="product-details" class="product-details section">

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4">
          <!-- Product Gallery -->
          <div class="col-lg-7 aos-init aos-animate" data-aos="zoom-in" data-aos-delay="150">
    <div class="product-gallery">
        <?php if (!empty($images)) { 
            // First image is main
            $mainImage = $siteurl . $imagePath . trim($images[0]);
        ?>
        <!-- 🖼️ Main Showcase -->
        <div class="main-showcase">
            <div class="image-zoom-container">
                <img src="<?php echo $mainImage; ?>" 
                     alt="<?php echo $title; ?>" 
                     class="img-fluid main-product-image drift-zoom" 
                     id="main-product-image" 
                     data-zoom="<?php echo $mainImage; ?>">
                <div class="image-navigation">
                    <button class="nav-arrow prev-image image-nav-btn" type="button">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-arrow next-image image-nav-btn" type="button">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 🧩 Thumbnail Grid -->
        <div class="thumbnail-grid">
            <?php foreach ($images as $index => $imgName): 
                $imgUrl = $siteurl . $imagePath . trim($imgName);
                $active = $index === 0 ? 'active' : '';
            ?>
            <div class="thumbnail-wrapper thumbnail-item <?php echo $active; ?>" 
                 data-image="<?php echo $imgUrl; ?>">
                <img src="<?php echo $imgUrl; ?>" 
                     alt="View <?php echo $index + 1; ?>" 
                     class="img-fluid">
            </div>
            <?php endforeach; ?>
        </div>
        <?php } else { ?>
            <!-- Fallback if no images -->
            <img src="<?php echo $siteurl . 'assets/img/default-product.jpg'; ?>" 
                 alt="No Image" class="img-fluid rounded">
        <?php } ?>
    </div>
</div>

          <!-- Product Details -->
          <div class="col-lg-5 aos-init aos-animate" data-aos="fade-left" data-aos-delay="200">
            <div class="product-details">
              <div class="product-badge-container">
                <span class="badge-category"><?php echo $category; ?></span>
                <div class="rating-group">
                  <div class="stars">
                    <?php
                      $fullStars = floor($average);
                      $halfStar = ($average - $fullStars >= 0.5);
                      for ($i = 1; $i <= 5; $i++) {
                          if ($i <= $fullStars) {
                              echo '<i class="bi bi-star-fill"></i>';
                          } elseif ($halfStar && $i == $fullStars + 1) {
                              echo '<i class="bi bi-star-half"></i>';
                          } else {
                              echo '<i class="bi bi-star"></i>';
                          }
                      }
                      ?>
                  </div>
                  <span class="review-text">(<?php echo $total_reviews; ?> reviews)</span>
                </div>
              </div>

              <h1 class="product-name"><?php echo $title; ?></h1>

              <div class="pricing-section">
                <div class="price-display">
                  <span class="sale-price"><?php echo $displayPrice; ?></span>
                  <?php if ($pricing_notes): ?>
                  <small> <?php echo $pricing_notes; ?>  </small>
                  <?php endif; ?>
                </div>
              </div>

        <div class="product-description">

        <p class="bio-text">
          <span class="bio-short"><?php echo $shortBio; ?></span>
          <?php if ($isTruncated): ?>
            <span class="bio-full d-none"><?php echo $description; ?></span>
            <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
          <?php endif; ?>
        </p>
              </div>

              <!-- 🧩 Variation Dropdown -->
            <!-- ✅ Hidden input -->
          <input type="hidden" id="single-price" value="<?php echo $price; ?>">
          <input type="hidden" id="base-price" value="<?php echo $displayPrice; ?>">
          <input type="hidden" id="siteCurrency" value="<?php echo htmlspecialchars($sitecurrency); ?>">
          <input type="hidden" id="limited-slot" value="<?php echo $limited_slot; ?>">
          <input type="hidden" id="listing_id" value="<?php echo $listingId; ?>">
          <input type="hidden" id="user_id" value="<?php echo !empty($buyerId) ? $buyerId : ''; ?>">
               
          <?php if (!empty($variations)): ?>
        <div class="availability-status">
          <label for="variationSelect" class="form-label"><strong>Select Variation:</strong></label>

          <!-- ✅ Hidden input to store currency -->
          <input type="hidden" id="siteCurrency" value="<?php echo htmlspecialchars($sitecurrency); ?>">

          <select id="variationSelect" class="form-select" required>
              <option value="" disabled selected>Select Variation</option>
              <?php foreach ($variations as $variation): ?>
                  <option 
                      value="<?php echo htmlspecialchars($variation->variation_name); ?>" 
                      data-price="<?php echo htmlspecialchars($variation->variation_price); ?>">
                      <?php echo htmlspecialchars($variation->variation_name); ?> 
                      - <?php echo $sitecurrency . number_format($variation->variation_price, 2); ?>
                  </option>
              <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

        <?php if (isset($limited_slot)): ?>
    <?php if ($limited_slot > 0): ?>
        <div class="availability-status">
            <div class="stock-indicator">
                <i class="bi bi-check-circle-fill"></i>
                <span class="stock-text">Available</span>
            </div>
            <div class="quantity-left">Only <?php echo $limited_slot; ?> slot<?php echo $limited_slot > 1 ? 's' : ''; ?> remaining</div>
        </div>
    <?php else: ?>
        <div class="availability-status">
            <div class="stock-indicator text-danger">
                <i class="bi bi-x-circle-fill"></i>
                <span class="stock-text">Out of Stock</span>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="availability-status">
        <div class="stock-indicator">
            <i class="bi bi-check-circle-fill"></i>
            <span class="stock-text">Available</span>
        </div>
    </div>
<?php endif; ?>
           <!-- Purchase Options -->
            <!-- 🧩 Quantity Control -->
<div class="purchase-section">

<?php if ($type == 'product'): ?>

      <div class="quantity-control">
        <label class="control-label">Quantity:</label>
        <div class="quantity-input-group">
            <div class="quantity-selector">
                <button class="quantity-btn decrease" type="button"><i class="bi bi-dash"></i></button>
                <input type="number" class="quantity-input" value="1" min="1" id="quantity">
                <button class="quantity-btn increase" type="button"><i class="bi bi-plus"></i></button>
            </div>
        </div>
    </div>

   <div class="action-buttons">
                  <button class="btn primary-action" id="addCart">
                    <i class="bi bi-bag-plus"></i>
                    Add to Cart
                  </button>
                  
          <button 
  class="btn icon-action wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>" 
  title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>" 
  data-product-id="<?php echo $listingId; ?>"
>
  <?php if ($isWishlisted): ?>
    <i class="bi bi-heart-fill text-red-500"></i>
  <?php else: ?>
    <i class="bi bi-heart"></i>
  <?php endif; ?>
</button>
                </div>
      <?php else: ?>
            <!-- ✅ For Services: Book Now -->
            <div class="action-buttons">
                <button class="btn primary-action" id="bookService">
                  <i class="bi bi-calendar-check"></i>
                  Book Now
                </button>
               
            </div>
          <?php endif; ?>
  
              </div>

              <!-- Benefits List -->
             <div class="benefits-list">
          <?php if (!empty($availability) || !empty($capacity_volume) || !empty($delivery) || !empty($service_coverage)): ?>
            
            <?php if (!empty($availability)): ?>
              <div class="benefit-item">
                <i class="bi bi-calendar-check"></i>
                <span><strong>Availability:</strong> <?php echo htmlspecialchars($availability); ?></span>
              </div>
            <?php endif; ?>

            <?php if (!empty($capacity_volume)): ?>
              <div class="benefit-item">
                <i class="bi bi-box-seam"></i>
                <span><strong>Capacity/Volume:</strong> <?php echo htmlspecialchars($capacity_volume); ?></span>
              </div>
            <?php endif; ?>

            <?php if (!empty($delivery)): ?>
              <div class="benefit-item">
                <i class="bi bi-truck"></i>
                <span><strong>Delivery / In-person:</strong> <?php echo htmlspecialchars($delivery); ?></span>
              </div>
            <?php endif; ?>

            <?php if (!empty($service_coverage)): ?>
              <div class="benefit-item">
                <i class="bi bi-globe2"></i>
                <span><strong>Service Coverage:</strong> <?php echo $service_coverage; ?></span>
              </div>
            <?php endif; ?>

          <?php endif; ?>
        </div>

            </div>
          </div>
        </div>

        <!-- Information Tabs -->
        <div class="row mt-5 aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">
          <div class="col-12">
            <div class="info-tabs-container">
              <nav class="tabs-navigation nav" role="tablist">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-overview" type="button" aria-selected="true" role="tab">Description</button>
                <?php if (!empty($videos)) : ?>
  <!-- 🎥 Videos Tab Button -->
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-videos" type="button" aria-selected="false" tabindex="-1" role="tab">
              Videos
            </button>
          <?php endif; ?>
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-customer-reviews" type="button" aria-selected="false" tabindex="-1" role="tab">Reviews (<?php echo $total_reviews; ?>)</button>
              </nav>

              <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="ecommerce-product-details-5-overview" role="tabpanel">
                  <div class="overview-content">
                    <div class="row g-4">
                      <div class="col-lg-12">
                        <div class="content-section">
                          <h3>Description</h3>
                           <p class="bio-text">
                    <span class="bio-short"><?php echo $shortBio; ?></span>
                    <?php if ($isTruncated): ?>
                      <span class="bio-full d-none"><?php echo $description; ?></span>
                      <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
                    <?php endif; ?>
                  </p> 
                    </div>
                  </div>
                </div>
                 </div>
                </div>
                <!-- Videos Tab -->
                <?php if (!empty($videos)) : ?>
  <!-- 🎥 Videos Tab Content -->
  <div class="tab-pane fade" id="ecommerce-product-details-5-videos" role="tabpanel">
    <div class="videos-content">
      <div class="video-grid row g-4">
        <?php foreach ($videos as $vidUrl): 
            $vidUrl = trim($vidUrl);
            if (!empty($vidUrl)): ?>
          <div class="col-md-6">
            <div class="video-item card shadow-sm border-0">
              <div class="ratio ratio-16x9">
                <video controls preload="none" class="rounded">
                  <source src="<?php echo  $siteurl . $imagePath . $vidUrl; ?>" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </div>
            </div>
          </div>
        <?php endif; endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="ecommerce-product-details-5-customer-reviews" role="tabpanel">
                  <div class="reviews-content">

                   <div class="comment-form section" id="write_review">
                  <div class="container">
                    <?php if ($activeLog == 1) { ?>
                    <form id="postproductreview" method="POST">
                      <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                      <h4>Give Review</h4>
                      <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                      <input name="listing_id" type="hidden" class="form-control" value="<?php echo $listingId; ?>">
                            <!-- Star rating -->
                      <div class="mb-3">
                        <label class="form-label">Your rating</label>
                        <div class="star-rating" role="radiogroup" aria-label="Rating">
                          <button type="button" class="star" data-value="1" aria-label="1 star">☆</button>
                          <button type="button" class="star" data-value="2" aria-label="2 stars">☆</button>
                          <button type="button" class="star" data-value="3" aria-label="3 stars">☆</button>
                          <button type="button" class="star" data-value="4" aria-label="4 stars">☆</button>
                          <button type="button" class="star" data-value="5" aria-label="5 stars">☆</button>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                      </div>
                      <div class="row">
                          <input type="hidden" value="post_productreview" name="action">
                        <div class="col form-group">
                          <textarea name="comment" class="editor" placeholder="Your Review*"></textarea>
                        </div>
                      </div>

                      <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="submit-btn">Post Review</button>
                      </div>

                    </form>

                    <?php } ?>

                  </div>
                </div>

             

                    <div class="reviews-header">
                <div class="rating-overview">
                  <div class="average-score">
                    <div class="score-display"><?php echo $average; ?></div>
                    <div class="score-stars">
                      <?php
                      $fullStars = floor($average);
                      $halfStar = ($average - $fullStars >= 0.5);
                      for ($i = 1; $i <= 5; $i++) {
                          if ($i <= $fullStars) {
                              echo '<i class="bi bi-star-fill"></i>';
                          } elseif ($halfStar && $i == $fullStars + 1) {
                              echo '<i class="bi bi-star-half"></i>';
                          } else {
                              echo '<i class="bi bi-star"></i>';
                          }
                      }
                      ?>
                    </div>
                    <div class="total-reviews">
                      <?php echo $total_reviews; ?> customer review<?php echo ($total_reviews != 1) ? 's' : ''; ?>
                    </div>
                  </div>

                  <div class="rating-distribution">
                    <div class="rating-row">
                      <span class="stars-label">5★</span>
                      <div class="progress-container"><div class="progress-fill" style="width: <?php echo $five; ?>%;"></div></div>
                      <span class="count-label"><?php echo $stats['five_star'] ?? 0; ?></span>
                    </div>
                    <div class="rating-row">
                      <span class="stars-label">4★</span>
                      <div class="progress-container"><div class="progress-fill" style="width: <?php echo $four; ?>%;"></div></div>
                      <span class="count-label"><?php echo $stats['four_star'] ?? 0; ?></span>
                    </div>
                    <div class="rating-row">
                      <span class="stars-label">3★</span>
                      <div class="progress-container"><div class="progress-fill" style="width: <?php echo $three; ?>%;"></div></div>
                      <span class="count-label"><?php echo $stats['three_star'] ?? 0; ?></span>
                    </div>
                    <div class="rating-row">
                      <span class="stars-label">2★</span>
                      <div class="progress-container"><div class="progress-fill" style="width: <?php echo $two; ?>%;"></div></div>
                      <span class="count-label"><?php echo $stats['two_star'] ?? 0; ?></span>
                    </div>
                    <div class="rating-row">
                      <span class="stars-label">1★</span>
                      <div class="progress-container"><div class="progress-fill" style="width: <?php echo $one; ?>%;"></div></div>
                      <span class="count-label"><?php echo $stats['one_star'] ?? 0; ?></span>
                    </div>
                  </div>
                </div>
                         <?php if ($activeLog == 1) { ?>
                <div class="write-review-cta">
                  <h4>Share Your Experience</h4>
                  <p>Help others make informed decisions</p>
                  <a href="#write_review" class="btn review-btn">Write Review</a>
                </div>

                <?php } ?>
              </div>


                    <div class="customer-reviews-list">
                     <?php 
$sitelink = $siteurl . "script/";
$comments_url = $sitelink . "user.php?action=productcommentsdata&listing_id=" . $listingId;

$data = curl_get_contents($comments_url);
if ($data !== false) {
    $comments = json_decode($data);
    if (!empty($comments) && is_array($comments)) {

        $reviewsPerPage = 12;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $totalReviews = count($comments);
        $totalPages = max(1, ceil($totalReviews / $reviewsPerPage));
        if ($page > $totalPages) $page = $totalPages;
        $start = ($page - 1) * $reviewsPerPage;
        $commentsToShow = array_slice($comments, $start, $reviewsPerPage);

        foreach ($commentsToShow as $comment) {
            $username     = htmlspecialchars(trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? '')) ?: 'Anonymous');
            $avatar       = htmlspecialchars($siteurl . $imagePath . ($comment->photo ?? 'default.png'));
            $commentText  = nl2br(htmlspecialchars($comment->comment ?? ''));
            $rating       = max(0, min(5, intval($comment->rating ?? 0)));
            $headline     = htmlspecialchars($comment->headline ?? 'Review');
            $created_date = !empty($comment->created_at) ? date('F d, Y', strtotime($comment->created_at)) : '';
            $verified     = !empty($comment->verified_buyer) ? true : false;
            ?>

            <div class="review-card">
              <div class="reviewer-profile">
                <img src="<?php echo $avatar; ?>" alt="<?php echo $username; ?>" class="profile-pic">
                <div class="profile-details">
                  <div class="customer-name">
                    <?php echo $username; ?>
                    <?php if ($verified): ?>
                      <span class="badge bg-success ms-2">Verified Buyer</span>
                    <?php else: ?>
                      <span class="badge bg-light text-muted ms-2">&nbsp;</span>
                    <?php endif; ?>
                  </div>
                  <div class="review-meta">
                    <div class="review-stars">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi <?php echo ($i <= $rating) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                      <?php endfor; ?>
                    </div>
                    <span class="review-date"><?php echo $created_date; ?></span>
                  </div>
                </div>
              </div>
              <h5 class="review-headline"><?php echo $headline; ?></h5>
              <div class="review-text">
                <p><?php echo $commentText; ?></p>
              </div>
        
            </div>

            <?php
           
        include 'inline-ad.php';

        }

        // Pagination UI
        if ($totalPages > 1) {
            $baseParams = $_GET;
            if (!isset($baseParams['listing_id']) && !empty($listingId)) {
                $baseParams['listing_id'] = $listingId;
            }
            ?>
            <nav aria-label="Reviews pagination" class="mt-4">
              <ul class="pagination justify-content-center">
                <?php
                $prev = $baseParams;
                $prev['page'] = max(1, $page - 1);
                ?>
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                  <a class="page-link" href="?<?php echo http_build_query($prev); ?>" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                  </a>
                </li>

                <?php
                $displayRange = 5;
                $startPage = max(1, $page - floor($displayRange / 2));
                $endPage = min($totalPages, $startPage + $displayRange - 1);
                if ($endPage - $startPage + 1 < $displayRange) {
                    $startPage = max(1, $endPage - $displayRange + 1);
                }
                for ($i = $startPage; $i <= $endPage; $i++):
                    $p = $baseParams;
                    $p['page'] = $i;
                    ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                      <a class="page-link" href="?<?php echo http_build_query($p); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php
                $next = $baseParams;
                $next['page'] = min($totalPages, $page + 1);
                ?>
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                  <a class="page-link" href="?<?php echo http_build_query($next); ?>" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
            <?php
        }
    } else {
        echo "<p class='text-center text-muted py-3'>No reviews yet.</p>";
    }
} else {
    echo "<p class='text-center text-danger py-3'>Unable to fetch reviews.</p>";
}
?>
                    
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>



      <section id="best-sellers" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Related Products & Services</h2>
       
      </div><!-- End Section Title -->

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
         
          <!-- Product 4 -->
  <?php
$limit = 4; // Number of listings to show
$queryParts = [];
// We'll request a slightly larger page of candidates and filter client-side using the product's context
$queryParts[] = 'items_per_page=' . intval($limit * 3);
$queryParts[] = 'ajax=1';
$url = $siteurl . "script/admin.php?action=listinglists" . (count($queryParts) ? '&' . implode('&', $queryParts) : '');
$data = curl_get_contents($url);
$count = 0;

// collect buckets: subcategory matches, category matches, fallback
$bySub = [];
$byCat = [];
$fallbackListings = [];

if ($data !== false) {
  $listingsRaw = json_decode($data);
  $listings = [];
  if (is_object($listingsRaw) && isset($listingsRaw->data) && is_array($listingsRaw->data)) {
    $listings = $listingsRaw->data;
  } elseif (is_array($listingsRaw)) {
    $listings = $listingsRaw;
  }

  if (!empty($listings)) {
    foreach ($listings as $l) {
      // skip same listing
            $sameListing = (isset($l->id) && $currentInternalId && intval($l->id) === $currentInternalId) || (isset($l->listing_id) && $currentExternalListingId && intval($l->listing_id) === $currentExternalListingId);
      if ($sameListing) continue;

      if (!isset($l->status) || strtolower($l->status) !== 'active') continue;
      $listing_id = $l->listing_id ?? '';
      $featuredImg = !empty($l->featured_image)
                    ? $siteurl . $imagePath . $l->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                    $slug = $l->slug ?? '';
                $listingUrl  = $siteurl . "products/" . $slug;

      $lCats = array_filter(array_map('trim', explode(',', strtolower($l->category_names ?? ''))));
      $lSubs = array_filter(array_map('trim', explode(',', strtolower($l->subcategory_names ?? ''))));

      $matchedSub = !empty($contextSubs) && array_intersect($contextSubs, $lSubs);
      $matchedCat = !$matchedSub && !empty($contextCats) && array_intersect($contextCats, $lCats);

      if ($matchedSub) $bySub[$l->id ?? uniqid('l')] = $l;
      elseif ($matchedCat) $byCat[$l->id ?? uniqid('l')] = $l;
      else $fallbackListings[$l->id ?? uniqid('l')] = $l;
    }

    // Merge with priority and limit to $limit
    $merged = [];
    foreach ([$bySub, $byCat, $fallbackListings] as $pool) {
      foreach ($pool as $id => $item) {
        if (count($merged) >= $limit) break 2;
        if (!isset($merged[$id])) $merged[$id] = $item;
      }
    }

    $listings = array_values($merged);
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
                                  title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>" 
                                  data-product-id="<?php echo $listing_id; ?>"
                              >
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

                            <!--Seller Info -->
                            <div class="mt-3 d-flex align-items-center">
                                <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                                <span class="small text-muted"><?php echo $sellerName; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
    
?>

          <!-- End Product 4 -->

        </div>

      </div>

</section>

<!-- Questions Slider Section -->
<section id="questions-slider" class="section">
   <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Related Question and Answer</h2>
      <p><a href="<?php echo $siteurl; ?>questions-answers.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <?php
    // Fetch and Filter Questions
    $url = $siteurl . "script/admin.php?action=questionlists";
    $data = curl_get_contents($url);
    $questionsList = [];

    if ($data !== false) {
        $questions = json_decode($data);
        if (!empty($questions)) {
            $count = 0;

            // Prefer subcategory matches then category matches
            $matches = [];
            $fallback = [];

            foreach ($questions as $question) {

                // Skip inactive questions (if status field exists)
                if (isset($question->status) && strtolower($question->status) !== 'active') continue;

                // compare on category/subcategory NAMES (product context uses names)
                $qCats = array_filter(array_map('trim', explode(',', strtolower($question->category_names ?? ''))));
                $qSubs = array_filter(array_map('trim', explode(',', strtolower($question->subcategory_names ?? ''))));

                $isMatch = false;
                if (!empty($contextSubs) && array_intersect($contextSubs, $qSubs)) {
                  $isMatch = true; // subcategory name matched
                } elseif (!empty($contextCats) && array_intersect($contextCats, $qCats)) {
                  $isMatch = true; // category name matched
                }

                // collect matches; fallback collects all recent entries to show when no matches
                if ($isMatch) {
                    $matches[] = $question;
                } else {
                    $fallback[] = $question;
                }
            }

            // Choose source: matched questions (prefer) or fallback (latest)
            $sourceQuestions = !empty($matches) ? $matches : array_slice($fallback, 0, 4);
            $count = 0;
            foreach ($sourceQuestions as $question) {
                if ($count >= 4) break;

                $questionId = $question->id;
                $title = $question->title;
                $article = $question->article;
                  $slug = $question->slug ?? '';
                $date = date('M d, Y', strtotime($question->created_at));
                $category = !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : 'Uncategorized';
                $subcategory = !empty($question->subcategory_names) ? trim(explode(',', $question->subcategory_names)[0]) : 'General';

                $anonymous = intval($question->anonymous ?? 0);
                $authorDisplay = ($anonymous === 1)
                    ? "Anonymous"
                    : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: "Unknown User");

                // Limit text preview to 5 words
                $words = explode(' ', strip_tags($article));
                $shortText = implode(' ', array_slice($words, 0, 5));
                $hasMore = count($words) > 5;
                
                $questionsList[] = [
                    'id' => $questionId,
                    'title' => $title,
                    'slug' => $slug,
                    'author' => $authorDisplay,
                    'category' => $category,
                    'date' => $date,
                    'shortText' => $shortText,
                    'hasMore' => $hasMore
                ];
                $count++;
            }
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
          <h2>Related Articles</h2>
          <p><a href="<?php echo $siteurl; ?>blog.php">View All</a></p>
        </div>
      </div><!-- End Section Title -->

  <!-- Blog Grid Container -->
  <div class="container my-5">
    <div class="row g-4">
      <?php
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);
      $limit = 4; // Number of blogs to show (match products)
      $relatedBlogs = [];

      // Use product page context (category/subcategory names)
      $pCats = $contextCats;
      $pSubs = $contextSubs;

      if ($data !== false) {
          $blogs = json_decode($data);
          $bySub = [];
          $byCat = [];
          $fallback = [];

          if (!empty($blogs) && is_array($blogs)) {
              foreach ($blogs as $blog) {
                  if (!isset($blog->status) || strtolower($blog->status) !== 'active') continue;
                  if (!empty($blog->group_id)) continue; // skip group posts
                  $bid = $blog->id ?? null; if (!$bid) continue;

                  $bCats = array_filter(array_map('trim', explode(',', strtolower($blog->category_names ?? ''))));
                  $bSubs = array_filter(array_map('trim', explode(',', strtolower($blog->subcategory_names ?? ''))));

                  $matchedSub = (!empty($pSubs) && array_intersect($pSubs, $bSubs));
                  $matchedCat = (!$matchedSub && !empty($pCats) && array_intersect($pCats, $bCats));

                  if ($matchedSub) $bySub[$bid] = $blog;
                  elseif ($matchedCat) $byCat[$bid] = $blog;
                  else $fallback[$bid] = $blog;
              }

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
              $photo = !empty($blog->photo) ? $siteurl . $imagePath . $blog->photo : $siteurl . "assets/img/user.jpg";
              $blogimage = !empty($blog->featured_image) ? $siteurl . $imagePath . $blog->featured_image : $siteurl . "assets/img/default-blog.jpg";
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


 <!-- Community Group -->
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
        // Fetch groups and select related ones based on product context (subcat -> cat -> fallback)
        $url = $siteurl . "script/admin.php?action=grouplists";
        $groupsData = curl_get_contents($url);

        $groupsToShow = [];
        if ($groupsData !== false) {
          $groups = json_decode($groupsData);
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

            // merge and limit to 4
            $merged = [];
            foreach ([$bySub, $byCat, $fallback] as $pool) {
              foreach ($pool as $id => $item) {
                if (count($merged) >= 4) break 2;
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

    <!-- Inline Advert -->
    <?php
    $placementSlug = 'product-details-inline-ad';
    include "inline-ad.php";
    ?>

<!-- Related Products Section -->
<?php if (isset($listingId) && isset($category)): ?>
<section id="related-products" class="section bg-light">
  <div class="container">
    <h3 class="mb-4">Related Products</h3>
    <div class="row g-4">
      <?php
      // Fetch related products with same category
      $relatedUrl = $siteurl . "script/admin.php?action=listinglists";
      $relatedData = curl_get_contents($relatedUrl);
      
      if ($relatedData !== false) {
          $relatedListings = json_decode($relatedData);
          $relatedCount = 0;
          
          if (!empty($relatedListings)) {
              foreach ($relatedListings as $relL) {
                  // Skip current product and only show active ones
                  if ($relL->listing_id == $listingId || $relL->status != 'active') continue;
                  
                  // Check if categories match
                  $relCategories = $relL->category_names ?? '';
                  if (strpos($relCategories, $category) === false && strpos($category, $relCategories) === false) continue;
                  
                  $relatedCount++;
                  if ($relatedCount > 4) break; // Limit to 4 related products
                  
                  $relTitle = htmlspecialchars($relL->title);
                  $relSlug = htmlspecialchars($relL->slug);
                  $relPrice = intval($relL->price ?? 0);
                  $relPriceDisplay = $sitecurrency . number_format($relPrice);
                  $relImage = !empty($relL->featured_image) 
                      ? $siteurl . $imagePath . $relL->featured_image 
                      : $siteurl . "assets/img/default-product.jpg";
                  $relUrl = $siteurl . "listing-details/" . $relSlug;
                  $relVendor = htmlspecialchars(trim($relL->first_name . ' ' . $relL->last_name));
                  ?>
                  
                  <div class="col-lg-3 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                      <a href="<?php echo $relUrl; ?>">
                        <img src="<?php echo $relImage; ?>" class="card-img-top" alt="<?php echo $relTitle; ?>" style="height: 180px; object-fit: cover;">
                      </a>
                      <div class="card-body">
                        <h5 class="card-title">
                          <a href="<?php echo $relUrl; ?>" class="text-dark text-decoration-none"><?php echo $relTitle; ?></a>
                        </h5>
                        <p class="fw-bold text-primary mb-2"><?php echo $relPriceDisplay; ?></p>
                        <small class="text-secondary">By <?php echo $relVendor; ?></small>
                      </div>
                    </div>
                  </div>
                  
                  <?php
              }
          }
          
          if ($relatedCount == 0) {
              echo '<p class="text-center text-muted">No related products found.</p>';
          }
      }
      ?>
    </div>
  </div>
</section>
<?php endif; ?>

    <?php include "footer.php"; ?>