
<?php 
$requireLogin = true;

include "header.php"; 

?>

<div class="container">
          <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                <div class="card mt-5 mb-5">
                 <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">All Reviews</h4>
                    </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Product</th>
            <th>Comment</th>
            <th>Rating</th>
            <th>Date</th>
            <th>Action</th>
           
          </tr>
        </thead>
        <tfoot>
          <tr>
           <th>Product</th>
            <th>Comment</th>
            <th>Rating</th>
            <th>Date</th>
          </tr>
        </tfoot>
       <tbody>
                                    <?php
                                    $url = $siteurl . "script/admin.php?action=all_user_product_reviews&user_id=" . $buyerId;
                                    $data = curl_get_contents($url);

                                    if ($data !== false) {
                                        $reviews = json_decode($data);
                                        if (!empty($reviews)) {
                                            foreach ($reviews as $review):
                                                $reviewId     = $review->review_id;
                                                $customerName = trim(($review->first_name ?? '') . ' ' . ($review->last_name ?? '')) ?: 'Anonymous';
                                                $productTitle = htmlspecialchars($review->listing_title);
                                                $comment      = $review->comment ?? '';
                                                $rating       = intval($review->rating ?? 0);
                                                $createdDate  = !empty($review->created_at) ? date('M d, Y h:i A', strtotime($review->created_at)) : '';
                                                $response     = $review->seller_response ?? '';
                                                $limitComment = limitWords($comment, 5);

                                    ?>

                                    
                                    <tr>
                                        <td><?php echo $productTitle; ?></td>
                                        <td><?php echo $limitComment; ?></td>
                                        <td><?php echo $rating; ?>/5</td>
                                        <td><?php echo $createdDate; ?></td>
                                        <td>
                                            <!-- View Button -->
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $reviewId; ?>">
                                                View
                                            </button>
                                   
                        <a href='edit-reviews.php?review_id=<?php echo $reviewId; ?>' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='bi bi-pencil'></i> 
                        </a>
                        <a href='#' id='<?php echo $reviewId; ?>' class='btn btn-link btn-danger  deleteReview' data-bs-toggle='tooltip' title='Delete'>
                            <i class='bi bi-trash'></i>
                        </a>
    
                                        </td>
                                    </tr>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $reviewId; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $reviewId; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel<?php echo $reviewId; ?>">Review Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Product:</strong> <?php echo $productTitle; ?></p>
                                                    <p><strong>Rating:</strong> <?php echo $rating; ?>/5</p>
                                                    <p><strong>Date:</strong> <?php echo $createdDate; ?></p>
                                                    <hr>
                                                    <p><strong>Comment:</strong><br><?php echo $comment; ?></p>
                                                    <?php if (!empty($response)): ?>
                                                        <hr>
                                                        <p><strong>Seller Response:</strong><br><?php echo $response; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                 

                                    <?php
                                            endforeach;
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No reviews found for your products.</td></tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center text-danger">Unable to fetch reviews.</td></tr>';
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
