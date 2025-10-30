
<?php include "header.php"; ?>
   <section id="pricing" class="pricing section">

      <div class="container pricing-toggle-container" data-aos="fade-up" data-aos-delay="100">


        <!-- Pricing Plans -->
        <div class="row gy-4 justify-content-center">

          <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Vendor Subscription Plans </h2>
      <p>Your re currently on a free plan , upgrade to enjoy more feature</p>
    </div>
  </div><!-- End Section Title -->
<?php
$url = $siteurl . "script/admin.php?action=subscriptionlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $plans = json_decode($data);

    if (!empty($plans)) {
        foreach ($plans as $plan) {
            $planName = ucfirst($plan->name);
            $planPrice = number_format($plan->price);
            $duration = $plan->duration_days ? $plan->duration_days . ' days' : 'No duration';
            $id = $plan->id;

            // Generate features dynamically based on TRUE/FALSE
            $features = [
                'Vendor Profile Page' => $plan->vendor_profile_page,
                'Visibility on Homepage & Search' => $plan->homepage_visibility,
                'Featured Vendor Badge' => $plan->featured_badge,
                'Vendor Dashboard Access' => $plan->dashboard_access,
                'Messaging System' => $plan->messaging_system,
                'Directory Appearance' => $plan->directory_appearance,
                'Response to Reviews' => $plan->review_response,
                'Highlighted Listing' => $plan->highlighted_listing,
                'Product Limit: ' . $plan->product_limit => true,
                'Lead Requests / Client Inquiries: ' . $plan->lead_request_limit => true,
                'Portfolio Limit: ' . $plan->portfolio_limit => true,
                'Specialization Limit: ' . $plan->specialization_limit => true,
                'Article Limit: ' . $plan->article_limit => true,
            ];
?>
<div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
  <div class="pricing-item">
    <div class="pricing-header">
      <h6 class="pricing-category"><?php echo $planName; ?></h6>
      <div class="price-wrap">
        <div class="price monthly">
          ₦<?php echo $planPrice; ?><span>/<?php echo $plan->duration_days ? 'year' : 'm'; ?></span>
        </div>
      </div>
      <p class="pricing-description">
        <?php
          if ($planName == 'Free') echo "Start with the essentials";
          elseif ($planName == 'Classic') echo "Ideal for growing vendors";
          elseif ($planName == 'Enterprise') echo "Best for established vendors";
          else echo "For vendors who want it all";
        ?>
      </p>
    </div>

    <div class="pricing-cta">
      <a href="subscribe.php?plan_id=<?php echo $id; ?>" class="btn btn-primary w-100">
        Subscribe
      </a>
    </div>

    <div class="pricing-features">
      <h6>Included Features:</h6>
      <ul class="feature-list">
        <?php foreach ($features as $label => $available) { ?>
          <li>
            <i class="bi <?php echo $available ? 'bi-check' : 'bi-x'; ?>"></i> <?php echo $label; ?>
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

    </section><!-- /Pricing Section -->

  </main>


<?php include "footer.php"; ?>