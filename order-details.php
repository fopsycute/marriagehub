
<?php
include "header.php";

$ref = mysqli_real_escape_string($con, $_GET['ref'] ?? '');
if (empty($ref)) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Invalid order reference.</div></div>");
}

$url = $siteurl . "script/admin.php?action=fetchorderdetails&ref=" . $ref;
$data = curl_get_contents($url);
$orders = json_decode($data, true);

if (empty($orders) || isset($orders['error'])) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Order not found.</div></div>");
}

$order = $orders[0];
$items = $order['items'];
$buyer_name = $order['buyer_name'];
$buyer_email = $order['buyer_email'];
$order_date = $order['date'] ?? date('Y-m-d H:i:s');

// Normalize items locally (don't change admin.php). Ensure event rows include listing_id
// and fill missing title/seller info where possible, then sort by item date DESC.
if (is_array($items) && count($items) > 0) {
    foreach ($items as &$it) {
        // Mirror event_id into listing_id for event items if missing
        if (($it['type'] ?? '') === 'event') {
            if (empty($it['listing_id']) && !empty($it['event_id'])) {
                $it['listing_id'] = $it['event_id'];
            }
        }

        // Populate listing title/slug when missing
        if (empty($it['listing_title']) && !empty($it['listing_id'])) {
            $safeId = mysqli_real_escape_string($con, $it['listing_id']);
            if (($it['type'] ?? '') === 'event') {
                $q = "SELECT title, slug, user_id FROM {$siteprefix}events WHERE event_id='{$safeId}' LIMIT 1";
            } else {
                $q = "SELECT title, slug, user_id FROM {$siteprefix}listings WHERE listing_id='{$safeId}' LIMIT 1";
            }
            $r = mysqli_query($con, $q);
            if ($r) {
                $row = mysqli_fetch_assoc($r);
                if ($row) {
                    $it['listing_title'] = $row['title'] ?? ($it['listing_title'] ?? '');
                    $it['listing_slug']  = $row['slug'] ?? ($it['listing_slug'] ?? '#');
                    if (empty($it['seller_id']) && !empty($row['user_id'])) $it['seller_id'] = $row['user_id'];
                }
            }
        }

        // Populate seller name/email if missing
        if ((empty($it['seller_name']) || empty($it['seller_email'])) && !empty($it['seller_id'])) {
            $safeSeller = mysqli_real_escape_string($con, $it['seller_id']);
            $qq = "SELECT first_name, email FROM {$siteprefix}users WHERE id='{$safeSeller}' LIMIT 1";
            $rr = mysqli_query($con, $qq);
            if ($rr) {
                $srow = mysqli_fetch_assoc($rr);
                if ($srow) {
                    if (empty($it['seller_name'])) $it['seller_name'] = trim(($srow['first_name'] ?? ''));
                    if (empty($it['seller_email'])) $it['seller_email'] = $srow['email'] ?? '';
                }
            }
        }
    }
    unset($it);

    // Sort by item date descending (newest first). Fallback to 0 when missing.
    usort($items, function($a, $b){
        $ta = isset($a['date']) ? strtotime($a['date']) : 0;
        $tb = isset($b['date']) ? strtotime($b['date']) : 0;
        return $tb <=> $ta; // desc
    });
}
?>


<div class="container mt-5 mb-5">

    <!-- RECEIPT START -->
    <div class="receipt-card" id="ticketContent">

        <div class="receipt-header d-block text-center">
        <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" alt="Logo" class="receipt-logo mb-2">
            <h6 class="receipt-title mb-0">Order Receipt</h6>
           
        </div>

        <div class="receipt-details mt-3">
            <p><strong>Buyer:</strong> <?php echo htmlspecialchars($buyer_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($buyer_email); ?></p>
            <p><strong>Reference:</strong> <?php echo htmlspecialchars($ref); ?></p>
            <p><strong>Date:</strong> <?php echo formatDateTime($order_date); ?></p>
        </div>

        <hr>

        <h5 class="mt-3 mb-2">Order Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Listing</th>
                    <th>Variation</th>
                    <th>Seller</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo htmlspecialchars($item['listing_title']); ?></td>
                    <td><?php echo htmlspecialchars($item['variation']); ?></td>
                    <td><?php echo htmlspecialchars($item['seller_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo $sitecurrency . htmlspecialchars($item['total_price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>

        <p class="text-center mt-3">
            Thank you for your purchase!  
            <br>For support, contact: <?php echo $siteMail; ?>
        </p>

    </div>
    <!-- RECEIPT END -->

    <!-- ACTION BUTTONS -->
    <div class="text-center mt-4" id="actionButtons">
    <!--    <button onclick="window.print()" class="btn btn-dark print-btn"><i class="fa fa-print"></i> Print</button>  -->
        <button class="btn btn-success pdf-btn" id="downloadBtn" onclick="downloadPDF()">
            <i class="fa fa-download"></i> Download PDF
        </button>
    </div>

</div>

<?php include "footer.php"; ?>
