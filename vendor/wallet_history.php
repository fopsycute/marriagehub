

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Payment History</h3>
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
                  <a href="#">Payment History</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Payment History</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Payment History</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                    <table id="multi-filter-select" class="display table table-striped table-hover">
    <thead>
        <tr>
            <th>Date</th>
            <th>Transaction Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Transaction Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
<?php
$url = $siteurl . "script/admin.php?action=walletuser&user_id=$buyerId";
$data = curl_get_contents($url);
$count = 1;

if ($data !== false) {
    $wallets = json_decode($data, true);

    // Check if response contains an error
    if (isset($wallets['error'])) {
        echo '<tr><td colspan="5" class="text-center text-danger">Error: ' . htmlspecialchars($wallets['error']) . '</td></tr>';
    } elseif (!empty($wallets) && is_array($wallets)) {
        foreach ($wallets as $wallet) {
            $date   = $wallet['date'] ?? '';
            $amount = $wallet['amount'] ?? '0';
            $reason = $wallet['reason'] ?? '';
            $status = $wallet['status'] ?? '';
            $id     = $wallet['id'] ?? $count; // unique modal ID

            $badge = '<span class="badge bg-success">' . htmlspecialchars($status) . '</span>';

            echo "
            <tr>
                <td>" . htmlspecialchars($date) . "</td>
                <td>" . htmlspecialchars($reason) . "</td>
                <td><strong>₦" . htmlspecialchars($amount) . "</strong></td>
                <td>$badge</td>
                <td>
                    <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#transactionModal$id'>
                        View
                    </button>

                    <!-- Modal -->
                    <div class='modal fade' id='transactionModal$id' tabindex='-1' aria-hidden='true'>
                      <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content'>
                          <div class='modal-header bg-primary text-white'>
                            <h5 class='modal-title'>Transaction Details</h5>
                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                          </div>
                          <div class='modal-body'>
                            <p><strong>Date:</strong> " . htmlspecialchars($date) . "</p>
                            <p><strong>Type:</strong> " . htmlspecialchars($reason) . "</p>
                            <p><strong>Amount:</strong> ₦" . htmlspecialchars($amount) . "</p>
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
        echo '<tr><td colspan="5" class="text-center text-muted">No wallet transactions found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="5" class="text-center text-danger">Error fetching wallet data.</td></tr>';
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