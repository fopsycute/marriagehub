<?php 
include "header.php"; 

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=vendorslug&slug=" . $slug;

    // Fetch vendor details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $vendordetails = json_decode($data);
        if (!empty($vendordetails)) {
            $vendordetail = $vendordetails[0]; 
            $vendor_id  = $vendordetail->id ?? '';
            $vendor_slug = $vendordetail->slug ?? '';
            $user_type = $vendordetail->user_type ?? '';
            $subscription_status = $vendordetail->subscription_status ?? '';
            $subscription_plan_id = $vendordetail->subscription_plan_id ?? '';
        } else {
            echo "<div class='alert alert-warning'>No vendor found with the given slug.</div>";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching vendor data. Please try again later.</div>";
        exit;
    }
} else {
    header("Location: $siteurl");
    exit;
}

// ✅ Redirect if not a vendor
if (strtolower($user_type) !== 'vendor') {
    header("Location: $siteurl");
    exit;
}
?>

<section id="pricing" class="pricing section">
  <div class="container pricing-toggle-container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4 justify-content-center">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Vendor Subscription Plans</h2>
          <p>
            <?php if ($subscription_status && $subscription_plan_id) { ?>
              You are currently on the 
              <strong>
                <?php echo ucfirst($subscription_status); ?> 
              </strong> plan.
            <?php } else { ?>
              You are currently on a free plan. Upgrade to enjoy more features.
            <?php } ?>
          </p>
        </div>
      </div>
      <!-- End Section Title -->

      <?php
      // ✅ Fetch all plans
      $url = $siteurl . "script/admin.php?action=subscriptionlists";
      $data = curl_get_contents($url);

      if ($data !== false) {
          $plans = json_decode($data);

          if (!empty($plans)) {
              // Get current plan price for comparison
              $currentPlanPrice = 0;
              foreach ($plans as $p) {
                  if ($p->id == $subscription_plan_id) {
                      $currentPlanPrice = $p->price;
                      break;
                  }
              }

              foreach ($plans as $plan) {
                  $planName = ucfirst($plan->name);
                  $planPrice = number_format($plan->price);
                  $planRawPrice = $plan->price;
                  $id = $plan->id;

                  // ✅ Duration text
                  if ($plan->duration_days >= 365) {
                      $durationText = 'per annum';
                  } elseif ($plan->duration_days >= 30) {
                      $durationText = 'per month';
                  } else {
                      $durationText = 'No duration';
                  }

                  // ✅ Lead request text
                  if (empty($plan->lead_request_limit) || strtolower($plan->lead_request_limit) === 'n/a') {
                      $leadAccess = 'N/A';
                  } elseif (strtolower($plan->lead_request_limit) === 'unlimited') {
                      $leadAccess = 'Unlimited per month';
                  } else {
                      $leadAccess = $plan->lead_request_limit . ' per month';
                  }

                  // ✅ Features
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

                  // ✅ Button Logic
                  $buttonText = "Subscribe";
                  $buttonDisabled = false;
                  $buttonClass = "btn btn-primary w-100";

                  if ($subscription_plan_id == $id) {
                      $buttonText = "Your Current Plan";
                      $buttonDisabled = true;
                      $buttonClass = "btn btn-secondary w-100 disabled";
                  } elseif ($currentPlanPrice > 0 && $planRawPrice > $currentPlanPrice) {
                      $buttonText = "Upgrade";
                  } elseif ($currentPlanPrice > 0 && $planRawPrice < $currentPlanPrice) {
                      $buttonText = "Subscribe"; // downgrade not allowed
                  }
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
      <?php if ($buttonDisabled) { ?>
        <button class="btn btn-secondary w-100 disabled" disabled>
          <?php echo $buttonText; ?>
        </button>
      <?php } else { ?>
        <button class="btn btn-primary w-100 subscribeButton"
          data-plan-id="<?= $id ?>"
          data-amount="<?= $planRawPrice ?>"
          data-plan-name="<?= htmlspecialchars($planName, ENT_QUOTES) ?>"
          data-user-id="<?= $vendor_id ?>"
          data-email="<?= $vendordetail->email ?? '' ?>">
          <?= $buttonText ?>
        </button>
      <?php } ?>
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
