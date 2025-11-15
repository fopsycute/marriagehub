
<?php include "header.php"; ?>


<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Change Password</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Change Password</div>
          </div>
          <div class="card-body">
            <div class="container mt-5">
    <h3>Change Password</h3>
    <form method="POST" id="changePasswordForm">
            <div id="messages" class="mt-3"></div>
        <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
        </div>
            <input type="hidden" name="action" value="changePassword">
            <input type="hidden" name="user_id" value="<?php echo $buyerId; ?>">

        <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
        </div>

        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>

        <button type="submit" id="changePasswordButton" class="btn btn-primary">Change Password</button>
    </form>


</div>
</div>
</div>
</div>
</div>
</div>
</div>


<?php include "footer.php"; ?>