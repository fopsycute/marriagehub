
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">List My Products & Services</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Product</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Product & Service Listing</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        
    <form  method="POST" id="listingForm" enctype="multipart/form-data">  
        <div class="text-center mt-1" id="messages"></div> 
        <!-- Section 1: Product or Service Details -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">Section 1: Product or Service Details</div>
          </div>
          <div class="card-body">
            
            <div class="form-group">
              <label for="listingTitle">Listing Title</label>
              <input type="text" class="form-control" id="listingTitle"  placeholder="Enter title" name="listingTitle" />
            </div>

            <div class="form-group">
              <label class="form-label" for="course-id">Listing ID</label>
              <input type="text" id="listing-id" name="listing_id" class="form-control" value="MA<?php echo sprintf('%06d', rand(1, 999999)); ?>" readonly required>
              </div>
            <input type="hidden" name="action" value="addlisting">
             <div class="row">
              <div class="col-lg-6">
            <div class="form-group">
              <label for="blogCategories">Categories</label>
              <select name="category[]" id="category" class="form-control select-multiple" required multiple>
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
              </div>

              <div class="col-lg-6">

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="blogSubcategories">Sub-Categories</label>
              <select name="subcategory[]" id="subcategory" class="form-control select-multiple" required multiple>
              <option value="">-- Select Sub-Category --</option>
                            
          </select>
            </div>
              </div>
                </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe what it is, how it works, and why it matters..."></textarea>
            </div>

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

            <!-- Single Price -->
            <div class="form-group" id="singlePriceGroup">
              <label for="price">Price (NGN)</label>
              <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" />
            </div>

            <!-- Price Range -->
            <div class="form-group d-none" id="priceRangeGroup">
              <label>Price Range (NGN)</label>
              <div class="d-flex">
                <input type="number" class="form-control mr-2" id="minPrice" placeholder="Min price" name="priceMin" />
                <input type="number" class="form-control" id="maxPrice" placeholder="Max price" name="priceMax" />
              </div>
            </div>

            <!-- Custom Quote -->
            <div class="form-group d-none" id="customQuoteNote">
              <label>Pricing</label>
              <p class="text-muted mb-0">Custom pricing — please contact us for a quote.</p>
            </div>

            <div class="form-group mt-3">
              <label for="pricingNotes">Pricing Notes (Optional)</label>
              <textarea class="form-control" id="pricingNotes" rows="2" name="pricingNotes" placeholder="Additional pricing details..."></textarea>
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
              <select class="select2-single form-control" id="availability" name="availability">
                <option value="Always Available">Always Available</option>
                <option value="By Appointment">By Appointment</option>
                <option value="Limited Slot">Limited Slot</option>
                <option value="Seasonal">Seasonal</option>
              </select>
            </div>

            <div class="form-group">
              <label for="capacity">Capacity/Volume (Optional)</label>
              <input type="text" class="form-control" id="capacity" name="capacity" placeholder="Enter capacity or volume" />
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
            <input type="hidden" name="user" id="user" value="<?php echo $buyerId; ?>">

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
              <input type="file" class="form-control" id="productImages" name="productImages[]" multiple  accept="image/*" />
              <small class="form-text text-muted">
                Free - 1, Classic - 5, Enterprise - 15, Premium - Unlimited
              </small>
            </div>

            <div class="form-group">
              <label for="videos">Upload Videos (Optional)</label>
              <input type="file" class="form-control" name="videos[]"  id="videos" accept="video/*" multiple />
              <small class="form-text text-muted">
                Max 3, 50MB each (Free - 1, Classic - 5, Enterprise - 15, Premium - Unlimited)
              </small>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary"> Submit </button>
                    </form>
      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>