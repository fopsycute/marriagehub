<?php include "header.php"; ?>
<div class="container">
<div class="page-inner">
    <div class="page-header">
<h3 class="fw-bold mb-3">Edit My Products & Services</h3>
    </div>

<?php
$listingId = isset($_GET['listing_id']) ? $_GET['listing_id'] : '';
$title = $description = $type = $pricing_type = $price = $price_min = $price_max = $pricing_notes = '';
$availability = $limited_slot = $capacity = $delivery = $status = $coverage = '';
$images = $videos = $variations = $categories_selected = $subcategories_selected = [];

if (!empty($listingId)) {
    $url = $siteurl . "script/admin.php?action=editlist&listing_id=" .$listingId;
    $data = curl_get_contents($url);
    $response = json_decode($data, true);

    if (!empty($response['data'])) {
        $listdetail = $response['data'];

        $title = $listdetail['title'] ?? '';
        $description = $listdetail['description'] ?? '';
        $type = $listdetail['type'] ?? '';
        $pricing_type = $listdetail['pricing_type'] ?? '';
        $price = $listdetail['price'] ?? '';
        $price_min = $listdetail['price_min'] ?? '';
        $price_max = $listdetail['price_max'] ?? '';
        $pricing_notes = $listdetail['pricing_notes'] ?? '';
        $availability = $listdetail['availability'] ?? '';
        $limited_slot = $listdetail['limited_slot'] ?? '';
        $capacity = $listdetail['capacity'] ?? '';
        $delivery = $listdetail['delivery'] ?? '';
        $status = $listdetail['status'] ?? '';
        $coverage = $listdetail['coverage'] ?? '';
        $suspendReason = $listdetail['suspend_reason'] ?? '';
        $categories_selected = !empty($listdetail['categories']) ? explode(',', $listdetail['categories']) : [];
        $subcategories_selected = !empty($listdetail['subcategories']) ? explode(',', $listdetail['subcategories']) : [];
        $variations = $listdetail['variations'] ?? [];
        $images = $listdetail['images'] ?? [];
        $videos = $listdetail['videos'] ?? [];
    } else {
        echo "<div class='alert alert-warning'>Listing not found.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Listing ID is missing.</div>";
}
?>

<form method="POST" id="editlistingForm" enctype="multipart/form-data">
  <div class="text-center mt-1" id="messages"></div>

  <!-- TITLE -->
  <div class="form-group">
    <label for="listingTitle">Listing Title</label>
    <input type="text" class="form-control" id="listingTitle" name="listingTitle"
           value="<?= htmlspecialchars($title) ?>" required>
  </div>

  <input type="hidden" name="listing_id" value="<?= $listingId ?>">
  <input type="hidden" name="action" value="edit_listing">

  <!-- DESCRIPTION -->
  <div class="form-group">
    <label for="description">Description</label>
    <textarea class="editor" name="description" ><?= $description ?></textarea>
  </div>

  <!-- TYPE -->
  <div class="form-group">
    <label>Type</label><br>
    <?php
    $types = ['Product', 'Service'];
    foreach ($types as $t) {
        $checked = ($type === $t) ? 'checked' : '';
        echo "<label class='mr-3'><input type='radio' name='itemType' value='$t' $checked> $t</label>";
    }
    ?>
  </div>
<input type="hidden" name="user" value="<?php echo $buyerId; ?> " >
  <!-- PRICING -->
  <div class="form-group">
    <label>Pricing Type</label>
    <select class="form-control" name="pricingType">
      <option value="Starting Price" <?= ($pricing_type == 'Starting Price') ? 'selected' : '' ?>>Starting Price</option>
      <option value="Price Range" <?= ($pricing_type == 'Price Range') ? 'selected' : '' ?>>Price Range</option>
      <option value="Custom Quote" <?= ($pricing_type == 'Custom Quote') ? 'selected' : '' ?>>Custom Quote</option>
    </select>
  </div>

  <?php if ($pricing_type == 'Price Range' && !empty($variations)) { ?>
    <div class="form-group">
      <label>Existing Variations</label>
      <?php foreach ($variations as $var) { ?>
        <div class="d-flex mb-2">
          <input type="text" class="form-control mr-2" name="variation_name[]" value="<?= htmlspecialchars($var['variation_name']) ?>">
          <input type="number" class="form-control" name="variation_price[]" value="<?= htmlspecialchars($var['variation_price']) ?>">
        </div>
      <?php } ?>
    </div>
  <?php } ?>

  <!-- PRICE -->
  <div class="form-group">
    <label for="price">Price (₦)</label>
    <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price) ?>">
  </div>

  <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Category</label>
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
              <div class="col-md-6">
                <label class="form-label">Sub-Category</label>
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
  <!-- AVAILABILITY -->
  <div class="form-group">
    <label>Availability</label>
    <select class="form-control" name="availability">
      <option value="Always Available" <?= ($availability == 'Always Available') ? 'selected' : '' ?>>Always Available</option>
      <option value="By Appointment" <?= ($availability == 'By Appointment') ? 'selected' : '' ?>>By Appointment</option>
      <option value="Limited Slot" <?= ($availability == 'Limited Slot') ? 'selected' : '' ?>>Limited Slot</option>
    </select>
  </div>

  <?php if ($availability == 'Limited Slot') { ?>
    <div class="form-group">
      <label>Slots Available</label>
      <input type="number" class="form-control" name="available_slots" value="<?= htmlspecialchars($limited_slot) ?>">
    </div>
  <?php } ?>

  <!-- DELIVERY -->
  <div class="form-group">
    <label>Delivery Option</label><br>
    <?php
    $deliveryOptions = ['Yes', 'No', 'Depends on Location'];
    foreach ($deliveryOptions as $opt) {
        $checked = ($delivery == $opt) ? 'checked' : '';
        echo "<label class='mr-3'><input type='radio' name='delivery' value='$opt' $checked> $opt</label>";
    }
    ?>
  </div>

  <!-- COVERAGE -->
  <div class="form-group">
    <label>Service Coverage</label><br>
    <?php
    $coverageOptions = ['Local', 'Statewide', 'Nationwide', 'International'];
    foreach ($coverageOptions as $c) {
        $checked = (strpos($coverage, $c) !== false) ? 'checked' : '';
        echo "<label class='mr-3'><input type='checkbox' name='coverage[]' value='$c' $checked> $c</label>";
    }
    ?>
  </div>

  <!-- EXISTING IMAGES -->
<div class="form-group">
  <label>Existing Images</label><br>
  <div class="d-flex flex-wrap">
    <?php foreach ($images as $img) { ?>
      <div class="position-relative m-1" style="width:120px;height:120px;">
        <img src="<?php echo $siteurl; ?>uploads/<?= $img ?>" class="img-thumbnail" style="width:100%;height:100%;object-fit:cover;">
        <a href="#" id="<?= htmlspecialchars($img) ?>" class="btn btn-danger btn-sm deleteimage" 
           style="position:absolute;top:2px;right:2px;"><i class="fa fa-trash"></i></a>
      </div>
    <?php } ?>
  </div>
</div>


  <!-- UPLOAD NEW IMAGES -->
  <div class="form-group">
    <label><strong>Upload New Images</strong></label>
    <input type="file" name="productImages[]" multiple accept="image/*" class="form-control">
    <small class="text-muted">You can upload multiple images.</small>
  </div>

 <!-- EXISTING VIDEOS -->
  <div class="form-group">
  <label>Existing Videos</label><br>
  <div class="d-flex flex-wrap">
    <?php foreach ($videos as $vid) { ?>
      <div class="position-relative m-1" style="width:180px;height:100px;">
        <video src="<?php echo $siteurl; ?>uploads/<?= $vid ?>" class="img-thumbnail" 
               style="width:100%;height:100%;object-fit:cover;" controls></video>
        <a href="#" id="<?= htmlspecialchars($vid) ?>" class="btn btn-danger btn-sm deletevideo" 
           style="position:absolute;top:2px;right:2px;"><i class="fa fa-trash"></i></a>
      </div>
    <?php } ?>
  </div>
</div>

  <!-- UPLOAD NEW VIDEOS -->
  <div class="form-group">
    <label><strong>Upload New Videos</strong></label>
    <input type="file" name="videos[]" multiple accept="video/*" class="form-control">
    <small class="text-muted">Max 50MB per video.</small>
  </div>


    <input type="hidden" id="imageLimit" value="<?= htmlspecialchars(getFeatureLimit($con, $buyerId, 'images', $siteprefix)) ?>">
      <input type="hidden" id="videoLimit" value="<?= htmlspecialchars(getFeatureLimit($con, $buyerId, 'videos', $siteprefix)) ?>">
  <!-- STATUS -->
 
  <div class="form-group">
    <label>Status</label>
      <?php if ($status !== 'suspended') { ?>
    <select name="status" class="form-control">
      <option value="active" <?= ($status == 'active') ? 'selected' : '' ?>>Published</option>
      <option value="pending" <?= ($status == 'pending') ? 'selected' : '' ?>>Pending</option>
    </select>
    <?php }  else { ?>
      <input type="hidden" name="status" value="<?php echo $status; ?>">
      <span class="badge bg-danger">Suspended</span>
    <?php } ?>
  </div>

  <button type="submit" class="btn btn-primary mt-3" id="submitListing">Update Listing</button>
</form>

<?php include "footer.php"; ?>
