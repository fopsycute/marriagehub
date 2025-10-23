<?php 
include "header.php"; 
?>

<main class="main">

<div class="container mt-4 mb-5">
  <div class="row">
    <!-- Sidebar Filter -->
    <div class="col-lg-3">
      <div class="filter-box p-3 shadow-sm rounded-3">
        <h5 class="mb-3">Filter Groups</h5>
        <form method="get" id="groupFilterForm">
          <!-- Search -->
          <div class="mb-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" 
              placeholder="Search group or author" 
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          </div>

          <!-- Category -->
          <div class="mb-3">
            <label class="form-label">Category</label>
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
                          $selected = in_array($category->id, $selectedCategories) ? 'selected' : '';
                          echo "<option value='{$category->id}' $selected>" . htmlspecialchars($category->category_name) . "</option>";
                      }
                  }
              }
              ?>
            </select>
          </div>

          <!-- Subcategory -->
          <div class="mb-3">
            <label class="form-label">Subcategory</label>
            <select name="subcategory[]" id="subcategory" class="form-select select-multiple" multiple>
              <option value="">-- Select Subcategory --</option>
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
                              $selected = in_array($subcat->id, $selectedSubcategories) ? 'selected' : '';
                              echo "<option value='{$subcat->id}' $selected>" . htmlspecialchars($subcat->category_name) . "</option>";
                          }
                      }
                  }
              }
              ?>
            </select>
          </div>

          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </form>
      </div>
    </div>

    <!-- Groups Section -->
    <div class="col-lg-9">
      <?php
      // Pagination setup
      $limit = 12;
      $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
      $offset = ($page - 1) * $limit;

      // Get filters
      $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
      $selectedCategories = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
      $selectedSubcategories = isset($_GET['subcategory']) ? array_map('intval', (array)$_GET['subcategory']) : [];

      // Fetch all groups
      $url = $siteurl . "script/admin.php?action=grouplists";
      $data = curl_get_contents($url);

      $filteredGroups = [];
      if ($data !== false) {
          $groups = json_decode($data);
          if (!empty($groups)) {
              foreach ($groups as $group) {
                  if (isset($group->status) && strtolower($group->status) === 'active') {

                      // Category filter
                      if (!empty($selectedCategories)) {
                          $groupCategories = array_map('intval', explode(',', $group->categories ?? ''));
                          if (empty(array_intersect($selectedCategories, $groupCategories))) {
                              continue;
                          }
                      }

                      // Subcategory filter
                      if (!empty($selectedSubcategories)) {
                          $groupSubcategories = array_map('intval', explode(',', $group->subcategories ?? ''));
                          if (empty(array_intersect($selectedSubcategories, $groupSubcategories))) {
                              continue;
                          }
                      }

                      // Search filter
                      if ($searchTerm !== '') {
                          $title = strtolower($group->group_name ?? '');
                          $author = strtolower(trim(($group->first_name ?? '') . ' ' . ($group->last_name ?? '')));
                          $categoryNames = strtolower($group->category_names ?? '');
                          $subcategoryNames = strtolower($group->subcategory_names ?? '');
                          if (
                              strpos($title, strtolower($searchTerm)) === false &&
                              strpos($author, strtolower($searchTerm)) === false &&
                              strpos($categoryNames, strtolower($searchTerm)) === false &&
                              strpos($subcategoryNames, strtolower($searchTerm)) === false
                          ) {
                              continue;
                          }
                      }

                      $filteredGroups[] = $group;
                  }
              }
          }
      }

      // Pagination
      $totalGroups = count($filteredGroups);
      $totalPages = ceil($totalGroups / $limit);
      $groupsToDisplay = array_slice($filteredGroups, $offset, $limit);
      ?>

      <section id="featured-courses" class="featured-courses section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
          <div class="row gy-4">
            <?php if (!empty($groupsToDisplay)): ?>
              <?php foreach ($groupsToDisplay as $group): 
                $groupId = $group->id;
                $title = htmlspecialchars($group->group_name);
                $author = htmlspecialchars(trim($group->first_name . ' ' . $group->last_name));
                $group_access = $group->group_access;
                $group_type = $group->group_type;
                $photo = !empty($group->photo) ? $siteurl . $imagePath . $group->photo : $siteurl . "assets/img/user.jpg";
                $date = date('M d, Y', strtotime($group->created_at));
                $banner = $group->banner ?? '';
                $content = limitWords(strip_tags($group->group_description), 10);
                $slug = $group->slug ?? '';
                $bannerimage = $siteurl . $imagePath . $banner;
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
                    $fees = array_filter($fees, fn($f) => $f > 0);
                    if (!empty($fees)) {
                        $minFee = min($fees);
                        $maxFee = max($fees);
                        $price = $minFee === $maxFee ? '₦' . number_format($minFee) : '₦' . number_format($minFee) . ' - ₦' . number_format($maxFee);
                    } else {
                        $price = 'Paid';
                    }
                }
              ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
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
                    <h3><a href="group?slug=<?php echo $slug; ?>"><?php echo $title; ?></a></h3>
                    <p><?php echo $content; ?>...</p>
                    <div class="instructor">
                      <img src="<?php echo $photo; ?>" alt="Instructor" class="instructor-img">
                      <div class="instructor-info">
                        <h6><?php echo $author; ?></h6>
                      </div>
                    </div>
                    <a href="group?slug=<?php echo $slug; ?>" class="btn-course">Join Group</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center">No groups found for this filter.</p>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination-wrapper" data-aos="fade-up" data-aos-delay="300">
        <nav aria-label="Groups pagination">
          <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php
                $params = $_GET;
                $params['page'] = max(1, $page - 1);
                echo http_build_query($params);
              ?>"><i class="bi bi-chevron-left"></i></a>
            </li>

            <!-- Pages -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php
                  $params = $_GET;
                  $params['page'] = $i;
                  echo http_build_query($params);
                ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php
                $params = $_GET;
                $params['page'] = min($totalPages, $page + 1);
                echo http_build_query($params);
              ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>




</main>

<?php include "footer.php"; ?>
