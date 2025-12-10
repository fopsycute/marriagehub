<?php include "header.php"; ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Bookings</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Bookings</div>
          </div>
          <div class="card-body">

          <?php
if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editbooking&booking_id=" . $bookingId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $bookingdetails = json_decode($data);
        if (!empty($bookingdetails)) {
            $bookingdetail = $bookingdetails[0]; 
            $message = $bookingdetail->message;
            $therapistId   = $bookingdetail->therapist_id;
$client_name   = $bookingdetail->client_name;
$client_email  = $bookingdetail->client_email;
$therapistName = trim(($bookingdetail->first_name ?? '') . ' ' . ($bookingdetail->last_name ?? '')) ?: 'N/A';
$consultDate   = date('M d, Y h:i A', strtotime($bookingdetail->consultation_date));
$amount        = number_format((float)$bookingdetail->amount, 2);
$reference     = htmlspecialchars($bookingdetail->reference ?? 'N/A');

$booking_status = strtolower(trim($bookingdetail->booking_status ?? 'unknown'));
$paymentStatus = strtolower(trim($bookingdetail->payment_status ?? 'unknown'));

        } else {
            echo "<div class='alert alert-warning'>No booking found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching booking data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

?>
 <form method="POST" id="editBookingForm">
      <div class="col-lg-12 text-center mt-1" id="messages"></div> 
    <!-- Client Info -->
    <div class="mb-3">
        <label class="form-label">Client Name</label>
        <input type="text" name="client_name" class="form-control"
               value="<?php echo  $client_name; ?>" readonly>
    </div>
<input type="hidden" name="action" value="updatebooking">
    <div class="mb-3">
        <label class="form-label">Client Email</label>
        <input type="email" name="client_email" class="form-control"
               value="<?php echo $client_email; ?>" required  readonly>
    </div>

    <div class="mb-3">

 <label class="form-label">Message</label>
    <textarea class="editor" value="<?php echo $message; ?>" name= "message"> </textarea>

</duv>

    <!-- Booking Status -->
    <div class="mb-3">
    <label class="form-label">Booking Status</label>
    <select name="booking_status" id="booking_status" class="form-select" required>
        <option value="">Select Status</option>

        <option value="pending" 
            <?php echo ($booking_status === 'pending') ? 'selected' : ''; ?>>
            Pending
        </option>

        <option value="confirmed" 
            <?php echo ($booking_status === 'confirmed') ? 'selected' : ''; ?>>
            Confirmed
        </option>

        <option value="in progress" 
            <?php echo ($booking_status === 'in progress') ? 'selected' : ''; ?>>
            In Progress
        </option>

        <option value="cancelled" 
            <?php echo ($booking_status === 'cancelled') ? 'selected' : ''; ?>>
            Cancelled
        </option>

        <option value="completed" 
            <?php echo ($booking_status === 'completed') ? 'selected' : ''; ?>>
            Completed
        </option>
    </select>
</div>



    <!-- Consultation Date -->
    <div class="mb-3">
        <label class="form-label">Consultation Date</label>
        <input 
            type="datetime-local" 
            name="consultation_date" 
            class="form-control" 
            value="<?php echo isset($consultDate) ? date('Y-m-d\TH:i', strtotime($consultDate)) : ''; ?>" 
            required>
    </div>

    <!-- Reason (hidden by default) -->
    <div class="mb-3" id="reasonContainer" style="display:none;">
        <label class="form-label" id="reasonLabel">Reason for Status Update</label>
        <textarea name="status_reason" id="status_reason" class="form-control"
                  rows="4" placeholder="Please provide your reason..."></textarea>
    </div>

    <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">

    <div class="text-center">
        <button type="submit" class="btn btn-primary px-5 py-2"  id="submitBtn">Update Booking</button>
    </div>
</form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>