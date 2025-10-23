
<?php 
$requireLogin = false;
include "header.php"; 

?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Register</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Register</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->


     <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Register</h2>
          <p>Select your role to get started</p>
        </div>
      </div><!-- End Section Title -->

      <div class="container">

 <div class="roles">

      <!-- Normal User -->
      <div class="role-card">
        <i class="bi bi-person-circle icon"></i>
        <h3>Buyer</h3>
        <p>Explore and connect with trusted services for love, marriage, and family.</p>
        <a href="register/user.php" class="signup-btn">Sign Up</a>
      </div>

      <!-- Vendor -->
      <div class="role-card">
        <i class="bi bi-shop-window icon"></i>
        <h3>Vendor</h3>
        <p>Showcase your services and reach thousands of clients.</p>
        <a href="register/vendor.php" class="signup-btn">Sign Up</a>
      </div>

      <!-- Therapist / Counselor -->
      <div class="role-card">
        <i class="bi bi-heart-pulse-fill icon"></i>
        <h3>Therapist / Counselor</h3>
        <p>Support individuals, couples, and families across Nigeria.</p>
        <a href="register/therapist.php" class="signup-btn">Sign Up</a>
      </div>

    </div>
    </div>
</section>

</main>






<?php include "footer.php"; ?>