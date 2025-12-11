

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

<!-- Related Events Section -->
<?php if (isset($event_id) && isset($category)): ?>
<section id="related-events" class="section bg-light">
  <div class="container">
    <h3 class="mb-4">Related Events</h3>
    <div class="row g-4">
      <?php
      // Fetch related events with same category
      $relatedUrl = $siteurl . "script/admin.php?action=eventslists";
      $relatedData = curl_get_contents($relatedUrl);
      
      if ($relatedData !== false) {
          $relatedEvents = json_decode($relatedData);
          $relatedCount = 0;
          
          if (!empty($relatedEvents)) {
              foreach ($relatedEvents as $relE) {
                  // Skip current event and only show active ones
                  if ($relE->event_id == $event_id || $relE->status != 'active') continue;
                  
                  // Check if categories match
                  $relCategories = $relE->category_names ?? '';
                  if (strpos($relCategories, $category) === false && strpos($category, $relCategories) === false) continue;
                  
                  $relatedCount++;
                  if ($relatedCount > 4) break; // Limit to 4 related events
                  
                  $relTitle = htmlspecialchars($relE->title);
                  $relSlug = htmlspecialchars($relE->slug);
                  $relType = htmlspecialchars($relE->event_type_name ?? 'Event');
                  $relPrice = intval($relE->amount ?? 0);
                  $relPriceDisplay = $relPrice > 0 ? $sitecurrency . number_format($relPrice) : 'Free';
                  $relImage = !empty($relE->featured_image) 
                      ? $siteurl . $imagePath . $relE->featured_image 
                      : $siteurl . "assets/img/default-event.jpg";
                  $relUrl = $siteurl . "event/" . $relSlug;
                  $relDate = date('M d, Y', strtotime($relE->start_date));
                  ?>
                  
                  <div class="col-lg-3 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                      <a href="<?php echo $relUrl; ?>">
                        <img src="<?php echo $relImage; ?>" class="card-img-top" alt="<?php echo $relTitle; ?>" style="height: 180px; object-fit: cover;">
                      </a>
                      <div class="card-body">
                        <span class="badge bg-info mb-2"><?php echo $relType; ?></span>
                        <h5 class="card-title">
                          <a href="<?php echo $relUrl; ?>" class="text-dark text-decoration-none"><?php echo $relTitle; ?></a>
                        </h5>
                        <p class="text-muted small mb-2">
                          <i class="bi bi-calendar"></i> <?php echo $relDate; ?>
                        </p>
                        <p class="fw-bold text-primary"><?php echo $relPriceDisplay; ?></p>
                      </div>
                    </div>
                  </div>
                  
                  <?php
              }
          }
          
          if ($relatedCount == 0) {
              echo '<p class="text-center text-muted">No related events found.</p>';
          }
      }
      ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
    }
}
?>

<!-- Sidebar Ad -->
<?php include "sidebar-ad.php"; ?>

<?php include "footer.php"; ?>
