<?php include "header.php";  ?>

<?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $group_id = intval($_GET['group_id'] ?? 0);

    // Build API URL
    $sitelink = $siteurl . "script/";


    $apiUrl = $sitelink . "admin.php?action=addviewsquest&slug=" . $slug;
    $response = curl_get_contents($apiUrl);

    $url = $sitelink . "admin.php?action=fetchquestionslug&slug=" . $slug;

    // Fetch blog details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 

            // Extract details
            $title = $blogdetail->title ?? '';
            $article = $blogdetail->article ?? '';
            $tags = $blogdetail->tags ?? '';
            $question_id = $blogdetail->id ?? '';
            $comment_count = $blogdetail->comment_count ?? '0';
            $category = $blogdetail->category_names ?? '';
            $subcategory = $blogdetail->subcategory_names ?? '';
            $status = $blogdetail->status ?? '';
            $created_at = date('F d, Y \a\t h:i A', strtotime($blogdetail->created_at));
             // Determine Author Display
                $anonymous = intval($blogdetail->anonymous ?? 0);
                if ($anonymous === 1) {
                    $authorDisplay = "Anonymous";
                } else {
                    $firstName = htmlspecialchars($blogdetail->first_name ?? '');
                    $lastName = htmlspecialchars($blogdetail->last_name ?? '');
                    $authorDisplay = trim("$firstName $lastName") ?: "Unknown User";
                }

                          // ✅ Get Group Slug & Creator ID from groups table
            $groupSlug = '';
            $groupCreatorId = 0;

            if (!empty($group_id)) {
                $groupUrl = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $group_id;
                $groupData = curl_get_contents($groupUrl);

                if ($groupData !== false) {
                    $groupInfo = json_decode($groupData);
                    if (!empty($groupInfo[0])) {
                        $groupSlug = $groupInfo[0]->slug ?? '';
                        $groupCreatorId = $groupInfo[0]->user_id ?? 0; // group creator
                    }
                }
            }

            // ✅ Check login cookies
            $adminAuth  = $_COOKIE['admin_auth']  ?? '';
            $therapistAuth = $_COOKIE['therapist_auth'] ?? '';
            $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
            $userAuth   = $_COOKIE['user_auth']   ?? '';

            $canAccess = false;

            // ✅ If current user is group creator → allow access
                if (
            ($adminAuth) || 
            ($vendorAuth == $groupCreatorId) || ($therapistAuth == $groupCreatorId) ||
            ($userAuth == $groupCreatorId)
            ) {
                // ✅ CASE 1: Admin — always has access
                if ($adminAuth) {
                    $buyerId = $adminAuth;
                }
                // ✅ CASE 2: Vendor — if the vendor is the group creator
                elseif ($vendorAuth == $groupCreatorId) {
                    $buyerId = $vendorAuth;
                }
                // ✅ CASE 3: Regular user — if the user is the group creator
                elseif ($userAuth == $groupCreatorId) {
                    $buyerId = $userAuth;
                }

                 elseif ($therapistAuth == $groupCreatorId) {
                    $buyerId = $therapistAuth;
                }

                $activeLog = 1;
                $canAccess = true;
            } else {
                // ✅ Otherwise, check if user is a group member
                 $activeUserId = $adminAuth ?: ($vendorAuth ?: ($userAuth ?: $therapistAuth));
                if (!empty($activeUserId) && !empty($group_id)) {
                    $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$group_id}&user_id={$activeUserId}";
                    $memberData = curl_get_contents($checkMemberUrl);

                    if ($memberData !== false) {
                        $memberResult = json_decode($memberData, true);
                        if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active') {
                            $canAccess = true;
                        }
                    }
                }
            }

            // ✅ Restrict access if not authorized
            if (!$canAccess) {
                echo "
                <script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Join Group to View',
                        html: 'You are not a member of this group. <br><b>Join our group to view this question.</b>',
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




  <main class="main">



    <div class="container">
      <div class="row">

        <div class="col-12  mx-auto">

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container">

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
                </div><!-- End meta bottom -->

              </article>

            </div>
          </section><!-- /Blog Details Section -->
           <!-- Blog Comments Section -->
<section id="blog-comments" class="blog-comments section">
  <div class="container">
    <h4 class="comments-count"> <?php echo $comment_count . ' comment' . ($comment_count != 1 ? 's' : '');  ?></h4>
    <?php
    // MAIN COMMENTS
    $sitelink = $siteurl . "script/";
    $comments_url = $sitelink . "user.php?action=answersdata&question_id=" . $question_id;

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
                $commentID = $comment->id;
                $user_id = $comment->user_id;
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

                // Inline View Replies (if exists)
                $replies_url = $sitelink . "user.php?action=answersreplydata&comment_id=" . $commentID;
                $reply_data = curl_get_contents($replies_url);
                $reply_count = json_decode($reply_data);
                if (!empty($reply_count)) {
                    echo " <a href='#' class='view-answer-link btn btn-sm' data-comment-id='$commentID'>View Replies (" . count($reply_count) . ")</a>";
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
                renderAnswers($commentID, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink);
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
</div>
</div>
</main>

<?php include "footer.php"; ?>