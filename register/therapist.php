<?php include "../header.php"; ?>

      <!-- Vendor Registration Section -->
  <section id="vendor" class="enroll section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Therapist</h2>
          <p>Join our network of trusted therapists and counselors.</p>
        </div>
      </div><!-- End Section Title -->
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <div class="enrollment-form-wrapper">

            <div class="enrollment-header text-center mb-5" data-aos="fade-up" data-aos-delay="200">
              <h4>Become a Therapist</h4>
              <p>Join Nigeria’s leading platform for marriage, relationship, and family-related services. Connect with thousands of users actively searching for trusted therapists like you.</p>
            </div>


            <form class="enrollment-therapist"  method="POST" enctype="multipart/form-data">
              <div id="messages" style="display:none;"></div>

              <!-- Section 1: Personal and Business Information -->
              <h4 class="mb-3">Section 1: Personal and Business Information</h4>

              <!-- Personal Details -->
              <div class="row mb-3">
                <div class="col-md-3">
                  <label class="form-label">Title *</label>
                  <select name="title" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option>
                    <option value="Ms">Ms</option>
                    <option value="Mx">Mx</option>
                    <option value="Sir">Sir</option>
                    <option value="Dr">Dr</option>
                    <option value="Lady">Lady</option>
                    <option value="Lord">Lord</option>
                    <option value="Professor">Professor</option>
                    <option value="Esq.">Esq.</option>
                    <option value="Hon.">Hon.</option>
                    <option value="Messrs.">Messrs.</option>
                    <option value="Msgr.">Msgr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Rev.">Rev.</option>
                    <option value="Rt. Hon.">Rt. Hon.</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" name="last_name" class="form-control" required>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Upload Photos</label>
                  <input type="file" name="photos" class="form-control" multiple accept="image">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Date of Birth *</label>
                  <input type="date" name="dob" class="form-control" required> 
                </div>
                <div class="col-md-3">
                  <label class="form-label">Gender *</label>
                  <select name="gender" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
              </div>
           <div class="row mb-3">
					  <div class="col-md-6 form-group">
					  <label>Password:</label>
				   <div class="input-group">
					<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
					<div class="input-group-append">
					<span class="input-group-text p-3" onclick="togglePasswordVisibility('password')">
						<i class="bi bi-eye" id="togglePasswordIcon"></i>
														</span>
													</div>
												</div>
					        </div>
                   <div class="col-md-6 form-group">
                  <label>Password:</label>
                                  <div class="input-group">
                                        <input type="password" class="form-control" id="retypePassword" name="retypePassword" placeholder="Password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text p-3" onclick="togglePasswordVisibility('retypePassword')">
                                                <i class="bi bi-eye" id="toggleRetypePasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                  </div>
                                </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nationality *</label>
                  <input type="text" name="nationality" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Languages Spoken Fluently</label>
                  <input type="text" name="languages" class="form-control" placeholder="e.g. English, Yoruba, Hausa">
                </div>
              </div>
              <!-- Business Details -->
              <h5 class="mt-4">Business Details</h5>
              <div class="mb-3">
                <label class="form-label">Business Name</label>
                <input type="text" name="business_name" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Registered Business Name (if different)</label>
                <input type="text" name="registered_business_name" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Business Owner’s Full Name</label>
                <input type="text" name="owner_name" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Business Logo</label>
                <input type="file" name="business_logo" class="form-control" >
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Sample Work or Portfolio</label>
                <input type="file" name="portfolio[]" class="form-control" multiple>
               
              </div>

              <!-- Contact Information -->
              <h5 class="mt-4">Contact Information</h5>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Phone Numbers *</label>
                  <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Website (Optional)</label>
                  <input type="url" name="website" class="form-control">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">State of Residence</label>
              <select id="state" name="state" class="form-control" >
              <option value="">-Select State-</option>
            </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">LGA</label>
             <select class="form-control" id="lga"  name="lga">
            <option value="">-Select LGA-</option>
          </select>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Practice Full Address</label>
                <textarea name="address" class="form-control"></textarea>
              </div>

              <h5 class="mt-4">Social Media Handles</h5>
              <div class="row mb-3">
                <div class="col-md-3"><input type="url" name="facebook" class="form-control" placeholder="Facebook"></div>
                <div class="col-md-3"><input type="url" name="twitter" class="form-control" placeholder="Twitter"></div>
                <div class="col-md-3"><input type="url" name="instagram" class="form-control" placeholder="Instagram"></div>
                <div class="col-md-3"><input type="url" name="linkedin" class="form-control" placeholder="LinkedIn"></div>
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
                    echo "<option value='{$professionId}'>{$name}</option>";
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
                </select>
            </div>
            </div>
              <div class="mb-3">
            <label class="form-label">Highest Academic Qualification *</label>
            <select name="highest_qualification" id="highest_qualification" class="form-control" required>
                <option value="">Select Highest Qualification</option>
                <option value="High School Diploma / GED">High School Diploma / GED</option>
                <option value="Associate Degree">Associate Degree</option>
                <option value="Bachelor’s Degree (BA, BSc, BSW, etc.)">Bachelor’s Degree (BA, BSc, BSW, etc.)</option>
                <option value="Postgraduate Diploma / Certificate">Postgraduate Diploma / Certificate</option>
                <option value="Master’s Degree (MA, MSc, MSW, M.Ed., etc.)">Master’s Degree (MA, MSc, MSW, M.Ed., etc.)</option>
                <option value="Doctorate (PhD, PsyD, EdD, etc.)">Doctorate (PhD, PsyD, EdD, etc.)</option>
                <option value="Professional Degree (MD, JD, etc.)">Professional Degree (MD, JD, etc.)</option>
                <option value="Other">Other / Non-traditional Qualification</option>
            </select>
            </div>

            <!-- Hidden input that appears only when 'Other' is selected -->
            <div class="mb-3" id="otherQualificationField" style="display: none;">
            <label class="form-label">Please specify your qualification</label>
            <input type="text" name="other_qualification" class="form-control" placeholder="Enter your qualification">
            </div>
              <div class="mb-3">
                <label class="form-label">Institution Attended</label>
                <input type="text" name="institution" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Year of Graduation</label>
                <input type="number" name="graduation_year" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Relevant Certifications / Licenses</label>
                <input type="text" name="certifications" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Professional Associations / Boards</label>
                <input type="text" name="associations" class="form-control">
              </div>

              <!-- Section 3: Practice Information -->
         <h4 class="mt-5">Section 3: Practice Information</h4>

        <div class="mb-3">
        <label class="form-label">Years of Professional Experience</label>
        <input type="number" name="experience" class="form-control">
        </div>    
        <input type="hidden" name="action" value="therapistregister">
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
                        echo "<option value='{$id}'>{$name}</option>";
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
            <label class="form-label">Preferred Session Format *</label>
            <select name="session_format" class="form-control" required>
                <option value="">-- Select Format --</option>
                <option value="In-person">In-person</option>
                <option value="Online/Virtual">Online/Virtual</option>
                <option value="Phone">Phone</option>
            </select>
            </div>

            <div class="mb-3">
            <label class="form-label">Who do you work with? *</label>
             <select name="work_with" id="workWith" class="form-control">
              <option value="Individuals">Individuals</option>
              <option value="Couples">Couples</option>
              <option value="Family">Family</option>
              <option value="Group">Group</option>
              <option value="Other">Other (Specify)</option>
          </select>

           <!-- Hidden input for "Other" option -->
          <input type="text" id="otherWorkInput" name="other_work" 
                class="form-control mt-2" 
                placeholder="Please specify..."
                style="display:none;">

            </div>

            <div class="row mb-3">
            <label class="form-label">Preferred Consultation Days & Hours (e.g. Monday - Friday, 9am - 5pm)</label>
       <div class="col-md-4">
          <select name="preferred_days[]" id="preferred_days" class="form-select select-multiple" required multiple>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
        </select>
        </div>
        <div class="col-md-4">
            <input type="time" name="start_time" class="form-control" placeholder="Start Time" required>
        </div>
        <div class="col-md-4">
            <input type="time" name="end_time" class="form-control" placeholder="End Time" required>
      </div>
      </div>

            <div class="mb-3">
            <label class="form-label">Session Duration</label>
            <input type="text" name="session_duration" class="form-control" placeholder="e.g. 45 mins, 1 hour">
            </div>

            <div class="mb-3">
            <label class="form-label">Proposed Rate per Session (NGN)</label>
            <input type="number" name="rate" class="form-control" placeholder="e.g. 15000">
            </div>


              <!-- Section 4: Profile & Uploads -->
              <h4 class="mt-5">Section 4: Profile & Uploads</h4>
              <div class="mb-3">
                <label class="form-label">Professional Bio (100–200 words)</label>
                <textarea name="bio" class="form-control" rows="5"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Upload CV / Resume (PDF or Word)</label>
                <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Passport Photograph (JPEG or PNG)</label>
                <input type="file" name="passport" class="form-control" accept=".jpg,.jpeg,.png">
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Proof of Certification / License (PDF or JPEG)</label>
                <input type="file" name="license" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>

              <!-- Section 5: Declarations & Consent -->
              <h4 class="mt-5">Section 5: Declarations & Consent</h4>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="declaration1" required>
                <label class="form-check-label">I confirm that the information provided above is accurate and truthful.</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="declaration2" required>
                <label class="form-check-label">I understand that all sessions with clients from MARRIAGE.NG must adhere to the platform’s code of ethics and confidentiality guidelines.</label>
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
  </section>
<?php include "../footer.php"; ?>