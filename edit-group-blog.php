

<?php 
$requireLogin = true;
include "header.php"; 
?>

<div class="container py-5 mb-5">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Blog Post</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Blog</div>
          </div>
          <div class="card-body">

          <?php
if (isset($_GET['blog_id'])) {
    $blogId = $_GET['blog_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editblog&blog_id=" . $blogId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 

            // Extract details
            $title = $blogdetail->title ?? '';
            $article = $blogdetail->article ?? '';
            $tags = $blogdetail->tags ?? '';
            $featured_image = $blogdetail->featured_image ?? '';
            $group_id = $blogdetail->group_id ?? '';
            $status = $blogdetail->status ?? '';
            // Convert the blog’s stored category/subcategory values into arrays
            $categories_selected = !empty($blogdetail->categories) ? explode(',', $blogdetail->categories) : [];
            $subcategories_selected = !empty($blogdetail->subcategories) ? explode(',', $blogdetail->subcategories) : [];
            $status = $blogdetail->status ?? '';
            $created_at = $blogdetail->created_at ?? '';
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));
            $group_id = $blogdetail->group_id ?? '';
              $blogimage = $siteurl . $imagePath . $featured_image;
              
        } else {
            echo "<div class='alert alert-warning'>No blog found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching blog data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

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
            $userAuth   = $_COOKIE['user_auth']   ?? '';

            $canAccess = false;

            // ✅ If current user is group creator → allow access
                if (
            ($adminAuth) || 
            ($vendorAuth == $groupCreatorId) || 
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

                $activeLog = 1;
                $canAccess = true;
            } else {
                // ✅ Otherwise, check if user is a group member
                $activeUserId = $adminAuth ?: ($vendorAuth ?: $userAuth);
                if (!empty($activeUserId) && !empty($group_id)) {
                    $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$group_id}&user_id={$activeUserId}";
                    $memberData = curl_get_contents($checkMemberUrl);

                    if ($memberData !== false) {
                        $memberResult = json_decode($memberData, true);
                        if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active' && ($memberResult[0]['role'] == 'admin' || $memberResult[0]['role'] == 'subadmin')) {
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
                            window.location.href = '{$siteurl}group?slug={$groupSlug}';
                        } else {
                            window.location.href = '{$siteurl}';
                        }
                    });
                </script>";
                exit;
            }

?>

 <form  method="POST" id="editForum" enctype="multipart/form-data">
     <div class="col-lg-12 text-center mt-1" id="messages"></div> 
            <!-- Blog Title -->
            <div class="form-group mb-2">
              <label for="blogTitle">Title</label>
              <input type="text" class="form-control" id="blogTitle" name="blogTitle" placeholder="Enter blog title" value="<?php echo $title; ?>" >
            </div>

            <!-- Cover Image -->
            <div class="form-group mb-2">
              <label for="blogCover">Cover Image</label><img src="<?php echo $blogimage; ?>" class="small-image">
              <input type="file" class="form-control" id="blogCover" name="blogCover" accept="image/*">
            </div>

            <!-- Content -->
            <div class="form-group mb-2">
              <label for="blogContent">Content</label>
              <textarea id="blogContent" name="blogContent" class="editor"  placeholder="Write your blog content here..."><?php echo $article; ?></textarea>
            </div>

            <!-- Categories -->
            <!-- Categories -->
      <div class="form-group mb-2">
        <label for="category">Categories</label>
     <select name="category[]" id="category" class="form-select select-multiple" required multiple>
  <option value="">-- Select Category --</option>
  <?php
  $url = $siteurl . "script/register.php?action=categorieslists";
  $data = curl_get_contents($url);

  if ($data !== false) {
      $categories = json_decode($data);
      if (!empty($categories)) {
          foreach ($categories as $category) {
              $categoryId = $category->id;
              $categoryName = $category->category_name;
              $selected = in_array($categoryId, $categories_selected) ? 'selected' : '';
              echo "<option value='{$categoryId}' {$selected}>{$categoryName}</option>";
          }
      }
  } else {
      echo "<option value=''>Error loading categories</option>";
  }
  ?>
</select>


      </div>


      <!-- Sub-Categories -->
      <div class="form-group mb-2">
        <label for="subcategory">Sub-Categories</label>
       <select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>
  <option value="">-- Select Sub-Category --</option>
  <?php
  if (!empty($categories_selected)) {
      $url = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=" . implode(',', $categories_selected);
      $data = curl_get_contents($url);
      if ($data !== false) {
          $subcategories = json_decode($data);
          if (!empty($subcategories)) {
              foreach ($subcategories as $subcat) {
                  $subcatId = $subcat->id;
                  $subcatName = $subcat->category_name;
                  $selected = in_array($subcatId, $subcategories_selected) ? 'selected' : '';
                  echo "<option value='{$subcatId}' {$selected}>{$subcatName}</option>";
              }
          }
      }
  }
  ?>
</select>

      </div>

       <input type="hidden" name="blog_id" value="<?php echo $blogId; ?>">
    <input type="hidden" name="action" value="updateblog">
            <!-- Tags -->
            <div class="form-group mb-2">
              <label for="blogTags">Tags</label>
              <input type="text" id="blogTags" name="blogTags" class="form-control" placeholder="Add tags (comma separated)" value="<?php echo $tags; ?>">
            </div>      
            
            <!-- Status -->
             <div class="form-group mb-2">
              <label for="status">Status</label>
            <select name="status" class="form-control" required>
            <option value="">Select Status</option>
            <option value="active" <?php echo ($status === 'active') ? 'selected' : ''; ?>>Published</option>
            <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
          </select>
            </div>
            <!-- Submit Button -->
            <button class="btn btn-primary"  id="submitBtn">Submit Blog</button>
</form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>