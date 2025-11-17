

<?php include "header.php"; ?>  

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Subategory</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Subcategory</div>
          </div>
          <div class="card-body">
<form method="POST" id="addsubcategory">
   <div class="text-center mt-1" id="messages"></div> 
    <input type="hidden" name="action" value="addsubcategory">
            <!-- Category Name -->
            <div class="form-group mb-3">
      <label for="categoryName">Category Name</label>
      <select class="form-control" id="parentId" name="parentId" required>
                 <?php
                        $url = $siteurl . "script/admin.php?action=categorylists";
                        $data = curl_get_contents($url);

                        if ($data !== false) {
                          $cats = json_decode($data);
                          if (!empty($cats)) {
                            foreach ($cats as $cat) {
                              echo '<option value="' . $cat->id . '">' .
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

    <!-- Subcategory Name (Text Input) -->
    <div class="form-group mb-3">
      <label for="subcategoryName">Subcategory Name</label>
      <input  type="text" class="form-control" id="subCategoryName" name="subCategoryName" placeholder="Enter sub-category name" required>
    </div>

    <!-- Submit Button -->
    <div class="form-group">
      <button type="submit" id="submitcategory" class="btn btn-primary">Save Subcategory</button>
    </div>
          </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>