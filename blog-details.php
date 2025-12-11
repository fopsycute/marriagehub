
<?php include "header.php";  ?>

<!-- Top Banner Advert -->
<?php
$placementSlug = 'blog-details-top-banner';
include "top-banner.php";
?>

<?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Build API URL
     $shareUrl = $siteurl . 'blog-details/' . $slug; // Clean URL
    $sitelink = $siteurl . "script/";
    $apiUrl = $sitelink . "admin.php?action=addviews&slug=" . $slug;
    $response = curl_get_contents($apiUrl);

    $url = $sitelink . "admin.php?action=fetchblogslug&slug=" . $slug;

    // Fetch blog details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 

            // Extract details
            
            $title = $blogdetail->title ?? '';
            $article = $blogdetail->article ?? '';
            $blogtitle = $blogdetail->title ?? '';
            $AuthorId = intval($blogdetail->user_id ?? 0);
            $tags = $blogdetail->tags ?? '';
            $like_count = $blogdetail->like_count ?? '';
            $facebook =  $blogdetail->facebook ?? '';
            $instagram =  $blogdetail->instagram ?? '';
            $linkedin =  $blogdetail->linkedin ?? '';
            $bio = $blogdetail->bio ?? '';
            $blog_id = $blogdetail->id ?? '';
            $comment_count = $blogdetail->comment_count ?? '0';
            $featured_image = $blogdetail->featured_image ?? '';
            $category = $blogdetail->category_names ?? '';
            $subcategory = $blogdetail->subcategory_names ?? '';
            $total_articles = $blogdetail->total_articles ?? '';
            $total_questions = $blogdetail->total_questions ?? '';
            $total_answers = $blogdetail->total_answers ?? '';
            $best_answers = $blogdetail->best_answers ?? '';
            $status = $blogdetail->status ?? '';
            $created_at = date('F d, Y \a\t h:i A', strtotime($blogdetail->created_at));
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));
            $blog_group_id = intval($blogdetail->group_id ?? 0);

            $blogimage = $siteurl . $imagePath . $featured_image;

            // Check if this is a group blog and validate membership
            if (isset($_GET['group_id']) && $blog_group_id > 0) {
                $requested_group_id = intval($_GET['group_id']);
                
                // Verify blog belongs to the requested group
                if ($blog_group_id !== $requested_group_id) {
                    die("<div class='alert alert-danger'>Invalid group context.</div>");
                }

                // Get group info
                $groupUrl = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $blog_group_id;
                $groupData = curl_get_contents($groupUrl);
                $groupInfo = json_decode($groupData);
                $groupCreatorId = 0;
                $groupSlug = '';
                
                if (!empty($groupInfo[0])) {
                    $groupCreatorId = $groupInfo[0]->user_id ?? 0;
                    $groupSlug = $groupInfo[0]->slug ?? '';
                }

                // Check access rights
                $adminAuth = $_COOKIE['admin_auth'] ?? '';
                $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
                $therapistAuth = $_COOKIE['therapist_auth'] ?? '';
                $userAuth = $_COOKIE['user_auth'] ?? '';
                
                $canAccess = false;

                // Admin or group creator has access
                if ($adminAuth || $vendorAuth == $groupCreatorId || $therapistAuth == $groupCreatorId || $userAuth == $groupCreatorId) {
                    $canAccess = true;
                } else {
                    // Check if user is an active group member
                    $activeUserId = $adminAuth ?: ($vendorAuth ?: ($userAuth ?: $therapistAuth));
                    if (!empty($activeUserId)) {
                        $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$blog_group_id}&user_id={$activeUserId}";
                        $memberData = curl_get_contents($checkMemberUrl);
                        
                        if ($memberData !== false) {
                            $memberResult = json_decode($memberData, true);
                            if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active') {
                                $canAccess = true;
                            }
                        }
                    }
                }

                // Restrict access if not authorized
                if (!$canAccess) {
                    echo "
                    <script>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Join Group to View',
                            html: 'You are not a member of this group. <br><b>Join the group to view this blog post.</b>',
                            confirmButtonText: 'Join Group',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{$siteurl}group/{$groupSlug}';
                            } else {
                                window.location.href = '{$siteurl}';
                            }
                        });
                    </script>";
                    exit;
                }
            }
        } else {
            echo "<div class='alert alert-warning'>No blog found with the given slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching blog data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>
<?php
// Check if logged-in user follows the profile
$followed = isFollowing($buyerId, $AuthorId);

// Get follower/following count
$followerCount = getFollowerCount($AuthorId);
$followingCount = getFollowingCount($AuthorId);

?>
  <main class="main">



    <div class="container">
      <div class="row">

        <div class="col-lg-8">
          

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container">
              <div class=" d-flex justify-content-between align-items-center">
                <?php 
                $item_type = 'blog';
                $bookmarked = is_bookmarked($buyerId, $blog_id, $item_type); 
                
                ?>
              
<button class="bookmarkBtn btn <?php echo $bookmarked ? 'btn-primary' : 'btn-outline-primary'; ?> mb-3" 
        data-item-id="<?php echo $blog_id; ?>"
        data-item-type="<?php echo $item_type; ?>">
    <i class="bi <?php echo $bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'; ?>"></i> 
    <?php echo $bookmarked ? 'Bookmarked' : 'Bookmark'; ?>
</button>
              <?php
            if ($activeLog == 1) {
            ?>

            <a class="btn btn-lg btn-primary mb-3" href="<?php echo $siteurl; ?>add-blog.php">Add Blog</a>
            <?php
            }
            ?>
            </div>
              
              <article class="article">

                <div class="post-img">
                  <img src="<?php echo $blogimage; ?>" alt="" class="img-fluid">
                </div>

                <h2 class="title"><?php echo $title; ?></h2>

                <div class="meta-top">
                  <ul>
                    <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href=""><?php echo $author; ?></a></li>
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

                <!-- Inline Advert after Blog Content -->
                <?php
                $placementSlug = 'blog-details-inline-ad';
                include "inline-ad.php";
                ?>

                <div class="meta-bottom">
                  <i class="bi bi-folder"></i>
                  <ul class="cats">
                    <li><a href="#"><?php echo $category; ?></a></li>
                  </ul>

                  <i class="bi bi-tags"></i>
                  <ul class="tags">
                    <li><a href="#"><?php echo $subcategory; ?></a></li>
                  </ul>

    <div class="like-section mt-3">
      <button class="btn btn-outline-danger" id="likeBtn"
              data-blog-id="<?php echo $blog_id; ?>"
              data-like-url="<?php echo $sitelink; ?>user.php">
        <i class="bi bi-hand-thumbs-up"></i>
        <span id="likeCount"><?php echo $like_count; ?></span> Likes
      </button> 
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


            </div>
          </section><!-- /Blog Details Section -->

   <div class="mt-4 p-3 border rounded">
    <h6>Was this helpful?</h6>

    <button class="btn btn-success btn-sm help-vote"
            data-vote="yes"
            data-type="<?= $item_type ?>" 
            data-id="<?= $blog_id ?>"
            data-user="<?= $buyerId ?? '' ?>">
        Yes
    </button>

    <button class="btn btn-danger btn-sm help-vote"
            data-vote="no"
            data-type="<?= $item_type ?>" 
            data-id="<?= $blog_id ?>"
            data-user="<?= $buyerId ?? '' ?>">
        No
    </button>

    <div id="feedback-response" class="mt-2 text-info" style="display:none;"></div>
</div>

           <!-- Blog Comments Section -->
<section id="blog-comments" class="blog-comments section">
  <div class="container">
    <h4 class="comments-count"> <?php echo $comment_count . ' comment' . ($comment_count != 1 ? 's' : '');  ?></h4>
    <?php
    // ðŸ”¹ MAIN COMMENTS
    $sitelink = $siteurl . "script/";
    $comments_url = $sitelink . "user.php?action=commentdata&blog_id=" . $blog_id;

    $data = curl_get_contents($comments_url);
    if ($data !== false) {
        $comments = json_decode($data);
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $username = trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? ''));
                $avatar = $siteurl . $imagePath . ($comment->photo ?? 'default.png');
                $commentText = $comment->comment;
                $commentID = $comment->id;
                $user_id = $comment->user_id;
                $created_date = date('F d, Y \a\t h:i A', strtotime($comment->created_at));

                echo "<div id='comment-$commentID' class='comment ms-0 mt-3'>";
                echo "  <div class='d-flex'>";
                echo "    <div class='comment-img'><img src='$avatar' alt='Avatar'></div>";
                echo "    <div>";
                echo "      <h5><span>$username</span>";

                // Inline buttons
                echo " <span class='action-buttons ms-2'>";
                if ($activeLog == 1) {
                    echo " <a href='#' class='reply' data-comment-id='$commentID'><i class='bi bi-reply-fill'></i> Reply</a>";
                }
                if ($user_id == $buyerId) {
                    echo " <a href='#' id='$commentID' class='btn btn-sm deletecomment'><i class='bi bi-trash'></i></a>";
                }

                // Inline View Replies (if exists)
                $replies_url = $sitelink . "user.php?action=repliesdata&comment_id=" . $commentID;
                $reply_data = curl_get_contents($replies_url);
                $reply_count = json_decode($reply_data);
                if (!empty($reply_count)) {
                    echo " <a href='#' class='view-replies-link btn btn-sm' data-comment-id='$commentID'>View Replies (" . count($reply_count) . ")</a>";
                }
                echo "</span>";

                echo "      </h5>";
                echo "      <time datetime='{$comment->created_at}'>$created_date</time>";
                echo "      <p>$commentText</p>";
                echo "    </div>";
                echo "  </div>";
                echo "</div>";

                // Reply form
                echo "
                <form class='reply-form mt-3' id='reply-form-$commentID' style='display:none;'>
                  <div class='text-center mt-1 message-box'></div>
                  <input type='hidden' name='user_id' value='$buyerId'>
                  <input type='hidden' name='blog_id' value='$blog_id'>
                  <input type='hidden' name='action' value='post_comment'>
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
                renderReplies($commentID, $siteurl, $imagePath, $buyerId, $activeLog, $blog_id, $sitelink);
                echo "</div>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to load comments at the moment.</div>";
    }
    ?>
  </div>
</section>

              
            <?php
            if ($activeLog == 1){

                ?>
           <!-- Comment Form Section -->
          <section id="comment-form" class="comment-form section">
            <div class="container">

              <form id="postcomment" method="POST">
                 <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                <h4>Post Comment</h4>
                 <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                 <input name="blog_id" type="hidden" class="form-control" value="<?php echo $blog_id; ?>">
                <div class="row">
                    <input type="hidden" value="post_comment" name="action">
                  <div class="col form-group">
                    <textarea name="comment" class="editor" placeholder="Your Comment*"></textarea>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary" id="submit-btn">Post Comment</button>
                </div>

              </form>

            </div>
          </section><!-- /Comment Form Section -->
                    <?php } ?>

             
                    <?php
                 if ($activeLog !== 1){
                      ?>
           <section id="comment-form" class="comment-form section">
            <div class="container">
            <h6><a href="<?php echo $siteurl; ?>login.php">Sign in</a> to comment</h6>
            </div>
            </section>
                      <?php
                    }

                    ?>

</div>


        <div class="col-lg-4 sidebar">

          <div class="widgets-container">

            <!-- Blog Author Widget -->
           <div class="blog-author-widget widget-item">
  <div class="">
    <div class="d-flex align-items-center w-100">
      <!-- Author Image -->
      <img 
        src="<?php echo $siteurl . $imagePath . ($blogdetail->photo ?? 'default.png'); ?>" 
        class="rounded-circle flex-shrink-0 me-3" 
        alt="<?php echo htmlspecialchars($author); ?>" 
        width="80" height="80">
		
		  <h4><?php echo htmlspecialchars($author); ?></h4>
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
        data-author-id="<?php echo $AuthorId; ?>" 
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
    <p class="mt-3 text-center bio-text">
      <span class="bio-short"><?php echo $shortBio; ?></span>
      <?php if ($isTruncated): ?>
        <span class="bio-full d-none"><?php echo $bio; ?></span>
        <a href="#" class="read-toggle text-primary ms-1" style="font-size: 0.9em;">Read More</a>
      <?php endif; ?>
    </p>
  </div>
</div><!--/Blog Author Widget -->

            <!-- Search Widget -->
            <div class="search-widget widget-item">

              <h3 class="widget-title">Categories</h3>
             <?php
        $url = $siteurl . "script/user.php?action=categorieslist";
        $data = curl_get_contents($url);

        if ($data !== false) {
            $categories = json_decode($data);

            if (!empty($categories)) {
                    foreach ($categories as $cat) {
                    // prefer slug for friendly URLs, fallback to numeric id
                    $catValue = !empty($cat->slug) ? $cat->slug : $cat->id;
                    $catValueEscaped = rawurlencode((string)$catValue);
                    $catName = htmlspecialchars($cat->category_name);
                    $count = htmlspecialchars($cat->post_count);

                    echo '<li><a href="' . $siteurl . 'blog.php?category[]=' . $catValueEscaped . '">' . $catName . ' <span>(' . $count . ')</span></a></li>';
                }
            } else {
                echo '<li>No categories found.</li>';
            }
        } else {
            echo '<li>Unable to load categories.</li>';
        }
        ?>
            </div><!--/Search Widget -->

            <!-- Recent Posts Widget -->
            <div class="recent-posts-widget widget-item">

              <h3 class="widget-title">Recent Posts</h3>
			  
			  <?php
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);

    if (!empty($blogs)) {
        $count = 0; // counter
        foreach ($blogs as $blog) {
            // ✅ Only display blogs where status is "active"
            if (isset($blog->status) && strtolower($blog->status) === 'active') {
                $blogId = $blog->id;
                $title = $blog->title;
                $category = $blog->category_names;
                $slug = $blog->slug;
                $subcategory = $blog->subcategory_names;
                $author = $blog->first_name . ' ' . $blog->last_name;
                $content = limitWords($blog->article, 5);
                $date = date('M d, Y', strtotime($blog->created_at));
                $blogimage = $siteurl . $imagePath . $blog->featured_image;

                // Display your blog HTML here
                ?>

                <!-- Your HTML output for the blog goes here -->
				
              <div class="post-item">
                <img src="<?php echo $blogimage; ?>" alt="" class="flex-shrink-0">
                <div>
                  <h4><a href="<?php echo  $siteurl . "blog-details/" . $slug; ?>"><?php echo $title; ?></a></h4>
                  <time datetime="2020-01-01"><?php echo $date; ?> </time>
                </div>
              </div><!-- End recent post item-->

                <?php
                $count++;
                if ($count >= 4) break; // stop after 4 blogs
            }
        }
    }
}
?>


            </div><!--/Recent Posts Widget -->

            <!-- Tags Widget -->
            <div class="tags-widget widget-item">

              <h3 class="widget-title">Tags</h3>
              <ul>
                <li><a href="#"><?php echo $tags; ?></a></li>
              </ul>

            </div><!--/Tags Widget -->

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
            Report Blog : <?php echo $blogtitle; ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="report_message" class="text-center mb-2"></div>

          <input type="hidden" name="action" value="report_item">
          <!-- Reporter (logged in user) -->
          <input type="hidden" name="reporter_id" value="<?php echo $buyerId; ?>">

          <!-- User being reported (author) -->
          <input type="hidden" name="reported_item_id" value="<?php echo $blog_id; ?>">

           <input type="hidden" name="reported_item_type" value="blog">
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


<!-- Questions Slider Section -->
<section id="questions-slider" class="section">
   <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Question and Answer</h2>
      <p><a href="<?php echo $siteurl; ?>questions-answers.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <?php
    // Fetch all questions and compute related ones for this blog
    // Priority: matching subcategory -> matching category -> fallback recent
    $url = $siteurl . "script/admin.php?action=questionlists";
    $data = curl_get_contents($url);
    $questionsList = [];

    // Build arrays of category & subcategory names for the current blog
    $blogCats = array_filter(array_map('trim', explode(',', strtolower($category ?? ''))));
    $blogSubs = array_filter(array_map('trim', explode(',', strtolower($subcategory ?? ''))));

    $bySub = [];
    $byCat = [];
    $fallback = [];

    if ($data !== false) {
      $questions = json_decode($data);
      if (!empty($questions) && is_array($questions)) {
        foreach ($questions as $question) {
          $qid = $question->id ?? null;
          if (!$qid) continue;

          // Prepare question's categories/subcategories for matching
          $qCats = array_filter(array_map('trim', explode(',', strtolower($question->category_names ?? ''))));
          $qSubs = array_filter(array_map('trim', explode(',', strtolower($question->subcategory_names ?? ''))));

          $matchedSub = false;
          $matchedCat = false;

          if (!empty($blogSubs) && array_intersect($blogSubs, $qSubs)) $matchedSub = true;
          if (!$matchedSub && !empty($blogCats) && array_intersect($blogCats, $qCats)) $matchedCat = true;

          // Prepare result entry
          $article = $question->article ?? '';
          $words = explode(' ', strip_tags($article));
          $shortText = implode(' ', array_slice($words, 0, 6));
          $hasMore = count($words) > 6;
          $entry = [
            'id' => $qid,
            'title' => $question->title ?? '',
            'slug' => $question->slug ?? '',
            'author' => (intval($question->anonymous ?? 0) === 1) ? 'Anonymous' : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: 'Unknown User'),
            'category' => !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : '',
            'date' => date('M d, Y', strtotime($question->created_at ?? '')),
            'shortText' => $shortText,
            'hasMore' => $hasMore,
            'answers' => intval($question->total_answers ?? 0)
          ];

          if ($matchedSub) $bySub[$qid] = $entry;
          elseif ($matchedCat) $byCat[$qid] = $entry;
          else $fallback[$qid] = $entry;
        }

        // merge with priority and limit to 6 unique items
        $merged = [];
        foreach ([$bySub, $byCat, $fallback] as $pool) {
          foreach ($pool as $id => $item) {
            if (count($merged) >= 6) break 2;
            if (!isset($merged[$id])) $merged[$id] = $item;
          }
        }

        $questionsList = array_values($merged);
      }
    }
    ?>

    <?php if (!empty($questionsList)): ?>
      <!-- Swiper Container -->
      <div class="swiper init-swiper">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "autoplay": {
              "delay": 4000,
              "disableOnInteraction": false
            },
            "grabCursor": true,
            "speed": 600,
            "slidesPerView": "auto",
            "spaceBetween": 20,
            "navigation": {
              "nextEl": ".questions-swiper-button-next",
              "prevEl": ".questions-swiper-button-prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 1,
                "spaceBetween": 10
              },
              "576": {
                "slidesPerView": 2,
                "spaceBetween": 15
              },
              "768": {
                "slidesPerView": 3,
                "spaceBetween": 20
              },
              "992": {
                "slidesPerView": 3,
                "spaceBetween": 20
              }
            }
          }
        </script>

   <div class="swiper-wrapper">
  <?php foreach ($questionsList as $q): ?>
    <div class="swiper-slide">
      <div class="post-list border-bottom p-3 bg-white rounded shadow-sm">
        <div class="post-meta mb-2">
          <span class="date fw-bold text-primary"><?php echo $q['category']; ?></span>
          <span class="mx-1">•</span>
          <span class="text-muted small"><?php echo $q['date']; ?></span>
        </div>

        <h2 class="mb-2 fs-5 d-flex align-items-center justify-content-between">
          <a href="<?php echo $siteurl; ?>single-questions/<?php echo $q['slug']; ?>" 
             class="text-dark text-decoration-none hover:text-primary flex-grow-1">
            <?php echo $q['title']; ?>
          </a>
          <a href="<?php echo $siteurl; ?>single-questions/<?php echo $q['slug']; ?>" 
             class="text-primary ms-2">
            <i class="bi bi-arrow-right fs-5"></i>
          </a>
        </h2>

        <p class="mb-1 text-muted">
          <?php echo $q['shortText']; ?><?php echo $q['hasMore'] ? '...' : ''; ?>
        </p>

        <div class="d-flex justify-content-between align-items-center">
          <span class="small text-muted">Answers: <strong><?php echo $q['answers']; ?></strong></span>
          <span class="author d-block text-secondary small ms-2"><?php echo $q['author']; ?></span>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>


        <!-- Swiper Navigation Buttons -->
        <div class="questions-swiper-button-prev swiper-button-prev"></div>
        <div class="questions-swiper-button-next swiper-button-next"></div>
      </div>
    <?php else: ?>
      <p>No questions available.</p>
    <?php endif; ?>

  </div>
</section>


 <!-- Community Group -->
  <section id="featured-courses" class="featured-courses section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Tribes & Groups</h2>
      <p><a href="<?php echo $siteurl; ?>all-groups.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <?php
        // Fetch all groups from API and compute related groups
        $url = $siteurl . "script/admin.php?action=grouplists";
        $data = curl_get_contents($url);

        $groupsToShow = [];

        // Build blog category & subcategory arrays for matching
        $blogCats = array_filter(array_map('trim', explode(',', strtolower($category ?? ''))));
        $blogSubs = array_filter(array_map('trim', explode(',', strtolower($subcategory ?? ''))));

        $bySub = [];
        $byCat = [];
        $fallback = [];

        if ($data !== false) {
          $groups = json_decode($data);
          if (!empty($groups) && is_array($groups)) {
            foreach ($groups as $group) {
              // only active groups
              if (!isset($group->status) || strtolower($group->status) !== 'active') continue;

              $gid = $group->id ?? null;
              if (!$gid) continue;

              $gCats = array_filter(array_map('trim', explode(',', strtolower($group->category_names ?? ''))));
              $gSubs = array_filter(array_map('trim', explode(',', strtolower($group->subcategory_names ?? ''))));

              $matchedSub = (!empty($blogSubs) && array_intersect($blogSubs, $gSubs));
              $matchedCat = (!$matchedSub && !empty($blogCats) && array_intersect($blogCats, $gCats));

              $entry = $group; // keep full object for rendering

              if ($matchedSub) $bySub[$gid] = $entry;
              elseif ($matchedCat) $byCat[$gid] = $entry;
              else $fallback[$gid] = $entry;
            }

            // Merge with priority and limit to 6 unique groups
            $merged = [];
            foreach ([$bySub, $byCat, $fallback] as $pool) {
              foreach ($pool as $id => $item) {
                if (count($merged) >= 3) break 2;
                if (!isset($merged[$id])) $merged[$id] = $item;
              }
            }

            $groupsToShow = array_values($merged);
          }
        }

        if (!empty($groupsToShow)):
          foreach ($groupsToShow as $group):
              $groupId = $group->id;
              $title = $group->group_name;
              $author = $group->first_name . ' ' . $group->last_name;
              $group_access = $group->group_access;
              $group_type = $group->group_type;
              $date = date('M d, Y', strtotime($group->created_at));
              $banner = $group->banner ?? '';
              $slug = $group->slug ?? '';
              $content = limitWords(strip_tags($group->group_description), 10);
              $photo = !empty($group->photo)
                          ? $siteurl . $imagePath . $group->photo
                          : $siteurl . "assets/img/user.jpg";
              $bannerimage = $siteurl . $imagePath . $banner;

              // Category & Subcategory — only first item
              $category = !empty($group->category_names) ? trim(explode(',', $group->category_names)[0]) : 'Uncategorized';
              $subcategory = !empty($group->subcategory_names) ? trim(explode(',', $group->subcategory_names)[0]) : 'General';

              // Price logic
        if (strtolower($group_access) === 'free') {
          $price = 'Free';
      } else {
          $fees = [
              floatval($group->fee_1m ?? 0),
              floatval($group->fee_3m ?? 0),
              floatval($group->fee_6m ?? 0),
              floatval($group->fee_12m ?? 0)
          ];

          // ✅ Use regular anonymous function for broader compatibility
          $fees = array_filter($fees, function ($f) {
              return $f > 0;
          });

          if (!empty($fees)) {
              $minFee = min($fees);
              $maxFee = max($fees);
              $price = ($minFee === $maxFee)
                  ? '₦' . number_format($minFee)
                  : '₦' . number_format($minFee) . ' - ₦' . number_format($maxFee);
          } else {
              $price = 'Paid';
          }
      }
      ?>
        <div class="col-lg-4 col-md-6 col-12 mb-4">
          <div class="course-card">
            <div class="course-image">
              <img src="<?php echo $bannerimage; ?>" alt="Course" class="img-fluid">
              <div class="badge featured"><?php echo $group_type; ?></div>
              <div class="price-badge"><?php echo $price; ?></div>
            </div>
            <div class="course-content">
              <div class="course-meta">
                <span class="level"><?php echo $category; ?></span>
                <span class="duration"><?php echo $subcategory; ?></span>
              </div>
              <h3><a href="group/<?php echo $slug; ?>"><?php echo $title; ?></a></h3>
              <p><?php echo $content; ?>...</p>
              <div class="instructor">
                <img src="<?php echo $photo; ?>" alt="Instructor" class="instructor-img">
                <div class="instructor-info">
                  <h6><?php echo $author; ?></h6>
                  <span>Admin</span>
                </div>
              </div>
              <a href="<?php echo $siteurl; ?>group/<?php echo $slug; ?>" class="btn-course">Join Group</a>
            </div>
          </div>
        </div>
      <?php
          endforeach;
      else:
          echo '<p>No active groups found.</p>';
      endif;
      ?>
    </div>
  </div>

</section><!-- /Community Group -->

<!-- Related Blogs Section -->
<section id="related-blogs" class="section bg-light">
  <div class="container">
    <h3 class="mb-4">Related Blogs</h3>
    <div class="row g-4">
      <?php
      // Fetch related blogs with same category
      $relatedUrl = $siteurl . "script/admin.php?action=bloglists";
      $relatedData = curl_get_contents($relatedUrl);
      
      if ($relatedData !== false) {
          $relatedBlogs = json_decode($relatedData);
          $relatedCount = 0;
          
          if (!empty($relatedBlogs)) {
              foreach ($relatedBlogs as $relBlog) {
                  // Skip current blog and only show blogs with same category
                  if ($relBlog->id == $blog_id || $relBlog->status != 'active') continue;
                  
                  // Check if categories match
                  $relCategories = $relBlog->category_names ?? '';
                  if (strpos($relCategories, $category) === false && strpos($category, $relCategories) === false) continue;
                  
                  $relatedCount++;
                  if ($relatedCount > 4) break; // Limit to 4 related blogs
                  
                  $relTitle = htmlspecialchars($relBlog->title);
                  $relSlug = htmlspecialchars($relBlog->slug);
                  $relAuthor = htmlspecialchars(trim($relBlog->first_name . ' ' . $relBlog->last_name));
                  $relExcerpt = limitWords(strip_tags($relBlog->article), 15);
                  $relDate = date('M d, Y', strtotime($relBlog->created_at));
                  $relImage = !empty($relBlog->featured_image) 
                      ? $siteurl . $imagePath . $relBlog->featured_image 
                      : $siteurl . "assets/img/default-blog.jpg";
                  $relUrl = $siteurl . "blog-details/" . $relSlug;
                  ?>
                  
                  <div class="col-lg-3 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                      <a href="<?php echo $relUrl; ?>">
                        <img src="<?php echo $relImage; ?>" class="card-img-top" alt="<?php echo $relTitle; ?>" style="height: 200px; object-fit: cover;">
                      </a>
                      <div class="card-body">
                        <small class="text-muted"><?php echo $relDate; ?></small>
                        <h5 class="card-title mt-2">
                          <a href="<?php echo $relUrl; ?>" class="text-dark text-decoration-none"><?php echo $relTitle; ?></a>
                        </h5>
                        <p class="card-text text-muted small"><?php echo $relExcerpt; ?>...</p>
                        <small class="text-secondary">By <?php echo $relAuthor; ?></small>
                      </div>
                    </div>
                  </div>
                  
                  <?php
              }
          }
          
          if ($relatedCount == 0) {
              echo '<p class="text-center text-muted">No related blogs found.</p>';
          }
      }
      ?>
    </div>
  </div>
</section>

</main>

<?php include "footer.php"; ?>