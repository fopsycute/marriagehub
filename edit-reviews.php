<?php 
$requireLogin = true;
include "header.php"; 
?>

<div class="container py-5 mb-5">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Review</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Edit Your Review</div>
          </div>

          <div class="card-body">

            <?php
            if (isset($_GET['review_id'])) {
    $reviewId = $_GET['review_id'];

    // Getting the review from API
    $apiUrl = $siteurl . "script/admin.php?action=fetch_review_by_id&review_id=" . $reviewId;
    $response = curl_get_contents($apiUrl);

    if ($response !== false) {

        $reviewData = json_decode($response);

        // API returned an array of objects
        if (is_array($reviewData) && isset($reviewData[0])) {
            $review = $reviewData[0];
        }
        // API returned a single object
        elseif (is_object($reviewData)) {
            $review = $reviewData;
        } 
        // Nothing valid returned
        else {
            echo "<div class='alert alert-warning'>No review found.</div>";
            $review = null;
        }

        if ($review) {
            $comment      = $review->comment ?? '';
            $rating       = intval($review->rating ?? 0);
            $productTitle = htmlspecialchars($review->listing_title ?? '');
        }

    } else {
        echo "<div class='alert alert-danger'>Unable to fetch review. Try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

            ?>

            <form method="POST" id="editReviewForm">
              <div class="col-lg-12 text-center mt-1" id="messages"></div>

              <h4>Edit << <?php echo $productTitle; ?> >> Review</h4>

              <input type="hidden" name="action" value="updateproduct_review">
              <input type="hidden" name="review_id" value="<?php echo $reviewId; ?>">
              <input type="hidden" name="user_id" value="<?php echo $buyerId; ?>">

              <!-- STAR RATING -->
              <div class="mb-3">
                <label class="form-label">Your Rating</label>
                <div class="star-rating" role="radiogroup" aria-label="Rating">
                  <?php
                  // Generate stars and preselect based on existing rating
                  for ($i = 1; $i <= 5; $i++) {
                      $selected = ($i <= $rating) ? 'selected-star' : '';
                      echo "<button type='button' class='star $selected' data-value='$i'>☆</button>";
                  }
                  ?>
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="<?php echo $rating; ?>">
              </div>
               
              <!-- COMMENT -->
              <div class="mb-3">
                <label class="form-label">Your Review</label>
                <textarea name="comment" class="form-control" rows="4" required><?php echo $comment; ?></textarea>
              </div>

              <div class="text-center">
               <button type="submit" class="btn btn-primary" id="submit-btn">Update Review</button>
              </div>
            </form>

          </div><!-- card-body -->
        </div>

      </div>
    </div>

  </div>
</div>

<?php include "footer.php"; ?>
