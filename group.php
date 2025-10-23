
<?php include "header.php"; ?>


  <?php

$adminAuth  = $_COOKIE['admin_auth']  ?? '';
$vendorAuth = $_COOKIE['vendor_auth'] ?? '';
$userAuth   = $_COOKIE['user_auth']   ?? '';

$canAccess  = false;
$accessRole = 'join to become a member';
$groupCreatorId = 0;
$groupCreatorType = '';
$avgRating = 0;
$reviewCount = 0;

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchgroupslug&slug=" . $slug;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 
            $group_id  = $groupdetail->id ?? '';
            $group_slug = $groupdetail-> slug;
            $member_count = $groupdetail->member_count ?? 0;
            $avgRating = floatval($groupdetail->avg_rating ?? 0);
            $reviewCount = intval($groupdetail->review_count ?? 0);
            // Extract details
            $group_name = $groupdetail->group_name ?? '';
            $group_description = $groupdetail->group_description ?? '';
            $status = $groupdetail->status ?? '';
            $group_type = $groupdetail->group_type ?? '';
            $group_access = $groupdetail->group_access ?? '';
            $fee_1m = $groupdetail->fee_1m ?? '';
            $fee_3m = $groupdetail->fee_3m ?? '';
            $fee_6m = $groupdetail->fee_6m ?? '';
            $fee_12m = $groupdetail->fee_12m ?? '';
            $group_rules = $groupdetail->group_rules ?? '';
            $groupCreatorId = $groupdetail->user_id ?? '';
            $agree_commission = $groupdetail->agree_commission ?? '';
            $agree_guidelines = $groupdetail->agree_guidelines ?? '';
            $agree_terms = $groupdetail->agree_terms ?? '';
            // Convert the blog’s stored category/subcategory values into arrays
            $category = $groupdetail->category_names ?? '';
            $subcategory = $groupdetail->subcategory_names ?? '';
            $created_at = $groupdetail->created_at ?? '';
            $banner = $groupdetail->banner ?? '';
            $bannerimage = $siteurl . $imagePath . $banner;
        } else {
            echo "<div class='alert alert-warning'>No group found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching group data. Please try again later.</div>";
    }
} else {
    header("Location: $siteurl");
    exit;
}


if ($status !== 'active') {
  header ("Location: $siteurl");
  exit;
}


// ✅ CASE 1: Group creator (any role type)
if (
    ($adminAuth) ||
    ($vendorAuth == $groupCreatorId) ||
    ($userAuth   == $groupCreatorId)
) {
    $canAccess = true;
    $accessRole = 'creator';
}

// ✅ CASE 2: Approved group member
elseif (!empty($userAuth)) {
    $memberUrl = $siteurl . "script/admin.php?action=checkuserMember&group_id={$group_id}&user_id={$userAuth}";
    $memberData = curl_get_contents($memberUrl);

    if ($memberData !== false) {
        $memberResult = json_decode($memberData, true);

        if (!empty($memberResult[0]) && $memberResult[0]['status'] === 'active') {
            $canAccess = true;
            $accessRole = $memberResult[0]['role'];
        }
    }
}

// round to nearest 0.5 for display
$rounded = round($avgRating * 2) / 2;
$fullStars = (int) floor($rounded);
$halfStar = ($rounded - $fullStars) == 0.5 ? 1 : 0;
$emptyStars = 5 - $fullStars - $halfStar;
?>

   <!-- Instructor Profile Section -->
    <section id="instructor-profile" class="instructor-profile section">


      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">

          <div class="col-lg-12">
            <div class="instructor-hero-banner" data-aos="zoom-out" data-aos-delay="200">
              <div class="hero-background">
                <img src="<?php echo $bannerimage; ?>" alt="Background" class="img-fluid">
                <div class="hero-overlay"></div>
              </div>
              <div class="hero-content">
                <div class="instructor-info">
                  <h2><?php echo $group_name; ?></h2>
                  
                  <div class="credentials">
                    <span class="credential"><?php echo $category; ?></span>
                    <span class="credential"><?php echo $subcategory; ?></span>
                    <span class="credential"><?php echo $member_count; ?> members</span>
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

                 <div class="status-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    <span><?php echo $accessRole; ?></span>
                  </div>
<?php
if (!$canAccess) {
    if ($group_access == "free") {
        // 🔓 Free group - check if it's open or closed
        if ($group_type == "open") {
            // ✅ Open group - join instantly
            ?>
            <div class="contact-actions">
                <a href="#" 
                   id="<?php echo $group_id; ?>" 
                   data-user="<?php echo $buyerId; ?>" 
                   class="btn-contact join-group">
                    <i class="bi bi-person-plus"></i> Join Group
                </a>
            </div>
            <?php
        } elseif ($group_type == "closed") {
            // 🔒 Closed group - requires admin approval
            ?>
            <div class="contact-actions">
                <a href="#" 
                   id="<?php echo $group_id; ?>" 
                   data-user="<?php echo $buyerId; ?>" 
                   class="btn-contact request-join-group">
                    <i class="bi bi-lock"></i> Request to Join
                </a>
            </div>
            <?php
        }

    } elseif ($group_access == "paid") {
        // 💰 Paid group - redirect to payment page
        ?>
        <div class="contact-actions">
            <a href="<?php echo $siteurl; ?>premium-group.php?group_id=<?php echo $group_id; ?>" 
               class="btn-contact">
                <i class="bi bi-person-plus"></i> Join Group
            </a>
        </div>
        <?php
    }
}
?>



                  <?php
                  if ($canAccess) {
                    ?>
                  <div class="contact-actions">
                    <a href="#" id="<?php echo $group_id; ?>" data-user="<?php echo $buyerId; ?>" class="btn-contact exit-group">
                        <i class="bi bi-person-dash"></i> Exit Group
                        </a>
                  </div>
                  <?php } ?>
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
                <?php if ($canAccess) { ?>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-profile-experience" type="button" role="tab">
                    <i class="bi bi-newspaper"></i>
                    Blog
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-profile-courses" type="button" role="tab">
                    <i class="bi bi-book"></i>
                    Q & A
                  </button>
                </li>
                <?php } ?>
				
				 <?php if ($accessRole === 'creator' || $accessRole === 'admin') { ?>
				 
				 <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-group" type="button" role="tab">
                    <i class="bi bi-people"></i>
                   Group Members
                  </button>
                </li>
				 
				 <?php } ?>

          <?php if ($accessRole === 'creator' || $accessRole === 'admin' && $group_type === 'closed') { ?>
				 
				 <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-request" type="button" role="tab">
                    <i class="bi bi-people"></i>
                   User Request
                  </button>
                </li>
				 
				 <?php } ?>
				
				
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
                      <h4>About</h4>
                      <p><?php echo $group_description; ?></p>
                     
                    </div>

                    <div class="expertise-grid">
                      <h4>Rules & Guidelines </h4>
                      <p><?php echo $group_rules; ?></p>
                    </div>
                  </div>
                </div>

            <div class="tab-pane fade" id="instructor-profile-experience" role="tabpanel">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Group Blog</h4>

      <div>
        <?php if ($accessRole === 'creator' || $accessRole === 'admin' || $accessRole === 'subadmin') { ?>
          <a href="<?php echo $siteurl; ?>create-group-blog.php?slug=<?php echo $slug; ?>" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-pencil-square"></i> Create Blog
          </a>   <a href="<?php echo $siteurl; ?>all-group-blog.php?slug=<?php echo $slug; ?>" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-pencil-square"></i> All Blog
          </a>
        <?php } ?>
        <a href="<?php echo $siteurl; ?>group-blogs.php?slug=<?php echo $slug; ?>" class="btn btn-outline-primary btn-sm">
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
                      isset($blog->status, $blog->group_id)
                      && strtolower($blog->status) === 'active'
                      && intval($blog->group_id) === intval($group_id)
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

                   
                       $photo = !empty($blog->photo)
                          ? $siteurl . $imagePath . $blog->photo
                          : $siteurl . "assets/img/user.jpg";
                      $blogimage = !empty($blog->featured_image)
                          ? $siteurl . $imagePath . $blog->featured_image
                          : $siteurl . "assets/img/default-blog.jpg";
                      $blogUrl = $siteurl . "single-blog.php?slug=" . $slug . "&group_id=" . $group_id;
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
                  echo "<p class='text-center'>No active blogs found for this group.</p>";
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

                <div class="tab-pane fade" id="instructor-profile-courses" role="tabpanel">
                 <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Question and Answer</h4>

      <div>
        <?php if ($accessRole === 'creator' || $accessRole === 'admin' || $accessRole === 'subadmin') { ?>
          <a href="<?php echo $siteurl; ?>create-group-question.php?slug=<?php echo $group_slug; ?>" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-pencil-square"></i> Create Q & A
          </a>
        <?php } ?>
        <a href="<?php echo $siteurl; ?>group-questions.php?slug=<?php echo $group_slug; ?>" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-grid"></i> View All
        </a>
      </div>
    </div>
    <div id="blog-posts" class="blog-posts section">
    <div class="row gy-4">
      <?php

    // Fetch and Filter Questions
    $url = $siteurl . "script/admin.php?action=questionlists";
    $data = curl_get_contents($url);
    $questionsList = [];

    if ($data !== false) {
        $questions = json_decode($data);
        if (!empty($questions)) {
            $count = 0;
            foreach ($questions as $question) {

                // ✅ Only include questions that belong to this group
                if (intval($question->group_id) == $group_id) continue;

                if ($count >= 6) break; // ✅ Limit to 6 per group

                $questionId = $question->id;
                $title = htmlspecialchars($question->title);
                $article = htmlspecialchars($question->article);
                $slug = $question->slug ?? '';
                $date = date('M d, Y', strtotime($question->created_at));
                $category = !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : 'Uncategorized';
                $subcategory = !empty($question->subcategory_names) ? trim(explode(',', $question->subcategory_names)[0]) : 'General';

                $anonymous = intval($question->anonymous ?? 0);
                $authorDisplay = ($anonymous === 1)
                    ? "Anonymous"
                    : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: "Unknown User");

                // ✅ Limit text preview to 5 words
                $words = explode(' ', strip_tags($article));
                $shortText = implode(' ', array_slice($words, 0, 5));
                $hasMore = count($words) > 5;

                $questionsList[] = [
                    'id' => $questionId,
                    'title' => $title,
                    'slug' => $slug,
                    'author' => $authorDisplay,
                    'category' => $category,
                    'date' => $date,
                    'shortText' => $shortText,
                    'hasMore' => $hasMore
                ];

                $count++;
                      }
                  }
              }

              ?>
              <?php if (!empty($questionsList)): ?>
              <?php foreach ($questionsList as $q): ?>
                         <div class="col-lg-6 col-md-6 col-12">
          <div class="border rounded-4 p-4 bg-white shadow-sm h-100 transition-all hover:shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-primary px-3 py-2 text-uppercase small"><?php echo $q['category']; ?></span>
              <small class="text-muted"><?php echo $q['date']; ?></small>
            </div>

            <h5 class="fw-semibold mb-2">
              <a href="single-questions?slug=<?php echo $q['slug']; ?>" class="text-dark text-decoration-none">
                <?php echo $q['title']; ?>
              </a>
            </h5>

            <p class="text-muted small mb-2">
              <?php echo $q['shortText']; ?><?php echo $q['hasMore'] ? '...' : ''; ?>
            </p>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="small text-secondary">
                <i class="bi bi-person me-1"></i> <?php echo $q['author']; ?>
              </span>
              <a href="group-single-questions.php?slug=<?php echo $q['slug']; ?>&group_id=<?php echo $group_id; ?>" class="text-primary fw-semibold small">
                Read More <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    </div>
    <?php else: ?>
  <p class="text-center text-muted py-5">No questions available.</p>
<?php endif; ?>
  </div>
                </div>
				
				   <div class="tab-pane fade" id="instructor-group" role="tabpanel">
				
				
 			<div class="container">
          <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">All Members</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>      
            <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
         
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=groupmemberlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $groups = json_decode($data);

    if (!empty($groups)) {
        foreach ($groups as $group) {
            if (isset($group->status) && $group->group_id == $group_id) {
                $groupId = $group->group_id;
                $id = $group->id;
                $title = $group->group_name;
                $userId = $group->user_id;
                $role = $group->role;
                 $status = $group->status;
                $author = $group->first_name . ' ' . $group->last_name;
                $date = date('M d, Y', strtotime($group->joined_at));
                if ($status === "notactive") {
                    $statuslog = 'danger';
                }

                 if ($status === "notactive") {
                    $statuslog = 'danger';
                } else if ($status === "pending") {
                    $statuslog = 'warning';
                } else if ($status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>

                    <td><?php echo $author; ?></td>
                    <td><?php echo $role; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
                  

                    <?php
                    echo "
                    <td>
                        <a href='edit-group-members.php?group_id=$groupId&user_id=$userId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='bi bi-pencil'></i> 
                        </a>
                        <a href='#' id='$id' class='btn btn-link btn-danger  deletegroupmembers' data-bs-toggle='tooltip' title='Delete'>
                            <i class='bi bi-trash'></i>
                        </a>
                    </td>";
                    ?>
                        <!-- Action buttons here -->
               
                </tr>

                <?php
            }
        }
    }
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
				
				
				</div>



        <div class="tab-pane fade" id="instructor-request" role="tabpanel">

    
 <div class="container">
          <div class="page-inner">
          
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=groupmemberlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $groups = json_decode($data);

    if (!empty($groups)) {
        foreach ($groups as $group) {
            if (isset($group->status) && $group->group_id == $group_id && $group->status == "pending") {
                $groupId = $group->group_id;
                $id = $group->id;
                $title = $group->group_name;
                $userId = $group->user_id;
                $role = $group->role;
                 $status = $group->status;
                $author = $group->first_name . ' ' . $group->last_name;
                $date = date('M d, Y', strtotime($group->joined_at));
                if ($status === "notactive") {
                    $statuslog = 'danger';
                }

                 if ($status === "notactive") {
                    $statuslog = 'danger';
                } else if ($status === "pending") {
                    $statuslog = 'warning';
                } else if ($status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>

                    <td><?php echo $author; ?></td>
                    <td><?php echo $role; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
                    <td>

                    <?php
                    echo "
                    <td>
                        <a href='edit-group-members.php?group_id=$groupId&user_id=$userId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$id' class='btn btn-link btn-danger  deletegroupmembers' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>
                    </td>";
                    ?>
                        <!-- Action buttons here -->
                    </td>
                </tr>

                <?php
            }
          
        }
    }
    
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





      </div>

                <div class="tab-pane fade" id="instructor-profile-reviews" role="tabpanel">
                  <div class="reviews-container">

                  <div class="comment-form section">
                  <div class="container">
                    <?php if ($canAccess) { ?>
                    <form id="postreview" method="POST">
                      <div class="col-lg-12 text-center mt-1" id="messages"></div> 

                      <h4>Give Review</h4>
                      <input name="user_id" type="hidden" class="form-control" value="<?php echo $buyerId; ?>">
                      <input name="group_id" type="hidden" class="form-control" value="<?php echo $group_id; ?>">
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
                          <input type="hidden" value="post_review" name="action">
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
$comments_url = $sitelink . "user.php?action=commentsdata&group_id=" . intval($group_id);

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
            if (!isset($baseParams['group_id']) && !empty($group_id)) {
                $baseParams['group_id'] = $group_id;
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


        </div>

      </div>

    </section><!-- /Instructor Profile Section -->




<?php include "footer.php"; ?>