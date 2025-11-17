<?php include "header.php"; ?>

<div class="container">
    <div class="page-inner">

        <!-- ✅ Page Header -->
        <div class="page-header">
            <h3 class="fw-bold mb-3">All Reviews</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">All Reviews</a></li>
            </ul>
        </div>

        <?php
        // 🔹 Fetch all seller reviews to calculate average
       $url = $siteurl . "script/admin.php?action=all_therapist_reviews&therapist_id=" . $buyerId;
        $data = curl_get_contents($url);

        $averageRating = 0;
        $totalReviews = 0;

        if ($data !== false) {
            $reviews = json_decode($data);
            if (!empty($reviews)) {
                $totalReviews = count($reviews);
                $sum = 0;
                foreach ($reviews as $rev) {
                    $sum += intval($rev->rating ?? 0);
                }
                $averageRating = $totalReviews > 0 ? round($sum / $totalReviews, 1) : 0;
            }
        }

        // 🔹 Determine progress bar width and color based on rating
        $progress = ($averageRating / 5) * 100;
        $progressColor = "bg-danger";
        if ($averageRating >= 4) $progressColor = "bg-success";
        elseif ($averageRating >= 3) $progressColor = "bg-info";
        elseif ($averageRating >= 2) $progressColor = "bg-warning";
        ?>

        <!-- ✅ Average Rating Card -->
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow rounded-4">
                    <div class="card-body text-center">
                        <h5 class="fw-bold mb-3">Overall Seller Rating</h5>
                        <h2 class="fw-bold text-warning mb-1">
                            <?php echo $averageRating; ?> <small class="text-muted">/ 5</small>
                        </h2>
                        <div class="mb-3">
                            <?php
                            // Display star icons
                            $fullStars = floor($averageRating);
                            $halfStar = ($averageRating - $fullStars) >= 0.5;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) {
                                    echo '<i class="fas fa-star text-warning"></i> ';
                                } elseif ($halfStar && $i == $fullStars + 1) {
                                    echo '<i class="fas fa-star-half-alt text-warning"></i> ';
                                } else {
                                    echo '<i class="far fa-star text-muted"></i> ';
                                }
                            }
                            ?>
                        </div>

                        <div class="progress" style="height: 15px; border-radius: 10px;">
                            <div class="progress-bar <?php echo $progressColor; ?>" 
                                role="progressbar" 
                                style="width: <?php echo $progress; ?>%;" 
                                aria-valuenow="<?php echo $averageRating; ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="5">
                            </div>
                        </div>

                        <p class="mt-2 text-muted mb-0">
                            Based on <strong><?php echo $totalReviews; ?></strong> review<?php echo $totalReviews != 1 ? 's' : ''; ?>.
                            <a href="all-reviews" class="btn btn-outline-primary">View Reviews </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
		          </div>
            </div>
<?php include "footer.php"; ?>