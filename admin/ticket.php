<?php
include "header.php";

// Get ticket number from URL
$ticket_id = $_GET['ticket_number'];
$sitelink = $siteurl . "script/admin.php?";

// --- Fetch ticket details ---
$ticketData = json_decode(
    curl_get_contents($sitelink . "action=getTicketDetails&ticket_number=" . $ticket_id),
    true
);

if ($ticketData['status'] !== 'success') {
    header("Location: $previousPage");
    exit();
}

$ticket = $ticketData['ticket'];
$sender_id = $ticket['user_id'];
$recipient_id = $ticket['recipient_id'] ?? null;
$ticket_number = $ticket['ticket_number'];
$category = $ticket['category'];
$order_reference = $ticket['order_reference'];
$issue = $ticket['issue'];
$status = $ticket['status'];
$dispute_id = $ticket['id'];

// --- Fetch evidence ---
$evidenceData = json_decode(
    curl_get_contents($sitelink . "action=getTicketEvidence&dispute_id=" . $dispute_id),
    true
);

// --- Fetch messages ---
$messageData = json_decode(
   curl_get_contents($sitelink . "action=getTicketMessages&ticket_number=" . $ticket_number),
    true
);

// --- Fetch user details ---
$sDetails = getUserDetails($con, $siteprefix, $sender_id);
$sender_name = $sDetails['first_name'];
$sender_wallet = $sDetails['wallet'];

if ($recipient_id) {
    $rDetails = getUserDetails($con, $siteprefix, $recipient_id);
    $recipient_name = $rDetails['first_name'];
    $recipient_wallet = $rDetails['wallet'];
}
?>

<div class="container py-5">
<div class="row">
<div class="col-md-12">
        <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                Update Ticket Status
            </div>
            <div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">
<h6>
    Dispute Resolution - <?= $issue ?><br>
    (Order: <?= htmlspecialchars($order_reference) ?>/Ticket: <?= htmlspecialchars($ticket_number) ?>)

    <?php if ($evidenceData['status'] === 'success'): ?>
        <?php foreach ($evidenceData['evidence'] as $e): ?>
            <?php if (!empty($e['file_path'])): ?>
                <?php $files = explode(',', $e['file_path']); ?>
                <div class="mt-2">
                    <?php foreach ($files as $file): ?>
                        <a href="<?= $siteurl.$imagePath.trim($file) ?>" target="_blank" 
                           class="btn btn-sm btn-outline-primary me-2 mb-2">
                            View Evidence: <?= htmlspecialchars(basename(trim($file))) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</h6>

<span class="badge bg-<?= getBadgeColor($status) ?>"><?= htmlspecialchars($status) ?></span>
</div>

<div class="card mb-4">
<div class="card-body">
<h5 class="mb-3">Ticket Messages</h5>

<?php if ($messageData['status'] !== 'success' || empty($messageData['messages'])): ?>
    <p>No messages found</p>
<?php else: ?>
    <?php foreach ($messageData['messages'] as $msg): ?>
        <?php 
        $profile_image = $siteurl.$imagePath.$msg['profile_image'];
        $files = !empty($msg['file']) ? explode(',', $msg['file']) : [];
        ?>
        <div class="d-flex mb-3">
            <div class="flex-shrink-0">
                <img src="<?= $profile_image ?>" class="rounded-circle" width="50" height="50">
            </div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex justify-content-between">
                    <h6><?= htmlspecialchars($msg['name']) ?></h6>
                    <small><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></small>
                </div>
                <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <?php if ($files): ?>
                    <div class="mt-2">
                        <?php foreach ($files as $file): ?>
                            <a href="<?= $siteurl.$imagePath.trim($file) ?>" target="_blank" 
                               class="btn btn-sm btn-outline-primary me-2 mb-2">
                                View <?= htmlspecialchars(basename(trim($file))) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>
</div>

<h5>Actions</h5>
<div class="mb-4">
    <form method="POST" id="updateStatusForm">
        <div class="col-lg-12 text-center text-black mt-1" id="display"></div>
    <input type="hidden" id="ticket_id" value="<?= $ticket_id ?>" name="ticket_id">
    <select class="form-select mb-2" id="statusAction" name="status">
        <option value="">Select Action...</option>
        <option value="resolved">Mark as Resolved</option>
        <option value="under-review">Mark as Under Review</option>
    </select>
    <input type="hidden" name="action" value="updateticketstatus">
    <button id="updateStatusBtn" type="submit" class="btn btn-primary mb-3">Update Status</button>
 </form>
    <?php if ($recipient_id): ?>
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#walletModal">
        Manage User Wallets
    </button>
    <?php endif; ?>
   
</div>

<!-- Wallet Modal -->
<div class="modal fade" id="walletModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage User Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="walletForm" method="POST" enctype="multipart/form-data">
                    <div class="text-center mt-1" id="messages"></div> 
                    <input type="hidden" name="dispute_id" value="<?= $ticket_id ?>">
                    <input type="hidden" name="update-wallet-dispute" value="update-wallet-dispute">
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select class="form-select" name="user" required>
                            <option value="<?= $sender_id ?>"><?= $sender_name ?> (<?= $sitecurrency.$sender_wallet ?>)</option>
                            <?php if ($recipient_id): ?>
                                <option value="<?= $recipient_id ?>"><?= $recipient_name ?> (<?= $sitecurrency.$recipient_wallet ?>)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" name="amount" required>
                    </div>
                    <input type="hidden" name="action" value="updatewallet">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select class="form-select" name="wallet-action" required>
                            <option value="add">Add Funds</option>
                            <option value="deduct">Deduct Funds</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary w-100" id="openConfirmModal">Update Wallet</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Wallet Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to update this wallet?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary w-100" id="confirmSubmit">Yes, Update</button>
      </div>
    </div>
  </div>
</div>


<h4>Send Message</h4>
<form method="POST" enctype="multipart/form-data" id="sendawaiting">
       <div class="col-lg-12 text-center mt-1" id="messages"></div> 
    <input type="hidden" name="dispute_id" value="<?= $ticket_number ?>">
    <textarea name="message" class="form-control" rows="3" required></textarea>
<input type="hidden" name="action" value="createsecondticket">
<input type="hidden" name="user_id" value="<?= $buyerId ?>">
    <div class="mb-3 mt-2">
        <label>Attach Files (Max 5MB)</label>
        <input type="file" class="form-control" name="attachment[]" multiple>
    </div>

    <button class="btn btn-primary w-100 mt-2" id="submitBtn" type="submit" name="send_dispute_message">Send Message</button>
</form>

<?php if ($status == 'resolved'): ?>
<h4>Dispute Resolved</h4>
<p>This dispute has been resolved, you can view the resolution above.</p>
<?php endif; ?>

</div>
</div>
</div>
</div>
</div>

<?php include "footer.php"; ?>
