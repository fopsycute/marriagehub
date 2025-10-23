
<!-- User Dashboard -->
<section>
  <div class="container d-flex align-items-center gap-1 mb-2">
    <!-- Profile Picture -->
    <img src="<?php echo $siteurl . $imagePath . $logo; ?>" alt="Profile Picture" class="profile-pic">

    <!-- Welcome Message -->
    <div class="welcome-message">
      <h4>Welcome back, <span id="userName"><?php echo $buyerfirstName; ?></span> 👋</h4>
    </div>
  </div>
  <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="user-dashboard d-flex justify-content-between align-items-stretch flex-wrap gap-3">

      <!-- Dashboard Card -->
      <a href="dashboard.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-person-circle"></i>
        </div>
        <h4>My Profile</h4>
      </a>

        <!-- Dashboard Card -->
      <a href="booking-history.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-calendar"></i>
        </div>
        <h4>My Bookings</h4>
      </a>

   <!-- Tribes / Groups Joined -->
<a href="my-tribes.php" class="dashboard-item text-center">
  <div class="dashboard-icon">
    <i class="bi bi-people"></i>
  </div>
  <h4>Tribes / Groups</h4>
</a>
<!-- My Events -->
<a href="all-events.php" class="dashboard-item text-center">
  <div class="dashboard-icon">
    <i class="bi bi-ticket-perforated"></i>
  </div>
  <h4>My Events</h4>
</a>


<!-- Review -->
<a href="my-reviews.php" class="dashboard-item text-center">
  <div class="dashboard-icon">
    <i class="bi bi-star"></i>
  </div>
  <h4>Review</h4>
</a>

      <a href="my-orders.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-file-earmark-text"></i>
        </div>
        <h4>My Orders</h4>
      </a>

            <a href="create-disputes.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-file-earmark-text"></i>
        </div>
        <h4>Create Ticket</h4>
      </a>
<!-- Notifications
      <a href="notifications.html" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-bell"></i>
        </div>
        <h4>Notifications</h4>
      </a>
 -->
      <a href="settings.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-gear"></i>
        </div>
        <h4>Settings</h4>
      </a>


      <!-- Payment History -->
<a href="transaction-history.php" class="dashboard-item text-center">
  <div class="dashboard-icon">
    <i class="bi bi-credit-card"></i>
  </div>
  <h4>Payment History</h4>
</a>

 <a href="reset_password.php" class="dashboard-item text-center">
        <div class="dashboard-icon">
          <i class="bi bi-lock"></i>
        </div>
        <h4>Reset Password</h4>
      </a>
    </div>
  </div>
</section>