<?php include "header.php"; ?>
 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Booking Request</h3>
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
                  <a href="#">Booking Request</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Booking Request</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Booking Request</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
          
            <th>Booking Id</th>
            <th>Full Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Price</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
           <th>Booking Id</th>
            <th>Full Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Price</th>
          
          </tr>
        </tfoot>
  <tbody>
<?php
$url = $siteurl . "script/admin.php?action=get_service_bookings";
$data = curl_get_contents($url);

$modals_html = '';

if ($data !== false) {
    $books = json_decode($data);

    if (!empty($books)) {
        foreach ($books as $book) {
            // Only pending bookings for this seller
            if (isset($book->status) && strtolower($book->status) === 'pending' || strtolower($book->status) === 'inprogress' && $book->seller_user_id == $buyerId) {

                // sanitize / prepare fields
                $bookingId = htmlspecialchars($book->id ?? '', ENT_QUOTES);
                if ($bookingId === '') continue; // skip invalid id

                $order_id = htmlspecialchars($book->order_id ?? '', ENT_QUOTES);
                $listing_id = htmlspecialchars($book->listing_id ?? '', ENT_QUOTES);
                $variation = htmlspecialchars($book->variation ?? '', ENT_QUOTES);
                $user_id = htmlspecialchars($book->user_id ?? '', ENT_QUOTES);
                $full_name = htmlspecialchars($book->full_name ?? 'N/A', ENT_QUOTES);
                $contact = htmlspecialchars($book->contact ?? '', ENT_QUOTES);
                $email = htmlspecialchars($book->email ?? '', ENT_QUOTES);
                $preferred_datetime = htmlspecialchars($book->preferred_datetime ?? '', ENT_QUOTES);
                $location = htmlspecialchars($book->location ?? '', ENT_QUOTES);
                $notes_raw = $book->notes ?? '';
                $notes = htmlspecialchars($notes_raw, ENT_QUOTES);
                $price_val = (float)($book->price ?? 0);
                $price = number_format($price_val, 2);
                $date_raw = $book->date ?? '';
                $dateDisplay = $date_raw ? date('M d, Y h:i A', strtotime($date_raw)) : 'N/A';
                $status = htmlspecialchars(ucfirst($book->status ?? 'Pending'), ENT_QUOTES);
                $payment_status = htmlspecialchars(ucfirst($book->payment_status ?? 'Unpaid'), ENT_QUOTES);

                // Table row
                echo "<tr>
                        <td>{$order_id}</td>
                        <td>{$full_name}</td>
                        <td>{$dateDisplay}</td>
                        <td><span class='badge bg-warning'>{$status}</span></td>
                        <td>₦{$price}</td>
                        <td>
                          <button type='button' class='btn btn-link btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#bookingModal{$bookingId}'>
                            <i class='fa fa-eye'></i> View
                          </button>
                        </td>
                      </tr>";

                // Build modal HTML and append to buffer (outside table)
                // Use nl2br for notes when rendering inside modal body later
                $notes_html = nl2br(htmlspecialchars($notes_raw, ENT_QUOTES));
                $modals_html .= "
                <!-- Modal for booking {$bookingId} -->
                <div class='modal fade' id='bookingModal{$bookingId}' tabindex='-1' aria-labelledby='bookingModalLabel{$bookingId}' aria-hidden='true'>
                  <div class='modal-dialog modal-lg modal-dialog-centered'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title fw-bold' id='bookingModalLabel{$bookingId}'>Booking Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                      </div>
                      <div class='modal-body'>
                        <table class='table table-bordered table-striped'>
                          <tbody>
                            <tr><th>Order ID</th><td>{$order_id}</td></tr>
                            <tr><th>Full Name</th><td>{$full_name}</td></tr>
                            <tr><th>Contact</th><td>{$contact}</td></tr>
                            <tr><th>Email</th><td>{$email}</td></tr>
                            <tr><th>Preferred Date/Time</th><td>{$preferred_datetime}</td></tr>
                            <tr><th>Location</th><td>{$location}</td></tr>
                            <tr><th>Notes</th><td>{$notes_html}</td></tr>
                            <tr><th>Price</th><td>₦{$price}</td></tr>
                            <tr><th>Date</th><td>{$dateDisplay}</td></tr>
                            <tr><th>Status</th><td>{$status}</td></tr>
                            <tr><th>Payment Status</th><td>{$payment_status}</td></tr>
                          </tbody>
                        </table>
                      </div>
                      <div class='modal-footer flex-column align-items-stretch'>
            <!-- Hidden rejection textarea -->
            <div class='rejection-section w-100 mb-2' style='display:none;'>
                <label for='reason_$bookingId' class='form-label fw-bold'>Reason for Rejection</label>
                <textarea id='reason_$bookingId' class='form-control' rows='3' placeholder='Enter rejection reason...'></textarea>
                <div class='mt-2 text-end'>
                <button type='button' class='btn btn-danger btn-sm confirm-reject' data-id='$bookingId'>Submit Rejection</button>
                <button type='button' class='btn btn-secondary btn-sm cancel-reject'>Cancel</button>
                </div>
            </div>

            <!-- Main action buttons -->
            <div class='action-buttons w-100 text-end'>
                <a href='#' id='$bookingId' class='btn btn-success approve-booking'><i class='fa fa-check'></i> Approve</a>
                <a href='#' id='$bookingId' class='btn btn-danger reject-booking'><i class='fa fa-times'></i> Reject</a>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
            </div>
            </div>

                    </div>
                  </div>
                </div>
                ";
            }
        }
    } else {
        echo "<tr><td colspan='6' class='text-center text-muted'>No bookings found.</td></tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-danger'>Unable to fetch data.</td></tr>";
}
?>
</tbody>
</table> <!-- close your table here -->

<?php
// Print all modals AFTER the table (important!)
echo $modals_html;
?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
<?php include "footer.php"; ?>