
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit My Products & Services</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Product & Service Listing</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Section 1: Product or Service Details</div>
          </div>
          <div class="card-body">
            
            <!-- Listing Title -->
            <div class="form-group">
              <label for="listingTitle">Listing Title</label>
              <input type="text" class="form-control" id="listingTitle" placeholder="Enter title" />
            </div>

            <!-- Categories -->
            <div class="form-group">
              <label for="categories">Categories</label>
              <select class="select2-multiple form-control" name="categories[]" multiple id="categories">
                <option value="Marriage">Marriage</option>
                <option value="Parenting">Parenting</option>
                <option value="Faith">Faith</option>
                <option value="Youth">Youth</option>
                <option value="Health">Health</option>
                <option value="Finance">Finance</option>
              </select>
            </div>

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="subcategories">Sub-Categories</label>
              <select class="select2-multiple form-control" name="subcategories[]" multiple id="subcategories">
                <option value="Pre-Marital Counseling">Pre-Marital Counseling</option>
                <option value="Conflict Resolution">Conflict Resolution</option>
                <option value="Parent Coaching">Parent Coaching</option>
                <option value="Teen Mentorship">Teen Mentorship</option>
                <option value="Financial Planning">Financial Planning</option>
                <option value="Health & Wellness">Health & Wellness</option>
              </select>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" rows="4" placeholder="Describe what it is, how it works, and why it matters..."></textarea>
            </div>

            <!-- Product or Service -->
            <div class="form-group">
              <label>Is this a Product or a Service?</label><br/>
              <div class="d-flex">
                <div class="form-check mr-3">
                  <input class="form-check-input" type="radio" name="type" id="product" value="Product">
                  <label class="form-check-label" for="product">Product</label>
                </div>
                <div class="form-check mr-3">
                  <input class="form-check-input" type="radio" name="type" id="service" value="Service">
                  <label class="form-check-label" for="service">Service</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="type" id="both" value="Both">
                  <label class="form-check-label" for="both">Both</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Pricing -->
        <div class="card mt-4">
          <div class="card-header">
            <div class="card-title">Section 2: Pricing</div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="pricingType">Pricing Type</label>
              <select class="select2-single form-control" id="pricingType" name="pricingType">
                <option value="Starting Price">Starting Price</option>
                <option value="Price Range">Price Range</option>
                <option value="Custom Quote">Custom Quote</option>
              </select>
            </div>

            <div class="form-group">
              <label for="price">Price (NGN)</label>
              <input type="number" class="form-control" id="price" placeholder="Enter price" />
            </div>

            <div class="form-group">
              <label for="pricingNotes">Pricing Notes (Optional)</label>
              <textarea class="form-control" id="pricingNotes" rows="2"></textarea>
            </div>
          </div>
        </div>

        <!-- Section 3: Availability & Capacity -->
        <div class="card mt-4">
          <div class="card-header">
            <div class="card-title">Section 3: Availability & Capacity</div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="availability">Availability</label>
              <select class="select2-single form-control" id="availability">
                <option value="Always Available">Always Available</option>
                <option value="By Appointment">By Appointment</option>
                <option value="Limited Slot">Limited Slot</option>
                <option value="Seasonal">Seasonal</option>
              </select>
            </div>

            <div class="form-group">
              <label for="capacity">Capacity/Volume (Optional)</label>
              <input type="text" class="form-control" id="capacity" placeholder="Enter capacity or volume" />
            </div>

            <div class="form-group">
              <label>Do you offer home delivery or in-person service?</label><br/>
              <div class="d-flex">
                <div class="form-check mr-3">
                  <input class="form-check-input" type="radio" name="delivery" id="yes" value="Yes">
                  <label class="form-check-label" for="yes">Yes</label>
                </div>
                <div class="form-check mr-3">
                  <input class="form-check-input" type="radio" name="delivery" id="no" value="No">
                  <label class="form-check-label" for="no">No</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="delivery" id="depends" value="Depends">
                  <label class="form-check-label" for="depends">Depends on location</label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Service Coverage</label><br/>
              <div class="d-flex flex-wrap">
                <div class="form-check mr-3">
                  <input class="form-check-input" type="checkbox" name="coverage[]" id="local" value="Local">
                  <label class="form-check-label" for="local">Local</label>
                </div>
                <div class="form-check mr-3">
                  <input class="form-check-input" type="checkbox" name="coverage[]" id="statewide" value="Statewide">
                  <label class="form-check-label" for="statewide">Statewide</label>
                </div>
                <div class="form-check mr-3">
                  <input class="form-check-input" type="checkbox" name="coverage[]" id="nationwide" value="Nationwide">
                  <label class="form-check-label" for="nationwide">Nationwide</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="coverage[]" id="international" value="International">
                  <label class="form-check-label" for="international">International</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 4: Media & Files -->
        <div class="card mt-4">
          <div class="card-header">
            <div class="card-title">Section 4: Media & Files</div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="productImages">Upload Product Images or Service Flyers</label>
              <input type="file" class="form-control" id="productImages" multiple />
              <small class="form-text text-muted">
                Free - 1, Classic - 5, Enterprise - 15, Premium - Unlimited
              </small>
            </div>

            <div class="form-group">
              <label for="videos">Upload Videos (Optional)</label>
              <input type="file" class="form-control" id="videos" multiple />
              <small class="form-text text-muted">
                Max 3, 50MB each (Free - 1, Classic - 5, Enterprise - 15, Premium - Unlimited)
              </small>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>