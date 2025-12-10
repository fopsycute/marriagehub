

<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Group / Tribe</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Group Details</div>
          </div>
          <div class="card-body">
     <form method="POST" class="addsubscription" >
         <div id="messages"></div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Plan Name *</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Classic, Premium" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Price (₦)</label>
            <input type="number" name="price" step="0.01" class="form-control" value="0.00">
          </div>
          <div class="col-md-3">
            <label class="form-label">Duration (days)</label>
            <input type="number" name="duration_days" class="form-control" value="365">
          </div>
        </div>

        <hr>
        <h6 class="fw-bold">Feature Toggles</h6>
        <div class="row">
          <?php
          $features = [
            'vendor_profile_page' => 'Vendor Profile Page',
            'homepage_visibility' => 'Visibility on Homepage & Search',
            'featured_badge' => 'Featured Vendor Badge',
            'dashboard_access' => 'Vendor Dashboard Access',
            'messaging_system' => 'Client Messaging System',
            'directory_appearance' => 'Appearance in Directory',
            'review_response' => 'Response to Reviews',
            'highlighted_listing' => 'Highlighted Listings on Category Pages'
          ];
          foreach ($features as $field => $label) {
            echo "
            <div class='col-md-6 mb-2'>
              <div class='form-check'>
                <input type='checkbox' class='form-check-input' name='$field' id='$field'>
                <label class='form-check-label' for='$field'>$label</label>
              </div>
            </div>";
          }
          ?>
        </div>

        <hr>
        <h6 class="fw-bold mt-3">Feature Limits (Use a number or type 'unlimited')</h6>
        <div class="row">
          <?php
          $limits = [
            'product_limit' => 'Number of Products/Services',
            'lead_request_limit' => 'Lead Request Limit',
            'portfolio_limit' => 'Portfolio Upload Limit',
            'specialization_limit' => 'Areas of Specialization Limit',
            'article_limit' => 'Articles Limit',
            'images' => 'Number of Images',
            'videos' => 'Number of Videos'
          ];
          foreach ($limits as $field => $label) {
            echo "
            <div class='col-md-4 mb-3'>
              <label class='form-label'>$label</label>
              <input type='text' name='$field' class='form-control' placeholder='e.g. 5 or unlimited' required>
            </div>";
          }
          ?>
        </div>
        <input type="hidden" name="action" value="subscription_plans">

        <button type="submit" id="submitBtn" class="btn btn-success mt-3">Save Plan</button>
      </form>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

<?php include "footer.php"; ?>