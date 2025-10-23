<?php include "script/connect.php"; ?>

<?php


$requireLogin = $requireLogin ?? false; // page-specific
$activeLog = 0; // 0 = guest, 1 = logged-in buyer
 $buyerId = 0;
$userType = "buyer"; // specify this header is for buyer

// Check if buyer is logged in via cookie
$authentication = $_COOKIE['user_auth'] ?? '';


if (!empty($_COOKIE['admin_auth'])) {
  $dashboardUrl = $siteurl . 'admin/index.php';
} elseif (!empty($_COOKIE['vendor_auth'])) {
  $dashboardUrl = $siteurl . 'therapist/index.php';
} elseif (!empty($_COOKIE['user_auth'])) {
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
            $dob            = $buyerData->dob ?? '';
            $gender         = $buyerData->gender ?? '';
            $nationality    = $buyerData->nationality ?? '';
            $languages      = $buyerData->languages ?? '';
            $phone          = $buyerData->phone ?? '';
            $website        = $buyerData->website ?? '';
            $email          = $buyerData->email ?? '';
            $state_residence = $buyerData->state_residence ?? '';
            $address        = $buyerData->address ?? '';
            $facebook       = $buyerData->facebook ?? '';
            $twitter        = $buyerData->twitter ?? '';
            $instagram      = $buyerData->instagram ?? '';
            $linkedin       = $buyerData->linkedin ?? '';
            $bio            = $buyerData->bio ?? '';
            $photo          = $buyerData->photo ?? '';
            $suspend_reason = $buyerData->suspend_reason ?? '';

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


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Marriage Hub Ng</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo $siteurl; ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo $siteurl; ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<!---
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
--->
  <!-- Vendor CSS Files -->
  <link href="<?php echo $siteurl; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="<?php echo $siteurl; ?>admin/assets/css/plugins.min.css" />
  <link href="<?php echo $siteurl; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $siteurl; ?>assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $siteurl; ?>assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <?php include "script/tinymce.php"; ?>


  <!-- Main CSS File -->
  <link href="<?php echo $siteurl; ?>assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: ZenBlog
  * Template URL: https://bootstrapmade.com/zenblog-bootstrap-blog-template/
  * Updated: Aug 08 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

<!-- ======= Top Header ======= -->
<div class="top-header bg-light py-2 border-bottom">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="top-links d-flex align-items-center gap-3 small">
      <a href="<?php echo $siteurl; ?>find-therapist.php" class="text-dark text-decoration-none">
        <i class="bi bi-person-heart me-1"></i> Find Therapists
      </a>
      <span class="text-secondary">|</span>
      <a href="<?php echo $siteurl; ?>create-tribes-group.php" class="text-dark text-decoration-none">
        <i class="bi bi-people me-1"></i> Create Tribes & Groups
      </a>
      <span class="text-secondary">|</span>
      <a href="<?php echo $siteurl; ?>dashboard.php" class="text-dark text-decoration-none">
        <i class="bi bi-journal-text me-1"></i> Submit Articles & Questions
      </a>
    </div>
  </div>
</div>
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="<?php echo $siteurl; ?>index.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" alt="">
       <!-- <h1 class="sitename">ZenBlog</h1> -->

      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="<?php echo $siteurl; ?>" class="active">Home</a></li>
          <li><a href="<?php echo $siteurl; ?>about.php">About</a></li>
          <li><a href="<?php echo $siteurl; ?>blog.php">Blogs</a></li>
          <li><a href="<?php echo $siteurl; ?>questions-answers.php">Q & A</a></li>
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
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
 
          <div class="dropdown account-dropdown">
  <button class="header-action-btn btn border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person fs-5"></i>
    <span class="action-text d-none d-md-inline-block">Account</span>
  </button>

  <div class="dropdown-menu shadow rounded-3">
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
<!-- Hidden Paystack public key -->
<input type="hidden" id="paystack-key" value="<?php echo $apikey; ?>">
<input type="hidden" value="<?php echo $siteurl; ?>" id="siteurl">




