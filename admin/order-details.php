
<?php
include "header.php";

$ref = mysqli_real_escape_string($con, $_GET['ref'] ?? '');
if (empty($ref)) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Invalid order reference.</div></div>");
}

// Load order details from API
$apiUrl = $siteurl . "script/admin.php?action=fetchorderdetails&ref=" . $ref;
$response = curl_get_contents($apiUrl);
$orders = json_decode($response, true);

if (empty($orders) || isset($orders['error'])) {
    die("<div class='container mt-5 mb-5'><div class='alert alert-danger'>Order not found.</div></div>");
}

$order = $orders[0];
$items = $order['items'];
$buyer_name = $order['buyer_name'];
$buyer_email = $order['buyer_email'];
$order_date = $order['date'] ?? date('Y-m-d H:i:s');


// Normalize items (local only)
if (is_array($items) && count($items) > 0) {
    foreach ($items as &$it) {

        // If it's an event and listing_id is missing, use event_id
        if (($it['type'] ?? '') === 'event') {
            if (empty($it['listing_id']) && !empty($it['event_id'])) {
                $it['listing_id'] = $it['event_id'];
            }
        }

        // Fill listing title/slug if missing
        if (empty($it['listing_title']) && !empty($it['listing_id'])) {
            $id = mysqli_real_escape_string($con, $it['listing_id']);

            if (($it['type'] ?? '') === 'event') {
                $q = "SELECT title, slug, user_id FROM {$siteprefix}events WHERE event_id='$id' LIMIT 1";
            } else {
                $q = "SELECT title, slug, user_id FROM {$siteprefix}listings WHERE listing_id='$id' LIMIT 1";
            }

            $r = mysqli_query($con, $q);
            if ($r && $row = mysqli_fetch_assoc($r)) {
                $it['listing_title'] = $row['title'] ?? '';
                $it['listing_slug'] = $row['slug'] ?? '#';
                if (empty($it['seller_id'])) $it['seller_id'] = $row['user_id'];
            }
        }

        // Fill seller info if missing
        if (!empty($it['seller_id']) && (empty($it['seller_name']) || empty($it['seller_email']))) {
            $sid = mysqli_real_escape_string($con, $it['seller_id']);
            $u = mysqli_query($con, "SELECT first_name, email FROM {$siteprefix}users WHERE id='$sid' LIMIT 1");

            if ($u && $urow = mysqli_fetch_assoc($u)) {
                if (empty($it['seller_name'])) $it['seller_name'] = $urow['first_name'];
                if (empty($it['seller_email'])) $it['seller_email'] = $urow['email'];
            }
        }
    }

    unset($it);

    // Sort items by date DESC
    usort($items, function ($a, $b) {
        return (strtotime($b['date'] ?? 0)) <=> (strtotime($a['date'] ?? 0));
    });
}
?>

<div class="container mt-5 mb-5">
<div class="page-inner">

    <div class="card shadow" id="ticketContent">
        <div class="card-body">

            <div class="text-center mb-4">
                <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" 
                     class="img-fluid mb-2" style="max-width: 80px;">
                <h5 class="fw-bold">Order Receipt</h5>
            </div>

            <h6 class="fw-bold">Buyer Information</h6>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($buyer_email); ?></p>
            <p><strong>Reference:</strong> <?php echo htmlspecialchars($ref); ?></p>
            <p><strong>Date:</strong> <?php echo formatDateTime($order_date); ?></p>

            <hr>

            <h6 class="fw-bold mb-2">Order Items</h6>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="multi-filter-select">
                    <thead class="table-light">
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
            </div>

            <hr>

       
        </div>
    </div>



</div>
</div>

<?php include "footer.php"; ?>
