<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Active Adverts</h3>
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
          <a href="#">Active Adverts</a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">All Adverts</a>
        </li>
      </ul>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">All Adverts</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>

                  <th>Name </th>
                    <th>Title</th>
                    <th>Size</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>Name </th>
                    <th>Title</th>
                    <th>Size</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=useradvertlists";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                      $adverts = json_decode($data);
                      $today = date('Y-m-d');

                      if (!empty($adverts)) {
                          foreach ($adverts as $advert) {

                              // Only show adverts that have not expired
                              if (isset($advert->end_date) && $advert->end_date < $today) continue;

                              $advertId      = $advert->id ?? '';
                              $name          = $advert->first_name . ' ' . $advert->last_name;
                              $title         = $advert->placement_name ?? '';
                              $size          = $advert->size ?? '';
                              $start_date    = formatDateTime($advert->start_date ?? '');
                              $end_date      = formatDateTime($advert->end_date ?? '');
                              $status        = $advert->status ?? '';
                              $price_per_day = $advert->price_per_day ?? '';
                              $bannerUrl     = !empty($advert->banner) ? ($siteurl . $imagePath . $advert->banner) : '';

                              ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($name); ?></td>
                                  <td><?php echo htmlspecialchars($title); ?></td>
                                  <td><?php echo htmlspecialchars($size); ?></td>
                                  <td><?php echo htmlspecialchars($start_date); ?></td>
                                  <td><?php echo htmlspecialchars($end_date); ?></td>
                                  <td><span class="badge bg-<?= getBadgeColor($status) ?>"><?= htmlspecialchars($status) ?></span></td>
                                  <td>
                                    
                                      <a href="#" id="<?php echo $advertId; ?>" class="btn btn-link btn-danger approveadvert" data-bs-toggle="tooltip" title="approve advert">
                                          <i class="fa fa-check"></i>Approve
                                      </a>
                                      
                                      <div class="modal fade" id="rejectModal<?php echo $advertId; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reason for Rejection</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form class="reject-advert-form" data-advert-id="<?php echo $advertId; ?>">
                                                <div class="modal-body">
                                                    <textarea name="reject_reason" class="form-control" placeholder="Enter reason for rejection..." required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Reject Advert</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                    <!-- Reject Button triggers modal -->
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $advertId; ?>">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                       
                                      <?php if ($bannerUrl): ?>
                                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bannerModal<?php echo $advertId; ?>">View Banner</button>

                                      <!-- Banner Modal -->
                                      <div class="modal fade" id="bannerModal<?php echo $advertId; ?>" tabindex="-1">
                                          <div class="modal-dialog modal-dialog-centered modal-lg">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h5 class="modal-title">Banner Preview</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                  </div>
                                                  <div class="modal-body text-center">
                                                      <img src="<?php echo $bannerUrl; ?>" class="img-fluid" style="max-height:70vh; object-fit:contain;">
                                                  </div>
                                                  <div class="modal-footer">
                                                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <?php endif; ?>
                                  </td>
                              </tr>
                              <?php
                          }
                      }
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
</div><!-- Reject Modal -->

<?php include "footer.php"; ?>
