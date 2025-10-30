
<?php include "header.php"; ?>  
  <!-- testing --->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Blog</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Blog</div>
          </div>
          <div class="card-body">
    <form  method="POST" id="addgroupblog" enctype="multipart/form-data">    
       <div class="col-lg-12 text-center mt-1" id="messages"></div> 
            <!-- Blog Title -->
            <div class="form-group">
              <label for="blogTitle">Title</label>
              <input type="text" class="form-control" id="blogTitle" placeholder="Enter blog title" name="title"> 
            </div>

            <!-- Cover Image -->
            <div class="form-group">
              <label for="blogCover">Cover Image</label>
              <input type="file" class="form-control" id="blogCover" name="featured_image" accept="image/*">
            </div>

            <!-- Content -->
            <div class="form-group">
              <label for="blogContent">Content</label>
              <textarea id="blogContent" class="editor" name="article"  placeholder="Write your blog content here..."></textarea>
            </div>
            <input type="hidden" name="action" id="action" value="addgroupforum">
            <!-- Categories -->
             <div class="row">
              <div class="col-lg-6">
            <div class="form-group">
              <label for="blogCategories">Categories</label>
              <select name="category[]" id="category" class="form-control select-multiple" required multiple>
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
              </div>

              <div class="col-lg-6">

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="blogSubcategories">Sub-Categories</label>
              <select name="subcategory[]" id="subcategory" class="form-control select-multiple" required multiple>
              <option value="">-- Select Sub-Category --</option>
                            
          </select>
            </div>
                    </div>
                    </div>

            <!-- Tags -->
            <div class="form-group">
              <label for="blogTags">Tags</label>
              <input type="text"name="tags" id="tags" class="form-control" placeholder="Add tags (comma separated)">
            </div>

            <!-- Tags -->
            <div class="form-group">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>

         
              <input type="hidden" name="user" value="<?php echo $buyerId; ?>">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary"  id="submitBtn">Submit Blog</button>
          </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>



<?php include "footer.php"; ?>