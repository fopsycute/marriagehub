
<?php 
include "header.php";

$ticket_id = $_GET['ticket_number'];
$sitelink = $siteurl . "script/admin.php?";

// Fetch ticket details
$ticketData = json_decode(
    curl_get_contents($sitelink . "action=getTicketDetails&ticket_number=" . $ticket_id),
    true
);

if ($ticketData['status'] !== 'success') {
    header("Location: $previousPage");
    exit();
}

$ticket = $ticketData['ticket'];

$user_id        = $ticket['user_id'];
$ticket_number  = $ticket['ticket_number'];
$category       = $ticket['category'];
$order_reference= $ticket['order_reference'];
$issue          = $ticket['issue'];
$status         = $ticket['status'];
$dispute_id     = $ticket['id'];

// Fetch evidence
$evidenceData = json_decode(
    curl_get_contents($sitelink . "action=getTicketEvidence&dispute_id=" . $dispute_id),
    true
);

// Fetch messages
$messageData = json_decode(
    curl_get_contents($sitelink . "action=getTicketMessages&ticket_number=" . $ticket_number),
    true
);
?>
<div class="container py-5">
<div class="row">
<div class="col-md-12">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h6>
        Dispute Resolution - <?= $issue ?><br>
        (Order: <?= $order_reference ?>/Ticket: <?= $ticket_number ?>)
        
        <?php 
        if ($evidenceData['status'] === 'success') {
            foreach ($evidenceData['evidence'] as $evidence) {
                if (!empty($evidence['file_path'])) {
                    $evidenceFiles = explode(',', $evidence['file_path']);
                    echo '<div class="mt-2">';
                    foreach ($evidenceFiles as $file) {
                        $fname = basename(trim($file));
                        echo '<a href="'.$imagePath.$file.'" target="_blank" class="btn btn-sm btn-outline-primary me-2 mb-2">
                                View Evidence: '.$fname.'
                              </a>';
                    }
                    echo '</div>';
                }
            }
        }
        ?>
    </h6>

    <span class="badge bg-<?= getBadgeColor($status) ?>"><?= $status ?></span>
</div>

<div class="card mb-4">
<div class="card-body">
<h5 class="mb-3">Ticket Messages</h5>

<?php if ($messageData['status'] !== 'success' || empty($messageData['messages'])): ?>
    <p>No messages found</p>
<?php else: ?>
    <?php foreach ($messageData['messages'] as $message): ?>
        <?php $files = !empty($message['file']) ? explode(',', $message['file']) : []; ?>
        
        <div class="d-flex mb-3">
            <div class="flex-shrink-0">
                <img src="<?= $imagePath . $message['profile_image'] ?>" width="50" height="50" class="rounded-circle">
            </div>

            <div class="flex-grow-1 ms-3">
                <div class="d-flex justify-content-between">
                    <h6><?= $message['name'] ?></h6>
                    <small><?= date('M d, Y H:i', strtotime($message['created_at'])) ?></small>
                </div>

                <p><?= $message['message'] ?></p>

                <?php if ($files): ?>
                    <div class="mt-2">
                        <?php foreach ($files as $file): ?>
                            <a href="<?= $imagePath.$file ?>" class="btn btn-sm btn-outline-primary me-2 mb-1" target="_blank">
                                View <?= basename($file) ?>
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

<?php if ($status == 'pending' || $status == 'awaiting-response'): ?>
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
<?php endif; ?>

<?php if ($status == 'under-review'): ?>
<h4>Dispute Under Review</h4>
<p>Our support team is reviewing this dispute.</p>
<?php endif; ?>

<?php if ($status == 'resolved'): ?>
<h4>Dispute Resolved</h4>
<p>This dispute has been resolved.</p>
<?php endif; ?>

</div>
</div>
</div>

<?php include "footer.php"; ?>
