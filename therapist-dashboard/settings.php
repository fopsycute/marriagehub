<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Settings</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Settings</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Settings</a></li>
      </ul>
    </div>

      <div class="row">
      <div class="col-md-12">


     
        <!-- Section 1: Personal Details -->
    <div class="card mb-4">
      <div class="card-header">
            <!-- Section 1: Personal Details -->
    <h5 class="mb-3">Personal Details</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
   <!-- Site Name -->
              
            <form class="update-enrollment-therapist"  method="POST" enctype="multipart/form-data">
              <div id="messages" style="display:none;"></div>

              <!-- Section 1: Personal and Business Information -->
              <h4 class="mb-3">Section 1: Personal and Business Information</h4>

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
                  <input type="text" name="middle_name" class="form-control" value="<?php echo  $buyermiddleName; ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" name="last_name" class="form-control"  value="<?php echo $buyerlastName; ?>" required>
                </div>
              </div>
			

              <div class="row mb-3">
                <div class="col-md-12">
      <label class="form-label">Photo</label>
      <?php if (!empty($logo)): ?>
        <div class="mb-2"><img src="<?= $mylogo ?>" width="100" class="rounded"></div>
      <?php endif; ?>
      <input type="file" name="photos" class="form-control" accept="image/*">
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

  <div class="mb-3">
    <label class="form-label">Brief Bio</label>
    <textarea name="bio" class="editor"><?= $bio ?? '' ?></textarea>

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
    <div class="md-3">
                <label class="form-label">Business Name</label>
                <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($business_name ?? '') ?>">
              </div>
      <div class="md-3">
                <label class="form-label">Registered Business Name (if different)</label>
                <input type="text" name="registered_business_name" class="form-control" value="<?= htmlspecialchars($registered_business_name ?? '') ?>">
              </div>
    <div class="col-md-3">
      <label class="form-label">Business Owner’s Full Name</label>
      <input type="text" name="owner_name" class="form-control" value="<?= htmlspecialchars($owner_name ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Business Logo</label>
      <?php if (!empty($business_logo)): ?>
        <div class="mb-2"><img src="<?= $business_logo ?>" width="100" class="rounded"></div>
      <?php endif; ?>
      <input type="file" name="business_logo" class="form-control">
    </div>
  </div>

<div class="mb-3">
    <label class="form-label">Portfolio (Photos & Videos)</label>

    <?php if (!empty($user->portfolio)): ?>
        <div id="portfolio-list" class="mb-2">

            <?php 
            $files = explode(",", $user->portfolio);
            foreach ($files as $file):

                $clean = trim($file);
                if ($clean == "") continue;

                $fileUrl = $siteurl . $imagePath . $clean;
            ?>

            <span class="badge bg-info text-dark me-1 portfolio-item" 
                  data-file="<?= $clean ?>">
                <a href="<?= $fileUrl ?>" target="_blank" class="text-white">View</a>
                <span class="remove-file text-danger ms-2" style="cursor:pointer;"><i class="fas fa-times"></i></span>
            </span>

            <?php endforeach; ?>

        </div>
    <?php endif; ?>

    <input type="file" name="portfolio[]" class="form-control" multiple>
</div>


  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Phone Numbers</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Website</label>
      <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($website ?? '') ?>">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
    </div>
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
    <label class="form-label">Practice Full Address</label>
  <textarea name="address" class="editor" rows="2"><?= $address ?? '' ?></textarea>
  </div>
   <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">State of Residence</label>
              <select id="state" name="state" class="form-control" >
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
         
    <div class="row">
              <h5 class="mt-4">Social Media Handles</h5>
          <div class="col-md-3"><input type="url" name="facebook" class="form-control" placeholder="Facebook" value="<?= $facebook ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="twitter" class="form-control" placeholder="Twitter" value="<?= $twitter ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="instagram" class="form-control" placeholder="Instagram" value="<?= $instagram ?? '' ?>"></div>
    <div class="col-md-3"><input type="url" name="linkedin" class="form-control" placeholder="LinkedIn" value="<?= $linkedin ?? '' ?>"></div>
              </div>
        

              <!-- Section 2: Professional Qualifications -->
              <h4 class="mt-5">Section 2: Professional Qualifications</h4>

              <div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Professional Field *</label>
    <select name="professional_field[]" id="professional_field" class="form-select select-multiple" required multiple>
      <option value="">-- Select Field --</option>
      <?php
        $url = $siteurl . "script/register.php?action=professionlists";
        $data = curl_get_contents($url);
        if ($data !== false) {
            $professions = json_decode($data);
            if (!empty($professions)) {
                foreach ($professions as $profession) {
                  $professionId = $profession->id;
                  $name = $profession->name; // Adjust field name if different
                  $selected = in_array($professionId, $profession_selected) ? 'selected' : '';
                  echo "<option value='" . htmlspecialchars($professionId) . "' {$selected}>" . htmlspecialchars($name) . "</option>";
                }
                        }
                    } else {
                        echo "<option value=''>Error fetching data</option>";
                    }
                ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Professional Title *</label>
                <select name="professional_title[]" id="professional_title" class="form-select select-multiple" required multiple>
                <option value="">-- Select Title --</option>
                 <?php
  if (!empty($profession_selected)) {
      $url = $siteurl . "script/register.php?action=subprofessionlists&parent_ids=" . implode(',', $profession_selected);
      $data = curl_get_contents($url);
      if ($data !== false) {
          $subprofessions = json_decode($data);
          if (!empty($subprofessions)) {
              foreach ($subprofessions as $subcat) {
                  $subcatId = $subcat->id;
                  $subcatName = $subcat->name;
                  $selected = in_array($subcatId, $subprofession_selected) ? 'selected' : '';
          echo "<option value='" . htmlspecialchars($subcatId) . "' {$selected}>" . htmlspecialchars($subcatName) . "</option>";
              }
          }
      }
  }
  ?>
                </select>
            </div>
            </div>
              <div class="mb-3">
            <label class="form-label">Highest Academic Qualification *</label>
          <?php
        $options = [
            "High School Diploma / GED",
            "Associate Degree",
            "Bachelor’s Degree (BA, BSc, BSW, etc.)",
            "Postgraduate Diploma / Certificate",
            "Master’s Degree (MA, MSc, MSW, M.Ed., etc.)",
            "Doctorate (PhD, PsyD, EdD, etc.)",
            "Professional Degree (MD, JD, etc.)",
            "Other"
        ];

        // If the value from DB is not in options, treat it as "Other"
        $is_other = !in_array($highest_qualification, $options);
        ?>
        <select name="highest_qualification" id="highest_qualification" class="form-control" required>
            <option value="">Select Highest Qualification</option>
            <?php foreach ($options as $opt):
                // Select "Other" if the value is not in the list
                $selected = ($is_other && $opt === 'Other') || ($highest_qualification === $opt) ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($opt) ?>" <?= $selected ?>><?= htmlspecialchars($opt) ?></option>
            <?php endforeach; ?>
        </select>
            </div>

            <!-- Hidden input that appears only when 'Other' is selected -->
            <div class="mb-3" id="otherQualificationField" style="<?= $is_other ? '' : 'display: none;' ?>">
            <label class="form-label">Please specify your qualification</label>
             <input type="text" name="other_qualification" class="form-control mt-2" placeholder="If other, specify" value="<?= htmlspecialchars($other_qualification ?? '') ?>">
            </div>
              <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Institution *</label>
                <input type="text" name="institution" class="form-control" required value="<?= htmlspecialchars($institution) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Graduation Year *</label>
                <input type="number" name="graduation_year" class="form-control" required value="<?= $graduation_year ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Certifications</label>
                <input type="text" name="certifications" class="form-control" value="<?= $certifications ?>">
            </div>
            </div>

            <div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Professional Associations</label>
    <input type="text" name="associations" class="form-control" value="<?= htmlspecialchars($associations ?? '') ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Years of Experience *</label>
    <input type="number" name="experience" class="form-control" required value="<?= $experience_years ?>">
  </div>
</div>
    <input type="hidden" name="action" value="edittherapist">
    <input type="hidden" name="user_id" id="user_id" value="<?= htmlspecialchars($buyerId ?? '') ?>">
     <div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Specializations</label>
        <select name="specializations[]" id="specializations" class="form-select select-multiple" required multiple>
        <option value="">-- Select Specialization --</option>
        <?php
            $url = $siteurl . "script/register.php?action=specializationlists";
            $data = curl_get_contents($url);
            if ($data !== false) {
                $specializations = json_decode($data);
                if (!empty($specializations)) {
                    foreach ($specializations as $specialization) {
                        $id = $specialization->id;
                        $name = $specialization->name;
                        $selected = in_array($id, $specialization_selected) ? 'selected' : '';
              echo "<option value='{$id}' {$selected}>{$name}</option>";
                    }
                }
            } else {
                echo "<option value=''>Error fetching data</option>";
            }
        ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Sub-Specializations</label>
        <select id="sub_specialization" name="sub_specialization[]" class="form-select select-multiple" required multiple>
        <option value="">-- Select Sub-Specialization --</option>
  <?php
  if (!empty($specialization_selected)) {
    $url = $siteurl . "script/register.php?action=subspecializationlists&parent_ids=" . implode(',', $specialization_selected);
      $data = curl_get_contents($url);
      if ($data !== false) {
          $subcategories = json_decode($data);
          if (!empty($subcategories)) {
              foreach ($subcategories as $subcat) {
                  $subcatId = $subcat->id;
                  $subcatName = $subcat->name;
          $selected = in_array($subcatId, $subspec_selected) ? 'selected' : '';
          echo "<option value='" . htmlspecialchars($subcatId) . "' {$selected}>" . htmlspecialchars($subcatName) . "</option>";
              }
          }
      }
  }
  ?>

        
        </select>
    </div>
    </div>

      <div class="mb-3">
      <label class="form-label">Preferred Session Format *</label>
      <select name="session_format" class="form-control" required>
        <option value="">-- Select Format --</option>
        <option value="In-person" <?= ($session_format === 'In-person') ? 'selected' : '' ?>>In-person</option>
        <option value="Online/Virtual" <?= ($session_format === 'Online/Virtual') ? 'selected' : '' ?>>Online/Virtual</option>
        <option value="Phone" <?= ($session_format === 'Phone') ? 'selected' : '' ?>>Phone</option>
      </select>
      </div>

          <?php
$options = ['Individuals', 'Couples', 'Family', 'Group', 'Other'];

// Check if current value is not in the options
$is_other = !in_array($work_with, $options);
?>

<div class="mb-3">
  <label class="form-label">Who do you work with? *</label>

  <select name="work_with" class="form-select" required>
    <?php
    foreach ($options as $opt) {
        $selected = ($is_other && $opt === 'Other') || ($work_with === $opt) ? 'selected' : '';
        echo "<option value='$opt' $selected>$opt</option>";
    }
    ?>
  </select>

  <?php if ($is_other): ?>
      <input type="text" name="other_work" class="form-control mt-2" 
             placeholder="Please specify..." 
             value="<?= htmlspecialchars($work_with) ?>">
  <?php endif; ?>
</div>

            <div class="row mb-3">
            <label class="form-label">Preferred Consultation Days & Hours (e.g. Monday - Friday, 9am - 5pm)</label>
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
            <label class="form-label">Session Duration</label>
            <input type="text" name="session_duration" class="form-control" placeholder="e.g. 45 mins, 1 hour" value="<?= htmlspecialchars($session_duration ?? '') ?>">
            </div>

            <div class="mb-3">
            <label class="form-label">Proposed Rate per Session (NGN)</label>
            <input type="number" name="rate" class="form-control" placeholder="e.g. 15000" value="<?= htmlspecialchars($rate ?? '') ?>">
            </div>


              <!-- Section 4: Profile & Uploads -->
              <h4 class="mt-5">Section 4: Profile & Uploads</h4>
              <div class="mb-3">
    <?php if (!empty($cv)): ?>
        <a href="<?= $siteurl . $imagePath . $cv ?>" target="_blank" class="btn btn-sm btn-primary mb-2">View CV / Resume</a>
    <?php endif; ?>
    <label class="form-label">Upload CV / Resume (PDF or Word)</label>
    <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
</div>

<!-- Passport Photograph -->
<div class="mb-3">
    <?php if (!empty($passport)): ?>
        <a href="<?= $siteurl . $imagePath . $passport ?>" target="_blank" class="btn btn-sm btn-primary mb-2">View Passport</a>
    <?php endif; ?>
    <label class="form-label">Upload Passport Photograph (JPEG or PNG)</label>
    <input type="file" name="passport" class="form-control" accept=".jpg,.jpeg,.png">
</div>

<!-- Proof of Certification / License -->
<div class="mb-3">
    <?php if (!empty($license)): ?>
        <a href="<?= $siteurl . $imagePath . $license ?>" target="_blank" class="btn btn-sm btn-primary mb-2">View License / Certificate</a>
    <?php endif; ?>
    <label class="form-label">Upload Proof of Certification / License (PDF or JPEG)</label>
    <input type="file" name="license" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
</div>         
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-enroll btn-primary w-100">
                  <i class="bi bi-check-circle me-2"></i> Submit
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
</div>
<?php include "footer.php"; ?>