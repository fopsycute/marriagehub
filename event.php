    <?php include "header.php"; ?>
<?php 
$sitelink = $siteurl . "script/";
if (isset($_GET['slug'])) {

    $slug = $_GET['slug'];
    $apiUrl = $siteurl . "script/admin.php?action=fetcheventslug&slug=" . urlencode($slug);

    $data = curl_get_contents($apiUrl);
    $listing = json_decode($data, true); // single associative array

    if (!empty($listing)) {

        // BASIC FIELDS
        $title       = htmlspecialchars($listing['title'] ?? '');
        $event_id    = $listing['event_id'];
        $description = $listing['description'] ?? '';
        $pricingType = strtolower($listing['pricing_type'] ?? '');
        $category    = htmlspecialchars($listing['category_names'] ?? '');
        $subcategory = htmlspecialchars($listing['subcategory_names'] ?? '');
        $status      = strtolower($listing['status'] ?? '');
        $event_type  = htmlspecialchars($listing['event_type_name'] ?? '');
        $format      = htmlspecialchars($listing['delivery_format'] ?? '');
        $target_audience = htmlspecialchars($listing['target_audience'] ?? '');

        // SHORT BIO
        $shortBio = limitWords(strip_tags($description), 20);
        $isTruncated = (str_word_count(strip_tags($description)) > 20);

        // SELLER
        $sellerName  = trim(($listing['first_name'] ?? '') . ' ' . ($listing['last_name'] ?? ''));
        $sellerPhoto = !empty($listing['photo'])
            ? $siteurl . $imagePath . $listing['photo']
            : $siteurl . "assets/img/default-user.jpg";

        // IMAGES
        $images = !empty($listing['all_images']) ? explode(',', $listing['all_images']) : [];

        // EVENT DATES
$eventDates = [];
$now = date('Y-m-d H:i:s');

if (!empty($listing['all_event_dates_times'])) {

    foreach (explode(',', $listing['all_event_dates_times']) as $d) {

        list($date, $start, $end) = explode('|', $d);

        // Convert to full datetime
        $event_end = $date . ' ' . $end;

        // Only add it if the date/time has NOT passed
        if ($event_end >= $now) {
            $eventDates[] = [
                'date'  => $date,
                'start' => $start,
                'end'   => $end
            ];
        }
    }
}


    $event_passed = false;

if (!empty($eventDates)) {
    $now = date('Y-m-d H:i:s');
    $all_past = true;

    foreach ($eventDates as $ed) {
        // Correct field names
        if (!empty($ed['date']) && !empty($ed['end'])) {

            $event_end = $ed['date'] . ' ' . $ed['end'];

            // If any event end time is still upcoming, event has NOT passed
            if ($event_end >= $now) {
                $all_past = false;
                break;
            }
        }
    }

    $event_passed = $all_past;
}


        // TICKETS
        $tickets = [];
        if (!empty($listing['tickets'])) {
            foreach (explode(',', $listing['tickets']) as $i => $t) {
                list($name, $price, $seat,$benefits,$ticket_id) = explode('|', $t);
              
                $tickets[] = [
                    'ticket_name' => $name,
                    'price'       => $price,
                    'seatremain'  => $seat,
                    'id'          => $ticket_id,
                    'benefits'    => $benefits
                ];
            }
        }

  $user_purchased = hasUserPurchased($con, $buyerId, $event_id, $siteprefix);
  $is_in_cart = isInCart($con, $order_id, $event_id, $pricingType, $siteprefix);
        // Redirect inactive
        if ($status !== 'active') {
            header("Location: index.php");
            exit;
        }
    }
}
?>

   <?php
          $stats_url = $sitelink . "user.php?action=fetcheventReviewStats&event_id=" . $event_id;
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
          <div class="col-lg-6 aos-init aos-animate" data-aos="zoom-in" data-aos-delay="150">
            <div class="justify-content-between d-flex align-items-start mb-3">
             <div class="product-share">
                    <button class="share-btn" id="webShareBtn" aria-label="Share product">
                      <i class="bi bi-share"></i>
                    </button>
                    <div class="share-dropdown">
                      <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($siteurl . $slug); ?>&text=<?php echo urlencode($title); ?>"
                        target="_blank" rel="noopener" title="Share on Twitter">
                        <i class="bi bi-twitter"></i>
                      </a>

                      <!-- Facebook -->
                      <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($siteurl . $slug); ?>"
                        target="_blank" rel="noopener" title="Share on Facebook">
                        <i class="bi bi-facebook"></i>
                      </a>

                      <!-- LinkedIn -->
                      <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($siteurl . $slug); ?>&title=<?php echo urlencode($title); ?>"
                        target="_blank" rel="noopener" title="Share on LinkedIn">
                        <i class="bi bi-linkedin"></i>
                      </a>

                    </div>
                     </div>
                     <div class="product-id">
                    <span class="me-1">Event ID: <?php echo $event_id; ?></span>
                      <span class="badge-category"><?php echo $event_type; ?></span>
                        </div>
                    </div>
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
          <div class="col-lg-6 aos-init aos-animate" data-aos="fade-left" data-aos-delay="200">
            <div class="product-details">
              <div class="product-badge-container">
                <span class="badge-category m-1"><?php echo $category; ?></span>
                <span class="badge-category m-1"><?php echo $subcategory; ?></span>
                <div class="rating-group">
                  <div class="stars">
                   
                 
                  </div>
                  <span class="review-text"></span>
                </div>
              </div>

              <h1 class="product-name"><?php echo $title; ?></h1>

              <div class="pricing-section">
                <div class="price-display">
                  <span class="sale-price">
               <?php
                if ($pricingType === 'paid') {
                  echo '<span id="paidPrice"></span>'; // Empty placeholder for JS
                } elseif ($pricingType === 'free') {
                echo 'Free';
                } elseif ($pricingType === 'donation') {
                echo 'Donate';
                }
            ?>
            </span>


                  </span>
                 
                </div>
              </div>

        <div class="product-description">

<<<<<<< HEAD
           <div class="mt-3 bio-text">
  
  <!-- Short Bio: OK to stay as span -->
  <span class="bio-short"><?php echo $shortBio; ?></span>

  <?php if ($isTruncated): ?>
    <!-- Full Bio: MUST be a <div> because TinyMCE content has <p>, <br>, etc. -->
    <div class="bio-full d-none">
        <?php echo $description; ?>
    </div>

    <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
  <?php endif; ?>

</div>
=======
        <p class="bio-text">
          <span class="bio-short"><?php echo $shortBio; ?></span>
          <?php if ($isTruncated): ?>
            <span class="bio-full d-none"><?php echo $description; ?></span>
            <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
          <?php endif; ?>
        </p>
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
              </div>
  <?php if ($pricingType === 'paid'): ?>
              <div class="availability-status">
                <!-- SELECTABLE TICKET BUTTONS -->

    <label class="form-label"><strong>Select Tickets:</strong></label>

    <!-- ✅ Hidden input to store currency -->
    <input type="hidden" id="siteCurrency" value="<?php echo htmlspecialchars($sitecurrency); ?>">

    <div class="ticket-options d-block mb-3">
        <?php foreach ($tickets as $i => $t): 
            $ticket_id =$t['id'];  
            $ticket_name = htmlspecialchars($t['ticket_name']);
            $benefits    = htmlspecialchars($t['benefits'] ?? '');
            $amount      = floatval($t['price']);
            $seatremain  = intval($t['seatremain']);
            $isSoldOut   = $seatremain <= 0;
        ?>
        <div class="mb-2 ticket-item w-100">
            <input
      type="checkbox"
      class="btn-check variation-checkbox"
      id="ticket<?php echo $ticket_id; ?>"
      name="variation_ids[]"
      value="<?php echo $ticket_id; ?>"
      data-price="<?php echo $amount; ?>"
      onchange="toggleTicketInfo(this)"
      <?php if ($isSoldOut) echo 'disabled'; ?>
      autocomplete="off">


            <label
                class="btn btn-outline-<?php echo $isSoldOut ? 'secondary' : 'primary'; ?> w-100 text-start px-3 py-2"
                for="ticket<?php echo $ticket_id; ?>"
                <?php if ($isSoldOut): ?>title="Ticket Sold Out"<?php endif; ?>
            >
                <?php echo $ticket_name; ?> (<?php echo $sitecurrency . number_format($amount, 2); ?>)
                <?php if ($isSoldOut): ?>
                    <span class="text-danger fw-bold"> - Sold Out</span>
                <?php endif; ?>
            </label>

            <!-- ✅ Hidden data for JS/frontend -->
            <input type="hidden" id="seat-<?php echo $ticket_id; ?>" value="<?php echo $seatremain; ?>">
            <input type="hidden" id="benefits-<?php echo $ticket_id; ?>" value="<?php echo $benefits; ?>">
            <input type="hidden" id="price-<?php echo $ticket_id; ?>" value="<?php echo $amount; ?>">
        </div>
        <?php endforeach; ?>
    </div>
          </div> 
<?php endif; ?>

  
<div class="purchase-section">
<div class="action-buttons">
    <?php if ($event_passed): ?>
        <a class="btn btn-danger">Event Passed</a>

    <?php elseif ($user_purchased): ?>
        <a href="<?php echo $siteurl; ?>dashboard.php" class="btn btn-success">
            <i class="bi bi-person"></i> Go to Dashboard
        </a>

    <?php else: ?>
        <?php if ($pricingType === 'paid' || $pricingType === 'free'): ?>
            <?php 
            // Check if this event/training is already in cart
            $is_in_cart = isInCart($con, $order_id ?? null, $event_id, $pricingType, $siteprefix);
            ?>
            <?php if ($is_in_cart): ?>
                <a href="<?php echo $siteurl; ?>cart.php" class="btn btn-primary add-to-cart-btn">
                    <i class="bi bi-cart-check"></i> View Cart
                </a>
            <?php else: ?>
               <input type="hidden" name="pricing" id="pricing" value="<?php echo $pricingType; ?>">
              <input type="hidden" name="event_id" id="current_event_id" value="<?php echo $event_id; ?>">
                <button class="btn primary-action" id="addtoCart">
                    <i class="bi bi-bag-plus"></i> Add to Cart
                </button>
            <?php endif; ?>

        <?php elseif ($pricingType === 'donation'): ?>
            <button class="btn btn-primary donate-btn" type="button"
                id="donateBtn"
                data-event-id="<?php echo $event_id; ?>"
                data-orders_id="<?php echo uniqid('OD'); ?>">
                <i class="bi bi-cash-coin"></i> Donate
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>
</div>

<div class="other-status">

<?php
$details_rows = "";

// 🔹 EVENT SCHEDULE (only upcoming dates already filtered)
if (!empty($eventDates)) {
    foreach ($eventDates as $d) {
        $dateFormatted = date("l, F j, Y", strtotime($d['date']));
        $timeFormatted = date("g:i A", strtotime($d['start'])) . " - " . date("g:i A", strtotime($d['end']));
        
        $details_rows .= "
            <tr>
                <td><strong>Event Date:</strong></td>
                <td>{$dateFormatted}</td>
            </tr>
            <tr>
                <td><strong>Time:</strong></td>
                <td>{$timeFormatted}</td>
            </tr>
        ";
    }
}

// 🔹 TARGET AUDIENCE
if (!empty($target_audience)) {
    $details_rows .= "
        <tr>
            <td><strong>Target Audience:</strong></td>
            <td>{$target_audience}</td>
        </tr>
    ";
}

// 🔹 DELIVERY FORMAT
$delivery_details = "";

if ($format === 'physical') {
    $fields = [
        'address' => 'Address',
        'state' => 'State',
        'lga' => 'LGA',
        'country' => 'Country'
    ];
    foreach ($fields as $col => $label) {
        if (!empty($listing[$col])) {
            $delivery_details .= "
                <tr>
                    <td><strong>{$label}:</strong></td>
                    <td>" . htmlspecialchars($listing[$col]) . "</td>
                </tr>
            ";
        }
    }

} elseif ($format === 'hybrid') {
    $fields = [
        'hybrid_physical_address' => 'Physical Address',
        'hybrid_state' => 'State',
        'hybrid_lga' => 'LGA',
        'hybrid_country' => 'Country',
        'hybrid_foreign_address' => 'Foreign Address'
    ];
    foreach ($fields as $col => $label) {
        if (!empty($listing[$col])) {
            $delivery_details .= "
                <tr>
                    <td><strong>{$label}:</strong></td>
                    <td>" . htmlspecialchars($listing[$col]) . "</td>
                </tr>
            ";
        }
    }

} elseif ($format === 'online') {
    $delivery_details .= "
        <tr>
            <td><strong>Format:</strong></td>
            <td>Online (Link will be sent after registration)</td>
        </tr>
    ";
}

// ADD DELIVERY FORMAT TO TABLE
$details_rows .= $delivery_details;
?>

<!-- FINAL TABLE OUTPUT -->
<?php if (!empty($details_rows)) : ?>
    <h6>Event Details</h6>
    <table class="table table-bordered table-sm">
        <tbody>
            <?= $details_rows ?>
        </tbody>
    </table>
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
<<<<<<< HEAD
                             <div class="mt-3 bio-text">
  
            <!-- Short Bio: OK to stay as span -->
            <span class="bio-short"><?php echo $shortBio; ?></span>

            <?php if ($isTruncated): ?>
              <!-- Full Bio: MUST be a <div> because TinyMCE content has <p>, <br>, etc. -->
              <div class="bio-full d-none">
                  <?php echo $description; ?>
              </div>

              <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
            <?php endif; ?>

</div>
=======
                           <p class="bio-text">
                    <span class="bio-short"><?php echo $shortBio; ?></span>
                    <?php if ($isTruncated): ?>
                      <span class="bio-full d-none"><?php echo $description; ?></span>
                      <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
                    <?php endif; ?>
                  </p> 
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
                    </div>
                  </div>
                </div>
                 </div>
                </div>
           

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="ecommerce-product-details-5-customer-reviews" role="tabpanel">
                  <div class="reviews-content">

                   <div class="comment-form section" id="write_review">
                  <div class="container">
                    <?php if ($activeLog == 1) { ?>
                    <form id="posteventreview" method="POST">
                      <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                      <h4>Give Review</h4>
                      <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                      <input name="event_id" type="hidden" class="form-control" value="<?php echo $event_id; ?>">
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
                          <input type="hidden" value="post_eventreview" name="action">
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

$comments_url = $sitelink . "user.php?action=eventcommentsdata&event_id=" . $event_id;

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
            if (!isset($baseParams['event_id']) && !empty($event_id)) {
                $baseParams['event_id'] = $event_id;
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

    <?php include "footer.php"; ?>