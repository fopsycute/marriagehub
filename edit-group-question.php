
<?php
$requireLogin = true;
include "header.php"; 
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
        <?php

if (isset($_GET['question_id'])) {
    $questionId = $_GET['question_id'];

    // Build API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "user.php?action=editquest&question_id=" . $questionId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0];

            // Extract and sanitize details
            $id = $blogdetail->id ?? '';
            $user_id = $blogdetail->user_id ?? '';
            $group_id = $blogdetail->group_id ?? '';
            $title = htmlspecialchars($blogdetail->title ?? '', ENT_QUOTES);
            $slug = htmlspecialchars($blogdetail->slug ?? '', ENT_QUOTES);
            $article = $blogdetail->article ?? '';
             $categories_selected = !empty($blogdetail->categories) ? explode(',', $blogdetail->categories) : [];
            $subcategories_selected = !empty($blogdetail->subcategories) ? explode(',', $blogdetail->subcategories) : [];
            $tags = $blogdetail->tags ?? '';
            $status = $blogdetail->status ?? '';
            $anonymous = $blogdetail->anonymous ?? '';
            $views = $blogdetail->views ?? 0;

            $created_at = $blogdetail->created_at ?? '';
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));
            $featured_image = $blogdetail->featured_image ?? '';

            // Handle image path
            $blogimage = !empty($featured_image)
                ? $siteurl . $imagePath . $featured_image
                : $siteurl . "assets/img/default-blog.jpg";

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
  $canManageStatus = false;
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
            $therapistAuth = $_COOKIE['therapist_auth'] ?? '';
            $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
            $userAuth   = $_COOKIE['user_auth']   ?? '';

            $canAccess = false;

            // ✅ If current user is group creator → allow access
                if (
            ($adminAuth) || 
            ($vendorAuth == $groupCreatorId) ||  ($therapistAuth == $groupCreatorId) || 
            ($userAuth == $groupCreatorId)
            ) {
                // ✅ CASE 1: Admin — always has access
                if ($adminAuth) {
                    $buyerId = $adminAuth;
                    $canManageStatus = true;
                }
                // ✅ CASE 2: Vendor — if the vendor is the group creator
                elseif ($vendorAuth == $groupCreatorId) {
                    $buyerId = $vendorAuth;
                    $canManageStatus = true;
                }

                elseif ($therapistAuth == $groupCreatorId) {
                    $buyerId = $therapistAuth;
                    $canManageStatus = true;
                }
                // ✅ CASE 3: Regular user — if the user is the group creator
                elseif ($userAuth == $groupCreatorId) {
                    $buyerId = $userAuth;
                    $canManageStatus = true;
                }

                $activeLog = 1;
                $canAccess = true;
            } else {
                // Otherwise, check if user is a group member
                 $activeUserId = $adminAuth ?: ($vendorAuth ?: ($userAuth ?: $therapistAuth));
                if (!empty($activeUserId) && !empty($group_id)) {
                    $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$group_id}&user_id={$activeUserId}";
                    $memberData = curl_get_contents($checkMemberUrl);

                    if ($memberData !== false) {
                        $memberResult = json_decode($memberData, true);
                        if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active' && ($memberResult[0]['role'] == 'admin' || $memberResult[0]['role'] == 'subadmin')) {
                            $canAccess = true;
                             $userRole = $memberResult[0]['role'];
                    if ($userRole == "admin" || $userRole == "subadmin") {
                $canManageStatus = true;
                  }   
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

    <form method="POST" id="editgroupQuestions" enctype="multipart/form-data">
  <div class="row">
    <div class="col-lg-12 text-center mt-1" id="messages"></div>

    <!-- Question Heading -->
    <div class="col-12 mb-4">
      <h4 class="fw-bold text-primary">Edit Your Question</h4>
    </div>

    <!-- Title -->
    <div class="col-sm-12 mb-3">
      <label class="form-label" for="title">Title:</label>
      <input 
        placeholder="Enter the title of your question" 
        id="title" 
        type="text" 
        class="form-control" 
        name="title" 
        value="<?php echo $title; ?>" 
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
      ><?php echo $article; ?></textarea>
    </div>

     <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
    <input type="hidden" name="user" value="<?php echo $buyerId; ?>">
    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">
    <input type="hidden" name="action" value="update_group_question">

    <!-- Categories and Sub-Categories -->
    <div class="row mb-3">
      <!-- Category -->
      <div class="col-md-6">
          <div class="form-group">
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
      </div>

      <!-- Sub-Category -->
      <div class="col-md-6">
         <div class="form-group">
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
    </div>
        </div>
    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">
    <!-- Tags -->
    <div class="col-sm-12 mb-3">
      <label class="form-label" for="tags">Tags:</label>
      <input 
        placeholder="Enter tags separated by commas (e.g. PHP, JavaScript, HTML)" 
        id="tags" 
        type="text" 
        class="form-control" 
        name="tags" 
        value="<?php echo htmlspecialchars($tags ?? ''); ?>"
      >
    </div>


         <!-- Status -->
            <?php if ($canManageStatus): ?>
             <div class="form-group mb-2">
              <label for="status">Status</label>
            <select name="status" class="form-control" id="statusSelects"  required>
            <option value="">Select Status</option>
            <option value="active" <?php echo ($status === 'active') ? 'selected' : ''; ?>>Published</option>
            <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="rejected" <?php echo ($status === 'rejected') ? 'selected' : ''; ?>>Disapprove</option>
          </select>

            <div class="mb-3" id="rejectReasonBox" style="display: none;">
        <label class="form-label">Reason for Rejection</label>
        <textarea name="reject_reason" class="editor"><?= $reject_reason ?? '' ?></textarea>
      </div>

            </div>
             <?php else: ?>
          <!-- Regular members → always pending -->
          <input type="hidden" name="status" value="<?php echo $status; ?>">
      <?php endif; ?>

    <!-- Anonymous Option -->
    <div class="col-sm-12 mb-3">
      <div class="form-check">
        <input 
          class="form-check-input" 
          type="checkbox" 
          id="anonymous" 
          name="anonymous" 
          value="1" 
          <?php if (!empty($anonymous) && $anonymous == '1') echo 'checked'; ?>
        >
        <label class="form-check-label" for="anonymous">
          Post Anonymously (Hide my name publicly)
        </label>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="col-sm-12 text-center mt-4">
      <button type="submit" class="btn btn-primary px-5">Update Question</button>
    </div>

  </div>
</form>
    </div>
  </div>
</div>
</section>
<?php include "footer.php"; ?>