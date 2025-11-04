
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
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                  </div>
                  <span class="review-text">(127 reviews)</span>
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
            <input type="hidden" id="user_id" value="<?php echo $buyerId ?? ''; ?>">
               
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

         <?php if (!empty($limited_slot)): ?>
              <div class="availability-status">
                <div class="stock-indicator">
                  <i class="bi bi-check-circle-fill"></i>
                  <span class="stock-text">Available</span>
                </div>
                <div class="quantity-left">Only <?php echo $limited_slot; ?> slot remaining</div>
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
                <button class="btn icon-action" title="Add to Wishlist">
                  <i class="bi bi-heart"></i>
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
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ecommerce-product-details-5-customer-reviews" type="button" aria-selected="false" tabindex="-1" role="tab">Reviews (127)</button>
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
                    <div class="reviews-header">
                      <div class="rating-overview">
                        <div class="average-score">
                          <div class="score-display">4.6</div>
                          <div class="score-stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                          </div>
                          <div class="total-reviews">127 customer reviews</div>
                        </div>

                        <div class="rating-distribution">
                          <div class="rating-row">
                            <span class="stars-label">5★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 68%;"></div>
                            </div>
                            <span class="count-label">86</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">4★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 22%;"></div>
                            </div>
                            <span class="count-label">28</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">3★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 6%;"></div>
                            </div>
                            <span class="count-label">8</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">2★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 3%;"></div>
                            </div>
                            <span class="count-label">4</span>
                          </div>
                          <div class="rating-row">
                            <span class="stars-label">1★</span>
                            <div class="progress-container">
                              <div class="progress-fill" style="width: 1%;"></div>
                            </div>
                            <span class="count-label">1</span>
                          </div>
                        </div>
                      </div>

                      <div class="write-review-cta">
                        <h4>Share Your Experience</h4>
                        <p>Help others make informed decisions</p>
                        <button class="btn review-btn">Write Review</button>
                      </div>
                    </div>

                    <div class="customer-reviews-list">
                      <div class="review-card">
                        <div class="reviewer-profile">
                          <img src="assets/img/person/person-f-3.webp" alt="Customer" class="profile-pic">
                          <div class="profile-details">
                            <div class="customer-name">Sarah Martinez</div>
                            <div class="review-meta">
                              <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                              </div>
                              <span class="review-date">March 28, 2024</span>
                            </div>
                          </div>
                        </div>
                        <h5 class="review-headline">Outstanding audio quality and comfort</h5>
                        <div class="review-text">
                          <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam. Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                        </div>
                        <div class="review-actions">
                          <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (12)</button>
                          <button class="action-btn"><i class="bi bi-chat-dots"></i> Reply</button>
                        </div>
                      </div>

                      <div class="review-card">
                        <div class="reviewer-profile">
                          <img src="assets/img/person/person-m-5.webp" alt="Customer" class="profile-pic">
                          <div class="profile-details">
                            <div class="customer-name">David Chen</div>
                            <div class="review-meta">
                              <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                              </div>
                              <span class="review-date">March 15, 2024</span>
                            </div>
                          </div>
                        </div>
                        <h5 class="review-headline">Great value, minor connectivity issues</h5>
                        <div class="review-text">
                          <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Overall satisfied with the purchase.</p>
                        </div>
                        <div class="review-actions">
                          <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (8)</button>
                          <button class="action-btn"><i class="bi bi-chat-dots"></i> Reply</button>
                        </div>
                      </div>

                      <div class="review-card">
                        <div class="reviewer-profile">
                          <img src="assets/img/person/person-f-7.webp" alt="Customer" class="profile-pic">
                          <div class="profile-details">
                            <div class="customer-name">Emily Rodriguez</div>
                            <div class="review-meta">
                              <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                              </div>
                              <span class="review-date">February 22, 2024</span>
                            </div>
                          </div>
                        </div>
                        <h5 class="review-headline">Perfect for work-from-home setup</h5>
                        <div class="review-text">
                          <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.</p>
                        </div>
                        <div class="review-actions">
                          <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (15)</button>
                          <button class="action-btn"><i class="bi bi-chat-dots"></i> Reply</button>
                        </div>
                      </div>

                      <div class="load-more-section">
                        <button class="btn load-more-reviews">Show More Reviews</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
    <?php include "footer.php"; ?>