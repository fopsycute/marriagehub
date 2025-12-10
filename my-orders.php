
<?php include "header.php";

// Pagination setup
$limit = 10; // Number of orders per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch orders via API
$apiUrl = $siteurl . "script/admin.php?action=getuserorders&user_id=" . urlencode($buyerId);
$ordersData = curl_get_contents($apiUrl);
$orders = [];
$totalOrders = 0;

if ($ordersData !== false) {
    $response = json_decode($ordersData, true);
    if (!empty($response)) {
        // Filter only 'paid' orders
        foreach ($response as $order) {
            if (isset($order['status']) && strtolower($order['status']) === 'paid') {
                $orders[] = $order;
            }
        }
        $totalOrders = count($orders);
    }
}

// Pagination calculations
$totalPages = ceil($totalOrders / $limit);
$currentOrders = array_slice($orders, $offset, $limit);

?>

<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="col-lg-10">
        <h2 class="mb-4 text-center">My Orders</h2>

        <?php if (!empty($currentOrders)) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-justify" id="multi-filter-select">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tfoot>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>             
                            </tfoot>
                    <tbody>
                        <?php foreach ($currentOrders as $row) { ?>
                            <tr>
                                <td>#<?php echo $row['order_id']; ?></td>
                                <td><?php echo formatDateTime($row['date']); ?></td>
                                <td>₦<?php echo formatNumber($row['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($row['status'] === 'paid') ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order-details.php?ref=<?php echo $row['order_id']; ?>" class="text-small btn btn-kayd btn-sm">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php } else { ?>
            <div class="alert alert-info text-center">You have no orders yet.</div>
        <?php } ?>
    </div>
</div>

<?php include "footer.php"; ?>
