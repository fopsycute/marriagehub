

<?php include "header.php"; ?>  

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Category</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Edit Category</div>
          </div>
          <div class="card-body">
            <?php
if (isset($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);

    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editcategory&category_id=" . $categoryId;
    $data = curl_get_contents($url);

    if ($data !== false) {
        $plandetails = json_decode($data);
        if (!empty($plandetails)) {
            $plan = $plandetails[0];
            $category_name = $plan->category_name ?? '';
            $category_id = $plan->id ?? '';

            // Extract values
        } else {
            echo "<div class='alert alert-warning'>No category found with this ID.</div>";
            include "footer.php";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch category data.</div>";
        include "footer.php";
        exit;
    }
} else {
    header("Location: manage-category.php");
    exit;
}
?>
<form method="POST" id="admineditcategoryForm">
            <!-- Category Name -->
            <div class="form-group">
              <label for="categoryName">Name</label>
              <input type="text" class="form-control" id="categoryName" name="category_name" value="<?= $category_name ?>" placeholder="Enter category name">
            </div>
         
            <div class="form-group">
            <!-- Submit Button -->
                         <input type="hidden" name="category_id" value="<?= $category_id ?>">
            <input type="hidden" name="action" value="editcategoryadmin"> 
            <button class="btn btn-primary" type="submit" id="submitCategory">Submit Category</button>
            </div>
</form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>