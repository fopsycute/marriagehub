<?php include "header.php"; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5 text-center">

          <h3 class="mb-3 fw-bold text-primary">
            <i class="fas fa-layer-group me-2"></i> Subscription Plan
          </h3>

          <hr class="my-4">

          <?php if (strtolower($subscription_status) == 'free'): ?>
            
            <div class="mb-4">
              <h4 class="fw-bold text-secondary">You’re currently on the <span class="text-primary">Free Plan</span>.</h4>
              <p class="text-muted mt-2">Upgrade your plan to unlock more premium features and priority support.</p>
            </div>

            <div class="d-grid">
              <a href="<?= htmlspecialchars($siteurl . $myslug); ?>" class="btn btn-primary btn-lg rounded-pill">
                <i class="fas fa-arrow-up me-2"></i> Upgrade Plan
              </a>
            </div>

          <?php else: ?>

            <div class="mb-4">
              <h5 class="text-muted mb-1">Current Plan</h5>
              <h4 class="fw-bold text-success"><?= htmlspecialchars($subscription_status); ?></h4>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-light">
                  <h6 class="text-muted mb-1">Start Date</h6>
                  <p class="fw-semibold mb-0">
                    <?= date("F j, Y", strtotime($subscription_start)); ?>
                  </p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-light">
                  <h6 class="text-muted mb-1">Expiration Date</h6>
                  <p class="fw-semibold mb-0 text-danger">
                    <?= date("F j, Y", strtotime($subscription_end)); ?>
                  </p>
                </div>
              </div>
            </div>

            <div class="d-grid">
              <a href="<?= htmlspecialchars($siteurl . "vendor-pricing/" . $myslug); ?>" class="btn btn-primary btn-lg rounded-pill">
                <i class="fas fa-arrow-up me-2"></i> Manage Plan
              </a>
            </div>

          <?php endif; ?>

        </div>
      </div>

    </div>
  </div>
</div>

<?php include "footer.php"; ?>
