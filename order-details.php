
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
$order_date = date('Y-m-d H:i:s');
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
                    <th>Listing</th>
                    <th>Variation</th>
                    <th>Seller</th>
                    <th>Qty</th>
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
