<?php include "../script/connect.php"; ?>

<?php


$activeLog = 0; // 0 = guest, 1 = logged-in buyer
$userType = "therapist"; // specify this header is for buyer

// Check if buyer is logged in via cookie
$authentication = $_COOKIE['therapist_auth'] ?? '';

if ($authentication) {
    // Fetch buyer data from API or DB
    $url =  $siteurl . "script/user?action=buyerdata&buyer=$authentication";
    $data = curl_get_contents($url);

    if ($data !== false) {
        $buyerData = json_decode($data);
        if ($buyerData && empty($buyerData->error)) {
            $buyerId = $buyerData->id ?? '';
            $buyerfirstName = $buyerData->first_name; 
            $buyermiddleName = $buyerData->middle_name;
            $buyerlastName = $buyerData->last_name;
            $email = $buyerData->email;
            $logo = $buyerData->photo;
            $address = $buyerData->address ?? '';
            $views = $buyerData->views ?? 0;
            $bank_accname = $buyerData->bank_accname ?? '';
            $total_questions = $buyerData->total_questions ?? 0;
            $total_articles = $buyerData->total_articles ?? 0;
            $total_answers = $buyerData->total_answers ?? 0;
            $total_earnings = $buyerData->total_earnings ?? 0;
            $bank_number = $buyerData->bank_number ?? '';
            $bank_name = $buyerData->bank_name ?? '';
            $facebook = $buyerData->facebook ?? '';
            $twitter = $buyerData->twitter ?? '';
            $instagram = $buyerData->instagram ?? '';
            $linkedin = $buyerData->linkedin ?? '';
            $bio = $buyerData->bio ?? '';
            $photo = $buyerData->photo ?? '';
            $certifications  = $buyerData->certifications ?? '';
            $suspend_reason = $buyerData->suspend_reason ?? '';
             $session_duration = $buyerData->session_duration ?? '';
            $rate = $buyerData->rate ?? $buyerData->proposed_rate ?? '';
            $associations = $buyerData->associations ?? '';
            $other_work = $buyerData->other_work ?? '';
            $preferred_days = $buyerData->preferred_days ?? '';
            $institution = $buyerData->institution  ?? '';
            $highest_qualification = $buyerData->qualification  ?? '';
            $graduation_year = $buyerData->graduation_year  ?? '';
            $cv = $buyerData->cv  ?? '';
            $passport = $buyerData->passport  ?? '';
            $license = $buyerData->license  ?? '';
            $status = $buyerData->status ?? '';
            $dob = $buyerData->dob ?? '';
            $gender = $buyerData->gender ?? '';
             $services = $buyerData->services ?? '';
             $other_qualification = $buyerData->qualification ?? '';
            $business_name = $buyerData->business_name;
            $registered_business_name = $buyerData->registered_business_name;
            $owner_name = $buyerData->owner_name;
            $session_format = $buyerData->session_format ?? '';
            $nationality = $buyerData->nationality ?? '';
            $languages = $buyerData->languages ?? '';
            $phone = $buyerData->phone ?? '';
            $website = $buyerData->website ?? '';
            $email = $buyerData->email ?? '';
            $work_with = $buyerData->work_with ?? '';
            $specialization_selected  = !empty($buyerData->specializations) ? explode(',', $buyerData->specializations) : [];
            $subspec_selected = !empty($buyerData->sub_specialization) ? explode(',', $buyerData->sub_specialization) : [];
            $categories_selected = !empty($buyerData->category_id) ? explode(',', $buyerData->category_id) : [];
            $subcategories_selected = !empty($buyerData->subcategory_id) ? explode(',', $buyerData->subcategory_id) : [];
            $profession_selected  = !empty($buyerData->professional_field) ? explode(',', $buyerData->professional_field) : [];
            $subprofession_selected  = !empty($buyerData->professional_title) ? explode(',', $buyerData->professional_title) : [];
            $state_residence = $buyerData->state_residence ?? '';
            $lga = $buyerData->lga ?? '';
            $experience_years = $buyerData->experience_years;
            $business_logo = $siteurl.$imagePath.$buyerData->business_logo;
            $portfolio = $buyerData->portfolio;
            $coverage = $buyerData->coverage;
            $onsite = $buyerData->onsite;
            $consultation_days = $buyerData->consultation_days ?? '';
            $preferred_days_selected = [];
            $start_time = '';
            $end_time = '';

          if (!empty($consultation_days)) {
              // Split the days from times
              $parts = explode('|', $consultation_days);
              if (count($parts) == 2) {
                  // Days
                  $preferred_days_selected = array_map('trim', explode(',', $parts[0]));
                  // Times
                  $times = trim($parts[1]);
                  if (strpos($times, '-') !== false) {
                      list($start_time, $end_time) = array_map('trim', explode('-', $times));
                  }
              }
          }
            
            $mylogo = $siteurl.$imagePath.$buyerData->photo;
            $buyerName = $buyerData->first_name . " " . $buyerData->last_name;
            $buyerEmail = $buyerData->email ?? '';
            $buyerStatus = $buyerData->status ?? 'inactive';
            $buyerVerified = $buyerData->is_verified ?? 0;
            $logo = $siteurl.$imagePath.$buyerData->photo;
            $wallet = $buyerData->wallet ?? '';
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
    header("Location: ../login.php");
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
      href="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>"
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
            <img
                  src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>"
                  alt="navbar brand"
                  class="small-logo"
                 
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

                  <li class="nav-item">
                <a data-bs-toggle="collapse" href="#appointment">
                  <i class="fas fa-balance-scale"></i>
                  <p> Appointments & Bookings</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="appointment">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="upcoming-session.php">
                        <span class="sub-item">Upcoming sessions </span>
                      </a>
                    </li>
                    <li>
                      <a href="booking-request.php">
                        <span class="sub-item">Booking requests</span>
                      </a>
                    </li>
                     <li>
                      <a href="booking-history.php">
                        <span class="sub-item">Booking history</span>
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
                      <a href="all-question.php">
                        <span class="sub-item">All Question</span>
                      </a>
                    </li>

                  <li>
                      <a href="add-question.php">
                        <span class="sub-item">Add Question</span>
                      </a>
                    </li>
                  
                    <li>
                      <a href="my-question.php">
                        <span class="sub-item">My Question</span>
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
      ---->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#forms">
                  <i class="fas fa-newspaper"></i>
                  <p>Blog</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="forms">
                  <ul class="nav nav-collapse">

                  <li>
                      <a href="all-blog.php">
                        <span class="sub-item">All Blog</span>
                      </a>
                    </li>
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
                  <i class="fas fa-pen-square"></i>
                  <p>Tribes & Groups</p>
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

                    <li>
                     
                    </li>
                    
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#wallet">
                  <i class="fas fa-wallet"></i>
                  <p>Wallet & Earnings</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="wallet">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="wallet.php">
                        <span class="sub-item">Wallet</span>
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
                <a data-bs-toggle="collapse" href="#reviews">
                  <i class="fas fa-star"></i>
                  <p>Reviews</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="reviews">
                  <ul class="nav nav-collapse">

                  <li>
                      <a href="all-reviews.php">
                        <span class="sub-item">All Reviews</span>
                      </a>
                    </li>
                    <li>
                      <a href="average-rating.php">
                        <span class="sub-item">Average rating</span>
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
                      <a href="change-password.php">
                        <span class="sub-item">Change Password</span>
                      </a>
                    </li>
                    
                  </ul>
                </div>
              </li>
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
              <a href="index.php" class="logo">
                <img
                  src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>"
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
                <!--
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
                $url = $siteurl . "script/admin.php?action=usernotificationlists&user_id=" . $buyerId;
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
                            $content  = limitWords($message, 5);
                            $dateAgo  = timeAgo($notification->date);
                    ?>
                            <a href="#">
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
              


                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img
                        src="<?php echo $logo; ?>"
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
                              src="<?php echo $logo; ?>"
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
                     <!---   <a class="dropdown-item" href="settings.php">My Profile</a>  -->
                      <!---  <a class="dropdown-item" href="chat.php">Inbox</a>  -->
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="settings.php">Account Setting</a>
                        <div class="dropdown-divider"></div>
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