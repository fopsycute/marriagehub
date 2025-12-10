

<?php 
$requireLogin = true;

include "header.php"; 

?>

<?php include "sidebar-header.php"; ?>
<div class="container mb-4">
  <div class="row g-4">

    <!-- My Bookings -->
    <div class="col-md-3 col-sm-6">
        <a href="booking-history.php" class="text-decoration-none">
      <div class="card text-center shadow-sm border-0 h-100  bg-normalsm">
        <div class="card-body">
          <div class="mb-3 text-white">
            <i class="fa fa-calendar-check fa-2x"></i>
          </div>
          <h6 class="card-title">My Bookings</h6>
          <h3 class="fw-bold">12</h3>
        </div>
      </div>
        </a>
    </div>

    <!-- My Reviews -->
    <div class="col-md-3 col-sm-6">
        <a href="my-reviews.php" class="text-decoration-none">
      <div class="card text-center shadow-sm border-0 h-100 bg-secondary">
        <div class="card-body">
          <div class="mb-3 text-white">
            <i class="fa fa-star fa-2x"></i>
          </div>
          <h6 class="card-title">My Reviews</h6>
          <h3 class="fw-bold">34</h3>
        </div>
      </div>
        </a>
    </div>

    <!-- My Orders -->
    <div class="col-md-3 col-sm-6">
        <a href="my-orders.php" class="text-decoration-none">
      <div class="card text-center shadow-sm border-0 h-100 bg-normal">
        <div class="card-body">
          <div class="mb-3 text-primary">
            <i class="fa fa-shopping-cart fa-2x"></i>
          </div>
          <h6 class="card-title">My Orders</h6>
          <h3 class="fw-bold">8</h3>
        </div>
      </div>
        </a>
    </div>

    <!-- Total Transactions -->
    <div class="col-md-3 col-sm-6">
        <a href="transaction-history.php" class="text-decoration-none">
      <div class="card text-center shadow-sm border-0 h-100 bg-success">
        <div class="card-body">
          <div class="mb-3 text-white">
            <i class="fa fa-credit-card fa-2x"></i>
          </div>
          <h6 class="card-title">Total Transactions</h6>
          <h3 class="fw-bold">#2,450</h3>
        </div>
      </div>
        </a>
    </div>

  </div>
  </div>
<?php include "footer.php"; ?>