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
$facebook     = $vendor['facebook'] ?? '';
$shareUrl     = $siteurl . "vendor-profile.php?slug=" . urlencode($slug);

?>

<main class="main">
    <section id="vendor-profile" class="instructor-profile section">
        <div class="container mt-4 mb-5">

            <!-- HERO SECTION -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="instructor-hero-banner position-relative p-4 rounded shadow-sm bg-light">
                        <div class="hero-background position-absolute top-0 start-0 w-100 h-100 rounded" 
                             style="background: url('<?php echo $photo; ?>') center/cover no-repeat; filter: blur(30px); opacity: 0.3;"></div>

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
                                    <button class="btn btn-success" id="shareProfileBtn"><i class="bi bi-share"></i> Share Profile</button>
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
                                                            $listingUrl  = $siteurl . "products.php?slug=" . $slug;

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
                                                <a href="<?php echo $siteurl . 'vendor-products.php?slug=' . urlencode($vendorSlug); ?>" class="btn btn-outline-primary">View All Vendor Products</a>
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
                                                            $listingUrl  = $siteurl . "products.php?slug=" . $slug;

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
                                                <a href="<?php echo $siteurl . 'vendor-services.php?slug=' . urlencode($vendorSlug); ?>" class="btn btn-outline-primary">View All Vendor Services</a>
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
</main>

<?php include "footer.php"; ?>
