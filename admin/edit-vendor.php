<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Vendors</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Vendors</a></li>
      </ul>
    </div>

      <div class="row">
      <div class="col-md-12">


      <?php
if (isset($_GET['vendor_id'])) {
    $userId = $_GET['vendor_id'];
  $categories_selected = [];
  $subcategories_selected = [];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editvendor&vendor_id=" . $userId;

    // Fetch user details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $userdetails = json_decode($data);
        if (!empty($userdetails)) {
             $user = $userdetails[0]; 
            // Assign data to variables
            $first_name = $user->first_name ?? '';
            $middle_name = $user->middle_name ?? '';
            $last_name = $user->last_name ?? '';
            $title = $user->title ?? '';
            $status = $user->status ?? '';
            $lga = $user->lga ?? '';
            $dob = $user->dob ?? '';
            $gender = $user->gender ?? '';
             $services = $user->services ?? '';
            $business_name = $user->business_name;
            $registered_business_name = $user->registered_business_name;
            $owner_name = $user->owner_name;
            $nationality = $user->nationality ?? '';
            $languages = $user->languages ?? '';
            $phone = $user->phone ?? '';
            $website = $user->website ?? '';
            $email = $user->email ?? '';
            $categories_selected = !empty($user->category_id) ? explode(',', $user->category_id) : [];
            $subcategories_selected = !empty($user->subcategory_id) ? explode(',', $user->subcategory_id) : [];
            $state_residence = $user->state_residence ?? '';
            $lga = $user->lga ?? '';
            $experience_years = $user->experience_years;
            $business_logo = $siteurl.$imagePath.$user->business_logo;
            $portfolio = $siteurl.$imagePath.$user->portfolio;
            $coverage = $user->coverage;
            $onsite = $user->onsite;
            $availability = $user->availability;
          $preferred_days_selected = [];
          $start_time = '';
          $end_time = '';

          if (!empty($availability)) {
              // Split the days from times
              $parts = explode('|', $availability);
              if (count($parts) == 2) {
                  // Days
                  $preferred_days_selected = array_map('trim', explode(',', $parts[0]));
                  // Times
                  $times = trim($parts[1]);
                  if (strpos($times, '-') !== false) {
                      list($start_time, $end_time) = array_map('trim', explode('-', $times));
                  }
              }
          }

            $address = $user->address ?? '';
            $facebook = $user->facebook ?? '';
            $twitter = $user->twitter ?? '';
            $instagram = $user->instagram ?? '';
            $linkedin = $user->linkedin ?? '';
            $bio = $user->bio ?? '';
            $photo = $user->photo ?? '';
            $suspend_reason = $user->suspend_reason ?? '';
            $logo = !empty($photo) ? $siteurl . "uploads/" . $photo : $siteurl . "images/default-avatar.png";
        } else {
            echo "<div class='alert alert-warning'>No user found with this ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch user data. Try again later.</div>";
    }
} else {
    header("Location: all-vendor.php");
    exit;
}
?>
        <!-- Section 1: Personal Details -->
    <div class="card mb-4">
      <div class="card-header">
            <!-- Section 1: Personal Details -->
    <h5 class="mb-3">Personal Details</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
   <!-- Site Name -->
                <form class="updateenrollment-form"  method="POST" enctype="multipart/form-data">
              <div id="messages" style="display:none;"></div>

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
                  <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" value="<?php echo $middle_name; ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" name="last_name" class="form-control"  value="<?php echo $last_name; ?>" required>
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
    <div class="col-md-"4>
      <label class="form-label">Phone Numbers</label>
      <input type="number" name="phone" class="form-control" value="<?= $phone ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Website</label>
      <input type="url" name="website" class="form-control" value="<?= $website ?>">
    </div>

        <div class="col-md-4">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" value="<?= $email ?>">
    </div>
  </div>

  <div class="row mb-3">

    <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">State of Residence</label>
              <select id="state" name="state_residence" class="form-control" >
              <option value="">-Select State-</option>
               <option value="<?= $state_residence ?>" selected><?= $state_residence ?></option>
            </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">LGA</label>
             <select class="form-control" id="lga"  name="lga">
            <option value="">-Select LGA-</option>
            <option value="<?= $lga ?>" selected><?= $lga ?></option>
          </select>
                </div>
              </div>
  </div>

   <div class="mb-3">
    <label class="form-label">Practice Full Address</label>
  <textarea name="address" class="editor" ><?= $address ?? ''?></textarea>
  </div>

  <div class="row mb-3">
    <div class="col-md-3"><input type="url" name="facebook" class="form-control" placeholder="Facebook" value="<?= $facebook ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="twitter" class="form-control" placeholder="Twitter" value="<?= $twitter ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="instagram" class="form-control" placeholder="Instagram" value="<?= $instagram ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="linkedin" class="form-control" placeholder="LinkedIn" value="<?= $linkedin ?? '' ?>"></div>
  </div>

          <input type="hidden" name="action" value="edit_adminvendor">
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

 <input type="hidden" name="user_id" value="<?= $userId; ?>">

               <div class="mb-3">
    <label class="form-label">Describe Your Products/Services</label>
    <textarea name="services" class="editor"><?= $services ?? '' ?></textarea>
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

  <div class="row mb-3">
    <label class="form-label">Availability (Days & Times)(e.g. Monday - Friday, 9am - 5pm)</label>
    <div class="col-md-4">
        <select name="preferred_days[]" id="preferred_days" class="form-select select-multiple" required multiple>
            <?php
            $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
            foreach ($days as $day) {
                $selected = in_array($day, $preferred_days_selected) ? 'selected' : '';
                echo "<option value='$day' $selected>$day</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-4">
        <input type="time" name="start_time" class="form-control" placeholder="Start Time" value="<?= htmlspecialchars($start_time) ?>" required>
    </div>
    <div class="col-md-4">
        <input type="time" name="end_time" class="form-control" placeholder="End Time" value="<?= htmlspecialchars($end_time) ?>" required>
    </div>
</div>

  <div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" id="statusSelect" class="form-select" required>
    <option value="pending" <?= ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
    <option value="active" <?= ($status == 'active') ? 'selected' : ''; ?>>Active</option>
    <option value="suspended" <?= ($status == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
  </select>
</div>

<div class="mb-3" id="suspendReasonBox" style="display: none;">
  <label class="form-label">Reason for Suspension</label>
  <textarea name="suspend_reason" class="feditor" rows="3"><?= $suspend_reason ?? '' ?></textarea>
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
</div>

<?php include "footer.php"; ?>