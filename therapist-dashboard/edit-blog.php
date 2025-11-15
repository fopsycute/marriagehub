<?php include "header.php"; ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Blog Post</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Blog</div>
          </div>
          <div class="card-body">

          <?php
if (isset($_GET['blog_id'])) {
    $blogId = $_GET['blog_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=editblog&blog_id=" . $blogId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 

            // Extract details
            $title = $blogdetail->title ?? '';
            $article = $blogdetail->article ?? '';
            $tags = $blogdetail->tags ?? '';
            $featured_image = $blogdetail->featured_image ?? '';
            // Convert the blog’s stored category/subcategory values into arrays
            $categories_selected = !empty($blogdetail->categories) ? explode(',', $blogdetail->categories) : [];
            $subcategories_selected = !empty($blogdetail->subcategories) ? explode(',', $blogdetail->subcategories) : [];
            $status = $blogdetail->status ?? '';
            $created_at = $blogdetail->created_at ?? '';
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));

              $blogimage = $siteurl . $imagePath . $featured_image;
        } else {
            echo "<div class='alert alert-warning'>No blog found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching blog data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

?>
 <form  method="POST" id="editForum" enctype="multipart/form-data">
     <div class="col-lg-12 text-center mt-1" id="messages"></div> 
            <!-- Blog Title -->
            <div class="form-group">
              <label for="blogTitle">Title</label>
              <input type="text" class="form-control" id="blogTitle" name="blogTitle" placeholder="Enter blog title" value="<?php echo $title; ?>" >
            </div>

            <!-- Cover Image -->
            <div class="form-group">
              <label for="blogCover">Cover Image</label><img src="<?php echo $blogimage; ?>" class="small-image">
              <input type="file" class="form-control" id="blogCover" name="blogCover" accept="image/*">
            </div>

            <!-- Content -->
            <div class="form-group">
              <label for="blogContent">Content</label>
              <textarea id="blogContent" name="blogContent" class="editor" placeholder="Write your blog content here..."><?php echo $article; ?></textarea>
            </div>

            <!-- Categories -->
            <!-- Categories -->
      <div class="form-group">
        <label for="category">Categories</label>
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


      <!-- Sub-Categories -->
      <div class="form-group">
        <label for="subcategory">Sub-Categories</label>
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

       <input type="hidden" name="blog_id" value="<?php echo $blogId; ?>">
    <input type="hidden" name="action" value="updateblog">
            <!-- Tags -->
            <div class="form-group">
              <label for="blogTags">Tags</label>
              <input type="text" id="blogTags" name="blogTags" class="form-control" placeholder="Add tags (comma separated)" value="<?php echo $tags; ?>">
            </div>

          <div class="form-group">
          <label for="status">Status</label>
          <select name="status" class="form-control" required>
            <option value="">Select Status</option>
            <option value="active" <?php echo ($status === 'active') ? 'selected' : ''; ?>>Published</option>
            <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
          </select>
        </div>
         

            <!-- Submit Button -->
            <button class="btn btn-primary"  id="submitBtn">Submit Blog</button>
</form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>