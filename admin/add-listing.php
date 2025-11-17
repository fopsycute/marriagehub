
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
        
   <form method="POST" id="adminlistingForm" enctype="multipart/form-data">  
  <div class="text-center mt-1" id="messages"></div> 

  <!-- Section 1: Product or Service Details -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Section 1: Product or Service Details</div>
    </div>
    <div class="card-body">

      <!-- Title -->
      <div class="form-group">
        <label for="listingTitle">Listing Title</label>
        <input type="text" class="form-control" id="listingTitle" placeholder="Enter title" name="listingTitle" required />
      </div>

      <!-- Listing ID -->
      <div class="form-group">
        <label class="form-label" for="listing-id">Listing ID</label>
        <input type="text" id="listing-id" name="listing_id" class="form-control" 
               value="MA<?php echo sprintf('%06d', rand(1, 999999)); ?>" readonly required>
      </div>

      <input type="hidden" name="action" value="adminlisting">

      <!-- Categories & Subcategories -->
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="category">Categories</label>
            <select name="category[]" id="category" class="form-control select-multiple" required multiple>
              <option value="">-- Select Category --</option>
              <?php
              $url = $siteurl . "script/register.php?action=categorieslists";
              $data = curl_get_contents($url);
              if ($data !== false) {
                  $categories = json_decode($data);
                  if (!empty($categories)) {
                      foreach ($categories as $category) {
                          echo "<option value='{$category->id}'>{$category->category_name}</option>";
                      }
                  }
              }
              ?>
            </select>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="form-group">
            <label for="subcategory">Sub-Categories</label>
            <select name="subcategory[]" id="subcategory" class="form-control select-multiple" required multiple>
              <option value="">-- Select Sub-Category --</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="editor" id="editor" name="description" rows="4"
                  placeholder="Describe what it is, how it works, and why it matters..."></textarea>
      </div>

      <!-- Type -->
     <div class="form-group">
      <label>Is this a Product or a Service?</label><br>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="itemType" id="productType" value="Product" checked>
        <label class="form-check-label" for="productType">Product</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="itemType" id="serviceType" value="Service">
        <label class="form-check-label" for="serviceType">Service</label>
      </div>
      
    </div>

    <!-- Booking notice -->
    <div class="alert alert-info d-none" id="bookingNotice">
      <strong>Note:</strong> Since this is a service, customers will be able to <b>book</b> instead of adding to cart.
    </div>

    </div>
  </div>

  <!-- Section 2: Pricing -->
  <div class="card mt-4">
    <div class="card-header">
      <div class="card-title">Section 2: Pricing</div>
    </div>
    <div class="card-body">

          <!-- Pricing Type -->
    <div class="form-group">
      <label for="pricingType">Pricing Type</label>
      <select class="form-control" id="pricingType" name="pricingType">
        <option value="Starting Price">Starting Price</option>
        <option value="Price Range">Price Range (Variations)</option>
        <option value="Custom Quote">Custom Quote</option>
      </select>
    </div>

    <!-- Single Price (Starting Price) -->
    <div class="form-group" id="singlePriceGroup">
      <label for="price">Price (₦)</label>
      <input
        type="number"
        class="form-control"
        id="price"
        name="price"
        placeholder="Enter starting price"
      />
    </div>

    <!-- Custom Quote Note -->
    <div class="form-group d-none" id="customQuoteNote">
      <label>Pricing</label>
      <p class="text-muted mb-0">
        This product/service uses a <strong>custom quote</strong>. Customers must contact you directly for pricing details.
      </p>
    </div>

    <!-- Variations / Packages -->
    <div class="form-group d-none" id="variationSection">
      <label>Variations / Packages</label>
      <small class="form-text text-muted mb-2">
        Add different options (e.g., Basic, Premium) with separate prices.
      </small>

      <!-- Main container for all variations -->
      <div id="variationContainer">
        <!-- First default row -->
        <div class="d-flex mb-2 variation-item">
          <input
            type="text"
            class="form-control mr-2"
            name="variation_name[]"
            placeholder="Variation name (e.g. Basic / Deluxe)"
          />
          <input
            type="number"
            class="form-control"
            name="variation_price[]"
            placeholder="Price (₦)"
          />
          <button type="button" class="btn btn-danger btn-sm removeVariation ml-2">&times;</button>
        </div>
      </div>

      <!-- Hidden template for cloning -->
      <div id="variationTemplate" class="d-none">
        <div class="d-flex mb-2 variation-item">
          <input
            type="text"
            class="form-control mr-2"
            name="variation_name[]"
            placeholder="Variation name"
          />
          <input
            type="number"
            class="form-control"
            name="variation_price[]"
            placeholder="Price (₦)"
          />
          <button type="button" class="btn btn-danger btn-sm removeVariation ml-2">&times;</button>
        </div>
      </div>

      <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addVariationBtn">+ Add More</button>
    </div>

    <!-- Pricing Notes -->
    <div class="form-group mt-3">
      <label for="pricingNotes">Pricing Notes (Optional)</label>
      <textarea
        class="editor"
        id="pricingNotes"
        rows="2"
        name="pricingNotes"
        placeholder="Add extra details about pricing (discounts, terms, etc.)"
      ></textarea>
    </div>

  <!-- Section 3: Availability & Capacity (Applies to Both Products and Services) -->
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

      <!-- Hidden input that appears only for Limited Slot -->
<div class="form-group" id="slotField" style="display:none;">
  <label for="available_slots">Number of Slots Available</label>
  <input type="number" class="form-control" id="available_slots" name="available_slots" min="1" placeholder="e.g. 10">
</div>

      <div class="form-group">
        <label for="capacity">Capacity/Volume (Optional)</label>
        <input type="text" class="form-control" id="capacity" name="capacity"
               placeholder="Enter capacity, stock or service volume" />
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
            <input class="form-check-input" type="radio" name="delivery" id="depends" value="Depends on Location">
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
      <input type="hidden" name="user" id="user" value="<?php echo $buyerId; ?>">
    </div>
  </div>

  <!-- Section 4: Media & Files -->
  <div class="card mt-4">
    <div class="card-header">
      <div class="card-title">Section 4: Media & Files</div>
    </div>
    <div class="card-body">
      <div class="form-group">
  <label for="productImages"><strong>Upload Product Images</strong></label>
    <input type="file" 
           id="productImages" 
           name="productImages[]" 
           multiple 
           accept="image/*" 
           class="form-control">

    <small id="imageCountText" class="text-muted"></small>
      
      </div>

      
  <div class="form-group">
    <label for="videos"><strong>Upload Videos (Optional)</strong></label>
    <input type="file" 
           id="videos" 
           name="videos[]" 
           multiple 
           accept="video/*" 
           class="form-control">
      </div>
                 <div class="form-group">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>

      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>