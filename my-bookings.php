<?php 
$requireLogin = true;
include "header.php"; 
?>

<div class="container">
  <div class="page-inner">
    <div class="row">
      <div class="col-md-12">
        <div class="card mt-5 mb-5">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">All Bookings</h4>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Therapist Name</th>
                    <th>Consultation Date</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
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
                              if ($booking->user_id == $buyerId) {

                                  $bookingId   = $booking->id;
                                  $therapistId = $booking->therapist_id;
                                  $therapistName = trim(($booking->first_name ?? '') . ' ' . ($booking->last_name ?? '')) ?: 'N/A';
                                  $consultDate = date('M d, Y h:i A', strtotime($booking->consultation_date));
                                  $amount      = number_format((float)$booking->amount, 2);
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
                                    <td><?php echo $therapistName; ?></td>
                                    <td><?php echo $consultDate; ?></td>
                                    <td><span class="badge bg-<?php echo $bookingBadge; ?>"><?php echo ucfirst($bookingStatus); ?></span></td>
                                    <td><span class="badge bg-<?php echo $paymentBadge; ?>"><?php echo ucfirst($paymentStatus); ?></span></td>
                                    <td><?php echo $sitecurrency . $amount; ?></td>
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
