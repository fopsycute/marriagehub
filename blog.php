<?php 
$requireLogin = false;
include "header.php"; 
?>

<main class="main">

<!-- Top Banner Advert -->
<?php
$placementSlug = 'blog-page-top-banner';
include "listing-banner.php";
?>

<div class="container mb-5 mt-5">
<div id="blogFilterWrapper">
<div class="row">
    <!-- small/medium toggle for filters (visible only below lg) -->
    <div class="row d-lg-none mb-2 w-100">
        <div class="col-12">
            <button id="toggleBlogFiltersBtn" class="btn btn-outline-primary w-100" aria-expanded="false" aria-controls="blogFilterBlock">Show filters</button>
        </div>
    </div>

<!-- ========================================================= -->
<!-- FILTER SIDEBAR -->
<!-- ========================================================= -->
 <section id="category-header" class="category-header section">
<div class="container aos-init aos-animate" data-aos="fade-up">
    <div id="blogFilterBlock" class="filter-container mb-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <form method="get" id="blogFilterForm">
   		<div class="row g-3">
                  <div class="col-12 col-md-6 col-lg-4">
                    <div class="filter-item search-form">
                <label>Search</label>
                <input type="text" name="search" id="searchInput" class="form-control" 
                       placeholder="Search title, author or category"
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
              </div>
               
            <!-- CATEGORY -->
           <div class="col-12 col-md-6 col-lg-4">
                    <div class="filter-item search-form">
                <label>Category</label>
               <select name="category[]" id="category" class="form-select select-multiple" multiple>
                    <?php
                    $url = $siteurl . "script/register.php?action=categorieslists";
                    $data = curl_get_contents($url);

                    // raw values passed in the URL (may be numeric ids or slugs)
                    $rawSelectedCategories = isset($_GET['category']) ? (array)$_GET['category'] : [];

                    if ($data !== false) {
                        foreach (json_decode($data) as $category) {
                            // allow selection by either numeric id or slug
                            $selected = (in_array($category->id, $rawSelectedCategories) || in_array($category->slug, $rawSelectedCategories)) ? "selected" : "";
                            // prefer slug as the option value (friendly URL), fallback to id
                            $value = !empty($category->slug) ? htmlspecialchars($category->slug) : intval($category->id);
                            echo "<option value='" . $value . "' $selected>" . htmlspecialchars($category->category_name) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
                </div>

            <!-- SUBCATEGORY -->
           <div class="col-12 col-md-6 col-lg-4">
                    <div class="filter-item search-form">
                <label>Subcategory</label>
                <select name="subcategory[]" id="subcategory" class="form-select select-multiple" multiple>
                    <?php
                    $selectedSub = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];

                    if (!empty($rawSelectedCategories)) {
                        $subitems = [];
                        // selected categories may be slugs or ids; fetch subcategories for each selected category
                        foreach ($rawSelectedCategories as $catVal) {
                            if (is_numeric($catVal) && intval($catVal) > 0) {
                                $parentIds = intval($catVal);
                                $url = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=" . $parentIds;
                                $data = curl_get_contents($url);
                                if ($data !== false) {
                                    $decoded = json_decode($data);
                                    if (is_array($decoded)) $subitems = array_merge($subitems, $decoded);
                                }
                            } else {
                                // try fetching by slug using admin endpoint
                                $url = $siteurl . "script/admin.php?action=subcategory_list&category_slug=" . rawurlencode($catVal);
                                $data = curl_get_contents($url);
                                if ($data !== false) {
                                    $decoded = json_decode($data);
                                    if (is_array($decoded)) $subitems = array_merge($subitems, $decoded);
                                }
                            }
                        }

                        // dedupe by id (preserve basic structure)
                        $seen = [];
                        foreach ($subitems as $subcat) {
                            if (!isset($subcat->id) || isset($seen[$subcat->id])) continue;
                            $seen[$subcat->id] = true;
                            $selected = (in_array($subcat->id, $selectedSub) || in_array($subcat->slug, $selectedSub)) ? "selected" : "";
                            $val = !empty($subcat->slug) ? htmlspecialchars($subcat->slug) : intval($subcat->id);
                            echo "<option value='" . $val . "' $selected>" . htmlspecialchars($subcat->category_name) . "</option>";
                        }
                    } else {
                        echo "<option value=''>-- Select Sub-Category --</option>";
                    }
                    ?>
                </select>
            </div>
            </div>

<div class="">

            <!-- CLEAR FILTERS -->
            <div class="filter-group mt-3 d-flex justify-content-end ">
                <button type="button" id="clearFilters" class="btn clear-all-btn btn-primary">
                    Clear Filters
                </button>
            </div>
			</div>
			</div>
        </form>
    </div>
</div>
</section>


<!-- ========================================================= -->
<!-- BLOG LIST -->
<!-- ========================================================= -->
<div class="col-lg-12">
<section id="blog-posts" class="blog-posts section">
<div class="container">
<div class="row gy-4" id="blogResults">

<?php
// GET FILTER VALUES
// Accept either numeric IDs or slugs in the URL (e.g. category[]=parent-slug)
$searchTerm = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$rawSelectedSubcategories = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];

// We'll resolve any slugs into numeric ids so server-side filtering keeps working
$selectedCategories = [];
$selectedSubcategories = [];

// Fetch a flat category list (id + slug) for mapping
$catUrl = $siteurl . "script/admin.php?action=category_list";
$catData = @curl_get_contents($catUrl);
$slugToCategoryId = [];
if ($catData !== false) {
    $cats = json_decode($catData);
    if (is_array($cats)) {
        foreach ($cats as $c) {
            if (!empty($c->slug)) $slugToCategoryId[(string)$c->slug] = intval($c->id);
            // also keep string of id -> id so we can safely look up numeric values
            $slugToCategoryId[(string)$c->id] = intval($c->id);
        }
    }
}

// Resolve selected categories (support both numeric values and slugs)
foreach ($rawSelectedCategories as $val) {
    if ($val === null || $val === '') continue;
    if (is_numeric($val) && intval($val) > 0) {
        $selectedCategories[] = intval($val);
    } elseif (isset($slugToCategoryId[(string)$val])) {
        $selectedCategories[] = $slugToCategoryId[(string)$val];
    }
}

// If the frontend provided subcategory slugs, we may need subcategory -> id mapping.
// We'll fetch subcategories for any resolved parent categories (if present) and attempt to resolve
$subSlugToId = [];
if (!empty($selectedCategories)) {
    $subitems = [];
    // fetch subcategories for each resolved category id (admin endpoint expects a single id)
    foreach ($selectedCategories as $catId) {
        $subUrl = $siteurl . "script/admin.php?action=subcategory_list&category_id=" . intval($catId);
        $subData = @curl_get_contents($subUrl);
        if ($subData !== false) {
            $decoded = json_decode($subData);
            if (is_array($decoded)) $subitems = array_merge($subitems, $decoded);
        }
    }

    if (!empty($subitems)) {
        // build a slug/id map
        foreach ($subitems as $s) {
            if (!empty($s->slug)) $subSlugToId[(string)$s->slug] = intval($s->id);
            $subSlugToId[(string)$s->id] = intval($s->id);
        }
    }
}

foreach ($rawSelectedSubcategories as $val) {
    if ($val === null || $val === '') continue;
    if (is_numeric($val) && intval($val) > 0) {
        $selectedSubcategories[] = intval($val);
    } elseif (isset($subSlugToId[(string)$val])) {
        $selectedSubcategories[] = $subSlugToId[(string)$val];
    }
}

// FETCH BLOGS
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);
    $activeBlogs = [];

    foreach ($blogs as $blog) {

        if (!isset($blog->status) || strtolower($blog->status) !== 'active' || $blog->group_id !== "") continue;

        // CATEGORY FILTER
        if (!empty($selectedCategories)) {
            $blogCats = array_map('intval', explode(',', $blog->categories ?? ''));
            if (!array_intersect($selectedCategories, $blogCats)) continue;
        }

        // SUBCATEGORY FILTER
        if (!empty($selectedSubcategories)) {
            $blogSubs = array_map('intval', explode(',', $blog->subcategories ?? ''));
            if (!array_intersect($selectedSubcategories, $blogSubs)) continue;
        }

        // SEARCH FILTER
        if ($searchTerm !== '') {
            $title = strtolower($blog->title ?? '');
            $author = strtolower(trim(($blog->first_name ?? '') . ' ' . ($blog->last_name ?? '')));
            $catNames = strtolower($blog->category_names ?? '');
            $subcatNames = strtolower($blog->subcategory_names ?? '');

            if (!(
                str_contains($title, $searchTerm) ||
                str_contains($author, $searchTerm) ||
                str_contains($catNames, $searchTerm) ||
                str_contains($subcatNames, $searchTerm)
            )) continue;
        }

        $activeBlogs[] = $blog;
    }

    // DISPLAY BLOGS
    if (!empty($activeBlogs)) {
        foreach ($activeBlogs as $blog) {

            $title = htmlspecialchars($blog->title);
            $slug = htmlspecialchars($blog->slug);
            $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
            $views = htmlspecialchars($blog->views);
            $date = date("F d, Y", strtotime($blog->created_at));

            $subcat = explode(',', $blog->subcategory_names ?? '')[0] ?? '';
            $cat = explode(',', $blog->category_names ?? 'General')[0];

            $blogimage = !empty($blog->featured_image) 
                ? $siteurl . $imagePath . $blog->featured_image 
                : $siteurl . "assets/img/default-blog.jpg";

            $content = limitWords(strip_tags($blog->article), 10);
            $blogUrl = $siteurl . "blog-details/" . $slug;
            ?>

            <div class="col-lg-4 col-md-6">
                <article class="position-relative h-100 shadow-sm rounded-4 overflow-hidden">
                    <div class="post-img">
                        <img src="<?php echo $blogimage; ?>" class="img-fluid w-100">
                        <span class="post-category"><?php echo $cat; ?></span>
                 
                    </div>

                    <div class="post-content p-3">
                        <div class="post-meta mb-2">
                            <i class="bi bi-clock"></i> <span><?php echo $date; ?></span>
                        </div>

                        <h3 class="post-title"><?php echo $title; ?></h3>

                        <div class="meta d-flex align-items-center">
                            <i class="bi bi-person"></i> 
                            <span class="ps-2"><?php echo $author; ?></span>
                            <span class="px-3 text-black-50">/</span>
                            <i class="bi bi-eye"></i> 
                            <span class="ps-2"><?php echo $views; ?></span>
                        </div>

                        <p><?php echo $content; ?></p>

                        <hr>
                        <a href="<?php echo $blogUrl; ?>" class="readmore stretched-link">
                            <span>Read More</span><i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>

<?php
        }
    } else {
        echo "<p class='text-center'>No blogs found for this filter.</p>";
    }

} else {
    echo "<p class='text-center text-danger'>Unable to fetch blogs.</p>";
}
?>

</div>
</div>
</section>

<section id="category-pagination" class="category-pagination section">
    <div class="container">
        <nav class="d-flex justify-content-center" aria-label="Page navigation">
            <ul id="marketplace-pagination-list" class="pagination">
                <!-- JS will populate pagination links -->
            </ul>
        </nav>
    </div>
</section>

</div>
</div>
</div>
</div>


  <section id="best-sellers" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Related Products & Services</h2>
       
      </div><!-- End Section Title -->

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
         
          <!-- Product 4 -->
  <?php
$limit = 4; // Number of listings to show
$queryParts = [];
// Prefer a selected subcategory (more specific) then category to find related listings
if (!empty($selectedSubcategories)) {
    $queryParts[] = 'subcategory_id=' . intval($selectedSubcategories[0]);
} elseif (!empty($selectedCategories)) {
    $queryParts[] = 'category_id=' . intval($selectedCategories[0]);
}
// request structured response & limit
$queryParts[] = 'items_per_page=' . intval($limit);
$queryParts[] = 'ajax=1';
$url = $siteurl . "script/admin.php?action=listinglists" . (count($queryParts) ? '&' . implode('&', $queryParts) : '');
$data = curl_get_contents($url);
$count = 0;

if ($data !== false) {
    $listingsRaw = json_decode($data);
    // listinglists returns structured object when ajax=1: { total, data }
    $listings = [];
    if (is_object($listingsRaw) && isset($listingsRaw->data) && is_array($listingsRaw->data)) {
        $listings = $listingsRaw->data;
    } elseif (is_array($listingsRaw)) {
        $listings = $listingsRaw;
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

      </div>

</section>

<!-- Questions Slider Section -->
<section id="questions-slider" class="section">
   <div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
      <h2>Related Question and Answer</h2>
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

            // Prefer subcategory matches then category matches
            $matches = [];
            $fallback = [];

            foreach ($questions as $question) {
                if ($count >= 6) break;

                // Skip inactive questions (if status field exists)
                if (isset($question->status) && strtolower($question->status) !== 'active') continue;

                $qCategories = !empty($question->categories) ? array_map('intval', array_filter(array_map('trim', explode(',', $question->categories)))) : [];
                $qSubcategories = !empty($question->subcategories) ? array_map('intval', array_filter(array_map('trim', explode(',', $question->subcategories)))) : [];

                $selectedSub = !empty($selectedSubcategories) ? intval($selectedSubcategories[0]) : 0;
                $selectedCat = !empty($selectedCategories) ? intval($selectedCategories[0]) : 0;

                $isMatch = false;
                if ($selectedSub && in_array($selectedSub, $qSubcategories)) {
                    $isMatch = true; // subcategory match
                } elseif ($selectedCat && in_array($selectedCat, $qCategories)) {
                    $isMatch = true; // category match
                }

                // collect matches; fallback collects all recent entries to show when no matches
                if ($isMatch) {
                    $matches[] = $question;
                } else {
                    $fallback[] = $question;
                }
            }

            // Choose source: matched questions (prefer) or fallback (latest)
            $sourceQuestions = !empty($matches) ? $matches : array_slice($fallback, 0, 6);
            $count = 0;
            foreach ($sourceQuestions as $question) {
                if ($count >= 6) break;

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

<!-- Sidebar Ad -->
<?php include "sidebar-ad.php"; ?>

</main>


<?php include "footer.php"; ?>
