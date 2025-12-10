

<?php include "header.php";  
// Fetch auth cookies
$adminAuth     = $_COOKIE['admin_auth']     ?? '';
$vendorAuth    = $_COOKIE['vendor_auth']    ?? '';
$therapistAuth = $_COOKIE['therapist_auth'] ?? '';
$userAuth      = $_COOKIE['user_auth']      ?? '';

// Initialize
$activeUserId = null;
$activeLog = 0;

// Determine active user
if (!empty($adminAuth)) {
    $activeUserId = $adminAuth;
    $activeLog = 1;
}
if (!empty($vendorAuth)) {
    $activeUserId = $vendorAuth;
    $activeLog = 1;
}
if (!empty($therapistAuth)) {
    $activeUserId = $therapistAuth;
    $activeLog = 1;
}
if (!empty($userAuth)) {
    $activeUserId = $userAuth;
    $activeLog = 1;
}

?>

<?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Build API URL
    $sitelink = $siteurl . "script/";
    $apiUrl = $sitelink . "admin.php?action=addviewsquest&slug=" . $slug;
    $response = curl_get_contents($apiUrl);

    $url = $sitelink . "admin.php?action=fetchquestionslug&slug=" . $slug;
    $shareUrl = $siteurl . "single-questions/" . urlencode($slug);

    // Fetch blog details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 

            // Extract details
            $title = $blogdetail->title ?? '';
            $bio = $blogdetail->bio ?? '';
            $question_id = $blogdetail->id ?? '';
            $questionAuthorId = intval($blogdetail->user_id ?? $blogdetail->userId ?? $blogdetail->user ?? 0);
            $article = $blogdetail->article ?? '';
            $user_id = $blogdetail->user_id ?? $blogdetail->userId ?? $blogdetail->user ?? 0;
            $tags = $blogdetail->tags ?? '';
            $question_id = $blogdetail->id ?? '';
            $comment_count = $blogdetail->comment_count ?? '0';
            $total_articles = $blogdetail->total_articles ?? '';
            $total_questions = $blogdetail->total_questions ?? '';
            $total_answers = $blogdetail->total_answers ?? '';
            $best_answers = $blogdetail->best_answers ?? '';
            $category = $blogdetail->category_names ?? '';
            $subcategory = $blogdetail->subcategory_names ?? '';
            $status = $blogdetail->status ?? '';
            $created_at = date('F d, Y \a\t h:i A', strtotime($blogdetail->created_at));


             // Determine Author Display
                $anonymous = intval($blogdetail->anonymous ?? 0);
                $canAccept = ($activeLog == 1 && $activeUserId == $questionAuthorId);
                if ($anonymous === 1) {
                    $authorDisplay = "Anonymous";
                } else {
                    $firstName = htmlspecialchars($blogdetail->first_name ?? '');
                    $lastName = htmlspecialchars($blogdetail->last_name ?? '');
                    $authorDisplay = trim("$firstName $lastName") ?: "Unknown User";
                }

        } else {
            echo "<div class='alert alert-warning'>No question found with the given slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching question data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>


<?php
   $shortBio = limitWords(strip_tags($bio), 10);
    $isTruncated = (str_word_count(strip_tags($bio)) > 10);
// Check if logged-in user follows the profile
$followed = isFollowing($buyerId, $user_id);

// Get follower/following count
$followerCount = getFollowerCount($user_id);
$followingCount = getFollowingCount($user_id);
?>

  <main class="main">



    <div class="container">
      <div class="row">

        <div class="col-lg-8">
           

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
                <div class=" d-flex justify-content-between align-items-center">
                <?php 
                $item_type = 'question';
                $bookmarked = is_bookmarked($buyerId, $question_id, $item_type); 
                
                ?>
                <button class="bookmarkBtn btn <?php echo $bookmarked ? 'btn-primary' : 'btn-outline-primary'; ?> mb-3" 
        data-item-id="<?php echo $question_id; ?>"
        data-item-type="<?php echo $item_type; ?>">
    <i class="bi <?php echo $bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'; ?>"></i> 
    <?php echo $bookmarked ? 'Bookmarked' : 'Bookmark'; ?>
</button>
                <?php
            if ($activeLog == 1) {
            ?>

            <a class="btn btn-md btn-outline-primary mb-3" href="<?php echo $siteurl; ?>create-questions.php">ASK A QUESTION</a>
            <?php
            }
            ?>
      </div>
              <article class="article">

                <h2 class="title"><?php echo $title; ?></h2>

                <div class="meta-top">
                  <ul>
                  <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href=""><?php echo $authorDisplay; ?></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="#"><time datetime="2020-01-01"><?php echo $created_at; ?></time></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i><a href="#">
                      <?php 
                          echo $comment_count . ' comment' . ($comment_count != 1 ? 's' : ''); 
                      ?>
                  </a></li>
                                    </ul>
                </div><!-- End meta top -->

                <div class="content">
                 <?php echo $article; ?>
                </div><!-- End post content -->

                <div class="meta-bottom">
                  <i class="bi bi-folder"></i>
                  <ul class="cats">
                    <li><a href="#"><?php echo $category; ?></a></li>
                  </ul>

                  <i class="bi bi-tags"></i>
                  <ul class="tags">
                    <li><a href="#"><?php echo $subcategory; ?></a></li>
                  </ul>

                   <i class="bi bi-tags"></i>
                  <ul class="tags">
                    <li><a href="#"><?php echo $tags; ?></a></li>
                  </ul>

                  <div class="like-section mt-3">
           <div class="d-inline-block me-2">
        <button class="btn btn-outline-success btn-sm" id="question-upvote"
                data-question-id="<?php echo $question_id; ?>"
                title="Upvote">
            <i class="bi bi-hand-thumbs-up"></i>
        </button>

        <span id="question-score" class="mx-2">0</span>

        <button class="btn btn-outline-danger btn-sm" id="question-downvote"
                data-question-id="<?php echo $question_id; ?>"
                title="Downvote">
            <i class="bi bi-hand-thumbs-down"></i>
        </button>
    </div>
        <div class="d-inline-block">
          <button class="btn btn-outline-primary btn-sm" id="webShareBtn" title="Share this post" data-title="<?php echo htmlspecialchars($title); ?>" data-url="<?php echo htmlspecialchars($shareUrl); ?>"><i class="bi bi-share-fill"></i> Share</button>
        </div>
    </div>

          <div class="article-share mt-2">
          <span>Share:</span>
          <button type="button"
                  class="btn btn-sm btn-primary"
                  id="webShareBtn"
                  title="Share this post"
                  data-title="<?php echo htmlspecialchars($title); ?>"
                  data-url="<?php echo htmlspecialchars($shareUrl); ?>">
            <i class="bi bi-share-fill"></i> Share
          </button>

          <!-- Social Links -->
          <a href="https://twitter.com/intent/tweet?url=<?php echo  urlencode($shareUrl); ?>&text=<?php  echo urlencode($title); ?>" target="_blank" rel="noopener" title="Share on Twitter">
            <i class="bi bi-twitter"></i>
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?php  echo urlencode($shareUrl); ?>" target="_blank" rel="noopener" title="Share on Facebook">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php  echo urlencode($shareUrl); ?>&title=<?php echo urlencode($title); ?>" target="_blank" rel="noopener" title="Share on LinkedIn">
            <i class="bi bi-linkedin"></i>
          </a>
                 
         <?php if ($activeLog == 1): ?>
                      <a type="button" class="btn btn-danger m-1" data-bs-toggle="modal" data-bs-target="#reportitemModal">
                        <i class="bi bi-flag"></i> Report
                      </a>

                    <?php endif; ?>
          </div>
        
          </div><!-- End meta bottom -->
    

              </article>

          </section><!-- /Blog Details Section -->

          
   <div class="mt-4 p-3 border rounded">
    <h6>Was this helpful?</h6>

    <button class="btn btn-success btn-sm help-vote"
            data-vote="yes"
            data-type="<?= $item_type ?>" 
            data-id="<?= $question_id ?>"
            data-user="<?= $buyerId ?? '' ?>">
        Yes
    </button>

    <button class="btn btn-danger btn-sm help-vote"
            data-vote="no"
            data-type="<?= $item_type ?>" 
            data-id="<?= $question_id ?>"
            data-user="<?= $buyerId ?? '' ?>">
        No
    </button>

    <div id="feedback-response" class="mt-2 text-info" style="display:none;"></div>
</div>
           <!-- Blog Comments Section -->
<section id="blog-comments" class="blog-comments section">
  <div class="container">
    <input type="hidden" id="siteurl" value="<?php echo htmlspecialchars($siteurl); ?>">
    <input type="hidden" id="activeLog" value="<?= intval($activeLog) ?>">
    <input type="hidden" id="currentUserId" value="<?= intval($buyerId) ?>">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <h4 class="comments-count mb-0"><?php echo $comment_count . ' comment' . ($comment_count != 1 ? 's' : '');  ?></h4>
    </div>
    <?php
    // MAIN COMMENTS
    $sitelink = $siteurl . "script/";
    $comments_url = $sitelink . "user.php?action=answersdata&question_id=" . $question_id;

    // Wrap the comments in a container so we can replace them via AJAX when sorting
    echo '<div id="answers-list">';

    $data = curl_get_contents($comments_url);
    if ($data !== false) {
    $comments = json_decode($data);
    if (!empty($comments)) {
      foreach ($comments as $comment) {
                
                $anonymous = intval($comment->anonymous ?? 0);
                if ($anonymous === 1) {
                    $username = "Anonymous";
                } else {
                    $firstName = htmlspecialchars($comment->first_name ?? '');
                    $lastName = htmlspecialchars($comment->last_name ?? '');
                    $username = trim("$firstName $lastName") ?: "Unknown User";
                }
                $avatar = $siteurl . $imagePath . ($comment->photo ?? 'default.png');
                $commentText = $comment->answer;
                $ans_up = intval($comment->upvotes ?? 0);
                $ans_down = intval($comment->downvotes ?? 0);
                $ans_score = intval($comment->score ?? 0);
                $commentID = $comment->id;
                $user_id = $comment->user_id;
                $isAccepted = intval($comment->is_accepted ?? 0);
                $isBest = intval($comment->is_best ?? 0);
                $created_date = date('F d, Y \a\t h:i A', strtotime($comment->created_at));

                echo "<div id='question-$commentID' class='comment ms-0 mt-3'>";
                echo "  <div class='d-flex'>";
                echo "    <div class='comment-img'><img src='$avatar' alt='Avatar'></div>";
                echo "    <div>";
                echo "      <h5><span>$username</span>";

                // Inline buttons
                echo " <span class='action-buttons ms-2'>";
                if ($activeLog == 1) {
                    echo " <a href='#' class='replyanswer' data-question-id='$commentID'><i class='bi bi-reply-fill'></i> Reply</a>";
                }
                if ($user_id == $buyerId) {
                    echo " <a href='#' id='$commentID' class='btn btn-sm deleteanswer'><i class='bi bi-trash'></i></a>";
                }
                if ($canAccept && !$isAccepted) {
                    echo " <a href='#' id='$commentID' class='btn btn-sm acceptanswer'><i class='bi bi-check-circle'></i> Accept</a>";
                }
                if ($canAccept && !$isBest) {
                   echo " <a href='#' id='$commentID' class='btn btn-sm acceptbest'><i class='bi bi-check-circle'></i> Accept as Best</a>";
                }
                if($isAccepted){
                  echo " <span class='badge bg-success ms-2'>Accepted Answer</span>";
                }
                if($isBest){
                  echo " <span class='badge bg-primary ms-2'>Best Answer</span>";
                }


                // Inline View Replies (if exists)
                $replies_url = $sitelink . "user.php?action=answersreplydata&comment_id=" . $commentID;
                $reply_data = curl_get_contents($replies_url);
                $reply_count = json_decode($reply_data);
        if (!empty($reply_count)) {
          echo " <a href='#' class='view-answer-link btn btn-sm' data-comment-id='$commentID'>View Replies (" . count($reply_count) . ")</a>";
        }
        echo "</span>";
        // Vote buttons for this answer/comment
                echo " <span class='vote-buttons ms-2'>";
                echo "<button class='btn btn-sm btn-outline-success comment-upvote' data-answer-id='$commentID' title='Upvote'><i class='bi bi-hand-thumbs-up'></i></button> ";
                echo "<small class='text-success ms-1' id='answer-up-$commentID'>{$ans_up}</small> ";
                echo "<span class='comment-score mx-2' id='answer-score-$commentID'>{$ans_score}</span> ";
                echo "<small class='text-danger me-1' id='answer-down-$commentID'>{$ans_down}</small> ";
                echo "<button class='btn btn-sm btn-outline-danger comment-downvote' data-answer-id='$commentID' title='Downvote'><i class='bi bi-hand-thumbs-down'></i></button>";
                echo "</span>";

                echo "      </h5>";
                echo "      <time datetime='{$comment->created_at}'>$created_date</time>";
                echo "      <p>$commentText</p>";
                echo "    </div>";
                echo "  </div>";
                echo "</div>";

                // Reply form
                echo "
                <form class='answer-form mt-3' id='answer-form-$commentID' style='display:none;'>
                  <div class='text-center mt-1 message-box'></div>
                  <input type='hidden' name='user_id' value='$buyerId'>
                  <input type='hidden' name='question_id' value='$question_id'>
                  <input type='hidden' name='action' value='post_answers'>
                  <input type='hidden' name='parent_id' value='$commentID'>
                  <div class='input-group'>
                    <textarea name='comment' class='editor' placeholder='Write a reply...'></textarea>
                    <div class='input-group-prepend'>
                      <button type='submit' class='btn btn-sm btn-primary'>Reply</button>
                    </div>
                  </div>
                </form>
                ";

                // Nested replies section
                echo "<div class='nested-replies' id='replies-$commentID' style='display:none;'>";
                renderAnswers($commentID, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink, $canAccept);
                echo "</div>";
            }
          } else {
              echo "<p>No comments yet.</p>";
            }
          } else {
            echo "<div class='alert alert-danger'>Unable to load comments at the moment.</div>";
          }

          // close answers-list wrapper
          echo '</div>';
    ?>
  </div>
</section>

              
            <?php
            if ($activeLog == 1){

                ?>
           <!-- Comment Form Section -->
          <section  class="comment-form section">
            <div class="container">

              <form id="postanswer" method="POST">
                 <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                <h4>Answer Question</h4>
                 <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                 <input name="question_id" type="hidden" class="form-control" value="<?php echo $question_id; ?>">
                <div class="row">
                    <input type="hidden" value="post_answers" name="action">
                  <div class="col form-group">
                    <textarea name="comment" class="editor" placeholder="Your Answer*"></textarea>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary" id="submit-btn">Post Answer</button>
                </div>

              </form>

            </div>
          </section><!-- /Comment Form Section -->
                    <?php } ?>

                    <?php
                    if ($activeLog == 0){
                      ?>
           <section class="comment-form section">
            <div class="container">
            <h6><a href="<?php echo $siteurl; ?>login.php"><u>Sign in</u></a> to answer</h6>
            </div>
            </section>
                      <?php
                    }

                    ?>

</div>



			
			<div class="col-lg-4 sidebar">

          <div class="widgets-container">

          <?php 
          if ($anonymous !== 1) {
             ?>
            <!-- Blog Author Widget -->
           <div class="blog-author-widget widget-item">
  <div class="">
    <div class="d-flex align-items-center w-100">
      <!-- Author Image -->
      <img 
        src="<?php echo $siteurl . $imagePath . ($blogdetail->photo ?? 'default.png'); ?>" 
        class="rounded-circle flex-shrink-0 me-3" 
        alt="<?php echo htmlspecialchars($authorDisplay); ?>" 
        width="80" height="80">
		
		  <h4><?php echo htmlspecialchars($authorDisplay); ?></h4>
</div>

      <div>
      
        <div class="profile-stats d-flex gap-3 mt-2">
        <p><strong>Articles:</strong> <?=$total_articles?></p>
        <p><strong>Questions Asked:</strong> <?=$total_questions?></p>
        <p><strong>Answers Given:</strong> <?=$total_answers?></p>
        <p><strong>Best Answers:</strong> <?=$best_answers?></p>
    </div>


        <div class="social-links">
          <?php if (!empty($facebook)): ?>
            <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank"><i class="bi bi-facebook"></i></a>
          <?php endif; ?>
          <?php if (!empty($instagram)): ?>
            <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank"><i class="bi bi-instagram"></i></a>
          <?php endif; ?>
          <?php if (!empty($linkedin)): ?>
            <a href="<?php echo htmlspecialchars($linkedin); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
          <?php endif; ?>
        </div>
            <div class="profile-actions mt-3 gap-2 ">
        
        <button id="followBtn" 
        data-author-id="<?php echo $user_id; ?>" 
        class="btn <?php echo $followed ? 'btn-secondary' : 'btn-primary'; ?>">
            <?php echo $followed ? 'Unfollow' : 'Follow'; ?>
        </button>

         <?php if ($activeLog == 1): ?>
                      <a type="button" class="btn btn-danger m-1" data-bs-toggle="modal" data-bs-target="#reportuserModal">
                        <i class="bi bi-flag"></i> Report
                      </a>

                    <?php else: ?>
                      <button class="btn btn-secondary m-1" disabled>
                        <i class="bi bi-flag"></i>Sign in to Report
                      </button>
                    <?php endif; ?>
    </div>

      </div>


    <?php 
      $shortBio = limitWords(strip_tags($bio), 10);
      $isTruncated = (str_word_count(strip_tags($bio)) > 10);
    ?>
  <p><?php echo $followerCount; ?> Followers | <?php echo $followingCount; ?> Following</p>
<<<<<<< HEAD
         <div class="mt-3 text-center bio-text">
  
  <!-- Short Bio: OK to stay as span -->
  <span class="bio-short"><?php echo $shortBio; ?></span>

  <?php if ($isTruncated): ?>
    <!-- Full Bio: MUST be a <div> because TinyMCE content has <p>, <br>, etc. -->
    <div class="bio-full d-none">
        <?php echo $bio; ?>
    </div>

    <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
  <?php endif; ?>

</div>
=======
    <p class="mt-3 text-center bio-text">
      <span class="bio-short"><?php echo $shortBio; ?></span>
      <?php if ($isTruncated): ?>
        <span class="bio-full d-none"><?php echo $bio; ?></span>
        <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
      <?php endif; ?>
    </p>
  </div>
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
</div><!--/Blog Author Widget -->

<?php 
          }
          ?>

          </div>
      </div>
 </div>
</div>
</div>


<div class="modal fade" id="reportitemModal" tabindex="-1" aria-labelledby="reportitemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="reportblogForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="reportitemModalLabel">
            Report Question : <?php echo $title; ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="report_message" class="text-center mb-2"></div>

          <input type="hidden" name="action" value="report_item">
          <!-- Reporter (logged in user) -->
          <input type="hidden" name="reporter_id" value="<?php echo $buyerId; ?>">

          <!-- User being reported (author) -->
          <input type="hidden" name="reported_item_id" value="<?php echo $question_id; ?>">

           <input type="hidden" name="reported_item_type" value="question">
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

<!-- Report Product Modal -->
<div class="modal fade" id="reportuserModal" tabindex="-1" aria-labelledby="reportuserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="reportblogForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="reportuserModalLabel">
            Report Author: <?php echo htmlspecialchars($author); ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="report_message" class="text-center mb-2"></div>

          <input type="hidden" name="action" value="report_user">
          <!-- Reporter (logged in user) -->
          <input type="hidden" name="reporter_id" value="<?php echo htmlspecialchars($buyerId); ?>">

          <!-- User being reported (author) -->
          <input type="hidden" name="reported_user_id" value="<?php echo htmlspecialchars($AuthorId); ?>">


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
</main>
<?php include "footer.php"; ?>