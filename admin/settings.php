
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

           <form method="POST" enctype="multipart/form-data" id="updateadminsettings">
                <div class="col-lg-12 text-center mt-1" id="messages"></div>
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo $sitename; ?>">
                    </div>
                    <input type="hidden" name="action" value="updateadmin">
                    <div class="mb-3">
                        <label class="form-label">Site Keywords</label>
                        <input type="text" name="site_keywords" class="form-control" value="<?php echo $sitekeywords; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Terms of Use</label>
                     <textarea class="editor" name="terms"></textarea>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">Privacy policy</label>
                     <textarea class="editor" name="privacy"></textarea>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Site URL</label>
                        <input type="url" name="site_url" class="form-control" value="<?php echo $siteurl; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Description</label>
                        <textarea name="site_description" class="form-control"><?php echo $sitedescription; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Logo</label>
                        <img src="<?php echo $siteurl;?>assets/img/<?php echo $siteimg; ?>" style="width: 20%; height: auto;" class="mb-2">
                        <input type="file" name="site_logo" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Email</label>
                        <input type="email" name="site_mail" class="form-control" value="<?php echo $sitemail; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site Phone Number</label>
                        <input type="text" name="site_number" class="form-control" value="<?php echo $sitenumber; ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Site Bank</label>
                      <input type="text" name="site_bank" class="form-control" value="<?php echo $site_bank; ?>"> 
                    </div>

                      <div class="mb-3">
                      <label class="form-label">Api Key</label>
                      <input type="text" name="paystack_key" class="form-control" value="<?php echo $apikey; ?>"> 
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Site Account Number</label>
                      <input type="text" name="account_number" class="form-control" value="<?php echo $siteaccno; ?>">
                    </div>
                   
                    
                    <div class="mb-3">
                      <label class="form-label">Commission Fee</label>
                      <input type="number" name="com_fee" class="form-control" value="<?php echo $escrowfee; ?>">
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Site Account Name</label>
                      <input type="text" name="account_name" class="form-control" value="<?php echo $siteaccname; ?>">
                    </div>

                       <div class="mb-3">
                      <label class="form-label">Seller's Minimum Withdrawal</label>
                      <input type="text" name="minimum_withdrawal" class="form-control" value="<?php echo $minimum_withdrawal; ?>">
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Google Map</label>
                      <input type="text" name="google_map" class="form-control" value="<?php echo $google_map; ?>">
                    </div>
                          <div class="mb-3">
                      <label class="form-label">TinyMCE key</label>
                      <input type="text" name="tinymce" class="form-control" value="<?php echo $tinymce; ?>">
                    </div>
                    <button type="submit" id="submitBtn" name="settings" value="course" class="btn btn-primary w-100">Update Settings</button>
                    </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>










<?php include "footer.php"; ?>
