<?php include "../script/connect.php"; ?>

<?php


$activeLog = 0; // 0 = guest, 1 = logged-in buyer
$userType = "admin"; // specify this header is for buyer

// Check if buyer is logged in via cookie
$authentication = $_COOKIE['admin_auth'] ?? '';

if ($authentication) {
    // Fetch buyer data from API or DB
    $url =  $siteurl . "script/user?action=buyerdata&buyer=$authentication";
    $data = curl_get_contents($url);

    if ($data !== false) {
        $buyerData = json_decode($data);
        if ($buyerData && empty($buyerData->error)) {
            $buyerId = $buyerData->id ?? '';
            $buyerfirstName = $buyerData->first_name;
            $email = $buyerData->email;
            $logo = $siteurl.$imagePath.$buyerData->photo;
            $buyerName = $buyerData->first_name . " " . $buyerData->last_name;
            $buyerEmail = $buyerData->email ?? '';
            $buyerStatus = $buyerData->status ?? 'inactive';
            $buyerVerified = $buyerData->is_verified ?? 0;
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
if (!$authentication) {
    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
    header("Location:" . $siteurl . "login.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Marriage Hub</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="assets/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <!-- Select2 -->
  <link href="assets/css/select2.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
<link rel="stylesheet" href="assets/css/kaiadmin.css" />
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="assets/css/demo.css" />
     <?php include "../script/tinymce.php"; ?>
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
          <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" alt="logo" class="small-logo">
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item active">
                <a
                  data-bs-toggle="collapse"
                  href="index.php"
                  class="collapsed"
                  aria-expanded="false"
                >
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
               
                </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-shop"></i>
                </span>
              </li>
              <!---
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-shopping-cart"></i>
                  <p>Services</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="add-listing.php">
                        <span class="sub-item">Add Listing</span>
                      </a>
                    </li>
                    <li>
                      <a href="pending-listings.php">
                        <span class="sub-item">Pending Listings</span>
                      </a>
                    </li>
                   
                       <li>
                      <a href="approved-listings.php">
                        <span class="sub-item">Approved Listings</span>
                      </a>
                    </li>

                         <li>
                      <a href="all-categories.php">
                        <span class="sub-item">All Categories</span>
                      </a>
                    </li>

                        <li>
                      <a href="all-subcategories.php">
                        <span class="sub-item">All Sub Categories</span>
                      </a>
                    </li>
                   
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#sidebarLayouts">
                  <i class="fas fa-calendar-alt"></i>
                  <p>Events</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="sidebarLayouts">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="add-event.php">
                        <span class="sub-item">Add Event</span>
                      </a>
                    </li>
                    <li>
                      <a href="all-events.php">
                        <span class="sub-item">Manage Events</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>

              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#disputes">
                  <i class="fas fa-balance-scale"></i>
                  <p>Disputes</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="disputes">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="all-disputes.php">
                        <span class="sub-item">All Disputes</span>
                      </a>
                    </li>
                    <li>
                      <a href="reported_items.php">
                        <span class="sub-item">Reported Items</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
--->
                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#users">
                  <i class="fas fa-user"></i>
                  <p>User Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="users">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="all-user.php">
                        <span class="sub-item">Manage Users</span>
                      </a>
                    </li>
                    
                  </ul>
                </div>
              </li>


              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#vendors">
                  <i class="fas fa-user"></i>
                  <p>Vendors Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="vendors">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="all-vendor.php">
                        <span class="sub-item">Manage Vendors</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>


              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#ServiceListing">
                  <i class="fas fa-shopping-cart"></i>
                  <p>Listing Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="ServiceListing">
                  <ul class="nav nav-collapse">

                  <li>
                      <a href="add-listing.php">
                        <span class="sub-item">Add Listing</span>
                      </a>
                    </li>
                     <li>
                      <a href="all-listings.php">
                        <span class="sub-item">All Listings</span>
                      </a>
                    </li>
                  
                    <li>
                      <a href="approved-listing.php">
                        <span class="sub-item">Approved Listings</span>
                      </a>
                    </li>

                   </ul>
                </div>
              </li>

                    <li class="nav-item">
                <a data-bs-toggle="collapse" href="#ads">
                 <i class="fa-brands fa-buysellads"></i>
                  <p>Ads Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="ads">
                  <ul class="nav nav-collapse">

                  <li>
                      <a href="add-ads.php">
                        <span class="sub-item">Add Ads</span>
                      </a>
                    </li>
                     <li>
                      <a href="advertlists.php">
                        <span class="sub-item">All Ads</span>
                      </a>
                    </li>

                   
                     <li>
                      <a href="active-adverts.php">
                        <span class="sub-item">Active Adverts</span>
                      </a>
                  </li>
                    
                    <li>
                      <a href="adverts.php">
                        <span class="sub-item">All Adverts Order</span>
                      </a>
                  </li>

                  </ul>
                </div>
              </li>

                  <li class="nav-item">
                <a data-bs-toggle="collapse" href="#subs">
                 <i class="fa-brands fa-buysellads"></i>
                  <p>Subscribers Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="subs">
                  <ul class="nav nav-collapse">
                    
                    <li>
                      <a href="subscriber-list.php">
                        <span class="sub-item">All Subscribers</span>
                      </a>
                  </li>

                  </ul>
                </div>
              </li>



                   <li class="nav-item">
                <a data-bs-toggle="collapse" href="#catego">
                  <i class="fas fa-wallet"></i>
                  <p>Order of categories</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="catego">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="add-category.php">
                        <span class="sub-item">Add Category</span>
                      </a>
                    </li>
                     <li>
                      <a href="add-sub-category.php">
                        <span class="sub-item">Add SubCategory</span>
                      </a>
                    </li>

                     <li>
                      <a href="manage-category.php">
                        <span class="sub-item">Manage Category</span>
                      </a>
                    </li>

                     <li>
                      <a href="manage-sub-category.php">
                        <span class="sub-item">Manage SubCategory</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
         


          <li class="nav-item">
                <a data-bs-toggle="collapse" href="#eve">
                  <i class="fas fa-calendar"></i>
                  <p> Events & Programs Management</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="eve">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="add-event.php">
                        <span class="sub-item">Add Event</span>
                      </a>
                    </li>

                     <li>
                      <a href="pending-events.php">
                        <span class="sub-item">Pending Events</span>
                      </a>
                    </li>

                      <li>
                      <a href="approved-event.php">
                        <span class="sub-item">Approved Events</span>
                      </a>
                    </li>

                     
                     </ul>
                </div>
              </li>

                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#questions">
                  <i class="fas fa-question"></i>
                  <p>Questions</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="questions">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="my-question.php">
                        <span class="sub-item">Admin Question</span>
                      </a>
                    </li>

                     <li>
                      <a href="pending-question.php">
                        <span class="sub-item">Pending Question</span>
                      </a>
                    </li>

                      <li>
                      <a href="approved-questions.php">
                        <span class="sub-item">Approved Question</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>

              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#books">
                  <i class="fas fa-wallet"></i>
                  <p>Bookings & Payments</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="books">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="booking-history.php">
                        <span class="sub-item">Booking history</span>
                      </a>
                    </li>

                     <li>
                      <a href="profits.php">
                        <span class="sub-item">Earnings</span>
                      </a>
                    </li>

                      <li>
                      <a href="withdrawals.php">
                        <span class="sub-item">withdrawal requests </span>
                      </a>
                    </li>

                      <li>
                      <a href="payout-logs.php">
                        <span class="sub-item">Payout Logs </span>
                      </a>
                    </li>
                     </ul>
                </div>
              </li>

                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#questions">
                  <i class="fas fa-question"></i>
                  <p>Questions</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="questions">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="my-question.php">
                        <span class="sub-item">Admin Question</span>
                      </a>
                    </li>

                     <li>
                      <a href="pending-question.php">
                        <span class="sub-item">Pending Question</span>
                      </a>
                    </li>

                      <li>
                      <a href="approved-questions.php">
                        <span class="sub-item">Approved Question</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>

<!---

              
                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#chats">
                  <i class="fas fa-comments"></i>
                  <p>Messages</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="chats">
                  <ul class="nav nav-collapse">
                  
                    <li>
                      <a href="chat-list.php">
                        <span class="sub-item">Chat</span>
                      </a>
                    </li>

                    <li>
                      <a href="chat-list.php">
                        <span class="sub-item">Chat</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
--->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#forms">
                  <i class="fas fa-pen-square"></i>
                  <p>Blog</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="forms">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="add-blog.php">
                        <span class="sub-item">Add Blog</span>
                      </a>
                    </li>
                    <li>
                      <a href="pending-blog.php">
                        <span class="sub-item">Pending Blog</span>
                      </a>
                    </li>

                    <li>
                      <a href="approved-blog.php">
                        <span class="sub-item">Approved Blog</span>
                      </a>
                    </li>
                       
                  </ul>
                </div>
              </li>
                 <li class="nav-item">
                <a data-bs-toggle="collapse" href="#tribe">
                  <i class="fas fa-users"></i>
                  <p>Tribe & Group Moderation</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="tribe">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="add-tribe.php">
                        <span class="sub-item">Add Tribe & Groups</span>
                      </a>
                    </li>

                    <li>
                      <a href="all-groups.php">
                        <span class="sub-item">All Groups</span>
                      </a>
                    </li>

                     <li>
                      <a href="pending-groups.php">
                        <span class="sub-item">Pending Groups</span>
                      </a>
                    </li>

                     <li>
                      <a href="approved-groups.php">
                        <span class="sub-item">Approved Groups</span>
                      </a>
                    </li>

                    <li>
                      <a href="closed-groups.php">
                        <span class="sub-item">View GroupRequests</span>
                      </a>
                    </li>

                    <li>
                      <a href="my-groups.php">
                        <span class="sub-item">My Groups</span>
                      </a>
                    </li>
                         
                  </ul>
                </div>
              </li>


                   <li class="nav-item">
                <a data-bs-toggle="collapse" href="#disputes">
                  <i class="fas fa-comments"></i>
                  <p>Disputes & Reports</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="disputes">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="new-disputes.php">
                        <span class="sub-item">New Disputes</span>
                      </a>
                    </li>

                    <li>
                      <a href="all-disputes.php">
                        <span class="sub-item">All Disputes</span>
                      </a>
                    </li>

                    <li>
                      <a href="ongoing-disputes.php">
                        <span class="sub-item">Ongoing Disputes</span>
                      </a>
                    </li>

                    <li>
                      <a href="reported_items.php">
                        <span class="sub-item">Reported Items</span>
                      </a>
                    </li>


                         
                  </ul>
                </div>
              </li>

                      <li class="nav-item">
                <a data-bs-toggle="collapse" href="#prcing">
                  <i class="fas fa-users"></i>
                  <p>Vendor Pricing Plans</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="prcing">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="add-subscription.php">
                        <span class="sub-item">Add Subscription</span>
                      </a>
                    </li>

                    <li>
                      <a href="all-plans.php">
                        <span class="sub-item">All Subscriptions</span>
                      </a>
                    </li>

                         
                  </ul>
                </div>
              </li>

                    <li class="nav-item">
                <a data-bs-toggle="collapse" href="#settings">
                  <i class="fas fa-cogs"></i>
                  <p>Settings & Integrations</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="settings">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="settings.php">
                        <span class="sub-item">Settings</span>
                      </a>
                    </li>
                       
                 
                  <!----
                    <li>
                      <a href="group-message.php">
                        <span class="sub-item">Group Message</span>
                      </a>
                    </li>

                    ---->
                    
                  </ul>
                </div>
              </li>
             
              <!---
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#maps">
                  <i class="fas fa-map-marker-alt"></i>
                  <p>Bookings & Payments</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="maps">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="booking-history.php">
                        <span class="sub-item">Booking History</span>
                      </a>
                    </li>
                         <li>
                      <a href="earnings-breakdown.php">
                        <span class="sub-item">Earnings Breakdown</span>
                      </a>
                    </li>
                    <li>
                      <a href="withdrawal.php">
                        <span class="sub-item">Withdrawal & Payout Logs</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>

                <li class="nav-item">
                <a data-bs-toggle="collapse" href="#settings">
                  <i class="fas fa-cog"></i>
                  <p>Settings</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="settings">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="settings.php">
                        <span class="sub-item">Settings</span>
                      </a>
                    </li>
                         <li>
                      <a href="earnings-breakdown.php">
                        <span class="sub-item">Earnings Breakdown</span>
                      </a>
                    </li>
                    <li>
                      <a href="withdrawal.php">
                        <span class="sub-item">Withdrawal & Payout Logs</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              ---->
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.html" class="logo">
                <img
                  src="<?php echo $logo; ?>"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
              <!---
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input
                    type="text"
                    placeholder="Search ..."
                    class="form-control"
                  />
                </div>
                --->
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                <!----
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="messageDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fa fa-envelope"></i>
                  </a>
                  <ul
                    class="dropdown-menu messages-notif-box animated fadeIn"
                    aria-labelledby="messageDropdown"
                  >
                    <li>
                      <div
                        class="dropdown-title d-flex justify-content-between align-items-center"
                      >
                        Messages
                        <a href="#" class="small">Mark all as read</a>
                      </div>
                    </li>
                    <li>
                      <div class="message-notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="assets/img/jm_denis.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jimmy Denis</span>
                              <span class="block"> How are you ? </span>
                              <span class="time">5 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="assets/img/chadengle.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Chad</span>
                              <span class="block"> Ok, Thanks ! </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="assets/img/mlane.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jhon Doe</span>
                              <span class="block">
                                Ready for the meeting today...
                              </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="assets/img/talha.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Talha</span>
                              <span class="block"> Hi, Apa Kabar ? </span>
                              <span class="time">17 minutes ago</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="javascript:void(0);"
                        >See all messages<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>

                --->
                <?php
                $url = $siteurl . "script/admin.php?action=notificationlists";
                $data = curl_get_contents($url);

                $notifCount = 0; // default
                $notifications = [];

                if ($data !== false) {
                    $notificationsData = json_decode($data);

                    if (!empty($notificationsData)) {
                        $notifCount = count($notificationsData);          // total unread notifications
                        $notifications = array_slice($notificationsData, 0, 5); // latest 5
                    }
                }
                ?>

                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="notifDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fa fa-bell"></i>
                    <span class="notification"><?= $notifCount; ?></span>
                  </a>
                  <ul
                    class="dropdown-menu notif-box animated fadeIn"
                    aria-labelledby="notifDropdown"
                  >
                    <li>
                      <div class="dropdown-title">
                        You have <?= $notifCount ?? 0; ?> new notification<?= ($notifCount ?? 0) != 1 ? 's' : ''; ?>
                      </div>
                    </li>
                    <li>
                      <div class="notif-scroll scrollbar-outer">
                        <div class="notif-center">
                  <?php
                    if (!empty($notifications)) {
                        foreach ($notifications as $notification) {
                            $Id       = $notification->s;
                            $message  = $notification->message;
                            $link     = !empty($notification->link) ? $notification->link : "#";
                            $content  = limitWords($message, 5);
                            $dateAgo  = timeAgo($notification->date);
                    ?>
                            <a href="<?= $link; ?>">
                                <div class="notif-icon notif-primary">
                                    <i class="icon-bell"></i>
                                </div>
                                <div class="notif-content">
                                    <span class="block"><?= $content; ?></span>
                                    <span class="time"><?= $dateAgo; ?></span>
                                </div>
                            </a>
                    <?php
                        }
                    } else {
                        echo '<p class="text-center text-muted">No notifications available.</p>';
                    }
                    ?>
                      
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="notifications.php"
                        >See all notifications<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>
                <!---
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <i class="fas fa-layer-group"></i>
                  </a>
                  <div class="dropdown-menu quick-actions animated fadeIn">
                    <div class="quick-actions-header">
                      <span class="title mb-1">Quick Actions</span>
                      <span class="subtitle op-7">Shortcuts</span>
                    </div>
                    <div class="quick-actions-scroll scrollbar-outer">
                      <div class="quick-actions-items">
                        <div class="row m-0">
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-danger rounded-circle">
                                <i class="far fa-calendar-alt"></i>
                              </div>
                              <span class="text">Calendar</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-warning rounded-circle"
                              >
                                <i class="fas fa-map"></i>
                              </div>
                              <span class="text">Maps</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-info rounded-circle">
                                <i class="fas fa-file-excel"></i>
                              </div>
                              <span class="text">Reports</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-success rounded-circle"
                              >
                                <i class="fas fa-envelope"></i>
                              </div>
                              <span class="text">Emails</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-primary rounded-circle"
                              >
                                <i class="fas fa-file-invoice-dollar"></i>
                              </div>
                              <span class="text">Invoice</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-secondary rounded-circle"
                              >
                                <i class="fas fa-credit-card"></i>
                              </div>
                              <span class="text">Payments</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                --->
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img
                        src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?php echo $buyerfirstName; ?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4><?php echo $buyerfirstName; ?></h4>
                            <p class="text-muted"><?php echo $email; ?></p>
                            <a
                              href="settings.php"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            >
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="settings.php">My Profile</a>
                        <a class="dropdown-item" href="<?php echo $siteurl; ?>logout.php">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
        <input type="hidden" value="<?php echo $siteurl; ?>" id="siteurl">