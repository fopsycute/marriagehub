
<?php 
$requireLogin = false;
include "header.php"; 

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
                        html: 'You are not a member of this group. <br><b>Join our group to view this blog post.</b>',
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

<main class="main">
<div class="container mb-5 mt-5">
<div id="groupblogFilterWrappers">

<!-- ========================================================= -->
<!-- SHOW/HIDE FILTER TOGGLE (Mobile & Tablet) -->
<!-- ========================================================= -->
<div class="d-lg-none mb-3">
    <button type="button" id="toggleFilterBtn" class="btn btn-primary w-100">
        <i class="bi bi-funnel"></i> Show Filters
    </button>
</div>

<!-- FILTER BACKDROP -->
<div class="filter-backdrop" id="filterBackdrop"></div>

<div class="row">

<!-- ========================================================= -->
<!-- FILTER SIDEBAR -->
<!-- ========================================================= -->
<div class="col-lg-3 d-none d-lg-block" id="filterSidebar">
    <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
        <button type="button" class="close-filter-btn d-lg-none">
            <i class="bi bi-x"></i>
        </button>
        <h4 class="filter-title">Filter Blog</h4>

        <form method="get" id="blogFilterForm">

            <!-- SEARCH -->
            <div class="filter-group">
                <h5>Search</h5>
                <input type="text" name="search" id="searchInput" class="form-control" 
                       placeholder="Search title, author or category"
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>

            <!-- CATEGORY -->
            <div class="filter-group">
                <h5>Category</h5>
               <select name="category[]" id="category" class="form-select select-multiple" required multiple>
                    <?php
                    $url = $siteurl . "script/register.php?action=categorieslists";
                    $data = curl_get_contents($url);
                    $selectedCategories = isset($_GET['category']) ? (array)$_GET['category'] : [];

                    if ($data !== false) {
                        foreach (json_decode($data) as $category) {
                            $selected = in_array($category->id, $selectedCategories) ? "selected" : "";
                            echo "<option value='{$category->id}' $selected>{$category->category_name}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- SUBCATEGORY -->
            <div class="filter-group">
                <h5>Sub Category</h5>
                <select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>
                    <?php
                    $selectedSub = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];

                    if (!empty($selectedCategories)) {
                        $parentIds = implode(",", array_map("intval", $selectedCategories));
                        $url = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=$parentIds";
                        $data = curl_get_contents($url);

                        if ($data !== false) {
                            foreach (json_decode($data) as $subcat) {
                                $selected = in_array($subcat->id, $selectedSub) ? "selected" : "";
                                echo "<option value='{$subcat->id}' $selected>{$subcat->category_name}</option>";
                            }
                        }
                    } else {
                        echo "<option value=''>-- Select Sub-Category --</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- CLEAR FILTERS -->
            <div class="filter-group mt-3">
                <button type="button" id="clearFilters" class="btn btn-secondary w-100">
                    Clear Filters
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ========================================================= -->
<!-- BLOG LIST -->
<!-- ========================================================= -->
<div class="col-lg-9">
<section id="blog-posts" class="blog-posts section">
<div class="container">
<div class="row gy-4" id="blogResults">

<?php
// GET FILTER VALUES
$searchTerm = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$selectedCategories = isset($_GET['category']) ? array_map('intval', $_GET['category']) : [];
$selectedSubcategories = isset($_GET['subcategory']) ? array_map('intval', $_GET['subcategory']) : [];

// FETCH BLOGS
=======
  <main class="main">
       <div class="container mb-5 mt-5">
      <div class="row">

      <div class="col-lg-3">
            <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
              <h4 class="filter-title">Filter Blog</h4>
        <form method="get" id="blogFilterForm">
             <div class="filter-group">
                <h5>Search</h5>
                <div class="filter-options mb-2">
                     <!-- keep group context when submitting filters -->
            <input type="hidden" name="slug" value="<?php echo $groupslug; ?>">

                    <input type="text" name="search" class="form-control" placeholder="Search title or author" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
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
      
              <div class="filter-group mb-2">
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
                <button type="submit" class="btn btn-primary w-100 mt-2">Filter</button>
              </div>
            </div><!-- End Course Filters -->
          </div>

        <div class="col-lg-9">

          <!-- Blog Posts Section -->
        <!-- Blog Posts Section -->
        <section id="blog-posts" class="blog-posts section">
          <div class="container">
            <div class="row gy-4">
<?php
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$blogsPerPage = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $blogsPerPage;
$selectedCategories = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
$selectedSubcategories = isset($_GET['subcategory']) ? array_map('intval', (array)$_GET['subcategory']) : [];

// Fetch all blogs
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);
    $activeBlogs = [];

<<<<<<< HEAD
    foreach ($blogs as $blog) {

        if (!isset($blog->status) || strtolower($blog->status) !== 'active' || $blog->group_id !== $group_id) continue;

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
            $blogUrl = $siteurl . "single-blog/" . $slug . "/" . $groupslug;
=======
    // Filter only active blogs
// ...existing code...
if (!empty($blogs)) {
    foreach ($blogs as $blog) {
        if (isset($blog->status) && strtolower($blog->status) === 'active' && $blog->group_id == $group_id) {
            // Category filter (multi)
            if (!empty($selectedCategories)) {
                $blogCategories = array_map('intval', explode(',', $blog->categories ?? ''));
                if (empty(array_intersect($selectedCategories, $blogCategories))) {
                    continue;
                }
            }
            // Subcategory filter (multi)
            if (!empty($selectedSubcategories)) {
                $blogSubcategories = array_map('intval', explode(',', $blog->subcategories ?? ''));
                if (empty(array_intersect($selectedSubcategories, $blogSubcategories))) {
                    continue;
                }
            }
            // Search filter (title, author, category name, subcategory name)
            if ($searchTerm !== '') {
                $title = strtolower($blog->title ?? '');
                $author = strtolower(trim(($blog->first_name ?? '') . ' ' . ($blog->last_name ?? '')));
                $categoryNames = strtolower($blog->category_names ?? '');
                $subcategoryNames = strtolower($blog->subcategory_names ?? '');
                if (
                    strpos($title, strtolower($searchTerm)) === false &&
                    strpos($author, strtolower($searchTerm)) === false &&
                    strpos($categoryNames, strtolower($searchTerm)) === false &&
                    strpos($subcategoryNames, strtolower($searchTerm)) === false
                ) {
                    continue;
                }
            }
            $activeBlogs[] = $blog;
        }
    }
}
// ...existing code...

    $totalBlogs = count($activeBlogs);
    $totalPages = ceil($totalBlogs / $blogsPerPage);

    // Slice only current page’s blogs
    $blogsToShow = array_slice($activeBlogs, $start, $blogsPerPage);

    if (!empty($blogsToShow)) {
        foreach ($blogsToShow as $blog) {
            $blogId = $blog->id;
            $title = htmlspecialchars($blog->title);
            $slug = htmlspecialchars($blog->slug);
            $views = htmlspecialchars($blog->views);
            $author = htmlspecialchars(trim($blog->first_name . ' ' . $blog->last_name));
            $content = limitWords(strip_tags($blog->article), 10);
            $date = date('F d, Y', strtotime($blog->created_at));
            $subcategoryNames = !empty($blog->subcategory_names) ? explode(',', $blog->subcategory_names) : [];
            $subcategory = isset($subcategoryNames[0]) ? htmlspecialchars(trim($subcategoryNames[0])) : '';
            $categoryNames = !empty($blog->category_names) ? explode(',', $blog->category_names) : ['General'];
            $category = htmlspecialchars(trim($categoryNames[0]));

            $blogimage = !empty($blog->featured_image) ? $siteurl . $imagePath . $blog->featured_image : $siteurl . "assets/img/default-blog.jpg";
            $blogUrl = $siteurl . "blog-details/" . $slug;
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
            ?>

            <div class="col-lg-4 col-md-6">
                <article class="position-relative h-100 shadow-sm rounded-4 overflow-hidden">
<<<<<<< HEAD
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

=======
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
                                <i class="bi bi-person"></i> <span class="ps-2"><?php echo $author; ?></span>
                            </div>
                            <span class="px-3 text-black-50">/</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-eye"></i> <span class="ps-2"><?php echo $views; ?></span>
                            </div>
                        </div>
                        <p><?php echo $content; ?></p>
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
                        <hr>
                        <a href="<?php echo $blogUrl; ?>" class="readmore stretched-link">
                            <span>Read More</span><i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>

<<<<<<< HEAD
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
</div>
</div>
</div>
</div>
</main>
=======
            <?php
        }
    } else {
        echo "<p class='text-center'>No active blogs found for this filter/page.</p>";
    }
} else {
    echo "<p class='text-center text-danger'>Unable to fetch blog data.</p>";
}
?>
            </div>
          </div>
        </section>

<?php if (isset($totalPages) && $totalPages > 1): ?>
<section id="blog-pagination" class="blog-pagination section">
    <div class="container">
        <div class="d-flex justify-content-center">
// ...existing code before pagination links...
<ul class="pagination">
    <!-- Previous Button -->
    <li class="<?php echo ($page <= 1) ? 'disabled' : ''; ?>">
        <a href="?<?php
            $params = $_GET;
            // ensure group_id is preserved even if not in $_GET
            if (!isset($params['slug']) && !empty($groupslug)) $params['slug'] = $groupslug;
            $params['page'] = max(1, $page - 1);
            echo http_build_query($params);
        ?>"><i class="bi bi-chevron-left"></i></a>
    </li>

    <!-- Page Numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li>
            <a href="?<?php
                $params = $_GET;
                if (!isset($params['slug']) && !empty($groupslug)) $params['slug'] = $groupslug;
                $params['page'] = $i;
                echo http_build_query($params);
            ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        </li>
    <?php endfor; ?>

    <!-- Next Button -->
    <li class="<?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
        <a href="?<?php
            $params = $_GET;
            if (!isset($params['slug']) && !empty($groupslug)) $params['slug'] = $groupslug;
            $params['page'] = min($totalPages, $page + 1);
            echo http_build_query($params);
        ?>"><i class="bi bi-chevron-right"></i></a>
    </li>
</ul>
// ...existing code...
        </div>
    </div>
</section>
<?php endif; ?>

</div>
</div>
</div>
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762

<?php include "footer.php"; ?>