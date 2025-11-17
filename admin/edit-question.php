



<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Question</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Question Details</div>
          </div>
          <div class="card-body">
        <?php

if (isset($_GET['question_id'])) {
    $questionId = $_GET['question_id'];

    // Build API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "user.php?action=editquest&question_id=" . $questionId;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0];

            // Extract and sanitize details
            $id = $blogdetail->id ?? '';
            $user_id = $blogdetail->user_id ?? '';
            $group_id = $blogdetail->group_id ?? '';
            $title = htmlspecialchars($blogdetail->title ?? '', ENT_QUOTES);
            $slug = htmlspecialchars($blogdetail->slug ?? '', ENT_QUOTES);
            $article = $blogdetail->article ?? '';
             $categories_selected = !empty($blogdetail->categories) ? explode(',', $blogdetail->categories) : [];
            $subcategories_selected = !empty($blogdetail->subcategories) ? explode(',', $blogdetail->subcategories) : [];
            $tags = $blogdetail->tags ?? '';
            $status = $blogdetail->status ?? '';
            $anonymous = $blogdetail->anonymous ?? '';
            $views = $blogdetail->views ?? 0;

            $created_at = $blogdetail->created_at ?? '';
            $author = trim(($blogdetail->first_name ?? '') . ' ' . ($blogdetail->last_name ?? ''));
            $featured_image = $blogdetail->featured_image ?? '';

            // Handle image path
            $blogimage = !empty($featured_image)
                ? $siteurl . $imagePath . $featured_image
                : $siteurl . "assets/img/default-blog.jpg";

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

    <form method="POST" id="editQuestions" enctype="multipart/form-data">
  <div class="row">
    <div class="col-lg-12 text-center mt-1" id="messages"></div>

    <!-- Question Heading -->
    <div class="col-12 mb-4">
      <h4 class="fw-bold text-primary">Edit Your Question</h4>
    </div>

    <!-- Title -->
    <div class="col-sm-12 mb-3">
      <label class="form-label" for="title">Title:</label>
      <input 
        placeholder="Enter the title of your question" 
        id="title" 
        type="text" 
        class="form-control" 
        name="title" 
        value="<?php echo $title; ?>" 
        required
      >
    </div>

    <!-- Body of Question -->
    <div class="col-sm-12 mb-3">
      <label class="form-label" for="article">Body of Question:</label>
      <textarea 
        id="article" 
        name="article" 
        rows="5" 
        class="form-control" 
        placeholder="Describe your question in detail..." 
        required
      ><?php echo $article; ?></textarea>
    </div>

    <input type="hidden" name="user" value="<?php echo $buyerId; ?>">
    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">
    <input type="hidden" name="action" value="updateQuestion">

    <!-- Categories and Sub-Categories -->
    <div class="row mb-3">
      <!-- Category -->
      <div class="col-md-6">
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
      </div>

      <!-- Sub-Category -->
      <div class="col-md-6">
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
    </div>
        </div>
    <input type="hidden" name="question_id" value="<?php echo $questionId; ?>">
    <!-- Tags -->
    <div class="col-sm-12 mb-3">
      <label class="form-label" for="tags">Tags:</label>
      <input 
        placeholder="Enter tags separated by commas (e.g. PHP, JavaScript, HTML)" 
        id="tags" 
        type="text" 
        class="form-control" 
        name="tags" 
        value="<?php echo htmlspecialchars($tags ?? ''); ?>"
      >
    </div>

    <!-- Anonymous Option -->
    <div class="col-sm-12 mb-3">
      <div class="form-check">
        <input 
          class="form-check-input" 
          type="checkbox" 
          id="anonymous" 
          name="anonymous" 
          value="1" 
          <?php if (!empty($anonymous) && $anonymous == '1') echo 'checked'; ?>
        >
        <label class="form-check-label" for="anonymous">
          Post Anonymously (Hide my name publicly)
        </label>
      </div>
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
    <div class="col-sm-12 text-center mt-4">
      <button type="submit" class="btn btn-primary px-5">Update Question</button>
    </div>

  </div>
</form>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

<?php include "footer.php"; ?>