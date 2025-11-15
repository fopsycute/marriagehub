


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
	  <form>
        <!-- Section 1: Personal Details -->
    <div class="card mb-4">
      <div class="card-header">
            <!-- Section 1: Personal Details -->
    <h5 class="mb-3">Personal Details</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Title</label>
        <select class="form-select">
          <option selected>Dr</option>
          <option>Mr</option>
          <option>Mrs</option>
          <option>Miss</option>
          <option>Prof.</option>
          <option>Rev.</option>
          <option>Other</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">First Name</label>
        <input type="text" class="form-control" value="Adewale">
      </div>
      <div class="col-md-3">
        <label class="form-label">Middle Name</label>
        <input type="text" class="form-control" value="Olu">
      </div>
      <div class="col-md-3">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-control" value="Johnson">
      </div>
      <div class="col-md-4">
        <label class="form-label">Photo</label>
        <input type="file" class="form-control">
        <small class="text-muted">Current: <a href="#">View Photo</a></small>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input type="date" class="form-control" value="1980-09-12">
      </div>
      <div class="col-md-4">
        <label class="form-label">Gender</label>
        <select class="form-select">
          <option selected>Male</option>
          <option>Female</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nationality</label>
        <input type="text" class="form-control" value="Nigerian">
      </div>
      <div class="col-md-6">
        <label class="form-label">Languages Spoken Fluently</label>
        <select class="form-control select2-multiple" multiple>
          <option selected>English</option>
          <option selected>Yoruba</option>
          <option>French</option>
          <option>Igbo</option>
          <option>Hausa</option>
        </select>
      </div>
    </div>
    </div>
     </div>

       <div class="card mb-4">
      <div class="card-header">
    <!-- Section 2: Business Details -->
    <h5 class="mt-4 mb-3">Business Details</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Business Name</label>
        <input type="text" class="form-control" value="Marriage Enrichment Hub">
      </div>
      <div class="col-md-6">
        <label class="form-label">Registered Business Name</label>
        <input type="text" class="form-control" value="Marriage Enrichment Ltd">
      </div>
      <div class="col-md-6">
        <label class="form-label">Business Owner’s Full Name</label>
        <input type="text" class="form-control" value="Dr. Adewale Johnson">
      </div>
      <div class="col-md-6">
        <label class="form-label">Upload Business Logo</label>
        <input type="file" class="form-control">
        <small class="text-muted">Current: <a href="#">View Logo</a></small>
      </div>
      <div class="col-md-12">
        <label class="form-label">Upload Sample Work / Portfolio</label>
        <input type="file" class="form-control" multiple>
        <small class="text-muted">Uploaded: 5 items</small>
      </div>
    </div>
     </div>
    </div>

     <div class="card mb-4">
      <div class="card-header">
    <!-- Section 3: Contact Info -->
    <h5 class="mt-4 mb-3">Contact Information</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" class="form-control" value="+2347084227348">
      </div>
      <div class="col-md-6">
        <label class="form-label">Website</label>
        <input type="url" class="form-control" value="https://marriagehub.ng">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" value="info@marriagehub.ng">
      </div>
      <div class="col-md-6">
        <label class="form-label">State of Residence</label>
        <input type="text" class="form-control" value="Lagos, Nigeria">
      </div>
      <div class="col-md-12">
        <label class="form-label">Practice Full Address</label>
        <textarea class="form-control" rows="2">No. 15, Allen Avenue, Ikeja, Lagos</textarea>
      </div>
    </div>
     </div>
    </div>

    
     <div class="card mb-4">
      <div class="card-header">
    <!-- Section 4: Social Media -->
    <h5 class="mt-4 mb-3">Social Media Handles</h5>
    </div>
     <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3"><input type="text" class="form-control" value="fb.com/marriagehub"></div>
      <div class="col-md-3"><input type="text" class="form-control" value="@marriagehub_ng"></div>
      <div class="col-md-3"><input type="text" class="form-control" value="instagram.com/marriagehub"></div>
      <div class="col-md-3"><input type="text" class="form-control" value="linkedin.com/in/marriagehub"></div>
    </div>
        </div>
        </div>
        
            
     <div class="card mb-4">
      <div class="card-header">
    <!-- Section 5: Vendor Category -->
    <h5 class="mt-4 mb-3">Vendor Category</h5>
    </div>
     <div class="card-body">
    <div class="mb-3">
      <label class="form-label">Categories & Sub-Categories</label>
      <select class="form-control select2-multiple" multiple>
        <option selected>Marriage</option>
        <option selected>Parenting</option>
        <option>Faith</option>
        <option>Youth</option>
        <option>Health</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Describe Services</label>
      <textarea class="form-control" rows="3">We provide professional counseling and marriage enrichment services...</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Years of Experience</label>
      <input type="number" class="form-control" value="15">
    </div>
        </div>
        </div>

    <!-- Section 6: Subscription -->
     
     <div class="card mb-4">
      <div class="card-header">
    <h5 class="mt-4 mb-3">Vendor Subscription Plan</h5>
    </div>
     <div class="card-body">
    <div class="card p-3 border-primary">
      <p><strong>Current Plan:</strong> Premium (₦25,000 / year)</p>
    </div>
        </div>
        </div>

    <!-- Save -->
    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success">Save Changes</button>
    </div>

  </form>
</div>
</div>
</div>
</div>

<?php include "footer.php"; ?>