


<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Group / Tribe</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Group Details</div>
          </div>
          <div class="card-body">
             <?php
if (isset($_GET['group_id'])) {
    $groupId = $_GET['group_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editgroup&group_id=" . $groupId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 

            // Extract details
            $group_name = $groupdetail->group_name ?? '';
            $group_description = $groupdetail->group_description ?? '';
            $status = $groupdetail->status ?? '';
            $group_type = $groupdetail->group_type ?? '';
            $group_access = $groupdetail->group_access ?? '';
            $fee_1m = $groupdetail->fee_1m ?? '';
            $fee_3m = $groupdetail->fee_3m ?? '';
            $fee_6m = $groupdetail->fee_6m ?? '';
            $fee_12m = $groupdetail->fee_12m ?? '';
            $group_rules = $groupdetail->group_rules ?? '';
            $user_id = $groupdetail->user_id ?? '';
            $agree_commission = $groupdetail->agree_commission ?? '';
            $agree_guidelines = $groupdetail->agree_guidelines ?? '';
            $agree_terms = $groupdetail->agree_terms ?? '';
            // Convert the blog’s stored category/subcategory values into arrays
            $categories_selected = !empty($groupdetail->category) ? explode(',', $groupdetail->category) : [];
            $subcategories_selected = !empty($groupdetail->subcategory) ? explode(',', $groupdetail->subcategory) : [];
            $created_at = $groupdetail->created_at ?? '';
            $banner = $groupdetail->banner ?? '';
              $bannerimage = $siteurl . $imagePath . $banner;
        } else {
            echo "<div class='alert alert-warning'>No group found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching group data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

?>
		<form class="enrollment-edittribes" id="admineditGroupForm" method="POST" enctype="multipart/form-data">
                 <!-- Group/Tribe Name -->
            <div class="row mb-3">
            <div class="col-lg-12 text-center mt-1" id="messages"></div> 
              <div class="col-12">
                <label class="form-label">Group/Tribe Name:</label>
                <input type="text" name="group_name" class="form-control" value="<?php echo $group_name; ?>"  required>
              </div>
            </div>

            <!-- Group Description -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group Description:</label>
                <textarea name="group_description" class="editor"><?php echo $group_description; ?></textarea>
              </div>
            </div>

            <!-- Group Type -->
            <div class="row mb-3">
  <!-- Group Type -->
  <div class="col-md-6">
    <label class="form-label">Group Type:</label>
    <select name="group_type" class="form-select" required>
      <option value="">Select Type</option>
      <option value="open"   <?= ($group_type === 'open')   ? 'selected' : '' ?>>Open (Anyone can join)</option>
      <option value="closed" <?= ($group_type === 'closed') ? 'selected' : '' ?>>Closed (Join only by approval/invitation)</option>
    </select>
  </div>

  <!-- Group Access -->
        <div class="col-md-6">
            <label class="form-label">Group Access:</label><br>

            <div class="form-check form-check-inline">
            <input 
                class="form-check-input" 
                type="radio" 
                name="group_access" 
                id="access_free" 
                value="free"
                <?= ($group_access === 'free') ? 'checked' : '' ?> 
                required
            >
            <label class="form-check-label" for="access_free">Free</label>
            </div>
<input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
            <div class="form-check form-check-inline">
            <input 
                class="form-check-input" 
                type="radio" 
                name="group_access" 
                id="access_paid" 
                value="paid"
                <?= ($group_access === 'paid') ? 'checked' : '' ?> 
                required
            >
            <label class="form-check-label" for="access_paid">Paid Subscription</label>
            </div>
        </div>
        </div>

            <!-- Paid Subscription Details -->
            <div class="row mb-3" id="paid-subscription-fields">
              <div class="col-md-6">
                <label class="form-label">1 Month - Subscription Fee (₦):</label>
                <input type="number" name="fee_1m" class="form-control" min="0" step="100">
              </div>
              <div class="col-md-6">
                <label class="form-label">3 Months - Subscription Fee (₦):</label>
                <input type="number" name="fee_3m" class="form-control" min="0" step="100">
              </div>
              <div class="col-md-6 mt-3">
                <label class="form-label">6 Months - Subscription Fee (₦):</label>
                <input type="number" name="fee_6m" class="form-control" min="0" step="100">
              </div>
              <div class="col-md-6 mt-3">
                <label class="form-label">12 Months - Subscription Fee (₦):</label>
                <input type="number" name="fee_12m" class="form-control" min="0" step="100">
              </div>
              <div class="col-12 mt-2">
                <small class="text-muted">Note: MARRIAGE.NG takes a twenty percent (20%) commission per payment.</small>
              </div>
            </div>

            <!-- Categories & Sub-Categories -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Category</label>
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
              <div class="col-md-6">
                <label class="form-label">Sub-Category</label>
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
            <input type="hidden" name="user" value="<?php echo $user_id; ?>" >
            <input type="hidden" name="action" value="edit_admingroup">
            <!-- Group Rules -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group Rules & Guidelines (Optional)</label>
                <textarea name="group_rules" class="editor"  placeholder="Basic rules or expectations for members"><?php echo $group_rules; ?></textarea>
              </div>
            </div>

            <!-- Upload Group Banner -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Upload Group Banner / Image</label><img src="<?php echo $bannerimage; ?>" class="small-image">
                <input type="file" name="group_banner" class="form-control" accept="image/png, image/jpeg">
                <small class="text-muted">Recommended size: 1200 x 600px (JPG/PNG)</small>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                <option value="" disabled <?= empty($status) ? 'selected' : '' ?>>Select</option>
                <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="active"  <?= ($status === 'active') ? 'selected' : '' ?>>Approved</option>
                </select>
              </div>
            </div>


            <!-- Submit Button -->
            <button class="btn btn-primary mt-3" type="submit" id="submitBtn" >Edit Group</button>
			</form>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

<?php include "footer.php"; ?>