<?php include 'header.php'; ?>
<?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

     // API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchtherapistslug&slug=" . $slug;

    // Fetch therapist details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $userdetails = json_decode($data);

        if (!empty($userdetails) && isset($userdetails[0])) {
            $userdetail = $userdetails[0];

            // 🧠 Basic details
            $user_id = $userdetail->id ?? '';
            $fullName = htmlspecialchars(trim(($userdetail->first_name ?? '') . ' ' . ($userdetail->last_name ?? '') . ' ' . ($userdetail->middle_name ?? '')));

            // 📋 Profile info
            $nationality = $userdetail->nationality ?? '';
            $languages = $userdetail->languages ?? '';
            $website = $userdetail->website ?? '';
            $email = $userdetail->email ?? '';
            $facebook = $userdetail->facebook ?? '';
            $twitter = $userdetail->twitter ?? '';
            $instagram = $userdetail->instagram ?? '';
            $linkedin = $userdetail->linkedin ?? '';
            $phone = $userdetail->phone ?? '';
            $total_articles = intval($userdetail->total_articles ?? 0);
            $total_questions = intval($userdetail->total_questions ?? 0);
            $total_answers = intval($userdetail->total_answers ?? 0);
            $best_answers = intval($userdetail->best_answers ?? 0);
            $avgRating = floatval($userdetail->avg_rating ?? 0);
            $reviewCount = intval($userdetail->review_count ?? 0);
            $address = $userdetail->address ?? '';
            $bio = $userdetail->bio ?? '';
            $experience_years = $userdetail->experience_years ?? '';
            $specializations_names = $userdetail->specializations_names ?? '';
            $sub_specialization_names = $userdetail->subspecializations_names ?? '';
            $professional_field_names = $userdetail->professional_field_names ?? '';
            $professional_title_names = $userdetail->professional_title_names ?? '';
            $work_with = $userdetail->work_with ?? '';
            $session_format = $userdetail->session_format ?? '';
            $consultation_days = $userdetail->consultation_days ?? '';
            $session_duration = $userdetail->session_duration ?? '';
            $rate = $userdetail->rate ?? '0';
            $qualification = $userdetail->qualification ?? '';
            $associations = $userdetail->associations ?? '';
            $certifications = $userdetail->certifications ?? '';

            // 📷 Photo fallback
            $photo = !empty($userdetail->photo)
                ? $siteurl . $imagePath . $userdetail->photo
                : $siteurl . "assets/img/user.jpg";

        } else {
            echo "<div class='alert alert-warning'>No therapist found for the provided slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching therapist data. Please try again later.</div>";
    }
} else {
    // No slug provided — redirect to homepage
    header("Location: {$siteurl}");
    exit;
}

// round to nearest 0.5 for display
$rounded = round($avgRating * 2) / 2;
$fullStars = (int) floor($rounded);
$halfStar = ($rounded - $fullStars) == 0.5 ? 1 : 0;
$emptyStars = 5 - $fullStars - $halfStar;
?>

<?php
// Check if logged-in user follows the profile
$followed = isFollowing($buyerId, $user_id);

// Get follower/following count
$followerCount = getFollowerCount($user_id);
$followingCount = getFollowingCount($user_id);
?>
    <!-- Instructor Profile Section -->
    <section id="instructor-profile" class="instructor-profile section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">

          <div class="col-lg-12">
            <div class="instructor-hero-banner" data-aos="zoom-out" data-aos-delay="200">
              <div class="hero-background">
                <div class="hero-overlay"></div>
              </div>
              <div class="hero-content">
                <div class="instructor-avatar">
                  <img src="<?php echo $photo; ?>" alt="Instructor" class="img-fluid">
                  <div class="status-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>Therapist</span>
                  </div>
                </div>
                <div class="instructor-info">
                  <h2><?php echo $fullName; ?></h2>
                 <p class="title">
              <?php echo htmlspecialchars($professional_field_names . ' | ' . $professional_title_names); ?>
          </p>

                  <div class="credentials">
                    <span class="credential"><?php echo $qualification; ?></span>
                    <span class="credential">Since <?php echo $experience_years; ?></span>
                    <span class="credential"><?php echo $followerCount; ?> Followers | <?php echo $followingCount; ?> Following</span>
                  </div>
                              <div class="rating-overview mt-3">
              <div class="stars" aria-hidden="true">
                <?php for ($i=0;$i<$fullStars;$i++): ?>
                  <i class="bi bi-star-fill text-warning"></i>
                <?php endfor; ?>
                <?php if ($halfStar): ?>
                  <i class="bi bi-star-half text-warning"></i>
                <?php endif; ?>
                <?php for ($i=0;$i<$emptyStars;$i++): ?>
                  <i class="bi bi-star"></i>
                <?php endfor; ?>
              </div>
              <span class="rating-text">
                <?php
                  if ($reviewCount > 0) {
                    // show one decimal if not integer, else show integer
                    $displayRating = number_format($avgRating, (floor($avgRating) == $avgRating) ? 0 : 1);
                    echo "{$displayRating} rating from " . number_format($reviewCount) . " reviews";
                  } else {
                    echo "No reviews yet";
                  }
                ?>
              </span>
        
            </div>
        <div class="profile-stats d-flex gap-3 mt-2">
        <p><strong>Articles:</strong> <?=$total_articles?></p>
        <p><strong>Questions Asked:</strong> <?=$total_questions?></p>
        <p><strong>Answers Given:</strong> <?=$total_answers?></p>
        <p><strong>Best Answers:</strong> <?=$best_answers?></p>
    </div>
                  <div class="contact-actions">

                   <a href="<?php echo $siteurl; ?>book-appointment/<?php echo $slug; ?>" class="btn-contact">
                                <i class="bi bi-hand-index"></i> Book Me
                            </a>
                            
             <button id="followBtn" 
        data-author-id="<?php echo $user_id; ?>" 
        class="btn <?php echo $followed ? 'btn-secondary' : 'btn-primary'; ?>">
            <?php echo $followed ? 'Unfollow' : 'Follow'; ?>
        </button>

         <?php if ($activeLog == 1): ?>
                      <a type="button" class="btn btn-danger m-1" data-bs-toggle="modal" data-bs-target="#reportuserModal">
                        <i class="bi bi-flag"></i> Report
                      </a>
                    
                    <?php endif; ?>
                     <a href="mailto:<?php echo $email; ?>" class="btn-contact">
                                <i class="bi bi-envelope"></i> Contact
                            </a>
                                    <?php if (!empty($website)): ?>
            <a href="<?php echo $website; ?>" class="btn-visit" target="_blank" rel="noopener">
            <i class="bi bi-globe"></i> Visit Website
            </a>
        <?php endif; ?>
                            <div class="social-media">
            <?php if (!empty($linkedin)): ?>
                <a href="<?php echo $linkedin; ?>" target="_blank">
                <i class="bi bi-linkedin"></i>
                </a>
            <?php endif; ?>

            <?php if (!empty($twitter)): ?>
                <a href="<?php echo $twitter; ?>" target="_blank">
                <i class="bi bi-twitter-x"></i>
                </a>
            <?php endif; ?>

            <?php if (!empty($instagram)): ?>
                <a href="<?php echo $instagram; ?>" target="_blank">
                <i class="bi bi-instagram"></i>
                </a>
            <?php endif; ?>

            <?php if (!empty($facebook)): ?>
                <a href="<?php echo $facebook; ?>" target="_blank">
                <i class="bi bi-facebook"></i>
                </a>
            <?php endif; ?>


            </div>

                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="row gy-5 mt-4">

          <div class="col-lg-8">
            <div class="content-tabs" data-aos="fade-right" data-aos-delay="300">

              <ul class="nav nav-tabs custom-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#instructor-profile-about" type="button" role="tab">
                    <i class="bi bi-person"></i>
                    About
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-profile-experience" type="button" role="tab">
                    <i class="bi bi-pen"></i>
                    My Blog
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-profile-reviews" type="button" role="tab">
                    <i class="bi bi-star"></i>
                    Reviews
                  </button>
                </li>
              </ul>

              <div class="tab-content custom-tab-content">

                <div class="tab-pane fade show active" id="instructor-profile-about" role="tabpanel">
                  <div class="about-content">
                    <div class="bio-section">
                      <h4>Professional Biography</h4>
                    <?php echo $bio; ?>
                    </div>

                    <?php if (!empty($specializations_names) || !empty($sub_specialization_names)) : ?>
                <div class="expertise-grid">
                    <h4>Specializations</h4>
                    <div class="skills-grid">
                        <?php if (!empty($specializations_names)) : ?>
                            <div class="skill-item">
                                <i class="bi bi-mortarboard"></i>
                                <span><?php echo $specializations_names; ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($sub_specialization_names)) : ?>
                            <div class="skill-item">
                                <i class="bi bi-mortarboard"></i>
                                <span><?php echo $sub_specialization_names; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                  </div>
                </div>
            <div class="tab-pane fade" id="instructor-profile-experience" role="tabpanel">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">My Blog</h4>

      <div>
        <a href="<?php echo $siteurl; ?>article/<?php echo $slug; ?>" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-grid"></i> View All
        </a>
      </div>
    </div>
    <div id="blog-posts" class="blog-posts section">
    <div class="row gy-4">
      <?php
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);
      $limit = 6;
      $count = 0;

      if ($data !== false) {
          $blogs = json_decode($data);

          if (!empty($blogs)) {
              foreach ($blogs as $blog) {
                  if (
                      isset($blog->status, $blog->user_id)
                      && strtolower($blog->status) === 'active'
                      && $blog->user_id == $user_id  && $blog->group_id == ''
                  ) {
                      $count++;
                      if ($count > $limit) break; // ✅ Limit to 6

                      $blogId = $blog->id;
                      $title = htmlspecialchars($blog->title);
                      $slug = htmlspecialchars($blog->slug);
                      $views = htmlspecialchars($blog->views);
                      $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
                      $content = limitWords(strip_tags($blog->article), 4);
                      $date = date('F d, Y', strtotime($blog->created_at));
                      $categoryNames = !empty($blog->category_names) ? explode(',', $blog->category_names) : ['General'];
                      $category = htmlspecialchars(trim($categoryNames[0]));
                      $subcategoryNames = !empty($blog->subcategory_names) ? explode(',', $blog->subcategory_names) : [];
                      $subcategory = isset($subcategoryNames[0]) ? htmlspecialchars(trim($subcategoryNames[0])) : '';
                       $photo = !empty($blog->photo)
                          ? $siteurl . $imagePath . $blog->photo
                          : $siteurl . "assets/img/user.jpg";
                      $blogimage = !empty($blog->featured_image)
                          ? $siteurl . $imagePath . $blog->featured_image
                          : $siteurl . "assets/img/default-blog.jpg";
                       $blogUrl = $siteurl . "blog-details/" . $slug;
                      ?>
                      <div class="col-lg-4 col-md-6">
                        <article class="position-relative h-100 shadow-sm rounded-4 overflow-hidden">
                          <div class="post-img position-relative overflow-hidden">
                            <img src="<?php echo $blogimage; ?>" class="img-fluid w-100" alt="<?php echo $title; ?>">
                            <span class="post-category"><?php echo $subcategory; ?></span>
                             <span class="post-date"><?php echo $category; ?></span>
                          </div>

                          <div class="post-content d-flex flex-column p-3">
                             <div class="post-meta mb-2">
                        <i class="bi bi-clock"></i> <span class="ps-2"><?php echo $date; ?></span>
                        </div>
                            <h3 class="post-title"><?php echo $title; ?></h3>
                            <div class="meta d-flex align-items-center">
                              <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> 
                                <span class="ps-2"><?php echo $author; ?></span>
                              </div>
                              <span class="px-3 text-black-50">/</span>
                              <div class="d-flex align-items-center">
                                <i class="bi bi-eye"></i> 
                                <span class="ps-2"><?php echo $views; ?></span>
                              </div>
                            </div>
                            <p><?php echo $content; ?></p>
                            <hr>
                            <a href="<?php echo $blogUrl; ?>" class="readmore stretched-link">
                              <span>Read More</span>
                              <i class="bi bi-arrow-right"></i>
                            </a>
                          </div>
                        </article>
                      </div>
                      <?php
                  }
              }

              if ($count === 0) {
                  echo "<p class='text-center'>No active blogs found for this therapist.</p>";
              }
          } else {
              echo "<p class='text-center'>No blogs available.</p>";
          }
      } else {
          echo "<p class='text-center text-danger'>Unable to fetch blog data. Please try again later.</p>";
      }
      ?>
    </div>
    </div>
  </div>
</div>

  <div class="tab-pane fade" id="instructor-profile-reviews" role="tabpanel">
                  <div class="reviews-container">

                  <div class="comment-form section">
                  <div class="container">
                    <?php if ($activeLog == 1) { ?>
                    <form id="posttherapistreview" method="POST">
                      <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                      <h4>Give Review</h4>
                      <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                      <input name="group_id" type="hidden" class="form-control" value="<?php echo $user_id; ?>">
                            <!-- Star rating -->
                      <div class="mb-3">
                        <label class="form-label">Your rating</label>
                        <div class="star-rating" role="radiogroup" aria-label="Rating">
                          <button type="button" class="star" data-value="1" aria-label="1 star">☆</button>
                          <button type="button" class="star" data-value="2" aria-label="2 stars">☆</button>
                          <button type="button" class="star" data-value="3" aria-label="3 stars">☆</button>
                          <button type="button" class="star" data-value="4" aria-label="4 stars">☆</button>
                          <button type="button" class="star" data-value="5" aria-label="5 stars">☆</button>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                      </div>
                      <div class="row">
                          <input type="hidden" value="post_reviewtherapist" name="action">
                        <div class="col form-group">
                          <textarea name="comment" class="editor" placeholder="Your Review*"></textarea>
                        </div>
                      </div>

                      <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="submit-btn">Post Review</button>
                      </div>

                    </form>

                    <?php } ?>

                  </div>
                </div>

<?php
//  MAIN COMMENTS (rendered as review-cards) with pagination (12 per page)
$sitelink = $siteurl . "script/";
$comments_url = $sitelink . "user.php?action=reviewtherapisdata&therapist_id=" . intval($user_id);

$data = curl_get_contents($comments_url);
if ($data !== false) {
    $comments = json_decode($data);
    if (!empty($comments) && is_array($comments)) {

        // Pagination setup
        $reviewsPerPage = 12;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $totalReviews = count($comments);
        $totalPages = max(1, ceil($totalReviews / $reviewsPerPage));
        if ($page > $totalPages) $page = $totalPages;
        $start = ($page - 1) * $reviewsPerPage;

        // Slice comments for current page
        $commentsToShow = array_slice($comments, $start, $reviewsPerPage);

        // Render comments
        foreach ($commentsToShow as $comment) {
            $username     = htmlspecialchars(trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? '')) ?: 'Anonymous');
            $avatar       = htmlspecialchars($siteurl . $imagePath . ($comment->photo ?? 'default.png'));
            $commentText  = nl2br(htmlspecialchars($comment->comment ?? ''));
            $rating       = max(0, min(5, intval($comment->rating ?? 0)));
            $commentID    = intval($comment->id ?? 0);
            $user_id      = intval($comment->user_id ?? 0);
            $created_date = !empty($comment->created_at) ? date('F d, Y \a\t h:i A', strtotime($comment->created_at)) : '';
            ?>
            <div class="review-card">
              <div class="review-header">
                <img src="<?php echo $avatar; ?>" alt="<?php echo $username; ?>" class="reviewer-avatar">
                <div class="reviewer-info">
                  <h6><?php echo $username; ?></h6>
                  <?php if ($created_date): ?>
                    <p class="text-muted small mb-1"><?php echo $created_date; ?></p>
                  <?php endif; ?>
                  <div class="review-rating" aria-hidden="true">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="bi <?php echo ($i <= $rating) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
              <p><?php echo $commentText; ?></p>
            </div>
            <?php
        }

        // Pagination UI
        if ($totalPages > 1) {
            // Preserve existing GET params and ensure group_id present
            $baseParams = $_GET;
            if (!isset($baseParams['therapist_id']) && !empty($user_id)) {
                $baseParams['therapist_id'] = $user_id;
            }
            ?>
            <nav aria-label="Reviews pagination" class="mt-4">
              <ul class="pagination justify-content-center">
                <?php
                // Previous
                $prev = $baseParams;
                $prev['page'] = max(1, $page - 1);
                ?>
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                  <a class="page-link" href="?<?php echo http_build_query($prev); ?>" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                  </a>
                </li>

                <?php
                // Page numbers (limit display if many pages)
                $displayRange = 5; // show up to 5 page links centered around current
                $startPage = max(1, $page - floor($displayRange / 2));
                $endPage = min($totalPages, $startPage + $displayRange - 1);
                if ($endPage - $startPage + 1 < $displayRange) {
                    $startPage = max(1, $endPage - $displayRange + 1);
                }
                for ($i = $startPage; $i <= $endPage; $i++):
                    $p = $baseParams;
                    $p['page'] = $i;
                    ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                      <a class="page-link" href="?<?php echo http_build_query($p); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php
                // Next
                $next = $baseParams;
                $next['page'] = min($totalPages, $page + 1);
                ?>
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                  <a class="page-link" href="?<?php echo http_build_query($next); ?>" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
            <?php
        }
    } else {
        echo "<p class='text-center text-muted py-3'>No reviews yet.</p>";
    }
} else {
    echo "<p class='text-center text-danger py-3'>Unable to fetch reviews.</p>";
}
?>



                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="sidebar-widgets" data-aos="fade-left" data-aos-delay="300">

            <div class="contact-widget">
            <h4>Get in Touch</h4>
            <div class="contact-info">
                <?php if (!empty($email)): ?>
                <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <a href="mailto:<?php echo htmlspecialchars($email); ?>">
                    <?php echo htmlspecialchars($email); ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($phone)): ?>
                <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <a href="tel:<?php echo htmlspecialchars($phone); ?>">
                    <?php echo htmlspecialchars($phone); ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($address)): ?>
                <div class="contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <span><?php echo htmlspecialchars($address); ?></span>
                </div>
                <?php endif; ?>
            </div>

        
                <div class="office-hours">
                <h6>Consultation Details</h6>
                <p>
                    <?php if (!empty($consultation_days)): ?>
                    <strong>Preferred Days:</strong> <?php echo htmlspecialchars($consultation_days); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($session_duration)): ?>
                    <strong>Session Duration:</strong> <?php echo htmlspecialchars($session_duration); ?>
                    <?php endif; ?>

                    <?php if (!empty($rate)): ?>
                    <p><strong>Fee:</strong> <?php echo $sitecurrency . $rate; ?></p>
                    <?php endif; ?>

                    
                </p>

               
                </div>
       

        
                <div class="extra-info mt-3">
                    <?php if (!empty($languages)): ?>
                <p>    <strong>Language:</strong> <?php echo $languages; ?></p>
                    <?php endif; ?>
                <?php if (!empty($work_with)): ?>
                    <p><strong>Works With:</strong> <?php echo htmlspecialchars($work_with); ?></p>
                <?php endif; ?>
                <?php if (!empty($session_format)): ?>
                    <p><strong>Session Format:</strong> <?php echo htmlspecialchars($session_format); ?></p>
                <?php endif; ?>

              
                </div>
       
            </div>

              <?php if (!empty($associations) || !empty($certifications)): ?>
                <div class="achievements-widget">
                    <h4>Recognition &amp; Awards</h4>
                    <div class="achievement-list">

                    <?php if (!empty($associations)): ?>
                        <div class="achievement-item">
                        <i class="bi bi-people"></i>
                        <div class="achievement-text">
                            <h6>Professional Associations</h6>
                            <p><?php echo nl2br(htmlspecialchars($associations)); ?></p>
                        </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($certifications)): ?>
                        <div class="achievement-item">
                        <i class="bi bi-patch-check"></i>
                        <div class="achievement-text">
                            <h6>Certifications &amp; Recognitions</h6>
                            <p><?php echo nl2br(htmlspecialchars($certifications)); ?></p>
                        </div>
                        </div>
                    <?php endif; ?>

                    </div>
                </div>
                <?php endif; ?>


            </div>
          </div>

        </div>

      </div>

    </section><!-- /Instructor Profile Section -->

    <!-- Report Product Modal -->
<div class="modal fade" id="reportuserModal" tabindex="-1" aria-labelledby="reportuserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="reportblogForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="reportuserModalLabel">
            Report Therapist: <?php echo htmlspecialchars($fullName); ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="report_message" class="text-center mb-2"></div>

          <input type="hidden" name="action" value="report_user">
          <!-- Reporter (logged in user) -->
          <input type="hidden" name="reporter_id" value="<?php echo htmlspecialchars($buyerId); ?>">

          <!-- User being reported (author) -->
          <input type="hidden" name="reported_user_id" value="<?php echo htmlspecialchars($user_id); ?>">


          <div class="mb-2">
            <label for="reason" class="form-label">Reason for Reporting</label>
            <select class="form-select" name="reason"  id="reason" required onchange="toggleCustomReason(this.value)">
              <option value="">Select Reason</option>
              <option value="Harassment or Abusive Behavior">Harassment or Abusive Behavior</option>
              <option value="Spam or Misleading Information">Spam or Misleading Information</option>
              <option value="Inappropriate or Offensive Profile">Inappropriate or Offensive Profile</option>
              <option value="Impersonation or Fake Account">Impersonation or Fake Account</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="mb-2" id="customReasonContainer" style="display:none;">
            <label for="custom_reason" class="form-label">Provide Details</label>
            <textarea class="form-control" name="custom_reason" id="custom_reason" rows="3" placeholder="Describe the issue..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="submit_report" id="submitReport" class="btn btn-danger">Submit Report</button>
        </div>

      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>