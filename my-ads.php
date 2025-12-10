<?php

$requireLogin = true;
include "header.php";

// Fetch orders via API
$apiUrl = $siteurl . "script/admin.php?action=getuseradverts&user_id=" . urlencode($buyerId);
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

<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="col-lg-10">
        <h2 class="mb-4 text-center">My Ads</h2>

        <?php if (!empty($orders)) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-justify" id="multi-filter-select">
                    <thead class="table-dark">
                        <tr>
                            <th>Preview</th>
                            <th>Ad Name</th>
                            <th>Size</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Renew</th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th>Preview</th>
                            <th>Ad Name</th>
                            <th>Size</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Renew</th>
                        </tr>
                    </tfoot>

                    <tbody>
                    <?php
                    foreach ($orders as $row) {

                        $now = time();
                        $start = strtotime($row['start_date']);
                        $end = strtotime($row['end_date']);

                        if ($now < $end) {
                            $statusText = 'Active';
                            $statusClass = 'badge bg-success';
                        } else {
                            $statusText = 'Expired';
                            $statusClass = 'badge bg-danger';
                        }

                        // Banner URL
                        $bannerUrl = !empty($row['banner']) ? ($siteurl . $imagePath . $row['banner']) : '';

                        // Modal ID (unique per row)
                        $modalId = "bannerModal_" . $row['id'];
                    ?>
                        <tr>
                            <td class="blog-thumb" style="vertical-align:middle; width:160px;">
                                <?php if ($bannerUrl): ?>
                                    <img loading="lazy" src="<?php echo $bannerUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($row['placement_name']); ?>" 
                                         class="img-fluid rounded" 
                                         style="max-width:120px; max-height:80px; object-fit:contain; display:block; margin-bottom:6px;">

                                    <!-- View Button -->
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?php echo $modalId; ?>">
                                        View
                                    </button>

                                    <!-- STATIC MODAL (NO JS NEEDED) -->
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Banner Preview</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body text-center">
                                                    <img src="<?php echo $bannerUrl; ?>"
                                                         alt="<?php echo htmlspecialchars($row['placement_name']); ?>"
                                                         class="img-fluid"
                                                         style="max-height:70vh; object-fit:contain;">
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <small class="text-muted">No banner</small>
                                <?php endif; ?>
                            </td>

                            <td><?php echo $row['placement_name']; ?></td>
                            <td><?php echo $row['size']; ?></td>
                            <td><?php echo formatDateTime($row['start_date']); ?></td>
                            <td><?php echo formatDateTime($row['end_date']); ?></td>
                            <td><span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                            <td>
                                <a href="<?php echo $siteurl . 'buy-advert/' . $row['slug']; ?>" 
                                   class="text-small btn btn-kayd btn-sm">
                                   Renew
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php } else { ?>
            <div class="alert alert-info text-center">You have no orders yet.</div>
        <?php } ?>
    </div>
</div>

<?php include "footer.php"; ?>
