



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

                elseif ($therapistAuth == $groupCreatorId) {
                    $buyerId = $therapistAuth;
                }
                // ✅ CASE 3: Regular user — if the user is the group creator
                elseif ($userAuth == $groupCreatorId) {
                    $buyerId = $userAuth;
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



<section>
          <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Create Questions</h2>
          <p></p>
        </div>
      </div><!-- End Section Title -->

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-body">
      <form method="POST" id="addQuestions" enctype="multipart/form-data">
        <div class="row">
          <div class="col-lg-12 text-center mt-1" id="messages"></div>

          <!-- Question Heading -->
          <div class="col-12 mb-4">
            <h4 class="fw-bold text-primary">What is your question?</h4>
          </div>

          <!-- Featured Image -->

          <!-- Title -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="title">Title:</label>
            <input 
              placeholder="Enter the title of your question" 
              id="title" 
              type="text" 
              class="form-control" 
              name="title" value=""
              required
            >
          </div>

          <!-- Body of Question -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="article">Body of Question:</label>
            <textarea 
              id="article" 
              name="article" 
              class="editor" 
              placeholder="Describe your question in detail..." 
            ></textarea>
          </div>

          <input type="hidden" name="user" value="<?php echo $buyerId; ?>" >
          <!-- Categories and Sub-Categories -->
          <div class="row mb-3">
            <!-- Category -->
            <div class="col-md-6">
              <label class="form-label">Categories:</label>
              <select 
                name="category[]" 
                id="category" 
                class="form-select select-multiple" 
                required 
                multiple
              >
                <option value="">-- Select Category --</option>
                <?php
                  $url = $siteurl . "script/register.php?action=categorieslists";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                    $categories = json_decode($data);
                    if (!empty($categories)) {
                      foreach ($categories as $category) {
                        $categoryId = $category->id;
                        $name = htmlspecialchars($category->category_name);
                        echo "<option value='{$categoryId}'>{$name}</option>";
                      }
                    }
                  } else {
                    echo "<option disabled>Error fetching categories</option>";
                  }
                ?>
              </select>
            </div>
           
               
         
            <!-- Sub-Category -->
            <div class="col-md-6">
              <label class="form-label">Sub-Categories:</label>
              <select 
                name="subcategory[]" 
                id="subcategory" 
                class="form-select select-multiple" 
                required 
                multiple
              >
                <option value="">-- Select Sub-Category --</option>
              </select>
            </div>
          </div>

                    <input type="hidden" name="action" value="createQuestion">
                  
          <!-- Tags -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="tags">Tags:</label>
              <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" class="form-control">
            <input 
              placeholder="Enter tags separated by commas (e.g. PHP, JavaScript, HTML)" 
              id="tags" 
              type="text" 
              class="form-control" 
              name="tags"
            >
          </div>
          <!---
            <div class="form-group mb-2">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>

            --->
          <!-- Anonymous Option -->
            <div class="col-sm-12 mb-3">
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="anonymous" 
                  name="anonymous" 
                  value="1"
                >
                <label class="form-check-label" for="anonymous">
                  Post Anonymously (Hide my name publicly)
                </label>
              </div>
            </div>

          <!-- Submit Button -->
          <div class="col-sm-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5" >Submit Question</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
</section>
<?php include "footer.php"; ?>