

<?php 
$requireLogin = false;
include 'header.php'; 

?>

<main class="">
    <section>
    <div class="container d-flex justify-content-center">
    <div class="login-box w-100 py-4">
        <h2 class="text-center mb-4">Reset Password</h2>
        <form method="POST" id="resetPasswordForm">
    <div class="mb-3">
        <label for="password" class="form-label">Enter your new password</label>
        <input type="hidden" name="action" value="reset-link" id="action">
      <div class="input-group">
	<input type="password" class="form-control" id="password" name="password" placeholder="New password" required>
	<div class="input-group-append">
	<span class="input-group-text p-3" onclick="togglePasswordVisibility('password')">
	<i class="bi bi-eye" id="togglePasswordIcon"></i>
	</span>
	</div>
	</div>
    </div>

    <div class="mb-3">
        <label for="confirm_password" class="form-label">Retype your new password</label>
        <div class="input-group">
	<input type="password" class="form-control" id="retypePassword" name="retypePassword" placeholder="Retype new password" required>
	<div class="input-group-append">
	<span class="input-group-text p-3" onclick="togglePasswordVisibility('retypePassword')">
	 <i class="bi bi-eye" id="toggleRetypePasswordIcon"></i>
	</span>
	</div>
	</div>
    </div>
    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
</form>
      </div>  
    </div>  
</section>
</main>
  
<?php include 'footer.php'; ?>
