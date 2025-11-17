<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Submit Articles & Questions</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <!-- Choice -->
        <div class="card mb-4">
          <div class="card-body">
            <label class="fw-bold">Do you want to submit a Question or an Article?</label><br>
            <label class="form-check mr-3">
              <input type="radio" name="submitType" value="question"> Question
            </label>
            <label class="form-check">
              <input type="radio" name="submitType" value="article"> Article
            </label>
          </div>
        </div>

        <!-- Question Section -->
        <div class="card mb-4">
          <div class="card-header"><div class="card-title">Submit a Question</div></div>
          <div class="card-body">
            <div class="form-group">
              <label for="questionTitle">Title</label>
              <input type="text" id="questionTitle" class="form-control" placeholder="Enter your question title">
            </div>
            <div class="form-group">
              <label for="questionBody">Body of Question</label>
              <textarea id="questionBody" class="form-control" rows="4" placeholder="Write your full question here..."></textarea>
            </div>
            <div class="form-group">
              <label for="questionCategories">Categories and Sub-Categories</label>
              <select id="questionCategories" class="form-control select2-multiple" multiple>
                <option>Marriage</option>
                <option>Parenting</option>
                <option>Faith</option>
                <option>Youth</option>
                <option>Health</option>
                <option>Finance</option>
              </select>
            </div>
            <div class="form-group">
              <label for="questionTags">Tags</label>
              <input type="text" id="questionTags" class="form-control" placeholder="Add tags (comma separated)">
            </div>
            <button class="btn btn-primary">Submit Question</button>
          </div>
        </div>

        <!-- Article Section -->
        <div class="card">
          <div class="card-header"><div class="card-title">Submit an Article</div></div>
          <div class="card-body">
            <div class="form-group">
              <label for="articleTitle">Title</label>
              <input type="text" id="articleTitle" class="form-control" placeholder="Enter your article title">
            </div>
            <div class="form-group">
              <label for="articleBody">Body of Article</label>
              <textarea id="articleBody" class="form-control" rows="6" placeholder="Write your full article here..."></textarea>
            </div>
            <div class="form-group">
              <label for="articleCategories">Categories and Sub-Categories</label>
              <select id="articleCategories" class="form-control select2-multiple" multiple>
                <option>Marriage</option>
                <option>Parenting</option>
                <option>Faith</option>
                <option>Youth</option>
                <option>Health</option>
                <option>Finance</option>
              </select>
            </div>
            <div class="form-group">
              <label for="articleTags">Tags</label>
              <input type="text" id="articleTags" class="form-control" placeholder="Add tags (comma separated)">
            </div>
            <button class="btn btn-success">Submit Article</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>











<?php include "footer.php"; ?>