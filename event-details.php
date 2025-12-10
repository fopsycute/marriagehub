

<?php 
$requireLogin = true;
include "header.php"; ?>  
<?php
$sitelink = $siteurl . "script/";
$user_id = $buyerId; // current user ID

if (isset($_GET['slug'])) {

    $slug = $_GET['slug'];
    $apiUrl = $siteurl . "script/admin.php?action=fetcheventslug&slug=" . urlencode($slug);

    $data = curl_get_contents($apiUrl);
    $listing = json_decode($data, true);

    if (!empty($listing)) {

        // BASIC FIELDS
        $title       = htmlspecialchars($listing['title'] ?? '');
        $event_id    = $listing['event_id'];
        $description = $listing['description'] ?? '';
        $format      = strtolower($listing['delivery_format'] ?? '');
        $event_type  = htmlspecialchars($listing['event_type_name'] ?? '');
        $category    = htmlspecialchars($listing['category_names'] ?? '');
        $subcategory = htmlspecialchars($listing['subcategory_names'] ?? '');
        $status      = strtolower($listing['status'] ?? '');

        // SECURE PURCHASE CHECK → redirect home if unauthorized
        if (!hasUserPurchased($con, $user_id, $event_id, $siteprefix)) {
            header("Location: ".$siteurl);
            exit;
        }

        // Get delivery details
        $eventDelivery = getEventDeliveryDetails(
            $con,
            $siteprefix,
            $event_id,
            $format,
            $documentPath
        );
        $deliveryDetails = $eventDelivery['details'];
        $attachments = $eventDelivery['attachments'];
?>

<!-- Bootstrap Event Page Layout -->
<div class="container py-5">

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">

            <h1 class="display-6 fw-bold mb-3"><?php echo $title; ?></h1>

            <p class="text-muted mb-2">
                <strong>Category:</strong> <?php echo $category; ?>  
                &nbsp; • &nbsp;
                <strong>Subcategory:</strong> <?php echo $subcategory; ?>  
                &nbsp; • &nbsp;
                <strong>Type:</strong> <?php echo $event_type; ?>  
                &nbsp; • &nbsp;
                <strong>Format:</strong> <?php echo ucfirst($format); ?>
            </p>

            <hr>

            <h4 class="fw-semibold mt-4">Event Description</h4>
            <p class="text-secondary" style="line-height:1.7;"><?php echo $description; ?></p>

            <h4 class="fw-semibold mt-4">Delivery Details</h4>

            <ul class="list-group list-group-flush mb-4">
                <?php echo $deliveryDetails; ?>
            </ul>

            <?php if (!empty($attachments)) { ?>
            <h4 class="fw-semibold mt-4">Download Materials</h4>

            <div class="list-group mt-3">
                <?php foreach ($attachments as $file) { ?>
                    <a href="<?php echo $siteurl . $file; ?>" 
                       class="list-group-item list-group-item-action d-flex align-items-center"
                       download>
                        <i class="bi bi-file-earmark-arrow-down fs-4 me-3"></i>
                        Download Material
                    </a>
                <?php } ?>
            </div>
            <?php } ?>

        </div>
    </div>

</div>

<?php
    }
}
?>

<?php include "footer.php"; ?>
