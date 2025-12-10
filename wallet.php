<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
    </div>

    <!-- Wallet & Earnings Section -->
    <div class="row">
      <div class="col-md-12 py-4">
        <div class="card border-0 shadow-sm rounded-4 py-4">
          <div class="card-header bg-light d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="card-title mb-0 fw-bold">Wallet & Earnings</h4>
          </div>
          <div class="card-body">
<?php

  $url = $siteurl . "script/admin.php?action=fetchwallet&user_id=$buyerId";
$data = curl_get_contents($url);
$totals = $data ? json_decode($data, true) : [];

$total_pending = $totals['total_pending'] ?? '0.00';
$total_requested  = $totals['total_requested'] ?? '';
$total_cleared = $totals['total_cleared'] ?? '';
$total_earned = $totals['total_earned'] ?? '';
$total_dispute_amount = $totals['total_dispute_amount'] ?? '0.00';

?>
            <!-- Wallet Summary Cards -->
            <div class="row g-4 mt-2">
              <!-- Withdrawal Requests -->
              <div class="col-md-3 col-sm-6">
                <div class="card shadow-lg rounded-4 border-0 bg-gradient bg-primary text-white h-100">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <i class="bi bi-cash-stack display-5"></i>
                    </div>
                    <h6 class="card-title fw-semibold">Withdrawal Requests</h6>
                    <h4 class="fw-bold mb-0">₦<?php echo number_format($total_pending, 2); ?></h4>
                  </div>
                </div>
              </div>

              
              <div class="col-md-3 col-sm-6">
                <div class="card shadow-lg rounded-4 border-0 bg-info text-white h-100">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <i class="bi bi-arrow-counterclockwise display-5"></i>
                    </div>
                    <h6 class="card-title fw-semibold">Dispute Refunds</h6>
                    <h4 class="fw-bold mb-0">₦<?php echo $total_dispute_amount; ?></h4>
                  </div>
                </div>
              </div>

              <!-- Total Amount Earned -->
              <div class="col-md-3 col-sm-6">
                <div class="card shadow-lg rounded-4 border-0 bg-warning text-white h-100">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <i class="bi bi-wallet2 display-5"></i>
                    </div>
                    <h6 class="card-title fw-semibold">Total Amount Earned</h6>
                    <h4 class="fw-bold mb-0">₦<?php echo number_format($total_earned, 2); ?></h4>
                  </div>
                </div>
              </div>

              <!-- Cleared Transactions -->
              <div class="col-md-3 col-sm-6">
                <div class="card shadow-lg rounded-4 border-0 bg-success text-white h-100">
                  <div class="card-body text-center py-4">
                    <div class="mb-3">
                      <i class="bi bi-check-circle display-5"></i>
                    </div>
                    <h6 class="card-title fw-semibold">Cleared Transactions</h6>
                    <h4 class="fw-bold mb-0">₦<?php echo number_format($total_cleared, 2); ?></h4>
                  </div>
                </div>
              </div>
            </div>

            <!-- Wallet Section -->
            <div class="row mt-5">
              <div class="col-12">
                <div class="section-title mb-4">
                  <h4 class="fw-bold">My Wallet</h4>
                  <p class="text-muted">Manage your transactions and monitor your balance.</p>
                </div>

                <div class="card rounded-4 shadow-sm border-0 mb-4">
                  <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-3 mb-md-0">
                      <h5 class="fw-bold mb-1">Available Balance</h5>
                      <h3 class="fw-bolder text-success mb-0"><?php echo $sitecurrency.$wallet; ?></h3>
                    </div>
                    <div>
                     <!-- ✅ Add this Withdraw button where you show the wallet balance -->
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#withDraw">
            <i class="bi bi-arrow-down-up"></i> Withdraw
            </button>
                                </div>
                  </div>
                </div>

                <div class="modal fade" id="withDraw" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" class="vendorwallet" >
        <div class="modal-header">
          <h5 class="modal-title" id="withdrawModalLabel">Withdraw from Wallet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="messages"></div>
          <p><b>Amount Available for withdrawal: <span class="text-primary">&#8358;<?php echo $wallet; ?></span></b></p>
          <?php  $disable=""; if($bank_name=="" || $bank_accname=="" || $bank_number==""){ $disable="disabled"; ?>
            <p class="text-danger">
              <a href="settings" class="btn btn-outline-primary rounded-pill px-4">Update Payment Details</a><br>
              <i>This is a required detail to be eligible to withdraw</i>
            </p>
          <?php } else { ?>
            <p>Your Bank Details</p>
            <p>
                <input type="hidden" name="action" value="withdraw">
                <input type="hidden" name="user_id" value="<?php echo $buyerId; ?>">
              <input type="text" name="bank" class="form-control mb-2" value="<?php echo $bank_name; ?>" placeholder="Enter bank name" required />
              <input type="text" name="bankname" class="form-control mb-2" value="<?php echo $bank_accname; ?>" placeholder="Enter bank account name" required />
              <input type="number" name="bankno" class="form-control mb-2" value="<?php echo $bank_number; ?>" placeholder="Enter bank account number" required />
            </p>
          <?php } ?>
          <p class="pt-3">
           <input type="number" name="amount" class="form-control" max="<?php echo $wallet; ?>" 
            min="<?php echo $minimum_withdrawal; ?>" placeholder="Enter Amount to withdraw" required />
            <span class="text-small text-primary">Minimum withdrawal is ₦<?php echo number_format($minimum_withdrawal, 2); ?></span>

          </p>
        </div>
        <div class="modal-footer">
          <button type="submit" id="submitBtn" class="btn btn-primary" <?php echo $disable; ?>>Withdraw</button>
        </div>
      </form>
    </div>
  </div>
</div>


                <!-- Recent Transactions -->
                <div class="card rounded-4 shadow-sm border-0">
                  <div class="card-header bg-light border-0 rounded-top-4 d-flex">
                    <h5 class="fw-bold mb-0">Recent Transactions</h5>
                    <a class="ms-auto btn btn-link" href="wallet_history">View All</a>
                  </div>

                  
                  <div class="card-body">
                    <div class="table-responsive">
 <table
    id="multi-filter-select"
    class="display table table-striped table-hover">
    
    <thead>
      <tr>
        <th>Date</th>
        <th>Transaction Type</th>
        <th>Amount</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <th>Date</th>
        <th>Transaction Type</th>
        <th>Amount</th>
        <th>Status</th>
        <th></th>
      </tr>
    </tfoot>

    <tbody>
<?php
$url = $siteurl . "script/admin.php?action=walletuser&user_id=$buyerId";
$data = curl_get_contents($url);
$limit = 4;
$count = 0;

if ($data !== false) {
    $wallets = json_decode($data, true);

    if (!empty($wallets)) {

        foreach ($wallets as $wallet) {
            if ($count >= $limit) break;

            $date   = $wallet['date'] ?? '';
            $amount = $wallet['amount'] ?? '0';
            $reason = $wallet['reason'] ?? '';
            $status = $wallet['status'] ?? '';
            $id     = $wallet['id'] ?? $count;

            $badge = '<span class="badge bg-success">' .$status . '</span>';

            echo "
            <tr>
                <td>$date</td>
                <td>$reason</td>
                <td><strong>₦$amount</strong></td>
                <td>$badge</td>
                <td>
                    <button 
                        class='btn btn-primary btn-sm' 
                        data-bs-toggle='modal' 
                        data-bs-target='#transactionModal$id'
                    >
                        View
                    </button>

                    <div class='modal fade' id='transactionModal$id' tabindex='-1' aria-hidden='true'>
                      <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content'>
                          <div class='modal-header bg-primary text-white'>
                            <h5 class='modal-title'>Transaction Details</h5>
                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                          </div>
                          <div class='modal-body'>
                            <p><strong>Date:</strong> $date</p>
                            <p><strong>Type:</strong> $reason</p>
                            <p><strong>Amount:</strong> ₦$amount</p>
                            <p><strong>Status:</strong> $badge</p>
                          </div>
                          <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                </td>
            </tr>";

            $count++;
        }

    } else {
        // NO DATA FOUND — ALWAYS RETURN 5 CELLS
        echo "
        <tr>
            <td colspan='1'>No wallet transactions found.</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>";
    }

} else {
    // ERROR FETCHING — ALSO 5 CELLS
    echo "
    <tr>
        <td colspan='1'>Error fetching data.</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>";
}
?>
</tbody>

</table>


                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div> <!-- card-body -->
        </div> <!-- main card -->
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
