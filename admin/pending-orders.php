


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Withdrawal requests </h3>
              <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                  <a href="#">
                    <i class="icon-home"></i>
                  </a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">withdrawal requests </a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">withdrawal requests </a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Withdrawal requests </h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
 <table id="multi-filter-select" class="display table table-striped table-hover">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Order ID</th>
            <th>User</th>
            <th>Amount</th>
            <th>Date Submitted</th>
            <th>Proof of Payment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>S/N</th>
            <th>Order ID</th>
            <th>User</th>
            <th>Amount</th>
            <th>Date Submitted</th>
            <th>Proof of Payment</th>
            <th>Status</th>
        </tr>
    </tfoot>
    <tbody>
<?php
$url = $siteurl . "script/admin.php?action=manualpaymentlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $wallets = json_decode($data);
    if (!empty($wallets)) {
        $sn = 1; // Initialize S/N
        foreach ($wallets as $wallet) {

            // Only show pending withdrawals
            if (isset($wallet->status) && strtolower($wallet->status) == 'pending') {

                $walletId         = $wallet->mid ?? 0;
                $author           = htmlspecialchars($wallet->first_name . ' ' . $wallet->last_name);
                $order_id         = $wallet->order_id;
                $amount           = $sitecurrency . number_format($wallet->amount, 2);
                $proof_of_payment = $siteurl . $imagePath . $wallet->proof;
                $date             = date("l, F j, Y g:i A", strtotime($wallet->date_created));
                $badge            = '<span class="badge bg-warning">Pending</span>';
?>
<tr>
    <td><?php echo $sn++; ?></td>
    <td><?php echo $order_id; ?></td>
    <td><?php echo $author; ?></td>
    <td><strong><?php echo $amount; ?></strong></td>
    <td><?php echo $date; ?></td>
    <td><a href="<?php echo $proof_of_payment; ?>" target="_blank">View Proof</a></td>
    <td><?php echo $badge; ?></td>
    <td>
        <!-- View Modal Button -->
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $walletId; ?>">
            View
        </button>

        <!-- Approve Button -->
        <a href="#" id="<?php echo $walletId; ?>" class="btn btn-link btn-success approveManualPayment" title="Approve">
            <i class="fa fa-check"></i> Approve
        </a>
<!-- Reject Button -->
            <a href="#" 
            id="<?php echo $walletId; ?>" 
            class="btn btn-link btn-danger rejectManualPayment" 
            data-bs-toggle="modal" 
            data-bs-target="#rejectModal<?php echo $walletId; ?>"
            title="Reject">
                <i class="fa fa-times"></i> Reject
            </a>
        <!-- Modal -->
        <div class="modal fade" id="viewModal<?php echo $walletId; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Wallet Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p><strong>Date:</strong> <?php echo $date; ?></p>
                <p><strong>Amount:</strong> <?php echo $amount; ?></p>
                <p><strong>Vendor:</strong> <?php echo $author; ?></p>
                <p><strong>Status:</strong> <?php echo $badge; ?></p>
                <p><strong>Proof of Payment:</strong> <a href="<?php echo $proof_of_payment; ?>" target="_blank">View Proof</a></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>


        <!-- Reject Modal -->
<div class="modal fade" id="rejectModal<?php echo $walletId; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Reject Manual Payment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Please provide a reason for rejecting this payment:</p>
        <textarea class="form-control rejectReason" id="rejectReason<?php echo $walletId; ?>" rows="3" placeholder="Enter reason..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger confirmReject" data-id="<?php echo $walletId; ?>">Reject Payment</button>
      </div>
    </div>
  </div>
</div>
    </td>
</tr>
<?php
            }
        }
    } else {
        echo '<tr><td colspan="8" class="text-center text-muted">No pending wallet transactions found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="8" class="text-center text-danger">Error fetching wallet data.</td></tr>';
}
?>
    </tbody>
</table>



          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
    </div>



<?php include "footer.php"; ?>