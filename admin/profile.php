

<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Admin Profile</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Admin Profile</div>
          </div>
          <div class="card-body">

          	<form id="update-user" method="POST" enctype="multipart/form-data">

    <div class="col-lg-8 text-center mt-1 mx-auto" id="messages"></div> 
  <input type="hidden" name="user_id" value="<?= $buyerId; ?>">

  <div class="row mb-3">
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
      <label>Email</label>
    <input type="email" name="email" value="<?= $email; ?>" class="form-control">
    </div>

    <div class="col-md-4">
      <label>Website</label>
      <input type="text" name="website" value="<?= $website; ?>" class="form-control">
    </div>
  </div>

  <div class="mb-3">
    <label>Current Photo</label><br>
    <img src="<?= $photo_url; ?>" alt="Profile Photo" width="120" class="rounded mb-2"><br>
    <input type="file" name="photo" class="form-control">
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



  
  <input type="hidden" name="status" value="<?php echo $buyerStatus; ?>" >


  <button type="submit" class="btn btn-primary">Update</button>
</form>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>










<?php include "footer.php"; ?>
