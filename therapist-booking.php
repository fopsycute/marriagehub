
<?php include "header.php"; ?>

<?php
if (isset($_GET['booking_id']) && !empty($_GET['booking_id'])) {
    $booking_id = trim($_GET['booking_id']);

    // ✅ API URL for booking order data
    $url = $siteurl . "script/user.php?action=gettherapistbookingorder&booking_id=" . urlencode($booking_id);
    $data = curl_get_contents($url);

    if ($data !== false) {
        $orderDetails = json_decode($data, true);

        if (is_array($orderDetails) && !empty($orderDetails)) {
            $order = $orderDetails[0];

            // ✅ Check if order status is "inprogress"
            if (isset($order['booking_status']) && $order['booking_status'] === 'confirmed' && $order['payment_status'] === 'unpaid') {
                
            $therapist_name = htmlspecialchars($order['therapist_name']);
            $client_name = htmlspecialchars($order['client_name']);
            $client_email = htmlspecialchars($order['client_email']);
            $price = floatval($order['price']);
            $reference = htmlspecialchars($order['reference']);
            } else {
                echo "<div class='alert alert-warning text-center'>This order is not confirmed or cannot be paid for.</div>";
                exit; // Stop further execution
            }

        } else {
            echo "<div class='alert alert-warning text-center'>No booking found with this Booking ID.</div>";
            exit;
        }

    } else {
        echo "<div class='alert alert-danger text-center'>Unable to fetch booking data.</div>";
        exit;
    }

} else {
    echo "<div class='alert alert-danger text-center'>Order ID is missing from the URL.</div>";
    exit;
}
?>
 <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center mb-4 header-text">Booking Payment Summary</h4>
                    <table class="table table-borderless mb-4">
                        <tr>
                            <th>Therapist:</th>
                            <td><?php echo $therapist_name; ?></td>
                        </tr>
                        <tr>
                            <th>Client Name:</th>
                            <td><?php echo $client_name; ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $client_email; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Reference:</th>
                            <td><?php echo $reference; ?></td>
                        </tr>
                        <tr>
                            <th>Amount:</th>
                            <td class="price"><?php echo $sitecurrency . number_format($price, 2); ?></td>
                        </tr>
                    </table>
                <input type="hidden" id="client_email" value="<?php echo $client_email; ?>">
                <input type="hidden" id="booking_amount" value="<?php echo $price; ?>">
                <input type="hidden" id="reference" value="<?php echo $reference; ?>">

                    <div class="text-center">
                        <button id="paystackBtn" class="btn btn-primary w-100">
                            <i class="bi bi-credit-card"></i> Pay with Paystack
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php include "footer.php"; ?>