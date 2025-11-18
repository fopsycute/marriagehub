

<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Adverts</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Adverts</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit Adverts</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
            <?php
if (isset($_GET['advert_id'])) {
    $advertId = intval($_GET['advert_id']);

    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editadverts&advert_id=" . $advertId;
    $data = curl_get_contents($url);

    if ($data !== false) {
        $advertetails = json_decode($data);
        if (!empty($advertetails)) {
            $advert = $advertetails[0];
                $advertId = $advert->id;
                $title = $advert->placement_name;
                $size = $advert->size;
                $price_per_day = $advert->price_per_day;
                $status = $advert->status;
                $description = $advert->description;

            // Extract values
        } else {
            echo "<div class='alert alert-warning'>No advert found with this ID.</div>";
            include "footer.php";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch advert data.</div>";
        include "footer.php";
        exit;
    }
} else {
    header("Location: manage-advert.php");
    exit;
}
?>
        
   <form method="post" id="editadvertsForm">
    <div class="text-center mt-1" id="messages"></div> 

    <!-- Ad Placement Name -->
    <div class="form-group">
        <label for="placement_name">Ad Placement Name</label>
        <input type="text" id="placement_name" name="placement_name" class="form-control" 
               placeholder="e.g. Home Page - Top Banner" value="<?php echo htmlspecialchars($title); ?>" required>
    </div>

    <!-- Ad Size -->
    <div class="form-group">
        <label for="size">Ad Size (Pixels)</label>
        <input type="text" id="size" name="size" class="form-control" 
               placeholder="e.g. 728x90" value="<?php echo htmlspecialchars($size); ?>" required>
    </div>
    <input type="hidden" name="adver_id" value="<?php echo htmlspecialchars($advertId); ?>">
    <input type="hidden" name="action" value="editplacement">

    <!-- Price Per Day -->
    <div class="form-group">
        <label for="price">Price Per Day (₦)</label>
        <input type="number" id="price" name="price" class="form-control" 
               placeholder="Enter price per day" value="<?php echo htmlspecialchars($price_per_day); ?>" required>
    </div>


    <!-- Description -->
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="editor" 
                  placeholder="Enter a brief description of the ad placement" rows="4"><?php echo $description; ?></textarea>
    </div>

    <!-- Status -->
    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control" required>
            <option value="active"<?php if ($status === 'active') echo ' selected'; ?>>Active</option>
            <option value="inactive"<?php if ($status === 'inactive') echo ' selected'; ?>>Inactive</option>
        </select>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <button type="submit" id="submitAdverts" class="btn btn-primary">Save Ad Placement</button>
    </div>

</form>


      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>