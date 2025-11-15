<?php
include "header.php"; // includes $con, $siteurl, $siteprefix, $sitecurrency, $siteMail, $siteName, etc.
?>

<?php
// Get ref and transaction ID from Paystack callback ---
$ref = mysqli_real_escape_string($con, $_GET['ref'] ?? '');
$transaction = mysqli_real_escape_string($con, $_GET['transaction'] ?? '');
$currentdatetime = date('Y-m-d H:i:s');

if (empty($ref)) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Invalid order reference.</div></div>");
}

// --- Fetch order details from admin API ---
$url = $siteurl . "script/admin.php?action=fetchorderdetails&ref=" .$ref;
$data = curl_get_contents($url);
$orders = json_decode($data, true);

// --- Validate ---
if (empty($orders) || isset($orders['error'])) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Order not found or invalid reference.</div></div>");
}

$order = $orders[0];
if ($order['status'] !== 'unpaid') {
    die("<div class='container mt-5 mb-5'><div class='alert alert-warning'>Order already processed or invalid.</div></div>");
}

// --- Extract main info ---
$order_id = $order['order_id'];
$user_id = $order['user'];
$buyer_name = $order['buyer_name'];
$buyer_email = $order['buyer_email'];
$items = $order['items'];
$sitecurrency = $sitecurrency ?? '₦';
$escrowfee = $escrowfee ?? ''; // Default commission %

/* ========================================================
   PROCESS EACH ITEM
======================================================== */
foreach ($items as $item) {
    $listing_id = $item['listing_id'];
    $listing = $item['listing_title'];
    $variation = $item['variation'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $total_price = $item['total_price'];
    $type = $item['product_type'];
    $seller_id = $item['seller_id'];
    $seller_name = $item['seller_name'];
    $seller_email = $item['seller_email'];
    $user_type = $item['seller_type'];

    // ✅ Commission calculations
    $admin_commission = $total_price * ($escrowfee / 100);
    $seller_amount = $total_price - $admin_commission;

    // ✅ Update stock
    mysqli_query($con, "
        UPDATE {$siteprefix}listings 
        SET limited_slot = GREATEST(limited_slot - $quantity, 0)
        WHERE listing_id = '$listing_id'
    ");

    if ($user_type === 'admin') {
        // ✅ Admin listing sale
        mysqli_query($con, "
            INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
            VALUES ('$total_price', '$listing_id', '$order_id', 'Direct Sale (Admin Listing)', '$currentdatetime')
        ");

        insertadminAlert($con, "Admin received {$sitecurrency}{$total_price} for Order #{$order_id}", "profits.php", $currentdatetime, "profits", 0);

        // --- Email admin ---
        $adminSubject = "New Payment Received for Admin Listing";
        $adminMessage = "
            <html><body style='font-family:Arial,sans-serif;'>
            <h3>New Admin Sale</h3>
            <p>You received <strong>{$sitecurrency}{$total_price}</strong> for order <strong>#{$order_id}</strong>.</p>
            <p>Listing: {$listing}</p>
            <p>Buyer: {$buyer_name}</p>
            <p>Date: {$currentdatetime}</p>
            </body></html>";
        sendEmail($siteMail, $siteName, $siteMail, $siteName, $adminMessage, $adminSubject);

    } else {
        // ✅ Normal seller sale
        // Record commission
        mysqli_query($con, "
            INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
            VALUES ('$admin_commission', '$listing_id', '$order_id', 'Commission from Listing Sale', '$currentdatetime')
        ");
        insertadminAlert($con, "Commission of {$sitecurrency}{$admin_commission} earned from Order #{$order_id}", "profits.php", $currentdatetime, "profits", 0);

        // Credit seller wallet
        mysqli_query($con, "
            UPDATE {$siteprefix}users 
            SET wallet = wallet + $seller_amount 
            WHERE id = '$seller_id'
        ");
        insertWallet($con, $seller_id, $seller_amount, "credit", "Earnings from Order #{$order_id}", $currentdatetime);
        insertAlert($con, $seller_id, "You received {$sitecurrency}{$seller_amount} for Order #{$order_id}", $currentdatetime, 0);

        // --- Email seller ---
        $sellerSubject = "Payment received for your listing ({$listing})";
        $sellerMessage = "
            <p>You received <strong>{$sitecurrency}{$seller_amount}</strong> for your listing <strong>{$listing}</strong>.</p>
            <p>Variation: {$variation}<br>Quantity: {$quantity}</p>
            <p><strong>Commission Deducted:</strong> {$sitecurrency}{$admin_commission}</p>
            <p>Thank you for using {$siteName}.</p>";
        sendEmail($seller_email, $siteName, $siteMail, $seller_name, $sellerMessage, $sellerSubject);
    }
}

/* ========================================================
   ✅ Update Order Status
======================================================== */
mysqli_query($con, "
    UPDATE {$siteprefix}orders 
    SET status='paid',date='$currentdatetime' 
    WHERE order_id='$ref'
");

    // Ensure service bookings are updated when the order contains service items.
    // The previous code checked a single $type value and compared against 'Service'
    // which could miss matches (case mismatch or when multiple items exist).
    $serviceFound = false;
    foreach ($items as $it) {
        $itType = strtolower($it['product_type'] ?? $it['type'] ?? '');
        if ($itType === 'service') {
            $serviceFound = true;
            break;
        }
    }
    if ($serviceFound) {
        $safeRef = mysqli_real_escape_string($con, $ref);
        mysqli_query($con, "
            UPDATE {$siteprefix}service_bookings
            SET status='approved', payment_status='paid' 
            WHERE order_id='$safeRef'
        ");
    }

/* ========================================================
   ✅ Buyer confirmation email
======================================================== */
$emailBody = "
<h2>Order Confirmation</h2>

<p>Thank you for your purchase! Your payment was successful.</p>
<p><strong>Order Reference:</strong> {$ref}</p>
<table border='1' cellpadding='6' cellspacing='0' width='100%'>
<thead><tr>
<th>Listing</th><th>Variation</th><th>Seller</th><th>Quantity</th><th>Total</th>
</tr></thead><tbody>";

foreach ($items as $item) {
    $emailBody .= "
    <tr>
        <td>{$item['listing_title']}</td>
        <td>{$item['variation']}</td>
        <td>{$item['seller_name']}</td>
        <td>{$item['quantity']}</td>
        <td>{$sitecurrency}{$item['total_price']}</td>
    </tr>";
}

$emailBody .= "</tbody></table><p>Thank you for shopping with {$siteName}!</p></body></html>";

sendEmail($buyer_email, $siteName, $siteMail, $buyer_name, $emailBody, "Order Confirmation - {$siteName}");

/* ========================================================
   ✅ Display confirmation to buyer
======================================================== */
?>
<div class="container mt-5 mb-5">
    <div class="alert alert-success">
        <h4 class="alert-heading">✅ Payment Successful!</h4>
        <p>Thank you, <strong><?php echo htmlspecialchars($buyer_name); ?></strong>. Your order has been processed successfully.</p>
        <hr>
        <p><strong>Reference:</strong> <?php echo htmlspecialchars($ref); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($currentdatetime); ?></p>
    </div>

    <h5>🧾 Order Details</h5>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Listing</th>
                <th>Variation</th>
                <th>Seller</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['listing_title']); ?></td>
                    <td><?php echo htmlspecialchars($item['variation']); ?></td>
                    <td><?php echo htmlspecialchars($item['seller_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($sitecurrency . $item['total_price']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include "footer.php"; ?>
