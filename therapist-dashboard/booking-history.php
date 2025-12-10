


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Booking History</h3>
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
                  <a href="#">My Bookings</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">My Bookings</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">My Bookings</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                             <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Date</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                    <th>Amount Paid</th>
                    <th></th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=bookinglist";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                      $bookings = json_decode($data);

                      if (!empty($bookings)) {
                          foreach ($bookings as $booking) {
                              // ✅ Only show records belonging to the logged-in user
                              if ($booking->therapist_id == $buyerId) {

                                  $bookingId   = $booking->id;
                                  $therapistId = $booking->therapist_id;
                                  $client_name = $booking->client_name;
                                  $client_email = $booking->client_email;
                                  $therapistName = trim(($booking->first_name ?? '') . ' ' . ($booking->last_name ?? '')) ?: 'N/A';
                                  $consultDate = date('M d, Y h:i A', strtotime($booking->consultation_date));
                                  $amount      = $sitecurrency . number_format((float)$booking->amount, 2);
                                  $reference   = htmlspecialchars($booking->reference ?? 'N/A');

                                  $bookingStatus = strtolower(trim($booking->booking_status ?? 'unknown'));
                                  $paymentStatus = strtolower(trim($booking->payment_status ?? 'unknown'));

                                  // ✅ Status color badges
                                  $bookingBadge = match ($bookingStatus) {
                                      'pending'   => 'warning',
                                      'confirmed' => 'success',
                                      'cancelled' => 'danger',
                                      default     => 'secondary'
                                  };

                                  $paymentBadge = match ($paymentStatus) {
                                      'paid'    => 'success',
                                      'unpaid'  => 'danger',
                                      'pending' => 'warning',
                                      default   => 'secondary'
                                  };
                                  ?>
                                  <tr>
                                    <td><?php echo $reference; ?></td>
                                     <td><?php echo $client_name; ?></td>
                                     <td><?php echo $client_email; ?></td>
                                    <td><?php echo $consultDate; ?></td>
                                    <td><span class="badge bg-<?php echo $bookingBadge; ?>"><?php echo ucfirst($bookingStatus); ?></span></td>
                                    <td><span class="badge bg-<?php echo $paymentBadge; ?>"><?php echo ucfirst($paymentStatus); ?></span></td>
                                    <td><?php echo $amount; ?></td>
                                        <?php
                                      echo "
                                      <td>
                                          <a href='edit-booking.php?booking_id=$bookingId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                                              <i class='fa fa-edit'></i> 
                                          </a>
                                          <a href='#' id='$bookingId' class='btn btn-link btn-danger  deletebooking' data-bs-toggle='tooltip' title='Delete'>
                                              <i class='fa fa-trash'></i>
                                          </a>
                                      </td>";
                                      ?>
                                  </tr>
                                  <?php
                              }
                          }
                      } else {
                          echo "<tr><td colspan='6' class='text-center text-muted'>No bookings found.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='6' class='text-center text-danger'>Failed to fetch data from server.</td></tr>";
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