
<?php 

$requireLogin = false;
include 'header.php'; 


?>
<?php 
if($activeLog == 0){
$buyerEmail='';

}

?>
<main class="">
    <section>
    <div class="container d-flex justify-content-center">
    <div class="login-box w-100 py-4">
        <h2 class="text-center mb-4">Forgot Your Password?</h2>
        <p class="text-center text-muted mb-4">
          Don’t worry! Enter your registered email address below, and we’ll send you a link to reset your password.
        </p>
        <form id="passwordforgotten">
            <div id="errorMessages"></div>
            <div class="mb-3">
                <label for="username" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="username" name="email" value="<?php echo $buyerEmail; ?>" placeholder="Enter your email" required>
            </div> 
            <input type="hidden" name="action" value="sendresetlink">
            <button type="submit" class="btn btn-primary w-100" id="submitBtn">Send Reset Link</button>
        </form>
      </div>  
    </div>  
</section>
</main>
  
<?php include 'footer.php'; ?>
