
<?php include "header.php"; ?>  

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Question</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Add Question</div>
          </div>
          <div class="card-body">
<form method="POST" id="addQuestions" enctype="multipart/form-data">
        <div class="row">
          <div class="col-lg-12 text-center mt-1" id="messages"></div>

          <!-- Question Heading -->
          <div class="col-12 mb-4">
            <h4 class="fw-bold text-primary">What is your question?</h4>
          </div>

          <!-- Featured Image -->

          <!-- Title -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="title">Title:</label>
            <input 
              placeholder="Enter the title of your question" 
              id="title" 
              type="text" 
              class="form-control" 
              name="title" 
              required
            >
          </div>

          <!-- Body of Question -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="article">Body of Question:</label>
            <textarea 
              id="article" 
              name="article" 
              class="editor" 
              placeholder="Describe your question in detail..." 
            ></textarea>
          </div>

          <input type="hidden" name="user" value="<?php echo $buyerId; ?>" >
          <!-- Categories and Sub-Categories -->
          <div class="row mb-3">
            <!-- Category -->
            <div class="col-md-6">
              <label class="form-label">Categories:</label>
              <select 
                name="category[]" 
                id="category" 
                class="form-select select-multiple" 
                required 
                multiple
              >
                <option value="">-- Select Category --</option>
                <?php
                  $url = $siteurl . "script/register.php?action=categorieslists";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                    $categories = json_decode($data);
                    if (!empty($categories)) {
                      foreach ($categories as $category) {
                        $categoryId = $category->id;
                        $name = htmlspecialchars($category->category_name);
                        echo "<option value='{$categoryId}'>{$name}</option>";
                      }
                    }
                  } else {
                    echo "<option disabled>Error fetching categories</option>";
                  }
                ?>
              </select>
            </div>
                    <input type="hidden" name="action" value="createQuestion">
            <!-- Sub-Category -->
            <div class="col-md-6">
              <label class="form-label">Sub-Categories:</label>
              <select 
                name="subcategory[]" 
                id="subcategory" 
                class="form-select select-multiple" 
                required 
                multiple
              >
                <option value="">-- Select Sub-Category --</option>
              </select>
            </div>
          </div>

          <!-- Tags -->
          <div class="col-sm-12 mb-3">
            <label class="form-label" for="tags">Tags:</label>
            <input 
              placeholder="Enter tags separated by commas (e.g. PHP, JavaScript, HTML)" 
              id="tags" 
              type="text" 
              class="form-control" 
              name="tags"
            >
          </div>
          
            <div class="form-group mb-2">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
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
                >
                <label class="form-check-label" for="anonymous">
                  Post Anonymously (Hide my name publicly)
                </label>
              </div>
            </div>

          <!-- Submit Button -->
          <div class="col-sm-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5" >Submit Question</button>
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