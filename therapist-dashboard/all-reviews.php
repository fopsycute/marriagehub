<?php include "header.php"; ?>


<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">All Reviews</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">All Reviews</a></li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Reviews</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="multi-filter-select" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                               
                                        <th>Comment</th>
                                        <th>Rating</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Client</th>
                                       
                                        <th>Comment</th>
                                        <th>Rating</th>
                                        <th>Date</th>
                                      
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $url = $siteurl . "script/admin.php?action=all_therapist_reviews&therapist_id=" . $buyerId;
                                    $data = curl_get_contents($url);

                                    if ($data !== false) {
                                        $reviews = json_decode($data);
                                        if (!empty($reviews)) {
                                            foreach ($reviews as $review):
                                                $reviewId     = $review->review_id;
                                                $customerName = trim(($review->first_name ?? '') . ' ' . ($review->last_name ?? '')) ?: 'Anonymous';
                                                $comment      = $review->comment ?? '';
                                                $rating       = intval($review->rating ?? 0);
                                                $createdDate  = !empty($review->created_at) ? date('M d, Y h:i A', strtotime($review->created_at)) : '';
                                                $response     = $review->seller_response ?? '';
                                                $limitComment = limitWords($comment, 5);

                                              
                                    ?>

                                    
                                    <tr>
                                        <td><?php echo $customerName; ?></td>
                                    
                                        <td><?php echo $limitComment; ?></td>
                                        <td><?php echo $rating; ?>/5</td>
                                        <td><?php echo $createdDate; ?></td>
                                        <td>
                                            <!-- View Button -->
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $reviewId; ?>">
                                                View
                                            </button>

                                            <!-- Respond Button (only if allowed) -->
                                          
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#respondModal<?php echo $reviewId; ?>">
                                                    Respond
                                                </button>
                                          
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
                                                    <p><strong>Client:</strong> <?php echo $customerName; ?></p>
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

                                  
                                    <!-- Respond Modal -->
                                    <div class="modal fade" id="respondModal<?php echo $reviewId; ?>" tabindex="-1" aria-labelledby="respondModalLabel<?php echo $reviewId; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="respondModalLabel<?php echo $reviewId; ?>">Respond to Review</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Client:</strong> <?php echo $customerName; ?></p>
                                                    <p><strong>Comment:</strong> <?php echo $comment; ?></p>

                                                    <div class="mb-3">
                                                        <label for="response<?php echo $reviewId; ?>" class="form-label">Your Response</label>
                                                        <textarea class="editor" id="response<?php echo $reviewId; ?>" rows="3"><?php echo $response; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-success save-response" data-id="<?php echo $reviewId; ?>">Submit Response</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 

                                    <?php
                                            endforeach;
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No reviews found for your services.</td></tr>';
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
