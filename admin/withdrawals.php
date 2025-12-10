
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
                        <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Name</th>
            <th>Account</th>
            <th>Amount</th>
            <th>Status</th>
              <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
           <th>Date</th>
           <th>Type</th>
            <th>Name</th>
            <th>Account</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </tfoot>
       <tbody>
<?php
$url = $siteurl . "script/admin.php?action=walletlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $wallets = json_decode($data);

    if (!empty($wallets)) {
        foreach ($wallets as $wallet) {

            if (isset($wallet->status) && strtolower($wallet->status) === 'pending') {
                $walletId = $wallet->s ?? 0;
                $author = htmlspecialchars($wallet->first_name . ' ' . $wallet->last_name);
                $account = htmlspecialchars($wallet->bank . ' - ' . $wallet->bank_number);
                $account_name = htmlspecialchars($wallet->bank_name);
                $amount = $sitecurrency . number_format($wallet->amount, 2);
               $date = date("l, F j, Y g:i A", strtotime($wallet->date));
                $reason = htmlspecialchars($wallet->reason ?? 'Withdrawal');

                // Always pending badge
                $badge = '<span class="badge bg-warning">Pending</span>';
?>
<tr>
    <td><?php echo $date; ?></td>
    <td><?php echo $reason; ?></td>
    <td><?php echo $author; ?></td>
    <td><?php echo $account; ?></td>
    <td><strong><?php echo $amount; ?></strong></td>
    <td><?php echo $badge; ?></td>
    <td>
        <!-- View Button -->
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $walletId; ?>">
            View
        </button>

        <!-- Approve Button -->
   <a href='#' id='<?php echo $walletId; ?>' class='btn btn-link btn-success approvewallet' data-bs-toggle='tooltip' title='approve'>
  <i class='fa fa-check'></i>approve
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
                <p><strong>Type:</strong> <?php echo $reason; ?></p>
                <p><strong>Amount:</strong> <?php echo $amount; ?></p>
                <p><strong>Vendor:</strong> <?php echo $author; ?></p>
                <p><strong>Account:</strong> <?php echo $account; ?></p>
                <P><strong>Account Name:</strong> <?php echo $account_name; ?></P>
                <p><strong>Status:</strong> <?php echo $badge; ?></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        echo '<tr><td colspan="7" class="text-center text-muted">No pending wallet transactions found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="7" class="text-center text-danger">Error fetching wallet data.</td></tr>';
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