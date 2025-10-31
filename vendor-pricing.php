<?php include "header.php"; ?>
<section id="pricing" class="pricing section">

  <div class="container pricing-toggle-container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4 justify-content-center">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Vendor Subscription Plans</h2>
          <p>You are currently on a free plan. Upgrade to enjoy more features.</p>
        </div>
      </div>
      <!-- End Section Title -->

      <?php
      $url = $siteurl . "script/admin.php?action=subscriptionlists";
      $data = curl_get_contents($url);

      if ($data !== false) {
          $plans = json_decode($data);

          if (!empty($plans)) {
              foreach ($plans as $plan) {
                  $planName = ucfirst($plan->name);
                  $planPrice = number_format($plan->price);
                  $id = $plan->id;

                  // ✅ Duration Text
                  if ($plan->duration_days >= 365) {
                      $durationText = 'per annum';
                  } elseif ($plan->duration_days >= 30) {
                      $durationText = 'per month';
                  } else {
                      $durationText = 'No duration';
                  }

                  // ✅ Format "Access to Lead Requests"
                  if (empty($plan->lead_request_limit) || strtolower($plan->lead_request_limit) === 'n/a') {
                      $leadAccess = 'N/A';
                  } elseif (strtolower($plan->lead_request_limit) === 'unlimited') {
                      $leadAccess = 'Unlimited per month';
                  } else {
                      $leadAccess = $plan->lead_request_limit . ' per month';
                  }

                  // ✅ Nicely formatted feature list
                  $features = [
                      'Vendor Profile Page' => $plan->vendor_profile_page,
                      'Number of Product/Service Listings: ' . ($plan->product_limit ?? 'N/A') => true,
                      'Visibility on Homepage & Search' => $plan->homepage_visibility,
                      'Featured Vendor Badge' => $plan->featured_badge,
                      'Access to Lead Requests / Client Inquiries (' . $leadAccess . ')' => true,
                      'Access to Vendor Dashboard' => $plan->dashboard_access,
                      'Upload Portfolio Images (Photos & Flyers): ' . ($plan->portfolio_limit ?? 'N/A') => true,
                      'Areas of Specialization: ' . ($plan->specialization_limit ?? 'N/A') => true,
                      'Number of Articles: ' . ($plan->article_limit ?? 'N/A') => true,
                      'Display Reviews & Ratings' => true,
                      'Client Messaging System' => $plan->messaging_system,
                      'Appearance in Directory' => $plan->directory_appearance,
                      'Response to Reviews' => $plan->review_response,
                      'Highlighted Listings in Category Pages' => $plan->highlighted_listing
                  ];
      ?>
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="pricing-item">
          <div class="pricing-header">
            <h6 class="pricing-category"><?php echo $planName; ?></h6>
            <div class="price-wrap">
              <div class="price monthly">
                ₦<?php echo $planPrice; ?> <span>/ <?php echo $durationText; ?></span>
              </div>
            </div>
          </div>

          <div class="pricing-cta">
            <a href="subscribe.php?plan_id=<?php echo $id; ?>" class="btn btn-primary w-100">
              SUBSCRIBE
            </a>
          </div>

          <div class="pricing-features">
            <h6>Included Features:</h6>
            <ul class="feature-list">
              <?php foreach ($features as $label => $available) { ?>
                <li>
                  <i class="bi <?php echo $available ? 'bi-check text-success' : 'bi-x text-danger'; ?>"></i>
                  <?php echo $label; ?>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
      <?php
              }
          }
      }
      ?>
    </div>
  </div>

</section>
<?php include "footer.php"; ?>
