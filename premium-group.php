<?php include "header.php"; ?>

<?php

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $group_id;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 
            $group_id  = $groupdetail->id ?? '';
            // Extract details
            $group_name = $groupdetail->group_name ?? '';
            $group_description = $groupdetail->group_description ?? '';
            $status = $groupdetail->status ?? '';
            $group_type = $groupdetail->group_type ?? '';
            $group_access = $groupdetail->group_access ?? '';
            $fee_1m = $groupdetail->fee_1m ?? '';
            $fee_3m = $groupdetail->fee_3m ?? '';
            $fee_6m = $groupdetail->fee_6m ?? '';
            $fee_12m = $groupdetail->fee_12m ?? '';
            $group_rules = $groupdetail->group_rules ?? '';
            $groupCreatorId = $groupdetail->user_id ?? '';
            $agree_commission = $groupdetail->agree_commission ?? '';
            $agree_guidelines = $groupdetail->agree_guidelines ?? '';
            $agree_terms = $groupdetail->agree_terms ?? '';
            // Convert the blog’s stored category/subcategory values into arrays
            $category = $groupdetail->category_names ?? '';
            $subcategory = $groupdetail->subcategory_names ?? '';
            $created_at = $groupdetail->created_at ?? '';
            $banner = $groupdetail->banner ?? '';
            $bannerimage = $siteurl . $imagePath . $banner;
        } else {
            echo "<div class='alert alert-warning'>No group found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching group data. Please try again later.</div>";
    }
} else {
    header("Location: $siteurl");
    exit;
}
?>

<section class="pricing-section py-5">
  <div class="container" data-aos="fade-up">
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2><?php echo $group_name; ?></h2>
          <p></p>
        </div>
      </div><!-- End Section Title -->
   
    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Duration</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $plans = [
              '1 Month' => $fee_1m,
              '3 Months' => $fee_3m,
              '6 Months' => $fee_6m,
              '12 Months' => $fee_12m
          ];
          foreach ($plans as $duration => $price):
              if ($price > 0):
          ?>
          <tr>
            <td><?php echo $duration; ?></td>
            <td><?php echo number_format($price, 2); ?></td>
            <td>
              <?php if ($activeLog == 1): ?>
                  <button class="btn btn-primary payButton"
                      data-group-id="<?= $group_id ?>"
                      data-amount="<?= $price ?>"
                      data-group-name="<?= htmlspecialchars($group_name, ENT_QUOTES) ?>"
                      data-user-id="<?= $buyerId ?>"
                      data-email="<?= $buyerEmail ?>"
                      data-duration="<?= htmlspecialchars($duration, ENT_QUOTES) ?>">
                      <i class="bi bi-credit-card"></i> Subscribe
                  </button>
              <?php else: ?>
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                      <i class="bi bi-lock"></i> Subscribe
                  </button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endif; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Modal for login prompt -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-3">You need to be logged in to subscribe to a group.</p>
        <a href="<?php echo $siteurl; ?>login.php" class="btn btn-primary w-100">
          Go to Login
        </a>
      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>