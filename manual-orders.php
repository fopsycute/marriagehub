<?php 
$requireLogin = true;
include "header.php";

// Fetch orders via API
$apiUrl = $siteurl . "script/admin.php?action=getusermanualpayments&user_id=" . urlencode($buyerId);
$ordersData = curl_get_contents($apiUrl);
$orders = [];

if ($ordersData !== false) {
    $response = json_decode($ordersData, true);
    if (!empty($response)) {
        foreach ($response as $order) {
            if (isset($order['status'])) {
                $orders[] = $order;
            }
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="col-lg-12">
        <h2 class="mb-4 text-center">Manual Payment Orders</h2>

        <?php if (!empty($orders)) { ?>

            <?php 
            $allModals = ""; // Storage for all modals
            ?>

            <div class="table-responsive">
                <table  id="multi-filter-select" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>View Proof</th>
                            <th>Status</th>
                            <th>Rejection Reason</th> <!-- NEW COLUMN -->
                            <th>Actions</th>
                            <th>View Order</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($orders as $row): 
                            $status = $row['status'];
                            $statusClass = getBadgeColor($status);
                            $proofUrl = $siteurl . $imagePath . $row['proof']; 
                            $orderId = $row['order_id'];
                            $reason = $row['rejection_reason'] ?? "";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($orderId) ?></td>
                            <td><?= htmlspecialchars($row['amount']) ?></td>
                            <td><?= formatDateTime($row['date_created']) ?></td>

                            <td>
                                <a href="<?= htmlspecialchars($proofUrl) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    View Proof
                                </a>
                            </td>

                            <td>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </td>

                            <!-- NEW REASON COLUMN -->
                            <td>
                                <?php if (!empty($reason)): ?>
                                    <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reasonModal<?= $orderId ?>">
                                        View
                                    </button>
                                <?php else: ?>
                                    <small class="text-muted">—</small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status === 'payment resend'): ?>
                                    <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reuploadModal<?= $orderId ?>">
                                        Reupload Proof
                                    </button>
                                <?php elseif ($status === 'cancelled'): ?>
                                    <small class="text-muted">Reorder Again</small>
                                <?php else: ?>
                                    <small class="text-muted">—</small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#orderModal<?= $orderId ?>">
                                    Details
                                </button>
                            </td>
                        </tr>

                        <?php
                        /* -----------------------------------
                           ORDER ITEMS FOR DETAILS MODAL
                        ------------------------------------ */
                        $detailsUrl = $siteurl . "script/admin.php?action=fetchorderdetails&ref=" . urlencode($orderId);
                        $detailsData = curl_get_contents($detailsUrl);
                        $orderItems = json_decode($detailsData, true)[0]['items'] ?? [];

                        /* -----------------------------------
                           DETAILS MODAL
                        ------------------------------------ */
                        ob_start();
                        ?>
                        <div class="modal fade" id="orderModal<?= $orderId ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title">Order Details - <?= htmlspecialchars($orderId) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body">
                                <?php if (!empty($orderItems)): ?>
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Listing Title</th>
                                                <th>Qty</th>
                                                <th>Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sn = 1; foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td><?= $sn++ ?></td>
                                                <td><?= htmlspecialchars($item['listing_title']) ?></td>
                                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                <td><strong><?= $sitecurrency . number_format($item['total_price'], 2) ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p class="text-muted text-center">No items found for this order.</p>
                                <?php endif; ?>
                              </div>

                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              </div>

                            </div>
                          </div>
                        </div>
                        <?php
                        $allModals .= ob_get_clean();


                        /* -----------------------------------
                           REUPLOAD PROOF MODAL
                        ------------------------------------ */
                        ob_start();
                        ?>
                        <div class="modal fade" id="reuploadModal<?= $orderId ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title">Reupload Payment Proof</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                            <form method="POST" class="reuploadproof-form" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="messages mb-2"></div>

        <input type="hidden" name="action" value="reuploadmanual">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId) ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($buyerId) ?>">

        <div class="mb-3">
            <label class="form-label">Upload Proof Again</label>
            <input type="file" name="proof" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Reason (why payment was rejected)</label>
            <textarea name="reason" class="form-control" rows="3" readonly><?= htmlspecialchars($row['rejection_reason'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit Proof</button>
    </div>
</form>


                            </div>
                          </div>
                        </div>
                        <?php
                        $allModals .= ob_get_clean();


                        /* -----------------------------------
                           REASON VIEW MODAL (NEW)
                        ------------------------------------ */
                        if (!empty($reason)) {
                            ob_start();
                        ?>
                        <div class="modal fade" id="reasonModal<?= $orderId ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Rejection Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p><?= nl2br(htmlspecialchars($reason)) ?></p>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php
                            $allModals .= ob_get_clean();
                        }

                        ?>

                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <!-- OUTPUT ALL MODALS -->
            <?= $allModals ?>

        <?php } else { ?>
            <div class="alert alert-info text-center">You have no orders yet.</div>
        <?php } ?>
    </div>
</div>

<?php include "footer.php"; ?>
