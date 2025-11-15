
<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Settings</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Settings</div>
          </div>
          <div class="card-body">

            <!-- Site Name -->
            <div class="form-group">
              <label for="Site name">Site Name</label>
              <input type="text" class="form-control" id="sitename" placeholder="Enter Site Name">
            </div>


             <!-- Site keyword -->
            <div class="form-group">
              <label for="siteKeyword">Site Keyword</label>
              <input type="text" class="form-control" id="siteKeyword" placeholder="Enter Site Keyword">
            </div>


            <div class="form-group">
              <label for="paymentApiKey">Payment Api Key</label>
              <input type="text" class="form-control" id="paymentApiKey" placeholder="Enter Payment Api Key">
            </div>

                <!-- Terms / Policies -->
            <div class="form-group">
              <label for="terms">Terms</label>
              <textarea id="terms" class="form-control" rows="4" placeholder="Update Terms"></textarea>
            </div>

            <div class="form-group">
              <label for="policies">Policies</label>
              <textarea id="policies" class="form-control" rows="4" placeholder="Update Policies"></textarea>
            </div>

            <!-- Commission Fee -->
            <div class="form-group">
              <label>Vendor's commission Fee (%)</label>
              <input type="number" class="form-control" id="commissionFee" placeholder="Enter commission fee percentage">
            </div>


            <!-- Submit Button -->
            <button class="btn btn-primary mt-3"> Update</button>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>










<?php include "footer.php"; ?>
