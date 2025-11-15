<?php include "header.php"; ?>


<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit User</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">User Details</div>
          </div>
          <div class="card-body">
            <?php
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=edituser&user_id=" . $userId;

    // Fetch user details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $userdetails = json_decode($data);
        if (!empty($userdetails)) {
            $user = $userdetails[0]; 

            // Assign data to variables
            $first_name = $user->first_name ?? '';
            $middle_name = $user->middle_name ?? '';
            $last_name = $user->last_name ?? '';
            $title = $user->title ?? '';
            $status = $user->status ?? '';
            $dob = $user->dob ?? '';
            $gender = $user->gender ?? '';
            $nationality = $user->nationality ?? '';
            $languages = $user->languages ?? '';
            $phone = $user->phone ?? '';
            $website = $user->website ?? '';
            $email = $user->email ?? '';
            $state_residence = $user->state_residence ?? '';
            $address = $user->address ?? '';
            $facebook = $user->facebook ?? '';
            $twitter = $user->twitter ?? '';
            $instagram = $user->instagram ?? '';
            $linkedin = $user->linkedin ?? '';
            $bio = $user->bio ?? '';
            $photo = $user->photo ?? '';
            $suspend_reason = $user->suspend_reason ?? '';
            $photo_url = !empty($photo) ? $siteurl . "uploads/" . $photo : $siteurl . "images/default-avatar.png";
        } else {
            echo "<div class='alert alert-warning'>No user found with this ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Unable to fetch user data. Try again later.</div>";
    }
} else {
    header("Location: users-list.php");
    exit;
}
?>
		<form id="update-user" method="POST" enctype="multipart/form-data">

    <div class="col-lg-12 text-center mt-1" id="messages"></div> 
  <input type="hidden" name="user_id" value="<?= $userId; ?>">

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
    <textarea name="bio" class="editor"><?= $bio; ?></textarea>
  </div>

  <div class="mb-3">
    <label>Current Photo</label><br>
    <img src="<?= $photo_url; ?>" alt="Profile Photo" width="120" class="rounded mb-2"><br>
    <input type="file" name="photo" class="form-control">
  </div>
<div class="mb-3">
  <label class="form-label">Status</label>
  <select name="status" id="statusSelect" class="form-select" required>
    <option value="pending" <?= ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
    <option value="active" <?= ($status == 'active') ? 'selected' : ''; ?>>Active</option>
    <option value="suspended" <?= ($status == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
  </select>
</div>

<div class="mb-3" id="suspendReasonBox" style="display: none;">
  <label class="form-label">Reason for Suspension</label>
  <textarea name="suspend_reason" class="form-control" rows="3"><?= htmlspecialchars($suspend_reason ?? '') ?></textarea>
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