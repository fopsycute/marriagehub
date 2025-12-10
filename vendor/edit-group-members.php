
<?php include "header.php"; ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit member</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Member Details</div>
          </div>
          <div class="card-body">

  <?php
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'] ?? null;
    $user_id = $_GET['user_id'] ?? null;

    // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=memberid&group_id=" . $group_id . "&user_id=" . $user_id;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 
            $status = $groupdetail->status ?? '';
            $role = $groupdetail->role ?? '';
            $firstName = $groupdetail->first_name ?? '';
            $lastName = $groupdetail->last_name ?? '';
            $email =  $groupdetail->email ?? '';
          
        } else {
            echo "<div class='alert alert-warning'>No user found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching user data. Please try again later.</div>";
    }
} else {
    header("Location: $siteurl");
    exit;
}

?>

 <div class="card">
    <div class="card-header">
      <h4>Edit Member – <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h4>
    </div>
    <div class="card-body">
      <form id="updategroupmember" method="POST">
        <div class="col-lg-12 text-center mt-1" id="messages"></div> 
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <select class="form-select" id="status" name="status" required>
            <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
           
          </select>
        </div>
    <input type="hidden" name="action" value="updategroupmember">
        <div class="mb-3">
          <label class="form-label d-block">Role</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="roleMember" value="member" <?php echo ($role == 'member') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="roleMember">Member</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="roleSubadmin" value="subadmin" <?php echo ($role == 'subadmin') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="roleSubadmin">Subadmin</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" <?php echo ($role == 'admin') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="roleAdmin">Admin</label>
          </div>
        </div>

        <button type="submit" name="update" class="btn btn-primary">
          <i class="fa fa-save"></i> Update Member
        </button>
        <a href="view-group.php?group_id=<?php echo $group_id; ?>" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
</div>
 </div>
</div>
</div>
</div>
<?php include "footer.php"; ?>