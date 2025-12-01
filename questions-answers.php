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
                      $views = intval($question->views ?? 0);
                      $total_answers = intval($question->total_answers ?? 0);
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
                            <div class="mt-1 text-muted small">
                              <?php echo $views; ?> Views | <?php echo $total_answers; ?> Answers
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



<section id="trending-category" class="trending-category section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Related Blogs</h2>
      <p><a href="<?php echo $siteurl; ?>blog.php">View All</a></p>
    </div>
  </div><!-- End Section Title -->

  <!-- Blog Grid Container (filtered: subcategory -> category -> fallback recent) -->
  <div class="container my-5">
    <div class="row g-4">
      <?php
      $limit = 4; // Number of blogs to show
      $url = $siteurl . "script/admin.php?action=bloglists";
      $data = curl_get_contents($url);

      $related = [];
      if ($data !== false) {
        $blogs = json_decode($data);
        $matches = [];
        $fallback = [];

        $selectedSub = !empty($selectedSubcategories) ? intval($selectedSubcategories[0]) : 0;
        $selectedCat = !empty($selectedCategories) ? intval($selectedCategories[0]) : 0;

        if (!empty($blogs)) {
          foreach ($blogs as $blog) {
            // skip inactive or group posts
            if (isset($blog->status) && strtolower($blog->status) !== 'active') continue;
            if (!empty($blog->group_id)) continue;

            $bCats = !empty($blog->categories) ? array_map('intval', array_filter(array_map('trim', explode(',', $blog->categories)))) : [];
            $bSubs = !empty($blog->subcategories) ? array_map('intval', array_filter(array_map('trim', explode(',', $blog->subcategories)))) : [];

            $isMatch = false;
            if ($selectedSub && in_array($selectedSub, $bSubs)) {
              $isMatch = true; // subcategory
            } elseif ($selectedCat && in_array($selectedCat, $bCats)) {
              $isMatch = true; // category
            }

            if ($isMatch) {
              $matches[] = $blog;
            } else {
              $fallback[] = $blog;
            }

            if (count($matches) >= $limit) break; // early stop on matches
          }

          $source = !empty($matches) ? array_slice($matches, 0, $limit) : array_slice($fallback, 0, $limit);

          foreach ($source as $b) {
            $blogId = $b->id ?? '';
            $title = htmlspecialchars($b->title ?? 'Untitled');
            $slug = htmlspecialchars($b->slug ?? '');
            $author = htmlspecialchars(trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? '')) ?: 'Unknown');
            $content = limitWords(strip_tags($b->article ?? ''), 10);
            $date = date('F d, Y', strtotime($b->created_at ?? ''));
            $views = htmlspecialchars($b->views ?? 0);
            $photo = !empty($b->photo) ? $siteurl . $imagePath . $b->photo : $siteurl . "assets/img/user.jpg";
            $blogimage = !empty($b->featured_image) ? $siteurl . $imagePath . $b->featured_image : $siteurl . "assets/img/default-blog.jpg";
            $blogUrl = $siteurl . "blog-details/" . $slug;
            $categoryNames = !empty($b->category_names) ? explode(',', $b->category_names) : ['General'];
            $category = htmlspecialchars(trim($categoryNames[0]));

            $related[] = [
              'title' => $title,
              'slug' => $slug,
              'author' => $author,
              'content' => $content,
              'date' => $date,
              'views' => $views,
              'photo' => $photo,
              'blogimage' => $blogimage,
              'blogUrl' => $blogUrl,
              'category' => $category
            ];
          }
        }
      }

      if (!empty($related)):
        foreach ($related as $r): ?>

          <div class="col-lg-3 col-md-6 col-12">
            <div class="card blog-card h-100 shadow-sm border-0">
              <a href="<?php echo $r['blogUrl']; ?>" class="blog-img-wrapper position-relative">
                <img src="<?php echo $r['blogimage']; ?>" class="card-img-top" alt="<?php echo $r['title']; ?>">
                <span class="badge category-badge-on-image"><?php echo $r['category']; ?></span>
              </a>
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between mb-2 align-items-center">
                  <small class="text-muted"><?php echo $r['date']; ?> • <?php echo $r['views']; ?> views</small>
                </div>
                <h5 class="card-title">
                  <a href="<?php echo $r['blogUrl']; ?>" class="text-dark text-decoration-none"><?php echo $r['title']; ?></a>
                </h5>
                <p class="card-text mb-4"><?php echo $r['content']; ?>...</p>
                <div class="mt-auto d-flex align-items-center">
                  <img src="<?php echo $r['photo']; ?>" alt="<?php echo $r['author']; ?>" class="rounded-circle me-2" style="width:40px;height:40px;">
                  <span><?php echo $r['author']; ?></span>
                </div>
              </div>
            </div>
          </div>

      <?php
        endforeach;
      else:
        echo '<p>No related blogs found.</p>';
      endif;
      ?>

    </div>
  </div>

</section>


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

$limit = 4; // Number of listings to show
$queryParts = [];
// prefer selected subcategory then category for related listings
if (!empty($selectedSubcategories)) {
  $queryParts[] = 'subcategory_id=' . intval($selectedSubcategories[0]);
} elseif (!empty($selectedCategories)) {
  $queryParts[] = 'category_id=' . intval($selectedCategories[0]);
}
$queryParts[] = 'items_per_page=' . intval($limit);
$queryParts[] = 'ajax=1';
$url = $siteurl . "script/admin.php?action=listinglists" . (count($queryParts) ? '&' . implode('&', $queryParts) : '');
$data = curl_get_contents($url);
$count = 0;

if ($data !== false) {
  $rawListings = json_decode($data);
  $listings = [];
  if (is_object($rawListings) && isset($rawListings->data) && is_array($rawListings->data)) {
    $listings = $rawListings->data;
  } elseif (is_array($rawListings)) {
    $listings = $rawListings;
  }

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active') {
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





<?php include "footer.php"; ?>
