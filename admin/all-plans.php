<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">All Vendors Plan</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#">
            <i class="icon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">All Vendors Plan</a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">All Vendors Plan</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">All Vendors Plan</h4>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    
                  </tr>
                </tfoot>

                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=planlists";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                      $plans = json_decode($data);

                      if (!empty($plans)) {
                          foreach ($plans as $plan) {
                              $planId = $plan->id;
                              $name   = $plan->name;
                              $price  = $plan->price;

                              // Prevent delete button for Free plans
                              $isFree = (strtolower(trim($name)) === 'free');
                              ?>
                              <tr>
                                <td><?php echo htmlspecialchars($name); ?></td>
                                <td><?php echo htmlspecialchars($price); ?></td>
                                <td>
                                  <a href="edit-plan.php?plan_id=<?php echo $planId; ?>" 
                                     class="btn btn-link btn-primary btn-lg" 
                                     data-bs-toggle="tooltip" title="Edit">
                                    <i class="fa fa-edit"></i>
                                  </a>

                                  <?php if (!$isFree): ?>
                                    <a href="#" id="<?php echo $planId; ?>" 
                                       class="btn btn-link btn-danger deleteplan" 
                                       data-bs-toggle="tooltip" title="Delete">
                                      <i class="fa fa-trash"></i>
                                    </a>
                                  <?php endif; ?>
                                </td>
                              </tr>
                              <?php
                          }
                      } else {
                          echo "<tr><td colspan='3' class='text-center text-muted'>No plans found.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='3' class='text-center text-danger'>Error loading plans.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
