

<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Add Adverts</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Adverts</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Add Adverts</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        
   <form method="post" id="advertsForm">
    <div class="text-center mt-1" id="messages"></div> 

    <!-- Ad Placement Name -->
    <div class="form-group">
        <label for="placement_name">Ad Placement Name</label>
        <input type="text" id="placement_name" name="placement_name" class="form-control" 
               placeholder="e.g. Home Page - Top Banner" required>
    </div>

    <!-- Ad Size -->
    <div class="form-group">
        <label for="size">Ad Size (Pixels)</label>
        <input type="text" id="size" name="size" class="form-control" 
               placeholder="e.g. 728x90" required>
    </div>

    <!-- Price Per Day -->
    <div class="form-group">
        <label for="price">Price Per Day (₦)</label>
        <input type="number" id="price" name="price" class="form-control" 
               placeholder="Enter price per day" required>
    </div>
    <input type="hidden" name="action" value="addplacement">

    <!-- Description -->
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="editor" 
                  placeholder="Enter a brief description of the ad placement" rows="4"></textarea>
    </div>

    <!-- Status -->
    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control" required>
            <option value="active">active</option>
            <option value="inactive">inactive</option>
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