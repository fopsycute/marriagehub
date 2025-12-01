
<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Settings</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Settings</div>
          </div>
          <div class="card-body">

            <!-- Site Name -->
                <form class="vendorenrollment-form"  method="POST" enctype="multipart/form-data">
              <div id="messages"></div>

              <!-- Section 1 -->
              <h5 class="">Section 1: Personal and Business Information</h5>
              <h6 class="mb-4">Please provide your personal and business details.</h6>

              <!-- Personal Details -->
              <div class="row mb-3">
                <div class="col-md-3">
                  <label class="form-label">Title *</label>
                 <select name="title" class="form-select" required>
        <?php
        $titles = ["Mr","Mrs","Miss","Ms","Mx","Sir","Dr","Lady","Lord","Professor","Esq.","Hon.","Messrs.","Msgr.","Prof.","Rev.","Rt. Hon.","Other"];
        foreach ($titles as $t) {
          $selected = ($title) === $t ? 'selected' : '';
          echo "<option value='$t' $selected>$t</option>";
        }
        ?>
      </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" name="first_name" class="form-control" value="<?php echo $buyerfirstName; ?>" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" value="<?php echo $buyerlastName; ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" name="last_name" class="form-control"  value="<?php echo $buyermiddleName; ?>" required>
                </div>
              </div>

             <div class="row mb-3">
    <div class="col-md-12">
      <label class="form-label">Photo</label>
      <?php if (!empty($logo)): ?>
        <div class="mb-2"><img src="<?= $logo ?>" width="100" class="rounded"></div>
      <?php endif; ?>
      <input type="file" name="photo" class="form-control" accept="image/*">
    </div>
        <div class="col-md-6">
      <label class="form-label">Date of Birth</label>
      <input type="date" name="dob" class="form-control" value="<?= $dob ?>">
    </div>
    
    <div class="col-md-6">
      <label class="form-label">Gender</label>
      <select name="gender" class="form-select">
        <option value="">Select</option>
        <option value="Male" <?= ($gender ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($gender ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
      </select>
    </div>
  </div>

    <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Nationality</label>
      <input type="text" name="nationality" class="form-control" value="<?= $nationality ?? '' ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Languages Spoken</label>
      <input type="text" name="languages" class="form-control" value="<?= $languages ?? '' ?>">
    </div>
  </div>

    <!-- Business Details -->
    <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Business Name</label>
      <input type="text" name="business_name" class="form-control" value="<?= $business_name ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Registered Business Name</label>
      <input type="text" name="registered_business_name" class="form-control" value="<?= $registered_business_name ?>">
    </div>
  </div>
  <input type="hidden" name="user_id" value="<?= $buyerId; ?>">
  <input type="hidden" name="action" value="edit_vendorsettings">

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Business Owner’s Full Name</label>
      <input type="text" name="owner_name" class="form-control" value="<?= $owner_name ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Business Logo</label>
      <?php if (!empty($business_logo)): ?>
        <div class="mb-2"><img src="<?= $business_logo ?>" width="100" class="rounded"></div>
      <?php endif; ?>
      <input type="file" name="business_logo" class="form-control">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Portfolio (Photos & Videos)</label>
    <?php if (!empty($portfolio)): ?>
      <div class="mb-2">
        <?php foreach (explode(',', $portfolio) as $file): ?>
          <a href="<?= $file ?>" target="_blank" class="badge bg-info text-dark me-1">View</a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <input type="file" name="portfolio[]" class="form-control" multiple>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Phone Numbers</label>
      <input type="text" name="phone" class="form-control" value="<?= $phone ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Website</label>
      <input type="url" name="website" class="form-control" value="<?= $website ?>">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" value="<?= $buyerEmail ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">State of Residence</label>
      <input type="text" name="state_residence" class="form-control" value="<?= $state_residence ?>">
    </div>
  </div>

   <div class="mb-3">
    <label class="form-label">Practice Full Address</label>
  <textarea name="address" class="editor"><?= $address ?? ''?></textarea>
  </div>

  <div class="row mb-3">
    <div class="col-md-3"><input type="url" name="facebook" class="form-control" placeholder="Facebook" value="<?= $facebook ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="twitter" class="form-control" placeholder="Twitter" value="<?= $twitter ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="instagram" class="form-control" placeholder="Instagram" value="<?= $instagram ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="linkedin" class="form-control" placeholder="LinkedIn" value="<?= $linkedin ?? '' ?>"></div>
  </div>

          <input type="hidden" name="action" value="register_vendor">
              <!-- Section 2 -->
         <h5 class="mt-4">Section 2: Vendor Category</h5>
              <h6 class="mb-4">Please provide your vendor category information.</h6>


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

               <div class="mb-3">
    <label class="form-label">Describe Your Products/Services</label>
    <textarea name="services" class="editor"><?= $services ?? '' ?></textarea>
  </div>
      <div class="row mt-3">
       <div class="col-md-4 form-group p_star mb-3">
         <input type="text" class="form-control" name="bank_name" placeholder="Bank Name" value="<?php echo htmlspecialchars($bank_name); ?>">
           </div>
            <div class="col-md-4 form-group p_star mb-3">
             <input type="text" class="form-control" name="bank_accname" placeholder="Bank Account Name" value="<?php echo htmlspecialchars($bank_accname); ?>">
              </div>
              <div class="col-md-4 form-group p_star mb-3">
              <input type="text" class="form-control" name="bank_number" placeholder="Bank Account Number" value="<?php echo htmlspecialchars($bank_number); ?>">
              </div>
      </div>

  <div class="mb-3">
    <label class="form-label">Years of Experience</label>
  <input type="number" name="experience_years" class="form-control" value="<?= $experience_years ?? '' ?>">
  </div>

  <!-- SECTION 3: Business Operations -->
  <h5 class="mt-4">Section 3: Business Operations</h5>
  <h6 class="mb-4">Provide details about your operations and availability.</h6>

  <div class="mb-3">
    <label class="form-label">Service Coverage</label><br>
    <?php
    $coverage_options = ["Local","Statewide","Nationwide","International"];
    $vendor_coverage = explode(',', $coverage ?? '');
    foreach ($coverage_options as $cov) {
      $checked = in_array($cov, $vendor_coverage) ? 'checked' : '';
      echo "<div class='form-check form-check-inline'>
              <input class='form-check-input' type='checkbox' name='coverage[]' value='$cov' $checked> $cov
            </div>";
    }
    ?>
  </div>

  <div class="mb-3">
    <label class="form-label">Do You Offer On-site Services?</label>
    <select name="onsite" class="form-select">
      <option value="">Select</option>
      <option value="Yes" <?= ($onsite ?? '') === 'Yes' ? 'selected' : '' ?>>Yes</option>
      <option value="No" <?= ($onsite ?? '') === 'No' ? 'selected' : '' ?>>No</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Availability (Days & Times)</label>
    <input type="text" name="availability" class="form-control" value="<?= $availability ?? '' ?>">
  </div>


  <div class="text-center">
    <button type="submit" id="submitBtn" class="btn btn-success">
      <i class="bi bi-check-circle me-2"></i> Update Vendor
    </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>
