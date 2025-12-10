

<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Trainers</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Trainers</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <!-- Section 1: Personal Details -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Personal Details</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <!-- Title -->
          <div class="col-md-4">
            <label class="form-label">Title</label>
            <select class="form-select">
              <option>Mr</option>
              <option>Mrs</option>
              <option>Miss</option>
              <option>Ms</option>
              <option>Dr</option>
              <option>Prof.</option>
              <option>Rev.</option>
              <option>Other</option>
            </select>
          </div>
          <!-- Names -->
          <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control">
          </div>
          <!-- Photo -->
          <div class="col-md-4">
            <label class="form-label">Photo</label>
            <input type="file" class="form-control">
             <small class="text-muted">Current: <a href="#">View Photo</a></small>
          </div>
          <!-- DOB -->
          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control">
          </div>
          <!-- Gender -->
          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select class="form-select">
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <!-- Nationality -->
          <div class="col-md-4">
            <label class="form-label">Nationality</label>
            <input type="text" class="form-control">
          </div>
          <!-- Languages -->
          <div class="col-md-12">
            <label class="form-label">Languages Spoken Fluently</label>
            <input type="text" class="form-control" placeholder="e.g. English, Yoruba, Igbo, Hausa">
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Business Details -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Business Details</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Business Name</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Registered Business Name (if different)</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Business Owner’s Full Name</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Upload Business Logo</label>
            <input type="file" class="form-control">
             <small class="text-muted">Current: <a href="#">View Logo</a></small>
          </div>
          <div class="col-md-12">
            <label class="form-label">Upload Sample Work or Portfolio</label>
            <input type="file" class="form-control" multiple>
             <small class="text-muted">Current: <a href="#">View Portfolio</a></small>
            <small class="text-muted">Free – 1, Classic – 5, Enterprise – 15, Premium – Unlimited</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 3: Contact Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Contact Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Phone Numbers</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Personal/Professional Website</label>
            <input type="url" class="form-control" placeholder="Optional">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">State of Residence</label>
            <input type="text" class="form-control" placeholder="LGA | State">
          </div>
          <div class="col-md-12">
            <label class="form-label">Practice Full Address</label>
            <textarea class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 4: Social Media Handles -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Social Media Handles</h5>
      </div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Facebook</label>
          <input type="url" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Twitter</label>
          <input type="url" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Instagram</label>
          <input type="url" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">LinkedIn</label>
          <input type="url" class="form-control">
        </div>
      </div>
    </div>

    <!-- Section 5: Professional Qualifications -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Professional Qualifications</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Highest Academic Qualification</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Institution Attended</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Year of Graduation</label>
            <input type="number" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Relevant Certifications / Licenses</label>
            <input type="text" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Professional Associations/Boards</label>
            <input type="text" class="form-control" placeholder="If any">
          </div>
          <div class="col-md-12">
            <label class="form-label">Area of Expertise</label>
          <select id="categories" class="form-control select2-multiple" multiple>
            <option>Marriage Counseling</option>
            <option>Relationship Coaching</option>
            <option>Parenting</option>
            <option>Faith-Based Counseling</option>
            <option>Personal Development</option>
            <option>Premarital Counseling</option>
            <option>Trauma Healing</option>
            <option>Other</option>
        </select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Brief Bio (Max. 200 words)</label>
            <textarea class="form-control" rows="4"></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit -->
    <div class="text-end">
      <button type="submit" class="btn btn-primary">Save & Continue</button>
    </div>
  </form>

      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>