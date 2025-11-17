
<?php 
$requireLogin = true;
include "header.php";

?>

<main class="main">

  <section id="enroll" class="enroll section">
     <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
        <h2>Create Tribes & Groups</h2>
        <div>
          <a href="<?php echo $siteurl; ?>my-group.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to My Groups
          </a>
      </div>
      </div>
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="enrollment-form-wrapper">

          <form class="enrollment-tribes" id="createGroupForm" method="POST" enctype="multipart/form-data">

            <div class="col-lg-12 text-center mt-1" id="messages"></div> 

            <h4 class="mb-4">Group Details</h4>

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
                <textarea name="group_description" class="editor"></textarea>
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
            <input type="hidden" name="action" value="create_group">
            <!-- Group Rules -->
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Group Rules & Guidelines (Optional)</label>
                <textarea name="group_rules" class="editor" placeholder="Basic rules or expectations for members"></textarea>
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


                   <div class="row mb-4">
                  <div class="col-12">
                 
                    <label class="form-label">Agreement & Acknowledgement</label>
                      <div class="agreement-section">
                         <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="agree_commission" name="agree_commission" required>
                      <label class="form-check-label" for="agree_commission">
                        I acknowledge that if I operate a <strong>paid group</strong>, <strong>Marriage.ng</strong> will deduct a 
                        <strong>20% commission</strong> from each successful subscription.
                      </label>
                    </div>

                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="agree_guidelines" name="agree_guidelines" required>
                      <label class="form-check-label" for="agree_guidelines">
                        I agree to follow the <strong>Marriage.ng Community Guidelines</strong> and to moderate my group 
                        with respect, fairness, and responsibility.
                      </label>
                    </div>

             

                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                      <label class="form-check-label" for="agree_terms">
                        I understand that <strong>Marriage.ng</strong> reserves the right to remove content or disable any group 
                        that violates its <strong>Terms and Conditions</strong>.
                      </label>
                    </div>
               
                </div>

            <!-- Submit -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary" id="createGroupBtn">
                <i class="bi bi-check-circle me-2"></i> SUBMIT AND CREATE
              </button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
</main>

<?php include "footer.php"; ?>