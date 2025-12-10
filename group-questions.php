
<?php include "header.php"; 
if (isset($_GET['slug'])) {
    $groupslug = $_GET['slug'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchgroupslug&slug=" . $groupslug;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 
            $group_id  = $groupdetail->id ?? '';
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

// Your site API URL
 $sitelink = $siteurl . "script/";
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
            $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
            $therapistAuth = $_COOKIE['therapist_auth'] ?? '';
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


?>
<<<<<<< HEAD
<div id ="questionscontainer">
=======

>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
<main class="main">

  <!-- Page Title -->
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Questions</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.html">Home</a></li>
          <li class="current">Questions</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section id="courses-2" class="courses-2 section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
<<<<<<< HEAD
        <div class="col-12">
          <!-- Small/medium toggle for filters -->
          <div class="row d-lg-none mb-2">
            <div class="col-12">
              <button id="toggleQAFiltersBtn" class="btn btn-outline-primary w-100" aria-expanded="false" aria-controls="qaFilterBlock">Show filters</button>
            </div>
          </div>

          <div id="qaFilterBlock">
          <div class="course-filters horizontal-filters mb-4" data-aos="fade-up" data-aos-delay="100">
            <form method="get" id="questionFilterForm" class="row g-2 align-items-end">
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label visually-hidden" for="searchInput">Search</label>
                 <input type="text" id="searchInput" name="search"
         class="form-control"
         placeholder="Search title, author, category, subcategory">
              </div>

              <div class="col-6 col-md-3 col-lg-3">
                <label class="form-label visually-hidden" for="category">Category</label>
                <select name="category[]" id="category" class="form-select select-multiple" multiple>
                  <option value="">-- Select Category --</option>
                  <?php
                  $url = $siteurl . "script/register.php?action=categorieslists";
                  $data = curl_get_contents($url);
                  $selectedCategories = isset($_GET['category']) ? (array)$_GET['category'] : [];

                  if ($data !== false) {
                    $categories = json_decode($data);
                    if (!empty($categories)) {
                      foreach ($categories as $category) {
                        $categoryId = $category->id;
                        $name = htmlspecialchars($category->category_name);
                        $selected = in_array($categoryId, $selectedCategories) ? 'selected' : '';
                        echo "<option value='{$categoryId}' {$selected}>{$name}</option>";
                      }
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="col-6 col-md-3 col-lg-3">
                <label class="form-label visually-hidden" for="subcategory">Sub Category</label>
                <select name="subcategory[]" id="subcategory" class="form-select select-multiple" multiple>
                  <option value="">-- Select Sub-Category --</option>
                  <?php
                  $selectedSubcategories = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];
                  if (!empty($selectedCategories)) {
                    $parentIds = implode(',', array_map('intval', $selectedCategories));
                    $url = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=" . $parentIds;
                    $data = curl_get_contents($url);
                    if ($data !== false) {
                      $subcategories = json_decode($data);
                      if (!empty($subcategories)) {
                        foreach ($subcategories as $subcat) {
                          $subcatId = $subcat->id;
                          $name = htmlspecialchars($subcat->category_name);
                          $selected = in_array($subcatId, $selectedSubcategories) ? 'selected' : '';
                          echo "<option value='{$subcatId}' {$selected}>{$name}</option>";
                        }
                      }
                    }
                  }
                  ?>
                </select>
              </div>

    

            </form>

            <div id="activeFilters" class="mt-2  d-flex justify-content-between align-items-center">
              <span class="active-filter-label">Active Filters:</span>
              <div class="filter-tags d-inline-block ms-2" id="filterTagsContainer">
              <span id="clearSearch"
        style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
               cursor:pointer; display:none; font-size:18px;">
              &times;
            </span>
              </div>
              <button id="clearFiltersBtn" class="d-flex justify-content-end btn btn-sm btn-outline-primary ms-3">Clear All</button>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="d-flex justify-content-end mb-3">
      <div class="sort-dropdown">
        <select id="sortDropdown" class="form-select" style="width:200px;">
          <option value="recent">Most recent</option>
          <option value="popular">Most Popular</option>
          <option value="upvoted">Most upvoted</option>
          <option value="answered">Most answered</option>
          <option value="unanswered">Unanswered</option>
        </select>
      </div>
    </div>

=======
        
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
          <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
            <h4 class="filter-title">Filter Questions</h4>

            <form method="get" id="questionFilterForm">
            <input type="hidden" name="slug" value="<?php echo $groupslug; ?>">
              <!-- Search -->
              <div class="filter-group">
                <h5>Search</h5>
                <div class="filter-options mb-2">
                  <input type="text" name="search" class="form-control"
                    placeholder="Search title, author, category, subcategory"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
              </div>

              <!-- Category -->
              <div class="filter-group">
                <h5>Category</h5>
                <div class="filter-options">
                  <select name="category[]" id="category" class="form-select select-multiple" multiple>
                    <option value="">-- Select Category --</option>
                    <?php
                    $url = $siteurl . "script/register.php?action=categorieslists";
                    $data = curl_get_contents($url);
                    $selectedCategories = isset($_GET['category']) ? (array)$_GET['category'] : [];

                    if ($data !== false) {
                      $categories = json_decode($data);
                      if (!empty($categories)) {
                        foreach ($categories as $category) {
                          $categoryId = $category->id;
                          $name = htmlspecialchars($category->category_name);
                          $selected = in_array($categoryId, $selectedCategories) ? 'selected' : '';
                          echo "<option value='{$categoryId}' {$selected}>{$name}</option>";
                        }
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- Subcategory -->
              <div class="filter-group">
                <h5>Sub Category</h5>
                <div class="filter-options">
                  <select name="subcategory[]" id="subcategory" class="form-select select-multiple" multiple>
                    <option value="">-- Select Sub-Category --</option>
                    <?php
                    $selectedSubcategories = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];
                    if (!empty($selectedCategories)) {
                      $parentIds = implode(',', array_map('intval', $selectedCategories));
                      $url = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=" . $parentIds;
                      $data = curl_get_contents($url);
                      if ($data !== false) {
                        $subcategories = json_decode($data);
                        if (!empty($subcategories)) {
                          foreach ($subcategories as $subcat) {
                            $subcatId = $subcat->id;
                            $name = htmlspecialchars($subcat->category_name);
                            $selected = in_array($subcatId, $selectedSubcategories) ? 'selected' : '';
                            echo "<option value='{$subcatId}' {$selected}>{$name}</option>";
                          }
                        }
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- Sort -->
              <div class="filter-group">
                <h5>Sort</h5>
                <div class="filter-options">
                  <select name="sort" class="form-select">
                    <option value="popular" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'popular') ? 'selected' : ''; ?>>Most Popular</option>
                    <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                  </select>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 mt-2">Filter</button>
            </form>
          </div>
        </div>
        <!-- End Filters Sidebar -->

        <!-- Questions Section -->
        <div class="col-lg-9">
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
          <div class="courses-header" data-aos="fade-left" data-aos-delay="100">
            <!----
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" placeholder="Search Questions..." readonly>
            </div>

           
          
            <div class="sort-dropdown">
              <select readonly>
                <option>Sort by: Most Popular</option>
                <option>Newest First</option>
              </select>
            </div>
             --->
          </div>

          <div class="courses-grid" data-aos="fade-up" data-aos-delay="200">
            <div class="row">
              <?php
              // Fetch and Filter Questions
              $url = $siteurl . "script/admin.php?action=questionlists";
              $data = curl_get_contents($url);

              $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
              $selectedCategories = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
              $selectedSubcategories = isset($_GET['subcategory']) ? array_map('intval', (array)$_GET['subcategory']) : [];
              $sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';

              if ($data !== false) {
                $questions = json_decode($data);
                $activeQuestions = [];

                if (!empty($questions)) {
                  foreach ($questions as $q) {
                    if (isset($q->status) && strtolower($q->status) === 'active' && $q->group_id == $group_id) {
                      // Category filter
                      if (!empty($selectedCategories)) {
                        $questionCategories = array_map('intval', explode(',', $q->categories ?? ''));
                        if (empty(array_intersect($selectedCategories, $questionCategories))) continue;
                      }

                      // Subcategory filter
                      if (!empty($selectedSubcategories)) {
                        $questionSubcategories = array_map('intval', explode(',', $q->subcategories ?? ''));
                        if (empty(array_intersect($selectedSubcategories, $questionSubcategories))) continue;
                      }

                      // Search filter
                      if ($searchTerm !== '') {
                        $title = strtolower($q->title ?? '');
                        $author = strtolower(trim(($q->first_name ?? '') . ' ' . ($q->last_name ?? '')));
                        $categoryNames = strtolower($q->category_names ?? '');
                        $subcategoryNames = strtolower($q->subcategory_names ?? '');
                        if (
                          strpos($title, strtolower($searchTerm)) === false &&
                          strpos($author, strtolower($searchTerm)) === false &&
                          strpos($categoryNames, strtolower($searchTerm)) === false &&
                          strpos($subcategoryNames, strtolower($searchTerm)) === false
                        ) continue;
                      }

                      $activeQuestions[] = $q;
                    }
                  }

                  // Sorting
                  if ($sort == 'newest') {
                    usort($activeQuestions, function ($a, $b) {
                      return strtotime($b->created_at) - strtotime($a->created_at);
                    });
                  } else { // popular
                    usort($activeQuestions, function ($a, $b) {
                      return ($b->views ?? 0) - ($a->views ?? 0);
                    });
                  }

                  // Pagination setup
                  $questionsPerPage = 16;
                  $totalQuestions = count($activeQuestions);
                  $totalPages = ceil($totalQuestions / $questionsPerPage);
                  $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                  $start = ($page - 1) * $questionsPerPage;

                  $questionsToShow = array_slice($activeQuestions, $start, $questionsPerPage);

                  // Display Questions
                  if (!empty($questionsToShow)) {
                    foreach ($questionsToShow as $question) {
                      $questionId = $question->id;
                      $title = htmlspecialchars($question->title);
                      $article = htmlspecialchars($question->article);
<<<<<<< HEAD
                      $views = intval($question->views ?? 0);
                      $total_answers = intval($question->total_answers ?? 0);
=======
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
                      $date = date('M d, Y', strtotime($question->created_at));
                      $category = !empty($question->category_names) ? trim(explode(',', $question->category_names)[0]) : 'Uncategorized';
                      $subcategory = !empty($question->subcategory_names) ? trim(explode(',', $question->subcategory_names)[0]) : 'General';

                      $anonymous = intval($question->anonymous ?? 0);
                      $authorDisplay = ($anonymous === 1)
                        ? "Anonymous"
                        : (trim(($question->first_name ?? '') . ' ' . ($question->last_name ?? '')) ?: "Unknown User");

                      $words = explode(' ', strip_tags($article));
                      $shortText = implode(' ', array_slice($words, 0, 5));
                      $hasMore = count($words) > 5;
                      ?>

                      <div class="col-lg-4 col-md-6">
                        <div class="course-card">
                          <div class="course-content">
                            <div class="course-meta">
                              <span class="category"><?php echo $category; ?></span>
                              <span class="level"><?php echo $subcategory; ?></span>
                            </div>
                            <h3 class="mb-2"><?php echo $title; ?></h3>

                            <div class="bio-text">
                              <p class="bio-short"><?php echo $shortText; ?><?php if ($hasMore) echo '...'; ?></p>
                              <?php if ($hasMore): ?>
                                <p class="bio-full d-none"><?php echo nl2br($article); ?></p>
                                <a href="#" class="read-toggle text-primary" style="font-size:14px;">Read More</a>
                              <?php endif; ?>
                            </div>

                            <div class="mt-2 text-muted small">
                              Asked by <?php echo $authorDisplay; ?> on <?php echo $date; ?>
                            </div>
<<<<<<< HEAD
                            <div class="mt-1 text-muted small">
                              <?php echo $views; ?> Views | <?php echo $total_answers; ?> Answers
                            </div>
=======

>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
                            <a href="single-questions/<?php echo $question->slug; ?>" class="btn-course mt-2">
                              View Question
                            </a>
                          </div>
                        </div>
                      </div>

                      <?php
                    }
                  } else {
                    echo "<p>No questions found for this filter.</p>";
                  }
                }
              } else {
                echo "<p>Failed to fetch questions.</p>";
              }
              ?>
            </div>
          </div>

          <!-- Pagination -->
<<<<<<< HEAD
          <?php if (!empty($totalPages) && $totalPages > 1): ?>
            <div class="pagination-wrapper" data-aos="fade-up" data-aos-delay="300">
              <nav aria-label="Courses pagination">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])); ?>">
                      <i class="bi bi-chevron-left"></i>
                    </a>
                  </li>
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                      <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)])); ?>">
                      <i class="bi bi-chevron-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          <?php endif; ?>
=======
<?php if (!empty($totalPages) && $totalPages > 1): ?>
  <div class="pagination-wrapper" data-aos="fade-up" data-aos-delay="300">
    <nav aria-label="Courses pagination">
      <ul class="pagination justify-content-center">
        <?php
        // base params (ensure group_id is present)
        $baseParams = $_GET;
        if (!isset($baseParams['slug']) && !empty($groupslug)) {
            $baseParams['slug'] = $groupslug;
        }

        // previous
        $prev = $baseParams;
        $prev['page'] = max(1, $page - 1);
        ?>
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?<?php echo http_build_query($prev); ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): 
            $p = $baseParams;
            $p['page'] = $i;
        ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="?<?php echo http_build_query($p); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>

        <?php
        $next = $baseParams;
        $next['page'] = min($totalPages, $page + 1);
        ?>
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?<?php echo http_build_query($next); ?>">
            <i class="bi bi-chevron-right"></i>
                </a>
                </li>
            </ul>
            </nav>
        </div>
        <?php endif; ?>

>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762

        </div>
      </div>
    </div>
  </section>
</main>
<<<<<<< HEAD
 </div>
=======


>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762

<?php include "footer.php"; ?>
