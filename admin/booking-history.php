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
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Bookings</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Booking History</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Booking History</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Vendor </th>
                    <th>Client Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Price</th>
                    <th></th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>Booking ID</th>
                     <th>Vendor </th>
                    <th>Client Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
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
                          $now = date('Y-m-d H:i:s');
                          $hasRows = false;

                          foreach ($books as $book) {
                              // Only show bookings for this seller that have passed
                              if (isset($book->date)) {

                                  $hasRows = true;
                                  $bookingId = htmlspecialchars($book->id ?? '', ENT_QUOTES);
                                  if ($bookingId === '') continue;
                                   $sellername = $book-> seller_first_name.' '.$book->seller_last_name;
                                  $order_id = htmlspecialchars($book->order_id ?? '', ENT_QUOTES);
                                  $full_name = htmlspecialchars($book->full_name ?? 'N/A', ENT_QUOTES);
                                  $contact = htmlspecialchars($book->contact ?? '', ENT_QUOTES);
                                  $email = htmlspecialchars($book->email ?? '', ENT_QUOTES);
                                  $preferred_datetime = htmlspecialchars($book->preferred_datetime ?? '', ENT_QUOTES);
                                  $location = htmlspecialchars($book->location ?? '', ENT_QUOTES);
                                  $notes_raw = $book->notes ?? '';
                                  $notes_html = nl2br(htmlspecialchars($notes_raw, ENT_QUOTES));
                                  $price_val = (float)($book->price ?? 0);
                                  $price = number_format($price_val, 2);
                                  $date_raw = $book->date ?? '';
                                  $dateDisplay = $date_raw ? date('M d, Y h:i A', strtotime($date_raw)) : 'N/A';

                                  $status = ucfirst(strtolower($book->status ?? 'Pending'));
                                  $payment_status = strtolower($book->payment_status ?? 'Unpaid');

                                  // Color classes based on status
                                  $status_class = 'bg-secondary';
                                  if ($status === 'Approved') $status_class = 'bg-success';
                                  elseif ($status === 'Pending') $status_class = 'bg-warning';
                                  elseif ($status === 'Rejected') $status_class = 'bg-danger';
                                  elseif ($status === 'Inprogress') $status_class = 'bg-info';

                                  // Payment badge
                                  $payment_class = ($payment_status === 'Paid') ? 'bg-success' : 'bg-warning';

                                  // Table row
                                  echo "<tr>
                                          <td>{$order_id}</td>
                                          <td>{$sellername}</td>
                                          <td>{$full_name}</td>
                                          <td>{$dateDisplay}</td>
                                          <td><span class='badge {$status_class}'>{$status}</span></td>
                                          <td><span class='badge {$payment_class}'>{$payment_status}</span></td>
                                          <td>₦{$price}</td>
                                          <td>
                                            <button type='button' class='btn btn-link btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#bookingModal{$bookingId}'>
                                              <i class='fa fa-eye'></i> View
                                            </button>
                                          </td>
                                        </tr>";

                                  // Build modal HTML
                                  $modals_html .= "
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
                                                <tr><th>Vendor</th><td>{$sellername}</td></tr>
                                              <tr><th>Full Name</th><td>{$full_name}</td></tr>
                                              <tr><th>Contact</th><td>{$contact}</td></tr>
                                              <tr><th>Email</th><td>{$email}</td></tr>
                                              <tr><th>Preferred Date/Time</th><td>{$preferred_datetime}</td></tr>
                                              <tr><th>Location</th><td>{$location}</td></tr>
                                              <tr><th>Notes</th><td>{$notes_html}</td></tr>
                                              <tr><th>Price</th><td>₦{$price}</td></tr>
                                              <tr><th>Date</th><td>{$dateDisplay}</td></tr>
                                              <tr><th>Status</th><td><span class='badge {$status_class}'>{$status}</span></td></tr>
                                              <tr><th>Payment Status</th><td><span class='badge {$payment_class}'>{$payment_status}</span></td></tr>
                                            </tbody>
                                          </table>
                                        </div>
                                        <div class='modal-footer'>
                                          <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>";
                              }
                          }

                          if (!$hasRows) {
                              echo "<tr><td colspan='7' class='text-center text-muted'>No past bookings found.</td></tr>";
                          }

                       
                      } else {
                          echo "<tr><td colspan='7' class='text-center text-muted'>No booking data available.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='7' class='text-center text-danger'>Unable to fetch data.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
              
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
