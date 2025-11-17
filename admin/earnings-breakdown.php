
<?php include "header.php"; ?>

<?php
// Fetch booking details if booking_id or reference provided
$booking = null;
$therapist = null;
if (isset($_GET['booking_id']) || isset($_GET['reference'])) {
  $sitelink = $siteurl . "script/";
  if (!empty($_GET['booking_id'])) {
    $bookingId = urlencode($_GET['booking_id']);
    $url = $sitelink . "admin.php?action=editbooking&booking_id=" . $bookingId;
  } else {
    // try to get by reference via user endpoint that supports reference lookup
    $reference = urlencode($_GET['reference']);
    // script/user.php has an endpoint that looks up by reference; fall back to search in admin if needed
    $url = $sitelink . "admin.php?action=editbooking&booking_id=" . $reference;
  }

  $data = curl_get_contents($url);
  if ($data !== false) {
    $decoded = json_decode($data, true);
    if (!empty($decoded) && isset($decoded[0])) {
      $booking = $decoded[0];
      // If therapist id present, fetch therapist user details
      $therapist_id = $booking['therapist_id'] ?? $booking['therapistID'] ?? null;
      if (!empty($therapist_id)) {
        $url2 = $sitelink . "admin.php?action=edittherapist&therapist_id=" . urlencode($therapist_id);
        $tdata = curl_get_contents($url2);
        if ($tdata !== false) {
          $tdecoded = json_decode($tdata, true);
          if (!empty($tdecoded) && isset($tdecoded[0])) {
            $therapist = $tdecoded[0];
          }
        }
      }
    }
  }
}
?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Earnings Breakdown</h3>
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
                  <a href="#">Earnings Breakdown</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Earnings Breakdown</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Earnings Breakdown</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                      <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Service</th>
            <th>Client</th>
            <th>User</th>
            <th>Total Amount</th>
            <th>Marriage.ng Cut (20%)</th>
            <th>Net Earnings</th>
            <th>Date</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Transaction ID</th>
            <th>Service</th>
            <th>Client</th>
            <th>User</th>
            <th>Total Amount</th>
            <th>Marriage.ng Cut (20%)</th>
            <th>Net Earnings</th>
            <th>Date</th>
          </tr>
        </tfoot>
        <tbody>
        <?php if (!empty($booking)): ?>
          <?php
            $amount = floatval($booking['amount'] ?? 0);
            $platform_cut = round($amount * 0.20, 2);
            $net = round($amount - $platform_cut, 2);
            $transaction = htmlspecialchars($booking['reference'] ?? ('#' . ($booking['id'] ?? 'N/A')));
            $service = htmlspecialchars($booking['service_name'] ?? 'Therapy Session');
            $client = htmlspecialchars($booking['client_name'] ?? $booking['client'] ?? 'N/A');
            $therapistName = 'N/A';
            if (!empty($therapist)) {
                $therapistName = htmlspecialchars(trim(($therapist['first_name'] ?? '') . ' ' . ($therapist['last_name'] ?? '')) ?: ($therapist['first_name'] ?? 'N/A'));
            } else {
                // fallback: booking may include therapist names
                $therapistName = htmlspecialchars(trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'N/A');
            }
            $date = htmlspecialchars(date('Y-m-d', strtotime($booking['created_at'] ?? $booking['consultation_date'] ?? 'now')));
          ?>
          <tr>
            <td><?php echo $transaction; ?></td>
            <td><?php echo $service; ?></td>
            <td><?php echo $client; ?></td>
            <td><?php echo $therapistName; ?></td>
            <td><?php echo "₦" . number_format($amount, 2); ?></td>
            <td><?php echo "₦" . number_format($platform_cut, 2); ?></td>
            <td><?php echo "₦" . number_format($net, 2); ?></td>
            <td><?php echo $date; ?></td>
          </tr>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No booking selected. Provide a booking_id or reference in the URL.</td></tr>
        <?php endif; ?>
        </tbody>
        </table>


          </div>
        </div>
      </div>
    </div>
  </div>
  </div>



<?php include "footer.php"; ?>