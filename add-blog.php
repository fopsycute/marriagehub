
<?php
$requireLogin = true;
include "header.php"; 
?>


<section>
          <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <div class="section-title-container d-flex align-items-center justify-content-between">
          <h2>Add Blog</h2>
          <a href="<?php echo $siteurl; ?>my-blog.php" class="btn btn-primary btn-sm"> <i class="bi bi-arrow-left"></i> Back to My Blogs</a>
        </div>
      </div><!-- End Section Title -->


<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
   <form  method="POST" id="addForum" enctype="multipart/form-data">
   
   <div class="row">
    <div class="col-lg-12 text-center mt-1" id="messages"></div> 
       
    <div class="col-sm-12">  
    <div class="form-group mb-3">
    <label class="form-label" for="featured image">Attach Featured Image</label>
    <input class="form-control" type="file"  name="featured_image" required  accept="image/*">
    </div></div>
    
   <div class="col-sm-12">
   <div class="form-group mb-3">
   <label class="form-label" for="Title">Title</label>
   <input placeholder="Enter Title" id="title" type="text" class="form-control" name="title" required>
   </div></div>
    <div class="row mb-3">
                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
             <select name="category[]" id="category" class="form-select select-multiple" required multiple>
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

<!-- Sub-Category -->
<div class="col-md-6">
<label class="form-label">Sub-Category *</label>
<select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>
<option value="">-- Select Sub-Category --</option>
                   
</select>
</div>
</div>
<input type="hidden" name="action" id="action" value="addforum">
<div class="col-sm-12">
   <div class="form-group">
    <label class="form-label" for="editor">Article Content</label>
    <textarea  class="editor" name="article"></textarea>
   </div></div>

   <div class="col-sm-12">
   <div class="form-group mb-2">
    <label class="form-label" for="tags">Tags</label>
    <input type="text" name="tags" id="tags" class="form-control">
   </div></div>
   <!---

    <div class="form-group mb-2">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>


            --->

  <input type="hidden" name="user" value="<?php echo $buyerId; ?>">
  <div class="col-lg-12 col-md-12 col-sm-12">
  <button type="submit" id="submitBtn"  class="btn btn-primary w-100" name="addforum">Create</button>
  </div></div>
</form></div></div></div>
</section>

<?php include "footer.php"; ?>