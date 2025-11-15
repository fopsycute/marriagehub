<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Earnings Breakdown</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Earnings</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Breakdown</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Earnings Breakdown</h4>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Source Type</th>
                    <th>Source Name</th>
                    <th>Amount (₦)</th>
                    <th>Profit (₦)</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                   <tfoot>
                  <tr>
                    <th>S/N</th>
                    <th>Source Type</th>
                    <th>Source Name</th>
                    <th>Amount (₦)</th>
                    <th>Profit (₦)</th>
                    <th>Type</th>
                    <th>Date</th>
               
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=earnings_breakdown";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                      $earnings = json_decode($data, true);

                      if (!empty($earnings) && is_array($earnings)) {
                          $count = 1;
                          foreach ($earnings as $item) {
                              $source = htmlspecialchars($item['source']);
                              $source_name = htmlspecialchars($item['source_name']);
                              $source_amount = number_format((float) str_replace(',', '', $item['source_amount']), 2);
                              $earned_amount = number_format((float) str_replace(',', '', $item['earned_amount']), 2);
                              $type = htmlspecialchars($item['type']);
                              $date = htmlspecialchars($item['date']);
                              $id = htmlspecialchars($item['id'] ?? $count);
                              echo "
                              <tr>
                                  <td>{$count}</td>
                                  <td>{$source}</td>
                                  <td>{$source_name}</td>
                                  <td>₦{$source_amount}</td>
                                  <td><strong class='text-success'>₦{$earned_amount}</strong></td>
                                  <td><span class='badge bg-info'>{$type}</span></td>
                                  <td>{$date}</td>
                                  <td>
                                    <button 
                                      type='button' 
                                      class='btn btn-primary btn-sm' 
                                      data-bs-toggle='modal' 
                                      data-bs-target='#viewDetailsModal{$id}'>
                                      <i class='fa fa-eye'></i>
                                    </button>
                                  </td>
                              </tr>

                              <!-- Individual Modal for Each Row -->
                              <div class='modal fade' id='viewDetailsModal{$id}' tabindex='-1' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                  <div class='modal-content'>
                                    <div class='modal-header bg-primary text-white'>
                                      <h5 class='modal-title'>Earning Details</h5>
                                      <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                                    </div>
                                    <div class='modal-body'>
                                      <p><strong>Source Type:</strong> {$source}</p>
                                      <p><strong>Name:</strong> {$source_name}</p>
                                      <p><strong>Total Amount:</strong> ₦{$source_amount}</p>
                                      <p><strong>Profit (Earned):</strong> ₦{$earned_amount}</p>
                                      <p><strong>Type:</strong> {$type}</p>
                                      <p><strong>Date:</strong> {$date}</p>
                                    </div>
                                    <div class='modal-footer'>
                                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              ";
                              $count++;
                          }
                      } else {
                          echo "<tr><td colspan='8' class='text-center text-muted'>No earnings found.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='8' class='text-center text-danger'>Error fetching data.</td></tr>";
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
