<?php require_once __DIR__ . '/script/connect.php'; ?>

<?php

$requireLogin = $requireLogin ?? false; // page-specific
$activeLog = 0; // 0 = guest, 1 = logged-in buyer
 $buyerId = 0;
$userType = "buyer"; // specify this header is for buyer

// Check if buyer is logged in via cookie
$authentication = $_COOKIE['user_auth'] ?? '';


if (!empty($_COOKIE['admin_auth'])) {
  $dashboardUrl = $siteurl . 'admin/index.php';
} elseif (!empty($_COOKIE['therapist_auth'])) {
  $dashboardUrl = $siteurl . 'therapist/index.php';
} 
elseif (!empty($_COOKIE['vendor_auth'])) {
  $dashboardUrl = $siteurl . 'vendor/index.php';
}
elseif (!empty($_COOKIE['user_auth'])) {
  $dashboardUrl = $siteurl . 'dashboard.php';
} else {
  $dashboardUrl = $siteurl . 'login';
}
if ($authentication) {
    // Fetch buyer data from API or DB
    $url =  $siteurl . "script/user?action=buyerdata&buyer=$authentication";
    $data = curl_get_contents($url);

    if ($data !== false) {
        $buyerData = json_decode($data);
        if ($buyerData && empty($buyerData->error)) {
            $buyerId = $buyerData->id ?? '';
            $buyerfirstName = $buyerData->first_name;
            $logo = $buyerData->photo;
            $buyerName = $buyerData->first_name . " " . $buyerData->last_name;
            $buyerEmail = $buyerData->email ?? '';
            $buyerStatus = $buyerData->status ?? 'inactive';
            $bio = $buyerData->bio ?? '';
            $buyerVerified = $buyerData->is_verified ?? 0;
            $first_name     = $buyerData->first_name ?? '';
            $middle_name    = $buyerData->middle_name ?? '';
            $last_name      = $buyerData->last_name ?? '';
            $title          = $buyerData->title ?? '';
            $status         = $buyerData->status ?? '';
            $wallet         = $buyerData->wallet ?? 0;
            $dob            = $buyerData->dob ?? '';
            $gender         = $buyerData->gender ?? '';
            $nationality    = $buyerData->nationality ?? '';
            $languages      = $buyerData->languages ?? '';
            $total_articles = $buyerData->total_articles ?? 0;
            $total_questions = $buyerData->total_questions ?? 0;
            $total_answers  = $buyerData->total_answers ?? 0;
            $best_answers   = $buyerData->best_answers ?? 0;
            $phone          = $buyerData->phone ?? '';
            $website        = $buyerData->website ?? '';
            $email          = $buyerData->email ?? '';
            $state_residence = $buyerData->state_residence ?? '';
            $address        = $buyerData->address ?? '';
            $facebook       = $buyerData->facebook ?? '';
            $twitter        = $buyerData->twitter ?? '';
            $instagram      = $buyerData->instagram ?? '';
            $linkedin       = $buyerData->linkedin ?? '';
            $photo          = $buyerData->photo ?? '';
            $suspend_reason = $buyerData->suspend_reason ?? '';
            $bank_name      = $buyerData->bank_name ?? '';
            $bank_accname   = $buyerData->bank_accname ?? '';
            $bank_number    = $buyerData->bank_number ?? '';

$photo_url = !empty($photo)
  ? $siteurl . "uploads/" . $photo
  : $siteurl . "images/default-avatar.png";

            $activeLog = 1;
        } else {
            // Invalid token or missing data
          
            $activeLog = 0;
        }
    } else {
       
        $activeLog = 0;
    }
}

// Redirect if the page requires login but user is not logged in
if ($requireLogin && !$authentication) {
    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
    header("Location: $siteurl" . "login.php");
    exit;
}

// If logged in but account not verified, redirect to verification notice page
/*
if ($activeLog === 1 && isset($buyerVerified) && !$buyerVerified) {
    header("Location: ../verify-account.php");
    exit;
}

*/

// Now you can use $activeLog to show/hide sections inside your page HTML
?>

<?php include "order-handler.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Marriage Hub Ng</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" rel="icon">
  <link href="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!---
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
--->
  <!-- Vendor CSS Files -->
  <link href="<?php echo $siteurl; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!---
   <link rel="stylesheet" href="admin/assets/css/plugins.min.css" />
  --->

  <link href="<?php echo $siteurl; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $siteurl; ?>assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $siteurl; ?>assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="<?php echo $siteurl;?>assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <?php include "script/tinymce.php"; ?>
  
  <!-- Custom Font Override -->
  <style>
    * {
      font-family: 'Poppins', sans-serif !important;
    }
    body, p, div, span, a, li, td, th, input, textarea, select, button {
      font-size: 14px;
    }
    small, .small {
      font-size: 12px;
    }
  </style>
  
  <!-- Main CSS File -->
  <link href="<?php echo $siteurl; ?>assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
<<<<<<< HEAD
=======
  <!-- Hidden input for JavaScript -->
  <input type="hidden" value="<?php echo $siteurl; ?>" id="siteurl">
  
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
  <?php 
  if (basename($_SERVER['PHP_SELF']) == 'index.php') {
    include "top-banner.php";
}

  if (basename($_SERVER['PHP_SELF']) == 'therapist.php' || basename($_SERVER['PHP_SELF']) == 'vendor-profile.php' || basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
    include "profile-banner.php";
}
  ?>

<!-- ======= Top Header (visible on large screens and up only) ======= -->
<div class="top-header bg-light py-2 border-bottom d-none d-lg-block">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="top-links d-flex align-items-center gap-3 small">
      <a href="<?php echo $siteurl; ?>register/therapist.php" class="text-dark text-decoration-none">
        <i class="bi bi-person-heart me-1"></i> Get Listed (Therapists) 
      </a>
      <span class="text-secondary">|</span>
      <a href="<?php echo $siteurl; ?>create-tribes-group.php" class="text-dark text-decoration-none">
        <i class="bi bi-people me-1"></i> Create Tribes & Groups
      </a>
      <span class="text-secondary">|</span>
      <a href="<?php echo $siteurl; ?>register/vendor.php" class="text-dark text-decoration-none">
        <i class="bi bi-people me-1"></i> Become a Vendor 
      </a>

      <span class="text-secondary">|</span>
      <a href="<?php echo $siteurl; ?>advertise.php" class="text-dark text-decoration-none">
        <i class="bi bi-badge-ad me-1"></i> Advertise with us
      </a>
    </div>
  </div>
</div>
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid position-relative d-flex align-items-center justify-content-between px-1">

  <a href="<?php echo $siteurl; ?>index.php" class="logo d-flex align-items-center order-0 me-auto me-xl-0">
        <div class="d-flex flex-column align-items-start">
          <!-- Image logo -->
          <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" alt="<?php echo htmlspecialchars($sitename); ?> logo">
          <!-- Small tagline under the logo -->
        <small class="text-muted ms-0" style="font-size:0.78rem; display:block;">Nurturing Marriages, Enriching Families!</small>
        </div>
      </a>

  <nav id="navmenu" class="navmenu order-1 flex-grow-1">
  <ul class="d-lg-flex justify-content-lg-center align-items-center mb-0 w-100">
          <li><a href="<?php echo $siteurl; ?>" class="active">Home</a></li>
          <li><a href="<?php echo $siteurl; ?>about.php">About</a></li>
          <li><a href="<?php echo $siteurl; ?>blog.php">Blogs</a></li>
          <li><a href="<?php echo $siteurl; ?>questions-answers.php">Q & A</a></li>
          <li><a href="<?php echo $siteurl; ?>marketplace.php">Marketplace</a></li>
          <li><a href="<?php echo $siteurl; ?>services.php">Services</a></li>
          <li><a href="<?php echo $siteurl; ?>events.php">Events</a></li>
          <li><a href="<?php echo $siteurl; ?>vendors.php">Vendors Directory</a></li>
          <li><a href="<?php echo $siteurl; ?>advertise.php">Advertise with us</a></li>
          <a href="<?php echo $siteurl; ?>find-therapist" class="btn btn-primary text-white p-2" >Find a Therapists</a>
          <!---
          <li class="dropdown"><a href="#"><span>Categories</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="category.html">Category 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="category.html">Category 2</a></li>
              <li><a href="category.html">Category 3</a></li>
              <li><a href="category.html">Category 4</a></li>
            </ul>
          </li>

          <li><a href="contact.html">Contact</a></li>
          --->
        </ul>
      </nav>

      <!-- Right-side actions for mobile: mobile toggle, cart, account -->
  <div class="d-flex align-items-center ms-auto order-2">
        <!-- Mobile nav toggle (visible on md and smaller screens) -->
        <button class="btn btn-link p-0 d-lg-none mobile-nav-toggle me-2" aria-label="Toggle navigation">
          <i class="bi bi-list fs-4"></i>
        </button>

        <!-- Cart icon -->
        <a href="<?php echo $siteurl; ?>cart.php" class="btn btn-link p-0 me-2 position-relative header-action-btn" aria-label="Cart">
          <i class="bi bi-cart fs-5"></i>
          <!-- badge (replace 0 with dynamic count if desired) -->
            <?php
            $cart_count = getCartCount($con, $siteprefix, $order_id);

            ?>
            <?php if ($cart_count >= 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size:10px"><?php echo $cart_count; ?></span>
          <?php endif; ?>
        </a>
        <?php
     
            $wishlist_count = getWishlistCountByUser($con, $buyerId);
         
          ?>

        <!-- wishlist icon -->
        <a href="<?php echo $siteurl; ?>wishlist.php" class="btn btn-link p-0 me-2 position-relative header-action-btn" aria-label="Cart">
          <i class="bi bi-heart-fill fs-5"></i>
          <!-- badge (replace 0 with dynamic count if desired) --> 
             <?php if ($wishlist_count >= 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger wishlist-count" style="font-size:10px"><?php echo $wishlist_count; ?></span>
         <?php endif; ?>
        </a>
      

        <div class="dropdown account-dropdown">
  <button class="header-action-btn btn border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person fs-5"></i>
    <span class="action-text d-none d-md-inline-block">Account</span>
  </button>

  <div class="dropdown-menu dropdown-menu-end shadow rounded-3">
    <div class="dropdown-header mb-2 px-3 pt-3">
      <h6>Welcome to <span class="sitename"><?php echo $sitename; ?></span></h6>

      <?php if ($activeLog == 0) { ?>
        <p class="mb-2 text-muted small">Access your account</p>
    </div>
    <div class="dropdown-footer px-3 pb-3">
      <a href="<?php echo $siteurl; ?>login" class="btn btn-primary w-100 mb-2">Sign In</a>
      <a href="<?php echo $siteurl; ?>registration.php" class="btn btn-outline-primary w-100">Register</a>
    </div>
    <?php } else { ?>
    </div>
    <div class="dropdown-body mb-2">
      <a class="dropdown-item d-flex align-items-center" href="<?php echo $dashboardUrl; ?>">
        <i class="bi bi-person-circle me-2"></i>
        <span>Dashboard</span>
      </a>
    </div>
    <div class="dropdown-footer px-3 pb-3">
      <a href="<?php echo $siteurl; ?>logout.php" class="btn btn-primary w-100 mb-2">Log Out</a>
    </div>
    <?php } ?>
  </div>
</div>
    </div>
  </header>
<!-- Hidden key -->
<input type="hidden" id="paystack-key" value="<?php echo $apikey; ?>">
<input type="hidden" id="order_id" value="<?php echo $order_id; ?>">
<input type="hidden" value="<?php echo $siteurl; ?>" id="siteurl">
 <input type="hidden" id="user_id" value="<?php echo !empty($buyerId) ? $buyerId : ''; ?>">


<?php include "banner-interstitial.php"; ?>

