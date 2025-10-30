<?php include "header.php"; ?>

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
        
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
          <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
            <h4 class="filter-title">Filter Questions</h4>

            <form method="get" id="questionFilterForm">
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
                    if (isset($q->status) && strtolower($q->status) === 'active') {
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

        </div>
      </div>
    </div>
  </section>
</main>



<?php include "footer.php"; ?>
