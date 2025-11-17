
<?php include "header.php"; ?>  

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Category</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Category</div>
          </div>
          <div class="card-body">
    <form method="POST" id="addcategory">
       <div class="text-center mt-1" id="messages"></div> 
            <!-- Category Name -->
            <div class="form-group">
              <label for="categoryName">Name</label>
              <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter category name">
            </div>
         <input type="hidden" name="action" value="addcategory">
            <div class="form-group">
            <!-- Submit Button -->
            <button type="submit" id="submitcategory" class="btn btn-primary">Submit Category</button>
            </div>
    </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>