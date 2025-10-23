
<?php 
$requireLogin = true;
include "header.php"; 

?>


<div class="container">
  <div class="page-inner">

    <div class="row">
      <div class="col-md-12">

        <div class="card mt-5 mb-5">
<div class="card-header d-flex justify-content-between align-items-center">
  <div class="card-title mb-0">User Details</div>
  <a href="<?php echo $siteurl; ?>dashboard.php" class="btn btn-sm btn-outline-primary">
    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
  </a>
</div>
          <div class="card-body">
		<form id="update-user" method="POST" enctype="multipart/form-data">

    <div class="col-lg-12 text-center mt-1" id="messages"></div> 
  <input type="hidden" name="user_id" value="<?= $buyerId; ?>">

  <div class="row mb-3">
    <div class="col-md-3">
      <label>Title</label>
      <input type="text" name="title" value="<?= $title; ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label>First Name</label>
      <input type="text" name="first_name" value="<?= $first_name; ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label>Middle Name</label>
      <input type="text" name="middle_name" value="<?= $middle_name; ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label>Last Name</label>
      <input type="text" name="last_name" value="<?= $last_name; ?>" class="form-control">
    </div>
 
  </div>
<input type="hidden" name="action" value="update_user">
  <div class="row mb-3">
    <div class="col-md-4">
      <label>Date of Birth</label>
      <input type="date" name="dob" value="<?= $dob; ?>" class="form-control">
    </div>
    <div class="col-md-4">
      <label>Gender</label>
      <select name="gender" class="form-select">
        <option value="">Select</option>
        <option value="Male" <?= $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?= $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
      </select>
    </div>
    <div class="col-md-4">
      <label>Nationality</label>
      <input type="text" name="nationality" value="<?= $nationality; ?>" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
   <div class="col-md-4">
      <label>Email</label>
    <input type="email" name="email" value="<?= $email; ?>" class="form-control">
    </div>

    <div class="col-md-4">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= $phone; ?>" class="form-control">
    </div>
    <div class="col-md-4">
      <label>Website</label>
      <input type="text" name="website" value="<?= $website; ?>" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label>State</label>
      <input type="text" name="state_residence" value="<?= $state_residence; ?>" class="form-control">
    </div>
    <div class="col-md-6">
      <label>Address</label>
      <input type="text" name="address" value="<?= $address; ?>" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-3"><input type="url" name="facebook" value="<?= $facebook; ?>" placeholder="Facebook" class="form-control"></div>
    <div class="col-md-3"><input type="url" name="twitter" value="<?= $twitter; ?>" placeholder="Twitter" class="form-control"></div>
    <div class="col-md-3"><input type="url" name="instagram" value="<?= $instagram; ?>" placeholder="Instagram" class="form-control"></div>
    <div class="col-md-3"><input type="url" name="linkedin" value="<?= $linkedin; ?>" placeholder="LinkedIn" class="form-control"></div>
  </div>

  <div class="mb-3">
    <label>Bio</label>
    <textarea name="bio" class="editor" rows="4"><?= $bio; ?></textarea>
  </div>

  <div class="mb-3">
    <label>Current Photo</label><br>
    <img src="<?= $photo_url; ?>" alt="Profile Photo" width="120" class="rounded mb-2"><br>
    <input type="file" name="photo" class="form-control">
  </div>
  <input type="hidden" name="status" value="<?php echo $status; ?>" >

<div class="mb-3" id="suspendReasonBox" style="display: none;">
  <label class="form-label">Reason for Suspension</label>
  <textarea name="suspend_reason" class="editor" rows="3"><?= htmlspecialchars($suspend_reason ?? '') ?></textarea>
</div>
  <button type="submit" class="btn btn-primary">Update</button>
</form>

          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

<?php include "footer.php"; ?>