

<?php 
$requireLogin = false;
include "header.php"; 

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

     // API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchtherapistslug&slug=" . $slug;

    // Fetch therapist details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $userdetails = json_decode($data);

        if (!empty($userdetails) && isset($userdetails[0])) {
            $userdetail = $userdetails[0];

            // 🧠 Basic details
            $user_id = $userdetail->id ?? '';
            $fullName = htmlspecialchars(trim(($userdetail->first_name ?? '') . ' ' . ($userdetail->last_name ?? '') . ' ' . ($userdetail->middle_name ?? '')));

            // 📋 Profile info
            $nationality = $userdetail->nationality ?? '';
            $languages = $userdetail->languages ?? '';
            $website = $userdetail->website ?? '';
            $email = $userdetail->email ?? '';
            $facebook = $userdetail->facebook ?? '';
            $twitter = $userdetail->twitter ?? '';
            $instagram = $userdetail->instagram ?? '';
            $linkedin = $userdetail->linkedin ?? '';
            $phone = $userdetail->phone ?? '';
            $address = $userdetail->address ?? '';
            $bio = $userdetail->bio ?? '';
            $experience_years = $userdetail->experience_years ?? '';
            $specializations_names = $userdetail->specializations_names ?? '';
            $sub_specialization_names = $userdetail->subspecializations_names ?? '';
            $professional_field_names = $userdetail->professional_field_names ?? '';
            $professional_title_names = $userdetail->professional_title_names ?? '';
            $work_with = $userdetail->work_with ?? '';
            $session_format = $userdetail->session_format ?? '';
            $consultation_days = $userdetail->consultation_days ?? '';
            $session_duration = $userdetail->session_duration ?? '';
            $rate = $userdetail->rate ?? '0';
            $qualification = $userdetail->qualification ?? '';
            $associations = $userdetail->associations ?? '';
            $certifications = $userdetail->certifications ?? '';

            // 📷 Photo fallback
            $photo = !empty($userdetail->photo)
                ? $siteurl . $imagePath . $userdetail->photo
                : $siteurl . "assets/img/user.jpg";

        } else {
            echo "<div class='alert alert-warning'>No therapist found for the provided slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching therapist data. Please try again later.</div>";
    }
} else {
    // No slug provided — redirect to homepage
    header("Location: {$siteurl}");
    exit;
}
?>

  <main class="main">
       <div class="container mb-5 mt-5">
      <div class="row">

      <div class="col-lg-3">
            <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
              <h4 class="filter-title">Filter Blog</h4>
        <form method="get" id="blogFilterForm">
             <input type="hidden" name="slug" value="<?php echo $slug; ?>">
             <div class="filter-group">
                <h5>Search</h5>
                <div class="filter-options mb-2">
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
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);
    $activeBlogs = [];

    // Filter only active blogs
// ...existing code...
if (!empty($blogs)) {
    foreach ($blogs as $blog) {
        if (isset($blog->status) && strtolower($blog->status) === 'active' && $blog->user_id == $user_id && $blog->group_id == '') {
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
            $blogUrl = $siteurl . "blog-details.php?slug=" . $slug;
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
                                <i class="bi bi-person"></i> <span class="ps-2"><?php echo $author; ?></span>
                            </div>
                            <span class="px-3 text-black-50">/</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-eye"></i> <span class="ps-2"><?php echo $views; ?></span>
                            </div>
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
            <ul class="pagination">
                <li class="<?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a href="?<?php
                        $params = $_GET;
                        if (!isset($params['slug']) && !empty($slug)) $params['slug'] = $slug;
                        $params['page'] = max(1, $page - 1);
                        echo http_build_query($params);
                    ?>"><i class="bi bi-chevron-left"></i></a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?<?php
                            $params = $_GET;
                            if (!isset($params['slug']) && !empty($slug)) $params['slug'] = $slug;
                            $params['page'] = $i;
                            echo http_build_query($params);
                        ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="<?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a href="?<?php
                        $params = $_GET;
                        if (!isset($params['slug']) && !empty($slug)) $params['slug'] = $slug;
                        $params['page'] = min($totalPages, $page + 1);
                        echo http_build_query($params);
                    ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </div>
    </div>
</section>
<?php endif; ?>

</div>
</div>
</div>

<?php include "footer.php"; ?>