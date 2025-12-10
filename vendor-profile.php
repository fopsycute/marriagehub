<?php
include "header.php"; // Must include $siteurl, $imagePath, $sitecurrency, and curl_get_contents()

// Check for slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    echo "<div class='alert alert-info text-center mt-5'>No vendor slug provided.</div>";
    include "footer.php";
    exit;
}



$slug = $_GET['slug'];
$vendorSlug = $slug;
$sitelink = $siteurl . "script/";
$apiUrl = $sitelink . "admin.php?action=adduserviews&slug=" . $slug;
$response = curl_get_contents($apiUrl);

$vendorApiUrl = $siteurl . "script/admin.php?action=vendorslug&slug=" . urlencode($slug);



// Fetch vendor details
$vendorData = curl_get_contents($vendorApiUrl);

if ($vendorData === false) {
    echo "<div class='alert alert-danger text-center mt-5'>Error fetching vendor data. Please try again later.</div>";
    include "footer.php";
    exit;
}

$vendordetails = json_decode($vendorData, true);

if (empty($vendordetails)) {
    echo "<div class='alert alert-warning text-center mt-5'>No vendor found for the provided slug.</div>";
    include "footer.php";
    exit;
}

// Single vendor structure
$vendor = isset($vendordetails[0]) ? $vendordetails[0] : $vendordetails;

// Vendor info
$vendorId     = intval($vendor['id'] ?? 0);
$firstName    = trim($vendor['first_name'] ?? '');
$lastName     = trim($vendor['last_name'] ?? '');
$middleName   = trim($vendor['middle_name'] ?? '');
$fullName     = htmlspecialchars(trim("$firstName $lastName $middleName"));
$photo        = !empty($vendor['photo']) ? $siteurl . $imagePath . $vendor['photo'] : $siteurl . "assets/img/user.jpg";
$bio          = $vendor['bio'] ?? $vendor['about'] ?? '';
$business     = $vendor['business_name'] ?? '';
$phone        = $vendor['phone'] ?? '';
$email        = $vendor['email'] ?? '';
$website      = $vendor['website'] ?? '';
$state        = $vendor['state_residence'] ?? '';
$availability = $vendor['availability'] ?? '';
$coverage     = $vendor['coverage'] ?? '';
$category     = htmlspecialchars($vendor['category_names'] ?? '');
$subcategory  = htmlspecialchars($vendor['subcategory_names'] ?? '');
$linkedin     = $vendor['linkedin'] ?? '';
$twitter      = $vendor['twitter'] ?? '';
$instagram    = $vendor['instagram'] ?? '';
$total_articles = $vendor['total_articles'] ?? '';
$total_questions = $vendor['total_questions'] ?? '';
$total_answers = $vendor['total_answers'] ?? '';
$best_answers = $vendor['best_answers'] ?? '';
$facebook     = $vendor['facebook'] ?? '';
$views        = intval($vendor['views'] ?? 0);
$shareUrl     = $siteurl . "vendor-profile/" . urlencode($slug);

?>
<?php
// Check if logged-in user follows the profile
$followed = isFollowing($buyerId, $vendorId);

// Get follower/following count
$followerCount = getFollowerCount($vendorId);
$followingCount = getFollowingCount($vendorId);
?>
<main class="main">
    <section id="vendor-profile" class="instructor-profile section">
        <div class="container mt-4 mb-5">

            <!-- HERO SECTION -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="instructor-hero-banner position-relative p-4 rounded shadow-sm bg-light">
                        <div class="hero-background position-absolute top-0 start-0 w-100 h-100 rounded" 
                             style="background: url('<?php echo $photo; ?>') center/cover no-repeat; filter: blur(30px); opacity: 0.3;">
                                <div class="hero-overlay"></div>
                                </div>
                        <div class="hero-content position-relative d-flex flex-wrap align-items-center gap-4">
                            <div class="instructor-avatar">
                                <img src="<?php echo $photo; ?>" alt="<?php echo $fullName; ?>" 
                                     class="img-fluid rounded-circle shadow" style="width:140px;height:140px;object-fit:cover;">
                            </div>
                            <div class="instructor-info text-dark">
                                <h2 class="fw-bold"><?php echo $fullName; ?></h2>
                                <p class="text-muted mb-2"><?php echo $category; ?><?php echo $subcategory ? ' | ' . $subcategory : ''; ?></p>

                                <?php if (!empty($business) || !empty($state) || !empty($availability)): ?>
                                    <div class="small mb-3">
                                        <?php if (!empty($business)): ?><div><strong>Business:</strong> <?php echo htmlspecialchars($business); ?></div><?php endif; ?>
                                        <?php if (!empty($state)): ?><div><strong>Location:</strong> <?php echo htmlspecialchars($state); ?></div><?php endif; ?>
                                        <?php if (!empty($availability)): ?><div><strong>Availability:</strong> <?php echo htmlspecialchars($availability); ?></div><?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                 <button id="followBtn" 
                                        data-author-id="<?php echo $vendorId; ?>" 
                                        class="btn <?php echo $followed ? 'btn-secondary' : 'btn-primary'; ?>">
                                            <?php echo $followed ? 'Unfollow' : 'Follow'; ?>
                                        </button>
                                         <p><?php echo $followerCount; ?> Followers | <?php echo $followingCount; ?> Following</p>   
                                        <p><strong>Profile Views:</strong> <?=$views?></p>
                                        </div>

                        <div class="profile-stats d-flex gap-3 mt-2">
                                <p><strong>Articles:</strong> <?=$total_articles?></p>
                                <p><strong>Questions Asked:</strong> <?=$total_questions?></p>
                                <p><strong>Answers Given:</strong> <?=$total_answers?></p>
                                <p><strong>Best Answers:</strong> <?=$best_answers?></p>
                            </div>

                                <div class="contact-actions mt-2 d-flex flex-wrap align-items-center gap-2">
                                    <?php if (!empty($phone)): ?>
                                        <a href="tel:<?php echo htmlspecialchars($phone); ?>" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
                                    <?php endif; ?>
                                    <?php if (!empty($email)): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="btn btn-outline-secondary"><i class="bi bi-envelope"></i> Contact</a>
                                    <?php endif; ?>
                                    <?php if (!empty($website)): ?>
                                        <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="btn btn-primary"><i class="bi bi-globe"></i> Visit Website</a>
                                    <?php endif; ?>
                                    <button class="btn btn-success"   id="webShareBtn" title="Share this post" data-title="<?php echo $fullName; ?>" data-url="<?php echo htmlspecialchars($shareUrl); ?>"><i class="bi bi-share"></i> Share Profile</button>
                                     <?php if ($activeLog == 1): ?>
                                    <a type="button" class="btn btn-danger m-1" data-bs-toggle="modal" data-bs-target="#reportuserModal">
                                        <i class="bi bi-flag"></i> Report
                                    </a>

                                    <?php else: ?>
                                    <button class="btn btn-secondary m-1" disabled>
                                        <i class="bi bi-flag"></i>Sign in to Report
                                    </button>
                                    <?php endif; ?>
                               
                                </div>

                                <!-- Hidden for JS -->
                                <input type="hidden" id="vendorName" value="<?php echo $fullName; ?>">
                                <input type="hidden" id="shareUrl" value="<?php echo $shareUrl; ?>">

                                <!-- Social Media -->
                                <div class="social-media mt-3">
                                    <?php if (!empty($linkedin)): ?><a href="<?php echo $linkedin; ?>" target="_blank" class="me-2 text-dark fs-5"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                                    <?php if (!empty($twitter)): ?><a href="<?php echo $twitter; ?>" target="_blank" class="me-2 text-dark fs-5"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                                    <?php if (!empty($instagram)): ?><a href="<?php echo $instagram; ?>" target="_blank" class="me-2 text-dark fs-5"><i class="bi bi-instagram"></i></a><?php endif; ?>
                                    <?php if (!empty($facebook)): ?><a href="<?php echo $facebook; ?>" target="_blank" class="me-2 text-dark fs-5"><i class="bi bi-facebook"></i></a><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABS: Products / Services -->
            <div class="row gy-5 mt-4">
                <div class="col-lg-12">
                    <div class="content-tabs" data-aos="fade-right" data-aos-delay="300">
                        <ul class="nav nav-tabs custom-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#vendor-products" type="button" role="tab">
                                    <i class="bi bi-box-seam"></i> Products
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vendor-services" type="button" role="tab">
                                    <i class="bi bi-briefcase"></i> Services
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content custom-tab-content">

                            <!-- PRODUCTS TAB -->
                            <div class="tab-pane fade show active" id="vendor-products" role="tabpanel">
                                <section class="best-sellers section">
                                    <div class="container section-title" data-aos="fade-up">
                                        <h2>Products</h2>
                                        <p>Explore <?php echo $fullName; ?>'s top products</p>
                                    </div>

                                    <div class="container" data-aos="fade-up" data-aos-delay="100">
                                        <div class="row g-5">
                                            <?php
                                            $listingsData = curl_get_contents($siteurl . "script/admin.php?action=listinglists");
                                            $limit = 4; $count = 0;

                                            if ($listingsData !== false) {
                                                $listings = json_decode($listingsData);
                                                if (!empty($listings)) {
                                                    foreach ($listings as $listing) {
                                                        if (isset($listing->status) && strtolower($listing->status) === 'active' && strtolower($listing->type) === 'product' && $listing->user_id == $vendorId) {
                                                            $count++;
                                                            if ($count > $limit) break;

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
                                                            $listingUrl  = $siteurl . "products/" . $slug;

                                                            $displayPrice = 'Contact for price';
                                                            if ($pricingType === 'Starting Price' && !empty($price)) $displayPrice = $sitecurrency . number_format($price, 2);
                                                            elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) $displayPrice = $sitecurrency . number_format($priceMin, 2) . ' - ' . $sitecurrency . number_format($priceMax, 2);
                                                            ?>
                                                            <div class="col-lg-3 col-md-6 col-6">
                                                                <div class="product-item">
                                                                    <div class="product-image">
                                                                        <div class="product-badge trending-badge"><?php echo $category; ?></div>
                                                                        <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                                                                    </div>
                                                                    <div class="product-info">
                                                                        <div class="product-category"><?php echo $category; ?></div>
                                                                        <h4 class="product-name"><a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a></h4>
                                                                        <div class="product-price"><?php echo $displayPrice; ?></div>
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

                                        <?php if ($count >= 4): ?>
                                            <div class="text-center mt-4">
                                                <a href="<?php echo $siteurl . 'vendor-products/' . urlencode($vendorSlug); ?>" class="btn btn-outline-primary">View All Vendor Products</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </section>
                            </div>

                            <!-- SERVICES TAB -->
                            <div class="tab-pane fade" id="vendor-services" role="tabpanel">
                                <section class="best-sellers section">
                                    <div class="container section-title" data-aos="fade-up">
                                        <h2>Services</h2>
                                        <p>Explore <?php echo $fullName; ?>'s professional services</p>
                                    </div>

                                    <div class="container" data-aos="fade-up" data-aos-delay="100">
                                        <div class="row g-5">
                                            <?php
                                            $limit = 4; $count = 0;
                                            if ($listingsData !== false) {
                                                $listings = json_decode($listingsData);
                                                if (!empty($listings)) {
                                                    foreach ($listings as $listing) {
                                                        if (isset($listing->status) && strtolower($listing->status) === 'active' && strtolower($listing->type) === 'service' && $listing->user_id == $vendorId) {
                                                            $count++;
                                                            if ($count > $limit) break;

                                                            $listingId   = $listing->id;
                                                            $title       = htmlspecialchars($listing->title);
                                                            $slug        = htmlspecialchars($listing->slug ?? '');
                                                            $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                                                            $price       = htmlspecialchars($listing->price ?? '');
                                                            $featuredImg = !empty($listing->featured_image) ? $siteurl . $imagePath . $listing->featured_image : $siteurl . "assets/img/default-service.jpg";
                                                            $listingUrl  = $siteurl . "products/" . $slug;

                                                            $displayPrice = 'Contact for price';
                                                            if ($pricingType === 'Starting Price' && !empty($price)) $displayPrice = $sitecurrency . number_format($price, 2);
                                                            ?>
                                                            <div class="col-lg-3 col-md-6 col-6">
                                                                <div class="product-item">
                                                                    <div class="product-image">
                                                                        <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                                                                    </div>
                                                                    <div class="product-info">
                                                                        <h4 class="product-name"><a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a></h4>
                                                                        <div class="product-price"><?php echo $displayPrice; ?></div>
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

                                        <?php if ($count >= 4): ?>
                                            <div class="text-center mt-4">
                                                <a href="<?php echo $siteurl . 'vendor-services/' . urlencode($vendorSlug); ?>" class="btn btn-outline-primary">View All Vendor Services</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </section>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Report Product Modal -->
<div class="modal fade" id="reportuserModal" tabindex="-1" aria-labelledby="reportuserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="reportblogForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="reportuserModalLabel">
            Report Vendor: <?php echo $fullName; ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="report_message" class="text-center mb-2"></div>

          <input type="hidden" name="action" value="report_user">
          <!-- Reporter (logged in user) -->
          <input type="hidden" name="reporter_id" value="<?php echo htmlspecialchars($buyerId); ?>">

          <!-- User being reported (author) -->
          <input type="hidden" name="reported_user_id" value="<?php echo htmlspecialchars($vendorId); ?>">


          <div class="mb-2">
            <label for="reason" class="form-label">Reason for Reporting</label>
            <select class="form-select" name="reason"  id="reason" required onchange="toggleCustomReason(this.value)">
              <option value="">Select Reason</option>
              <option value="Harassment or Abusive Behavior">Harassment or Abusive Behavior</option>
              <option value="Spam or Misleading Information">Spam or Misleading Information</option>
              <option value="Inappropriate or Offensive Profile">Inappropriate or Offensive Profile</option>
              <option value="Impersonation or Fake Account">Impersonation or Fake Account</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="mb-2" id="customReasonContainer" style="display:none;">
            <label for="custom_reason" class="form-label">Provide Details</label>
            <textarea class="form-control" name="custom_reason" id="custom_reason" rows="3" placeholder="Describe the issue..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="submit_report" id="submitReport" class="btn btn-danger">Submit Report</button>
        </div>

      </form>
    </div>
  </div>
</div>


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
// Prefer a selected subcategory (more specific) then category to find related listings
// If the page doesn't provide selected IDs, use the vendor's subcategory/category ids
if (!empty($selectedSubcategories)) {
    $queryParts[] = 'subcategory_id=' . intval($selectedSubcategories[0]);
} elseif (!empty($vendor['subcategory_id'])) {
    // vendor may store comma-separated ids — use the first one for related results
    $vSubsIds = array_filter(array_map('trim', explode(',', $vendor['subcategory_id'])));
    if (!empty($vSubsIds)) $queryParts[] = 'subcategory_id=' . intval($vSubsIds[0]);
} elseif (!empty($selectedCategories)) {
    $queryParts[] = 'category_id=' . intval($selectedCategories[0]);
} elseif (!empty($vendor['category_id'])) {
    $vCatsIds = array_filter(array_map('trim', explode(',', $vendor['category_id'])));
    if (!empty($vCatsIds)) $queryParts[] = 'category_id=' . intval($vCatsIds[0]);
}
// request structured response & limit
$queryParts[] = 'items_per_page=' . intval($limit);
$queryParts[] = 'ajax=1';
$url = $siteurl . "script/admin.php?action=listinglists" . (count($queryParts) ? '&' . implode('&', $queryParts) : '');
$data = curl_get_contents($url);
$count = 0;

if ($data !== false) {
    $listingsRaw = json_decode($data);
    // listinglists returns structured object when ajax=1: { total, data }
    $listings = [];
    if (is_object($listingsRaw) && isset($listingsRaw->data) && is_array($listingsRaw->data)) {
        $listings = $listingsRaw->data;
    } elseif (is_array($listingsRaw)) {
        $listings = $listingsRaw;
    }

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active') {
                $count++;
                if ($count > $limit) break;

                // 🧩 Extract data
                $listingId   = $listing->id;
                $listing_id = $listing->listing_id;
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
              $isWishlisted = false; // Always define first

        if (!empty($buyerId)) {
            // ✅ Use $siteurl instead of undefined $sitelink
            $apiCheckUrl = $siteurl . "script/user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
            $wishlistData = curl_get_contents($apiCheckUrl);

            if ($wishlistData !== false) {
                $wishlistResult = json_decode($wishlistData, true);

                // ✅ Make it flexible to match possible response structures
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
            }
        }
    }
}
?>

          <!-- End Product 4 -->

        </div>

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
      $limit = 3; // Number of blogs to show
      $relatedBlogs = [];

      // vendor context
      $vCats = array_filter(array_map('trim', explode(',', strtolower($vendor['category_names'] ?? ''))));
      $vSubs = array_filter(array_map('trim', explode(',', strtolower($vendor['subcategory_names'] ?? ''))));

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

                  $matchedSub = (!empty($vSubs) && array_intersect($vSubs, $bSubs));
                  $matchedCat = (!$matchedSub && !empty($vCats) && array_intersect($vCats, $bCats));

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



</main>

<?php include "footer.php"; ?>
