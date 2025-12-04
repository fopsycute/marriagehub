<?php include "header.php"; ?>


  <main class="main">

    <!-- Slider Section -->
    <section id="slider" class="slider section dark-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">

          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "centeredSlides": true,
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "navigation": {
                "nextEl": ".swiper-button-next",
                "prevEl": ".swiper-button-prev"
              }
            }
          </script>

          <div class="swiper-wrapper">

            <div class="swiper-slide" style="background-image: url('<?php echo $siteurl; ?>assets/img/black-family.jpg');">
              <div class="content">
                <h2><a>Building Strong Families, One Home at a Time</a></h2>
                <p>From parenting to partnership, discover proven ways to nurture harmony and joy in your family life.</p>
                <a class="btn btn-primary" href="<?php echo $siteurl; ?>all-groups.php">Join Our Community</a>
            </div>
            </div>

            <div class="swiper-slide" style="background-image: url('<?php echo $siteurl; ?>assets/img/black-family-enjoying.jpg');">
              <div class="content">
                <h2><a>Your Journey to a Stronger Relationship Starts Here</a></h2>
                <p>Get expert insights, real stories, and counseling support — and join our Q&A Blog to ask, share, and grow together.</p>
                <a class="btn btn-primary" href="<?php echo $siteurl; ?>find-therapist.php">Book a Counseling Session</a>
            </div>
            </div>

            <div class="swiper-slide" style="background-image: url('<?php echo $siteurl; ?>assets/img/couple-walking-through-vineyard.jpg');">
              <div class="content">
                <h2><a>Love That Feels Like Home</a></h2>
                <p>Rediscover peace, laughter, and connection — Marriagehub.ng is your trusted partner for lasting love.</p>    
                <a class="btn btn-primary" href="<?php echo $siteurl; ?>registration.php">Start Your Journey</a>
            </div>
            </div>
            <div class="swiper-slide" style="background-image: url('<?php echo $siteurl; ?>assets/img/people-sharing-feelings-emotions.jpg');">
              <div class="content">
                <h2><a>Because Every Relationship Deserves Support</a></h2>
                <p>Get professional, confidential counseling from certified marriage and family therapists who truly understand.</p>
                <a class="btn btn-primary" href="<?php echo $siteurl; ?>find-therapist.php">Find the Right Expert</a>
            </div>
            </div>
          </div>

          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>

          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Slider Section -->
<div class="container">
  <div class="row">
    <div class="col-lg-8 col-md-8 col-12">
   <!-- Trending Category Section -->
<section id="trending-category" class="trending-category section">
  <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Latest Blogs</h2>
          <p><a href="<?php echo $siteurl; ?>blog.php">View All</a></p>
        </div>
      </div><!-- End Section Title -->

  <!-- Blog Grid Container -->
  <div class="container my-5">
    <div class="row g-4">
      <?php
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);
      $limit = 4; // Number of blogs to show
      $count = 0;

      if ($data !== false) {
          $blogs = json_decode($data);

          if (!empty($blogs)) {
              foreach ($blogs as $blog) {
                  if (isset($blog->status) && strtolower($blog->status) === 'active' && $blog->group_id == '') {
                      $count++;
                      if ($count > $limit) break;

                      $blogId = $blog->id;
                      $title = htmlspecialchars($blog->title);
                      $slug = htmlspecialchars($blog->slug);
                      $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
                      $content = limitWords(strip_tags($blog->article), 10);
                      $date = date('F d, Y', strtotime($blog->created_at));
                      $views = htmlspecialchars($blog->views ?? 0);
                      $photo = !empty($blog->photo)
                          ? $siteurl . $imagePath . $blog->photo
                          : $siteurl . "assets/img/user.jpg";
                      $blogimage = !empty($blog->featured_image)
                          ? $siteurl . $imagePath . $blog->featured_image
                          : $siteurl . "assets/img/default-blog.jpg";
                      $blogUrl = $siteurl . "blog-details/" . $slug;
                     $categoryNames = !empty($blog->category_names) ? explode(',', $blog->category_names) : ['General'];
                    $category = htmlspecialchars(trim($categoryNames[0]));

                      ?>
                      
                      <div class="col-lg-6 col-md-6 col-12">
                 <div class="card blog-card h-100 shadow-sm border-0">
          <a href="<?php echo $blogUrl; ?>" class="blog-img-wrapper position-relative">
            <img src="<?php echo $blogimage; ?>" class="card-img-top" alt="<?php echo $title; ?>">
            <span class="badge category-badge-on-image"><?php echo $category; ?></span>
          </a>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between mb-2 align-items-center">
              <small class="text-muted"><?php echo $date; ?> • <?php echo $views; ?> views</small>
            </div>
            <h5 class="card-title">
              <a href="<?php echo $blogUrl; ?>" class="text-dark text-decoration-none"><?php echo $title; ?></a>
            </h5>
            <p class="card-text mb-4"><?php echo $content; ?>...</p>
            <div class="mt-auto d-flex align-items-center">
              <img src="<?php echo $photo; ?>" alt="<?php echo $author; ?>" class="rounded-circle me-2" style="width:40px;height:40px;">
              <span><?php echo $author; ?></span>
            </div>
          </div>
        </div>
              </div>
                  <?php
                  }
              }
          }
      }
      ?>
    </div>
  </div>

  <!-- Custom CSS -->


</section><!-- /Trending Category Section -->

</div>
    <div class="col-lg-4 col-md-4 col-12">
      <?php include "home-sidebar-banner.php"; ?>
    </div>
  </div>
</div>
</div>
</div>



  <!-- Trending Category Section -->
<section id="trending-category" class="trending-category section">
  <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>More Articles</h2>
          <p><a href="<?php echo $siteurl; ?>blog.php">View All</a></p>
        </div>
      </div><!-- End Section Title -->

  <!-- Blog Grid Container -->
  <div class="container my-5">
    <div class="row g-4">
      <?php
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);
      $limit = 6; // Number of blogs to show
      $count = 0;

      if ($data !== false) {
          $blogs = json_decode($data);
          
       // RANDOMIZE BLOG DISPLAY
         shuffle($blogs);

          if (!empty($blogs)) {
              foreach ($blogs as $blog) {
                  if (isset($blog->status) && strtolower($blog->status) === 'active' && $blog->group_id == '') {
                      $count++;
                      if ($count > $limit) break;

                      $blogId = $blog->id;
                      $title = htmlspecialchars($blog->title);
                      $slug = htmlspecialchars($blog->slug);
                      $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
                      $content = limitWords(strip_tags($blog->article), 10);
                      $date = date('F d, Y', strtotime($blog->created_at));
                      $views = htmlspecialchars($blog->views ?? 0);
                      $photo = !empty($blog->photo)
                          ? $siteurl . $imagePath . $blog->photo
                          : $siteurl . "assets/img/user.jpg";
                      $blogimage = !empty($blog->featured_image)
                          ? $siteurl . $imagePath . $blog->featured_image
                          : $siteurl . "assets/img/default-blog.jpg";
                      $blogUrl = $siteurl . "blog-details/" . $slug;
                     $categoryNames = !empty($blog->category_names) ? explode(',', $blog->category_names) : ['General'];
                    $category = htmlspecialchars(trim($categoryNames[0]));

                      ?>
                      
   <div class="col-lg-4 col-md-6 col-12">
  <div class="contentBox p-3 h-100">
    
    <!-- Category Badge -->
    <span class="category-outline-badge mb-2 d-inline-block">
      <?php echo $category; ?>
    </span>

    <!-- Date + Views -->
    <small class="text-muted d-block mb-2"><?php echo $date; ?> • <?php echo $views; ?> views</small>

    <!-- Blog Title -->
    <h5 class="card-title mb-2">
      <a href="<?php echo $blogUrl; ?>" class="text-dark text-decoration-none">
        <?php echo $title; ?>
      </a>
    </h5>

    <!-- Short Excerpt -->
    <p class="mb-3"><?php echo $content; ?>...</p>

    <!-- Author -->
    <div class="d-flex align-items-center mt-auto">
      <img src="<?php echo $photo; ?>" 
           alt="<?php echo $author; ?>" 
           class="rounded-circle me-2" 
           style="width:40px;height:40px;">
      <span><?php echo $author; ?></span>
    </div>

  </div>
</div>

                  <?php
                  }
              }
          }
      }
      ?>
    </div>
  </div>

</section><!-- /Trending Category Section -->


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
      // Fetch all groups from API
      $url = $siteurl . "script/admin.php?action=grouplists";
      $data = curl_get_contents($url);

      $allGroups = [];
      if ($data !== false) {
          $groups = json_decode($data);
          if (!empty($groups)) {
              // Only include active groups (limit 6)
              foreach ($groups as $group) {
                  if (isset($group->status) && strtolower($group->status) === 'active') {
                      $allGroups[] = $group;
                  }
                  if (count($allGroups) >= 6) break; // stop after 6
              }
          }
      }

      if (!empty($allGroups)):
          foreach ($allGroups as $group):
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
    // Fetch and Filter Questions
    $url = $siteurl . "script/admin.php?action=questionlists";
    $data = curl_get_contents($url);
    $questionsList = [];

    if ($data !== false) {
        $questions = json_decode($data);
        if (!empty($questions)) {
            $count = 0;
            foreach ($questions as $question) {
                if ($count >= 6) break; // ✅ limit 6 questions

                $questionId = $question->id;
                $title = $question->title;
                $article = $question->article;
                  $slug = $question->slug ?? '';
                $date = date('M d, Y', strtotime($question->created_at));
                $category = !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : 'Uncategorized';
                $subcategory = !empty($question->subcategory_names) ? trim(explode(',', $question->subcategory_names)[0]) : 'General';

                $anonymous = intval($question->anonymous ?? 0);
                $authorDisplay = ($anonymous === 1)
                    ? "Anonymous"
                    : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: "Unknown User");

                // Limit text preview to 5 words
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

        <span class="author d-block text-secondary small">
          <?php echo $q['author']; ?>
        </span>
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

<!-- Therapists Section -->
<section id="featured-instructors" class="featured-instructors section">

      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Therapists</h2>
        <p>Find trusted, licensed therapists for marriage, relationship, and emotional wellness — confidential, convenient, and personalized to your needs.</p>
      </div><!-- End Section Title -->

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
<div class="row gy-4">
<?php
// Fetch all users
$url = $siteurl . "script/admin.php?action=userlists";
$data = curl_get_contents($url);

$allUsers = [];
if ($data !== false) {
    $users = json_decode($data);
    if (!empty($users)) {
        foreach ($users as $user) {
            if (
                isset($user->status) &&
                strtolower($user->status) == 'active' &&
                $user->user_type == 'therapist'
            ) {
                $allUsers[] = $user;
            }
            if (count($allUsers) >= 6) break; // Limit to 6 users
        }
    }
}

if (!empty($allUsers)):
    foreach ($allUsers as $user):
        $userId = $user->id;
        $slug = $user->slug ?? '';
        $fullName = htmlspecialchars(trim($user->first_name . ' ' . $user->last_name));
        $professional_field = !empty($user->professional_field_names) ? trim(explode(',', $user->professional_field_names)[0]) : 'Uncategorized';
        $professional_title = !empty($user->professional_title_names) ? trim(explode(',', $user->professional_title_names)[0]) : 'General';
        $rate = !empty($user->rate) ? $user->rate : 0;
        $link= $siteurl . "therapist/" . $slug;

    $photo = !empty($user->photo)
      ? $siteurl . $imagePath . $user->photo
      : $siteurl . "assets/img/user.jpg";

    // Fetch rating for this therapist via new API endpoint
    $avgRating = 0.0;
    $reviewCount = 0;
    if (!empty($userId)) {
      $ratingApi = $siteurl . "script/admin.php?action=therapist_rating&therapist_id=" . urlencode($userId);
      $ratingRaw = curl_get_contents($ratingApi);
      if ($ratingRaw !== false) {
        $ratingJson = json_decode($ratingRaw, true);
        if (is_array($ratingJson)) {
          $avgRating = isset($ratingJson['avg_rating']) ? floatval($ratingJson['avg_rating']) : 0.0;
          $reviewCount = isset($ratingJson['review_count']) ? intval($ratingJson['review_count']) : 0;
        }
      }
    }

    // star calculation (round to nearest 0.5)
    $rounded = round($avgRating * 2) / 2;
    $fullStars = (int) floor($rounded);
    $halfStar = ($rounded - $fullStars) == 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

        // Social media links
        $socialLinks = [];
        if (!empty($user->facebook)) $socialLinks['facebook'] = $user->facebook;
        if (!empty($user->twitter)) $socialLinks['twitter'] = $user->twitter;
        if (!empty($user->instagram)) $socialLinks['instagram'] = $user->instagram;
        if (!empty($user->linkedin)) $socialLinks['linkedin'] = $user->linkedin;
        ?>

        <div class="col-lg-3 col-6">
          <div class="instructor-card">
            <div class="instructor-image">
              <img src="<?php echo $photo; ?>" class="img-fluid" alt="<?php echo $fullName; ?>">
              <div class="overlay-content">
                <div class="rating-stars">
                  <?php for ($i=0; $i<$fullStars; $i++): ?>
                    <i class="bi bi-star-fill"></i>
                  <?php endfor; ?>
                  <?php if ($halfStar): ?>
                    <i class="bi bi-star-half"></i>
                  <?php endif; ?>
                  <?php for ($i=0; $i<$emptyStars; $i++): ?>
                    <i class="bi bi-star"></i>
                  <?php endfor; ?>
                  <span><?php echo number_format($avgRating, (floor($avgRating) == $avgRating) ? 0 : 1); ?></span>
                </div>
                <div class="course-count">
                  <span><?php echo $sitecurrency . number_format($rate, 2); ?></span>
                </div>
              </div>
            </div>

            <div class="instructor-info">
              <h5><?php echo $fullName; ?></h5>
              <p class="specialty"><?php echo $professional_field; ?></p>
              
              <div class="stats-grid">
                <div class="stat">
                  <span class="number"><?php echo $sitecurrency . number_format($rate, 2); ?></span>
                  <span class="label">Per Session</span>
                </div>
                <div class="stat">
                  <span class="number"><?php echo number_format($avgRating, (floor($avgRating) == $avgRating) ? 0 : 1); ?></span>
                  <span class="label">Rating</span>
                </div>
              </div>

              <div class="action-buttons">
                <a href="<?php echo $link; ?>" class="btn-view">View Profile</a>
                <?php if (!empty($socialLinks)): ?>
                <div class="social-links">
                  <?php if (isset($socialLinks['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                  <?php endif; ?>
                  <?php if (isset($socialLinks['twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($socialLinks['twitter']); ?>" target="_blank"><i class="bi bi-twitter"></i></a>
                  <?php endif; ?>
                  <?php if (isset($socialLinks['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($socialLinks['instagram']); ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                  <?php endif; ?>
                  <?php if (isset($socialLinks['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($socialLinks['linkedin']); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
<?php
    endforeach;
else:
    echo "<p>No active therapists found.</p>";
endif;
?>


        </div>

      </div>

    </section><!--- therapists Section -->

    <section id="best-sellers" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Explore Top-Quality Products</h2>
        <p>Explore quality products sourced directly from our trusted vendors.</p>
      </div><!-- End Section Title -->

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
         
          <!-- Product 4 -->
  <?php
$url = $siteurl . "script/admin.php?action=listinglists";
$data = curl_get_contents($url);
$limit = 4; // Number of listings to show
$count = 0;

if ($data !== false) {
    $listings = json_decode($data);

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active' && $listing->type =='Product') {
                $count++;
                if ($count > $limit) break;

                // 🧩 Extract data
                $listingId   = $listing->id;
                $listing_id = $listing->listing_id;
                $title       = htmlspecialchars($listing->title);
                $slug        = htmlspecialchars($listing->slug ?? '');
                $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                $price       = htmlspecialchars($listing->price ?? '');
                $priceMin    = htmlspecialchars($listing->price_min ?? '');
                $priceMax    = htmlspecialchars($listing->price_max ?? '');
                $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                $category    = htmlspecialchars(trim($categoryNames[0]));
                $featuredImg = !empty($listing->featured_image)
                    ? $siteurl . $imagePath . $listing->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                $listingUrl  = $siteurl . "products/" . $slug;

                // 🧩 Seller Info
                $sellerName = htmlspecialchars(trim(($listing->first_name ?? '') . ' ' . ($listing->last_name ?? '')));
                $sellerPhoto = !empty($listing->photo)
                    ? $siteurl . $imagePath . $listing->photo
                    : $siteurl . "assets/img/user.jpg";

                // 🧩 Compute Display Price
                $displayPrice = 'Contact for price';
                if ($pricingType === 'Starting Price' && !empty($price)) {
                    $displayPrice = $sitecurrency  . number_format($price, 2);
                } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
                    $displayPrice = $sitecurrency . number_format($priceMin, 2) . $sitecurrency .'-'. number_format($priceMax, 2);
                }


                    // ✅ Check wishlist status
              $isWishlisted = false; // Always define first

        if (!empty($buyerId)) {
            // ✅ Use $siteurl instead of undefined $sitelink
            $apiCheckUrl = $siteurl . "script/user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
            $wishlistData = curl_get_contents($apiCheckUrl);

            if ($wishlistData !== false) {
                $wishlistResult = json_decode($wishlistData, true);

                // ✅ Make it flexible to match possible response structures
                if (is_array($wishlistResult)) {
                    if (isset($wishlistResult['isWishlisted'])) {
                        $isWishlisted = (bool)$wishlistResult['isWishlisted'];
                    } elseif (isset($wishlistResult['data']['isWishlisted'])) {
                        $isWishlisted = (bool)$wishlistResult['data']['isWishlisted'];
                    }
                }
            }
        }
                ?>

                <!-- 🛍️ Product Card -->
                <div class="col-lg-3 col-md-6 col-6">
                  
                    <div class="product-item">
                        <div class="product-image">
                            <div class="product-badge trending-badge"><?php echo $category; ?></div>
                            <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                            <div class="product-actions">
                                      <button 
                                  class="action-btn wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>" 
                                  title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>" 
                                  data-product-id="<?php echo $listing_id; ?>"
                              >
                                  <?php if ($isWishlisted): ?>
                                      <i class="bi bi-heart-fill text-red-500"></i>
                                  <?php else: ?>
                                      <i class="bi bi-heart"></i>
                                  <?php endif; ?>
                              </button>

                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo $category; ?></div>
                            <h4 class="product-name">
                                <a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a>
                            </h4>
                            <div class="product-price"><?php echo $displayPrice; ?></div>

                            <!--Seller Info -->
                            <div class="mt-3 d-flex align-items-center">
                                <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                                <span class="small text-muted"><?php echo $sellerName; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            }
        }
    }
}
?>

          <!-- End Product 4 -->

        </div>
<?php if ($count >= $limit): ?>
    <div class="text-center mt-4">
        <a href="<?php echo $siteurl; ?>marketplace" class="btn btn-primary px-4 py-2">
            View All Products
        </a>
    </div>
<?php endif; ?>

      </div>

</section>



<section id="best-sellers" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Quality Services You Can Trust</h2>
        <p>Explore quality services sourced directly from our trusted vendors.</p>
      </div><!-- End Section Title -->

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
         
          <!-- Product 4 -->
  <?php
$url = $siteurl . "script/admin.php?action=listinglists";
$data = curl_get_contents($url);
$limit = 4; // Number of listings to show
$count = 0;

if ($data !== false) {
    $listings = json_decode($data);

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active' && $listing->type =='Service') {
                $count++;
                if ($count > $limit) break;

                // 🧩 Extract data
                $listingId   = $listing->id;
                $title       = htmlspecialchars($listing->title);
                $slug        = htmlspecialchars($listing->slug ?? '');
                $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                $price       = htmlspecialchars($listing->price ?? '');
                $priceMin    = htmlspecialchars($listing->price_min ?? '');
                $priceMax    = htmlspecialchars($listing->price_max ?? '');
                $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                $category    = htmlspecialchars(trim($categoryNames[0]));
                $featuredImg = !empty($listing->featured_image)
                    ? $siteurl . $imagePath . $listing->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                $listingUrl  = $siteurl . "products/" . $slug;

                // 🧩 Seller Info
                $sellerName = htmlspecialchars(trim(($listing->first_name ?? '') . ' ' . ($listing->last_name ?? '')));
                $sellerPhoto = !empty($listing->photo)
                    ? $siteurl . $imagePath . $listing->photo
                    : $siteurl . "assets/img/user.jpg";

                // 🧩 Compute Display Price
                $displayPrice = 'Contact for price';
                if ($pricingType === 'Starting Price' && !empty($price)) {
                    $displayPrice = $sitecurrency  . number_format($price, 2);
                } elseif ($pricingType === 'Price Range' && !empty($priceMin) && !empty($priceMax)) {
                    $displayPrice = $sitecurrency . number_format($priceMin, 2) . $sitecurrency .'-'. number_format($priceMax, 2);
                }


                    // ✅ Check wishlist status
              $isWishlisted = false; // Always define first

        if (!empty($buyerId)) {
            // ✅ Use $siteurl instead of undefined $sitelink
            $apiCheckUrl = $siteurl . "script/user.php?action=checkWishlist&user_id={$buyerId}&listing_id={$listingId}";
            $wishlistData = curl_get_contents($apiCheckUrl);

            if ($wishlistData !== false) {
                $wishlistResult = json_decode($wishlistData, true);

                // ✅ Make it flexible to match possible response structures
                if (is_array($wishlistResult)) {
                    if (isset($wishlistResult['isWishlisted'])) {
                        $isWishlisted = (bool)$wishlistResult['isWishlisted'];
                    } elseif (isset($wishlistResult['data']['isWishlisted'])) {
                        $isWishlisted = (bool)$wishlistResult['data']['isWishlisted'];
                    }
                }
            }
        }
                ?>

                <!-- 🛍️ Product Card -->
                <div class="col-lg-3 col-md-6 col-6">
                  
                    <div class="product-item">
                        <div class="product-image">
                            <div class="product-badge trending-badge"><?php echo $category; ?></div>
                            <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid" loading="lazy">
                            <div class="product-actions">
                                       <button 
                      class="action-btn wishlist-btn <?php echo $isWishlisted ? 'added' : ''; ?>" 
                      title="<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>" 
                      data-product-id="<?php echo $listingId; ?>"
                    >
                      <?php if ($isWishlisted): ?>
                        <i class="bi bi-heart-fill text-danger"></i>
                      <?php else: ?>
                        <i class="bi bi-heart"></i>
                      <?php endif; ?>
                    </button>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo $category; ?></div>
                            <h4 class="product-name">
                                <a href="<?php echo $listingUrl; ?>"><?php echo $title; ?></a>
                            </h4>
                            <div class="product-price"><?php echo $displayPrice; ?></div>

                            <!--Seller Info -->
                            <div class="mt-3 d-flex align-items-center">
                                <img src="<?php echo $sellerPhoto; ?>" alt="<?php echo $sellerName; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                                <span class="small text-muted"><?php echo $sellerName; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            }
        }
    }
}
?>

          <!-- End Product 4 -->

        </div>
<?php if ($count >= $limit): ?>
    <div class="text-center mt-4">
        <a href="<?php echo $siteurl; ?>services.php" class="btn btn-primary px-4 py-2">
            View All Services
        </a>
    </div>
<?php endif; ?>

      </div>

</section>


<section id="best-sellers" class="best-sellers section">

    <div class="container section-title" data-aos="fade-up">
        <h2>Events & Programs</h2>
        <p>Stay connected and inspired through our curated events and programs designed to strengthen relationships, marriages, and families.</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">

<?php
$url = $siteurl . "script/admin.php?action=eventlists";
$data = curl_get_contents($url);

$limit = 8;
$count = 0;

if ($data !== false) {

    $events = json_decode($data);

    if (!empty($events)) {

        foreach ($events as $event) {

            // Extract next event date/time
            $eventDate = null;
            $startTime = null;
            $endTime   = null;

            if (!empty($event->next_event_date_time)) {
                list($eventDate, $startTime, $endTime) = explode("|", $event->next_event_date_time);
            }

            if (!$eventDate) continue;

            // Current date/time
            $today   = date("Y-m-d");
            $nowTime = date("H:i:s");

            // Filter: Active + upcoming/ongoing
            if (
                strtolower($event->status ?? "") === "active" &&
                (
                    $eventDate > $today ||
                    ($eventDate == $today && $endTime >= $nowTime)
                )
            ) {
                $count++;
                if ($count > $limit) break;

                // BASIC FIELDS
                $title = htmlspecialchars($event->title ?? "Untitled");
                $event_type = strtolower($event->event_type_name ?? "");
                $slug = $event->slug ?? "";
                $eventUrl = $siteurl .'event/' $slug;

                // CATEGORY
                $category = "General";
                if (!empty($event->category_names)) {
                    $catArray = explode(",", $event->category_names);
                    $category = htmlspecialchars(trim($catArray[0]));
                }

                // IMAGE
                $featuredImg = !empty($event->featured_image)
                    ? $siteurl . $imagePath . $event->featured_image
                    : $siteurl . "assets/img/default-product.jpg";

                // SELLER
                $sellerName = htmlspecialchars(trim(($event->first_name ?? "") . " " . ($event->last_name ?? "")));
                $sellerPhoto = !empty($event->photo)
                    ? $siteurl . $imagePath . $event->photo
                    : $siteurl . "assets/img/user.jpg";

                // PRICE LOGIC
                $pricingType = strtolower($event->pricing_type ?? "paid");
                $displayPrice = "N/A";

                if ($pricingType === "free") {
                    $displayPrice = "Free";
                } elseif ($pricingType === "donation") {
                    $displayPrice = "Donate";
                } else {
                    if (!empty($event->prices)) {
                        $prices = array_map('floatval', explode(",", $event->prices));

                        if (count($prices) === 1) {
                            $displayPrice = $sitecurrency . number_format($prices[0], 2);
                        } else {
                            $minPrice = min($prices);
                            $maxPrice = max($prices);

                            $displayPrice =
                                $sitecurrency . number_format($minPrice, 2) . " - " .
                                $sitecurrency . number_format($maxPrice, 2);
                        }
                    }
                }

                // Format date
                $formattedDate = date("M d, Y", strtotime($eventDate));
                $formattedStart = date("g:i A", strtotime($startTime));
                $formattedEnd   = date("g:i A", strtotime($endTime));
?>
<!-- Event Card -->
<div class="col-lg-3 col-md-6 col-6">
    <div class="product-item">

        <div class="product-image">
            <div class="product-badge trending-badge"><?php echo $category; ?></div>
            <img src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" class="img-fluid">
        </div>

        <div class="product-info">

            <div class="product-category"><?php echo $event_type; ?></div>
              <span class="product-date text-muted mb-2">
                <?php echo $formattedDate . " • " . $formattedStart . " - " . $formattedEnd; ?>
              </span>

            <h4 class="product-name">
                <a href="<?php echo $eventUrl; ?>"><?php echo $title; ?></a>
            </h4>

            <!-- Event Date Display -->
          

            <!-- Pricing -->
            <div class="product-price"><?php echo $displayPrice; ?></div>

            <!-- Seller -->
            <div class="mt-3 d-flex align-items-center">
                <img src="<?php echo $sellerPhoto; ?>" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                <span class="small text-muted"><?php echo $sellerName; ?></span>
            </div>

        </div>

    </div>
</div>

<?php
            }
        }
    }
}
?>

        </div>

<?php if ($count >= $limit): ?>
    <div class="text-center mt-4">
        <a href="<?php echo $siteurl; ?>events.php" class="btn btn-primary px-4 py-2">View All Events</a>
    </div>
<?php endif; ?>

    </div>

</section>


  </main>
<?php include "footer.php"; ?>