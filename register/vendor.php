
<?php include "../header.php"; ?>
      <!-- Vendor Registration Section -->
  <section id="vendor" class="enroll section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Vendor</h2>
          <p>Connect with clients searching for trusted vendors</p>
        </div>
      </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <div class="enrollment-form-wrapper">

            <div class="enrollment-header text-center mb-5" data-aos="fade-up" data-aos-delay="200">
              <h4>Become a Vendor</h4>
              <p>Join Nigeria’s leading platform for marriage, relationship, and family-related services. Connect with thousands of users actively searching for trusted vendors like you.</p>
            </div>

            <form class="enrollment-form"  method="POST" enctype="multipart/form-data">
              <div id="messages" style="display:none;"></div>

              <!-- Section 1 -->
              <h5 class="">Section 1: Personal and Business Information</h5>
              <h6 class="mb-4">Please provide your personal and business details.</h6>

              <!-- Personal Details -->
              <div class="row mb-3">
                <div class="col-md-3">
                  <label class="form-label">Title *</label>
                  <select name="title" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Mr">Mr</option><option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option><option value="Ms">Ms</option>
                    <option value="Mx">Mx</option><option value="Sir">Sir</option>
                    <option value="Dr">Dr</option><option value="Lady">Lady</option>
                    <option value="Lord">Lord</option><option value="Professor">Professor</option>
                    <option value="Esq.">Esq.</option><option value="Hon.">Hon.</option>
                    <option value="Messrs.">Messrs.</option><option value="Msgr.">Msgr.</option>
                    <option value="Prof.">Prof.</option><option value="Rev.">Rev.</option>
                    <option value="Rt. Hon.">Rt. Hon.</option><option value="Other">Other</option>
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
                <div class="col-md-4">
                  <label class="form-label">Photo</label>
                  <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" name="dob" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Gender</label>
                  <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nationality</label>
                  <input type="text" name="nationality" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Languages Spoken Fluently</label>
                  <input type="text" name="languages" class="form-control">
                </div>
              </div>

              <!-- Business Details -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Business Name</label>
                  <input type="text" name="business_name" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Registered Business Name</label>
                  <input type="text" name="registered_business_name" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Business Owner’s Full Name</label>
                  <input type="text" name="owner_name" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Upload Business Logo</label>
                  <input type="file" name="business_logo" class="form-control">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Upload Sample Work or Portfolio (Photos & Videos)</label>
                <input type="file" name="portfolio[]" class="form-control" multiple>
              </div>

              <!-- Contact Info -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Phone Numbers</label>
                  <input type="text" name="phone" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Website (Optional)</label>
                  <input type="url" name="website" class="form-control">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Email Address</label>
                  <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">State of Residence</label>
                  <input type="text" name="state_residence" class="form-control" placeholder="LGA | State">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Practice Full Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
              </div>

              <!-- Social Media -->
              <div class="row mb-3">
                <div class="col-md-3"><input type="url" name="facebook" class="form-control" placeholder="Facebook"></div>
                <div class="col-md-3"><input type="url" name="twitter" class="form-control" placeholder="Twitter"></div>
                <div class="col-md-3"><input type="url" name="instagram" class="form-control" placeholder="Instagram"></div>
                <div class="col-md-3"><input type="url" name="linkedin" class="form-control" placeholder="LinkedIn"></div>
              </div>
          <input type="hidden" name="action" value="register_vendor">
              <!-- Section 2 -->
         <h5 class="mt-4">Section 2: Vendor Category</h5>
              <h6 class="mb-4">Please provide your vendor category information.</h6>


              <div class="row mb-3">
                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
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

                <!-- Sub-Category -->
                <div class="col-md-6">
                    <label class="form-label">Sub-Category *</label>
                    <select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>


                    <option value="">-- Select Sub-Category --</option>
                   
                    </select>
                </div>
                </div>

              <div class="mb-3">
                <label class="form-label">Describe Your Products/Services</label>
                <textarea name="services" class="form-control" rows="3"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Years of Experience</label>
                <input type="number" name="experience_years" class="form-control">
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

                <h5 class="mt-4">Section 3: Business Operations</h5>
                <h6 class="mb-4">Provide details about your operations and availability.</h6>

              <div class="mb-3">
                <label class="form-label">Service Coverage</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="coverage[]" value="Local"> Local (My City Only)
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="coverage[]" value="Statewide"> Statewide
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="coverage[]" value="Nationwide"> Nationwide
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="coverage[]" value="International"> International
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Do You Offer On-site Services?</label>
                <select name="onsite" class="form-select">
                  <option value="">Select</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Availability (Days & Times)</label>
                <input type="text" name="availability" class="form-control" placeholder="e.g. Mon - Sat, 9AM - 6PM">
              </div>

              <!-- Section 4 -->
          
                <h5 class="mt-4">Section 4: Consent & Submission</h5>
                <h6 class="mb-4">Read carefully and agree before submitting your application.</h6>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="consent" required>
                <label class="form-check-label">
                  By submitting this form, I confirm that all information provided is accurate and that I agree to the platform's vendor guidelines, terms of service, and quality standards.
                </label>
              </div>

              <div class="text-center">
                <button type="submit" id="submitBtn" class="btn btn-primary">
                  <i class="bi bi-arrow-left-circle-fill me-2"></i> Submit
      </button>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </section>


<?php include "../footer.php"; ?>