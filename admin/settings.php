
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
                        <textarea name="site_description" class="editor"><?php echo $sitedescription; ?></textarea>
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
                    
                    <!-- Email Configuration -->
                    <div class="card mb-3">
                      <div class="card-header bg-info text-white">
                        <h5 class="mb-0">📧 Email Configuration (Brevo API)</h5>
                      </div>
                      <div class="card-body">
                        <div class="alert alert-info">
                          <strong>ℹ️ Brevo Setup:</strong> Get your API key from <a href="https://www.brevo.com" target="_blank">Brevo.com</a> (formerly Sendinblue). 
                          This is required for sending transactional emails like payment confirmations, registration emails, etc.
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Brevo API Key <span class="text-danger">*</span></label>
                          <input type="text" name="brevo_key" class="form-control" 
                                 value="<?php echo $brevokey ?? ''; ?>" 
                                 placeholder="xkeysib-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                          <small class="text-muted">Enter your Brevo API key to enable email sending</small>
                        </div>
                      </div>
                    </div>

                    <!-- VPay Payment Configuration -->
                    <div class="card mb-3">
                      <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">💳 VPay Payment Configuration</h5>
                      </div>
                      <div class="card-body">
                        <div class="alert alert-info">
                          <strong>ℹ️ VPay Setup:</strong> Get your API keys from <a href="https://vpay.africa" target="_blank">vpay.africa</a>. 
                          Use <strong>sandbox</strong> for testing, then switch to <strong>live</strong> for production.
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Payment Provider</label>
                          <select name="payment_provider" class="form-control" disabled>
                            <option value="vpay" selected>VPay</option>
                          </select>
                          <small class="text-muted">System is configured to use VPay payment gateway</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">VPay Domain/Environment <span class="text-danger">*</span></label>
                          <select name="vpay_domain" class="form-control" required>
                            <option value="sandbox" <?php echo ($vpay_domain ?? 'sandbox') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Testing)</option>
                            <option value="live" <?php echo ($vpay_domain ?? 'sandbox') === 'live' ? 'selected' : ''; ?>>Live (Production)</option>
                          </select>
                          <small class="text-muted">Select <strong>sandbox</strong> for testing or <strong>live</strong> for production payments</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">VPay Test/Sandbox Public Key</label>
                          <input type="text" name="vpay_test_public_key" class="form-control" 
                                 value="<?php echo $vpay_test_key ?? ''; ?>" 
                                 placeholder="fdcdb195-6553-4890-844c-ee576b7ea715">
                          <small class="text-muted">Default test key: fdcdb195-6553-4890-844c-ee576b7ea715</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">VPay Live/Production Public Key <span class="text-danger">*</span></label>
                          <input type="text" name="vpay_live_public_key" class="form-control" 
                                 value="<?php echo $vpay_live_key ?? ''; ?>" 
                                 placeholder="Enter your live public key from VPay dashboard">
                          <small class="text-muted">⚠️ Required for production. Get from your VPay merchant dashboard.</small>
                        </div>

                        <div class="alert alert-warning">
                          <strong>🧪 Test Cards (Sandbox Only):</strong><br>
                          • <strong>Success:</strong> 5061 0201 6604 6282 | CVV: 111 | PIN: 1111<br>
                          • <strong>Decline:</strong> 5061 1604 0000 0021 | CVV: 123 | PIN: 1234
                        </div>
                      </div>
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
                      <label class="form-label">Site Address</label>
                      <textarea name="address" class="editor"><?= $site_address; ?></textarea>
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
