
<?php 
$requireLogin = true;
include "header.php";

?>
<?php
// Get data from URL
$listing_id = $_GET['listing_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$variation = $_GET['variation'] ?? '';
$price = $_GET['price'] ?? '';
$order_id = $_GET['order_id'] ?? 1;
?>

<section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card p-4 shadow-sm">

            <h3 class="mb-4 text-center">Book Services</h3>
<form id="serviceBookingForm" method="POST">

  <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing_id); ?>">
  <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
  <input type="hidden" name="variation" value="<?php echo $variation; ?>">
  <input type="hidden" name="price" value="<?php echo $price; ?>">
  <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

  <div class="col-lg-12 text-center mt-1" id="messages"></div>

  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" value="<?php echo $buyerName; ?>" required readonly>
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required readonly value="<?php echo $buyerEmail; ?> ">
  </div>

  <div class="form-group">
    <label> Phone</label>
    <input type="number" name="contact" class="form-control" value="<?php echo $phone; ?> ">
  </div>

  <div class="form-group">
    <label>Preferred Date & Time</label>
    <input type="datetime-local" name="datetime" class="form-control" required>
  </div>


  <input type="hidden" name="action" value="book-service">
  <div class="form-group">
    <label>Location (if applicable)</label>
    <input type="text" name="location" class="form-control">
  </div>

  <div class="form-group">
    <label>Notes / Special Requests</label>
    <textarea name="notes" class="editor"></textarea>
  </div>

  <button type="submit" class="btn btn-success" id="submit-btn">Confirm Booking</button>
</form>

</div>
</div>
<div class="col-lg-4">
  <!-- Existing sidebar content (if any) -->

  <div class="booking-warning-box mt-4 p-3">
    <strong>IMPORTANT:</strong> Your booking is <span class="text-danger fw-bold">NOT secured</span> until your booking is approved and payment is verified.  
    A payment link will be sent to your email — please follow that link to complete your payment.  
    <br><br>
    If payment is not verified within the specified time, your booking will be <span class="text-danger fw-bold">canceled</span> and your slot immediately released to other clients.
  </div>

  <!-- You can keep your buttons or summary info here -->
</div>
</div>
</div>
</section>
<?php include "footer.php"; ?>