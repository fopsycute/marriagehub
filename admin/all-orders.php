<?php include "header.php";

// Fetch all orders via API
$apiUrl = $siteurl . "script/admin.php?action=Allorderlists";
$ordersData = curl_get_contents($apiUrl);

$orders = [];

if ($ordersData !== false) {
    $response = json_decode($ordersData, true);

    if (!empty($response)) {
        // Only keep paid orders
        foreach ($response as $order) {
            if (isset($order['status']) && strtolower($order['status']) === 'paid') {
                $orders[] = $order;
            }
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="page-inner">

        <div class="page-header">
            <h3 class="fw-bold mb-3">All Orders</h3>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Orders List</div>
                    </div>

                    <div class="card-body">

                        <?php if (!empty($orders)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-justify" id="multi-filter-select">

                                    <thead class="table-dark">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Buyer</th>
                                            <th>Date</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Buyer</th>
                                            <th>Date</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>
                                        <?php foreach ($orders as $row) { ?>
                                            <tr>
                                                <td>#<?php echo $row['order_id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); ?></td>
                                                <td><?php echo formatDateTime($row['date']); ?></td>
                                                <td>₦<?php echo formatNumber($row['total_amount'], 2); ?></td>

                                                <td>
                                                    <span class="badge bg-<?php echo ($row['status'] === 'paid') ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($row['status']); ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <a href="order-details.php?ref=<?php echo $row['order_id']; ?>" 
                                                       class="text-small btn btn-kayd btn-sm">
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>

                        <?php } else { ?>
                            <div class="alert alert-info text-center">No orders available.</div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
