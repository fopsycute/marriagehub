<?php include "header.php"; ?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">All Notifications</h3>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Notifications</h5>
                <a href="#" class="btn btn-primary read-notifications" data-userid="<?= $buyerId; ?>">
                    Mark All as Read
                </a>
            </div>

            <div class="card-body">
                <?php
                // Fetch notifications for the logged-in user
                $url = $siteurl . "script/admin.php?action=usernotificationlists&user_id=" . $buyerId;
                $data = curl_get_contents($url);

                if ($data !== false) {
                    $notificationsData = json_decode($data);

                    if (!empty($notificationsData)) {
                        ?>
                        <ul class="list-group">
                            <?php foreach ($notificationsData as $notification):
                                // Prepare date and styling
                                $dateAgo = timeAgo($notification->date);
                                $readClass = $notification->status == 1 ? 'list-group-item-secondary' : '';
                                $messageClass = $notification->status == 0 ? 'fw-bold' : '';
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center <?= $readClass; ?>">
                                    <div class="<?= $messageClass; ?>">
                                        <i class="fa fa-bell me-2"></i>
                                        <?= htmlspecialchars($notification->message); ?>
                                    </div>
                                    <span class="text-muted small"><?= $dateAgo; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                    } else {
                        echo '<p class="text-center text-muted">No notifications available.</p>';
                    }
                } else {
                    echo '<p class="text-center text-danger">Failed to load notifications.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
