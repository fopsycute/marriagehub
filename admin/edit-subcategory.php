<?php include "header.php"; ?>  

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Subcategory</h3>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Edit Subcategory</div>
          </div>
          <div class="card-body">

<?php
if (isset($_GET['subcategory_id'])) {
    $subcategory_id = intval($_GET['subcategory_id']);
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editsubcategory&subcategory_id=" . $subcategory_id;
    $data = curl_get_contents($url);

    if ($data !== false) {
        $plandetails = json_decode($data, true);

        if (!empty($plandetails)) {
            $plan = $plandetails[0];
            $category_name = $plan['category_name'] ?? '';
            $parent_id = $plan['parent_id'] ?? '';
        } else {
            echo "<div class='alert alert-warning'>No subcategory found with this ID.</div>";
            include "footer.php";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch subcategory data.</div>";
        include "footer.php";
        exit;
    }
} else {
    header("Location: manage-sub-category.php");
    exit;
}
?>

<form method="POST" id="adminEditSubCategoryForm">

  <!-- Parent Category -->
  <div class="form-group mb-3">
    <label for="parentId">Select Parent Category</label>
    <select class="form-control" id="parentId" name="parentId" required>
      <option value="">Select Category</option>
      <?php
      $url = $siteurl . "script/admin.php?action=categorylists";
      $data = curl_get_contents($url);

      if ($data !== false) {
          $cats = json_decode($data);
          if (!empty($cats)) {
              foreach ($cats as $cat) {
                  $selected = ($cat->id == $parent_id) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($cat->id) . '" ' . $selected . '>' .
                        htmlspecialchars($cat->category_name) . '</option>';
              }
          } else {
              echo '<option value="">No category found</option>';
          }
      } else {
          echo '<option value="">Error fetching categories</option>';
      }
      ?>
    </select>
  </div>

  <!-- Subcategory Name -->
  <div class="form-group mb-3">
    <label for="subCategoryName">Subcategory Name</label>
    <input 
      type="text" 
      class="form-control" 
      id="subCategoryName" 
      name="subCategoryName" 
      value="<?= htmlspecialchars($category_name) ?>" 
      placeholder="Enter subcategory name" 
      required
    >
  </div>

  <input type="hidden" name="subcategory_id" value="<?= htmlspecialchars($subcategory_id) ?>">
  <input type="hidden" name="action" value="editsubcategoryadmin">

  <button class="btn btn-primary" type="submit" id="submitCategory">Update Subcategory</button>
</form>

</script>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
