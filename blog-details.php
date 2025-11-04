
<?php include "header.php";  ?>

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
            $status = $blogdetail->status ?? '';
            $created_at = date('F d, Y \a\t h:i A', strtotime($blogdetail->created_at));
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));

            $blogimage = $siteurl . $imagePath . $featured_image;
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




  <main class="main">



    <div class="container">
      <div class="row">

        <div class="col-lg-8">

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container">

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
          </div>
        
          </div><!-- End meta bottom -->

          </article>


            </div>
          </section><!-- /Blog Details Section -->
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
                $commentText = htmlspecialchars($comment->comment);
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
  <div class="d-flex flex-column align-items-center">
    <div class="d-flex align-items-center w-100">
      <!-- Author Image -->
      <img 
        src="<?php echo $siteurl . $imagePath . ($blogdetail->photo ?? 'default.png'); ?>" 
        class="rounded-circle flex-shrink-0 me-3" 
        alt="<?php echo htmlspecialchars($author); ?>" 
        width="80" height="80">

      <div>
        <h4><?php echo htmlspecialchars($author); ?></h4>

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
      </div>
    </div>

    <?php 
      $shortBio = limitWords(strip_tags($bio), 10);
      $isTruncated = (str_word_count(strip_tags($bio)) > 10);
    ?>

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
                    $catId = htmlspecialchars($cat->id);
                    $catName = htmlspecialchars($cat->category_name);
                    $count = htmlspecialchars($cat->post_count);

                    echo '<li><a href="'.$siteurl.'blog.php?category='.$catId.'">'.$catName.' <span>(' . $count . ')</span></a></li>';
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
                if ($count >= 3) break; // stop after 3 blogs
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
</main>









<?php include "footer.php"; ?>