
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
		<form class="enrollment-tribes" id="admincreateGroupForm" method="POST" enctype="multipart/form-data">
            <!-- Group/Tribe Name -->
                 <!-- Group/Tribe Name -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group/Tribe Name:</label>
                <input type="text" name="group_name" class="form-control" required>
              </div>
            </div>

            <!-- Group Description -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group Description:</label>
                <textarea name="group_description" class="editor" rows="4"></textarea>
              </div>
            </div>

            <!-- Group Type -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Group Type:</label>
                <select name="group_type" class="form-select" required>
                  <option value="">Select Type</option>
                  <option value="open">Open (Anyone can join)</option>
                  <option value="closed">Closed (Join only by approval/invitation)</option>
                </select>
              </div>

              <!-- Group Access -->
              <div class="col-md-6">
                <label class="form-label">Group Access:</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="group_access" id="access_free" value="free" required>
                  <label class="form-check-label" for="access_free">Free</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="group_access" id="access_paid" value="paid" required>
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
              
            </div>

            <!-- Categories & Sub-Categories -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Category</label>
                  <select name="category[]" id="category" class="form-select select-multiple" required multiple>
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
              <div class="col-md-6">
                <label class="form-label">Sub-Category</label>
                <select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>
                <option value="">-- Select Sub-Category --</option>
                                
                </select>
                </div>
            </div>
            <input type="hidden" name="user" value="<?php echo $buyerId; ?>" >
            <input type="hidden" name="action" value="create_admingroup">
            <!-- Group Rules -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group Rules & Guidelines (Optional)</label>
                <textarea name="group_rules" class="editor" rows="4" placeholder="Basic rules or expectations for members"></textarea>
              </div>
            </div>

            <!-- Upload Group Banner -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Upload Group Banner / Image</label>
                <input type="file" name="group_banner" class="form-control" accept="image/png, image/jpeg">
                <small class="text-muted">Recommended size: 1200 x 600px (JPG/PNG)</small>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>

               <option value="" disabled selected>Select</option>
                <option value="pending">Pending</option>
                <option value="active">Approved</option>
                    </select>
              </div>
            </div>


            <!-- Submit Button -->
            <button class="btn btn-primary mt-3" type="submit" id="submitBtn" >Create Group</button>
			</form>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

<?php include "footer.php"; ?>