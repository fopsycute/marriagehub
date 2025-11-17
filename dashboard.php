
<?php 
$requireLogin = true;

include "header.php"; 

?>

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
                  <img src="<?php echo $siteurl . $imagePath . $logo; ?>" alt="user" class="img-fluid">
                  <div class="status-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>user</span>
                  </div>
                </div>
                <div class="instructor-info">
                  <h2>Welcome back 👋 <span class="text-primary"><?php echo $buyerfirstName; ?></span></h2>
<?php
  $bioShort = limitWords(strip_tags($bio), 10);
  $bioLong = strip_tags($bio);
  $isLong = (str_word_count($bioLong) > 10);
?>

<p class="bioBox mt-3 text-center">
    <span class="bioShort"><?php echo $bioShort; ?></span>

    <?php if ($isLong): ?>
        <span class="bioFull d-none"><?php echo $bioLong; ?></span>
      <a href="#" class="bioToggle text-primary ms-1" style="font-size: 0.9em;" onclick="toggleBio(this)">Read More</a>

    <?php endif; ?>
</p>

                </div>
				
	
              </div>
           

                  </div>
				         </div>
						 
<div class="col-lg-3 col-md-3">
  <div class="user-dashboard">
    
    <a href="<?php echo $siteurl; ?>dashboard.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-person-circle"></i></div>
      <h4>My Profile</h4>
    </a>
      <?php
       $url = $siteurl . "script/admin.php?action=usernotificationlists&user_id=" . $buyerId;
        $data = curl_get_contents($url);

          $notifCount = 0; // default
          $notifications = [];

          if ($data !== false) {
          $notificationsData = json_decode($data);

          if (!empty($notificationsData)) {
            $notifCount = count($notificationsData);          // total unread notifications
            $notifications = array_slice($notificationsData, 0, 5); // latest 5
                    }
                }
                ?>

    <a href="<?php echo $siteurl; ?>notifications.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-bell"></i></div>
      <h4>Notifications</h4><sup><?php echo $notifCount; ?> </sup>
    </a>

    <a href="<?php echo $siteurl; ?>my-blog.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-file-earmark-post"></i></div>
      <h4>My Blogs</h4>
    </a>

    <a href="<?php echo $siteurl; ?>my-bookings.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-calendar"></i></div>
      <h4>My Bookings</h4>
    </a>

    <a href="<?php echo $siteurl; ?>my-group.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-people"></i></div>
      <h4>Tribes / Groups</h4>
    </a>

    <a href="<?php echo $siteurl; ?>my-q-a.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-question"></i></div>
      <h4>My Question & Answer</h4>
    </a>

       <a href="<?php echo $siteurl; ?>create-tickets.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-exclamation-triangle"></i></div>
      <h4>Create Tickets</h4>
    </a>

       <a href="<?php echo $siteurl; ?>all-reviews.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-star-fill"></i></div>
      <h4>My Reviews</h4>
    </a>
    <a href="settings.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-gear"></i></div>
      <h4>Settings</h4>
    </a>

    <a href="wallet.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-credit-card"></i></div>
      <h4>Payment History</h4>
    </a>
    <a href="change-password.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-lock"></i></div>
      <h4>Change Password</h4>
    </a>

    <a href="forgot-password.php" class="dashboard-item">
      <div class="dashboard-icon me-2"><i class="bi bi-lock"></i></div>
      <h4>Reset Password</h4>
    </a>

    <a href="<?php echo $siteurl; ?>logout.php" class="dashboard-item logout-item">
      <div class="dashboard-icon me-2"><i class="bi bi-box-arrow-right"></i></div>
      <h4>Log Out</h4>
    </a>

  </div>
</div>

<div class="col-lg-9 col-md-9">
<div class="table-responsive">
  <table class="table blog-table align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th>Featured Image</th>
        <th>Title</th>
        <th>Author</th>
        <th>Content</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);

      if ($data !== false) {
          $blogs = json_decode($data);

          if (!empty($blogs)) {
              $count = 0; // limit counter

              foreach ($blogs as $blog) {
                  if (isset($blog->status) && $blog->user_id == $buyerId && $blog->group_id == '') {

                      // Stop after 5 records
                      if ($count >= 5) break;

                      $blogId = $blog->id;
                      $title = limitWords($blog->title, 3);
                      $author = htmlspecialchars($blog->first_name . ' ' . $blog->last_name);
                      $status = $blog->status;
                      $content = limitWords($blog->article, 5);
                      $date = date('M d, Y', strtotime($blog->created_at));
                      $blogimage = $siteurl . $imagePath . $blog->featured_image;

                      $statuslog = match ($status) {
                          'notactive' => 'danger',
                          'pending' => 'warning',
                          'active' => 'success',
                          default => 'secondary'
                      };
                      ?>
                      <tr>
                        <td><img src="<?php echo $blogimage; ?>" class="blog-thumb" alt="featured"></td>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $author; ?></td>
                        <td class="text-muted"><?php echo $content; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
                        <td class="d-flex">
                          <a href="<?php echo $siteurl; ?>edit-blog.php?blog_id=<?php echo $blogId; ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <a href="#" id="<?php echo $blogId; ?>" class="btn btn-sm btn-outline-danger deleteblog">
                            <i class="bi bi-trash"></i>
                          </a>
                        </td>
                      </tr>
                      <?php
                      $count++;
                  }
              }

              if ($count === 0) {
                  echo "<tr><td colspan='7' class='text-center text-muted'>No saved blog posts found.</td></tr>";
              }
          } else {
              echo "<tr><td colspan='7' class='text-center text-muted'>No blog data available.</td></tr>";
          }
      } else {
          echo "<tr><td colspan='7' class='text-center text-danger'>Failed to fetch blog data.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>



                  </div>
          </div>
        </div>
</section>


<?php include "footer.php"; ?>

