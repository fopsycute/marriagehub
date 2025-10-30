<?php 

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
            $group_name  = $groupdetail->group_name ?? '';
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

<div class="container">
  <div class="page-inner">

    <div class="row">
      <div class="col-md-10 mx-auto mb-5">

        <div class="card mt-4">
          <div class="card-header">
            <div class="card-title">Blog-<?php echo $group_name; ?></div>
          </div>
          <div class="card-body">
    <form  method="POST" id="addgroupblog" enctype="multipart/form-data">    
       <div class="col-lg-12 text-center mt-1" id="messages"></div> 
            <!-- Blog Title -->
            <div class="form-group">
              <label for="blogTitle">Title</label>
              <input type="text" class="form-control" id="blogTitle" placeholder="Enter blog title" name="title"> 
            </div>

            <!-- Cover Image -->
            <div class="form-group mb-2">
              <label for="blogCover">Cover Image</label>
              <input type="file" class="form-control" id="blogCover" name="featured_image" accept="image/*">
            </div>

            <!-- Content -->
            <div class="form-group mb-2">
              <label for="blogContent">Content</label>
              <textarea id="blogContent" class="editor" name="article" placeholder="Write your blog content here..."></textarea>
            </div>
            <input type="hidden" name="action" id="action" value="addgroupforum">
            <!-- Categories -->
             <div class="row mb-2">
              <div class="col-lg-6">
            <div class="form-group">
              <label for="blogCategories">Categories</label>
              <select name="category[]" id="category" class="form-control select-multiple" required multiple>
              <option value="">-- Select Category --</option>
              <?php
           $url = $siteurl . "script/register.php?action=categorieslists";
              $data = curl_get_contents($url); // using your helper from header
            if ($data !== false) {
                $categories = json_decode($data);
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                      foreach ($categories as $category) {
                          $categoryId = $category->id;
                          $name = $category->category_name; // adjust if DB column is different
                          echo "<option value='{$categoryId}'>{$name}</option>";
                      }
                  }
              }
            }

            else {
                   echo "Error fetching data: " . curl_error($ch);
                      }
              ?>
          </select>
            </div>
              </div>

              <div class="col-lg-6">

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="blogSubcategories">Sub-Categories</label>
              <select name="subcategory[]" id="subcategory" class="form-control select-multiple" required multiple>
              <option value="">-- Select Sub-Category --</option>
                            
          </select>
            </div>
                    </div>
                    </div>

            <!-- Tags -->
            <div class="form-group mb-2">
              <label for="blogTags">Tags</label>
              <input type="text"name="tags" id="tags" class="form-control" placeholder="Add tags (comma separated)">
            </div>


         
            <div class="form-group mb-2">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>

      
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
              <input type="hidden" name="user" value="<?php echo $buyerId; ?>">
              <div class="form-group">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary"  id="submitBtn">Submit Blog</button>
             </div>
          </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>