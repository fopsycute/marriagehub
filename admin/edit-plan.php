<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Plan</h3>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Update Subscription Plan</div>
          </div>
          <div class="card-body">

<?php
if (isset($_GET['plan_id'])) {
    $planId = intval($_GET['plan_id']);

    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editplan&plan_id=" . $planId;

    $data = curl_get_contents($url);

    if ($data !== false) {
        $plandetails = json_decode($data);
        if (!empty($plandetails)) {
            $plan = $plandetails[0];

            // Extract values
            $name = $plan->name ?? '';
            $price = $plan->price ?? '0.00';
            $duration_days = $plan->duration_days ?? '365';

            // Feature toggles
            $vendor_profile_page = $plan->vendor_profile_page ?? 0;
            $homepage_visibility = $plan->homepage_visibility ?? 0;
            $featured_badge = $plan->featured_badge ?? 0;
            $dashboard_access = $plan->dashboard_access ?? 0;
            $messaging_system = $plan->messaging_system ?? 0;
            $directory_appearance = $plan->directory_appearance ?? 0;
            $review_response = $plan->review_response ?? 0;
            $highlighted_listing = $plan->highlighted_listing ?? 0;

            // Limits
            $product_limit = $plan->product_limit ?? '';
            $lead_request_limit = $plan->lead_request_limit ?? '';
            $portfolio_limit = $plan->portfolio_limit ?? '';
            $specialization_limit = $plan->specialization_limit ?? '';
            $article_limit = $plan->article_limit ?? '';
            $images = $plan->images ?? '';
            $videos = $plan->videos ?? '';
        } else {
            echo "<div class='alert alert-warning'>No plan found with this ID.</div>";
            include "footer.php";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch plan data.</div>";
        include "footer.php";
        exit;
    }
} else {
    header("Location: all-plans.php");
    exit;
}
?>

<form method="POST" class="updatePlanForm">
  <div id="messages"></div>

  <input type="hidden" name="id" value="<?php echo $planId; ?>">
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Plan Name *</label>
      <input type="text" name="name" class="form-control" required 
             value="<?php echo htmlspecialchars($name); ?>" 
             <?php echo (strtolower($name) === 'free') ? 'readonly' : ''; ?>>
    </div>
    <div class="col-md-3">
      <label class="form-label">Price (₦)</label>
      <input type="number" name="price" step="0.01" class="form-control"
             value="<?php echo htmlspecialchars($price); ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Duration (days)</label>
      <input type="number" name="duration_days" class="form-control"
             value="<?php echo htmlspecialchars($duration_days); ?>">
    </div>
  </div>

  <hr>
  <h6 class="fw-bold">Feature Toggles</h6>
  <div class="row">
    <?php
    $features = [
      'vendor_profile_page' => ['Vendor Profile Page', $vendor_profile_page],
      'homepage_visibility' => ['Visibility on Homepage & Search', $homepage_visibility],
      'featured_badge' => ['Featured Vendor Badge', $featured_badge],
      'dashboard_access' => ['Vendor Dashboard Access', $dashboard_access],
      'messaging_system' => ['Client Messaging System', $messaging_system],
      'directory_appearance' => ['Appearance in Directory', $directory_appearance],
      'review_response' => ['Response to Reviews', $review_response],
      'highlighted_listing' => ['Highlighted Listings on Category Pages', $highlighted_listing]
    ];

    foreach ($features as $field => [$label, $value]) {
      $checked = ($value == 1) ? 'checked' : '';
      echo "
      <div class='col-md-6 mb-2'>
        <div class='form-check'>
          <input type='checkbox' class='form-check-input' name='$field' id='$field' value='1' $checked>
          <label class='form-check-label' for='$field'>$label</label>
        </div>
      </div>";
    }
    ?>
  </div>

  <hr>
  <h6 class="fw-bold mt-3">Feature Limits (Use a number or 'unlimited')</h6>
  <div class="row">
    <?php
    $limits = [
      'product_limit' => ['Number of Products/Services', $product_limit],
      'lead_request_limit' => ['Lead Request Limit', $lead_request_limit],
      'portfolio_limit' => ['Portfolio Upload Limit', $portfolio_limit],
      'specialization_limit' => ['Areas of Specialization Limit', $specialization_limit],
      'article_limit' => ['Articles Limit', $article_limit],
      'images' => ['Number of Images', $images],
      'videos' => ['Number of Videos', $videos]
    ];

    foreach ($limits as $field => [$label, $value]) {
      echo "
      <div class='col-md-4 mb-3'>
        <label class='form-label'>$label</label>
        <input type='text' name='$field' class='form-control' 
               value='" . htmlspecialchars($value) . "' placeholder='e.g. 5 or unlimited' required>
      </div>";
    }
    ?>
  </div>

  <input type="hidden" name="action" value="updateplan">

  <button type="submit" id="submitBtn" class="btn btn-primary mt-3">
    <i class="fa fa-save"></i> Update Plan
  </button>
</form>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
