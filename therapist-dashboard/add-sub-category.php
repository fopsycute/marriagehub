

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

            <!-- Category Name -->
            <div class="form-group mb-3">
      <label for="categoryName">Category Name</label>
      <select class="form-control" id="categoryName">
        <option value="">-- Select Category --</option>
        <option value="Marriage">Marriage</option>
        <option value="Parenting">Parenting</option>
        <option value="Faith">Faith</option>
        <option value="Health">Health</option>
        <option value="Finance">Finance</option>
      </select>
    </div>

    <!-- Subcategory Name (Text Input) -->
    <div class="form-group mb-3">
      <label for="subcategoryName">Subcategory Name</label>
      <input type="text" class="form-control" id="subcategoryName" placeholder="Enter subcategory name">
    </div>

    <!-- Submit Button -->
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Save Subcategory</button>
    </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>