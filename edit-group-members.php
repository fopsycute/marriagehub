

<?php include "header.php"; ?>
<div class="container mb-5 mt-5">
  <div class="page-inner">
    <div class="row">
      <div class="col-md-8 mx-auto">

        <div class="card mt-5">
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




            // ✅ Get Group Slug & Creator ID from groups table
            $groupSlug = '';
            $groupCreatorId = 0;

            if (!empty($group_id)) {
                $groupUrl = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $group_id;
                $groupData = curl_get_contents($groupUrl);

                if ($groupData !== false) {
                    $groupInfo = json_decode($groupData);
                    if (!empty($groupInfo[0])) {
                        $groupSlug = $groupInfo[0]->slug ?? '';
                        $groupCreatorId = $groupInfo[0]->user_id ?? 0; // group creator
                    }
                }
            }

            // ✅ Check login cookies
            $adminAuth  = $_COOKIE['admin_auth']  ?? '';
            $vendorAuth = $_COOKIE['vendor_auth'] ?? '';
            $therapistAuth = $_COOKIE['therapist_auth'] ?? '';
            $userAuth   = $_COOKIE['user_auth']   ?? '';

            $canAccess = false;

            // ✅ If current user is group creator → allow access
                if (
            ($adminAuth) || 
            ($vendorAuth == $groupCreatorId) ||  ($therapistAuth == $groupCreatorId) || 
            ($userAuth == $groupCreatorId)
            ) {
                // ✅ CASE 1: Admin — always has access
                if ($adminAuth) {
                    $buyerId = $adminAuth;
                }
                // ✅ CASE 2: Vendor — if the vendor is the group creator
                elseif ($vendorAuth == $groupCreatorId) {
                    $buyerId = $vendorAuth;
                }
                // ✅ CASE 3: Regular user — if the user is the group creator
                elseif ($userAuth == $groupCreatorId) {
                    $buyerId = $userAuth;
                }

                 elseif ($therapistAuth == $groupCreatorId) {
                    $buyerId = $therapistAuth;
                }

                $activeLog = 1;
                $canAccess = true;
            } else {
                // ✅ Otherwise, check if user is a group member
               $activeUserId = $adminAuth ?: ($vendorAuth ?: ($userAuth ?: $therapistAuth));
                if (!empty($activeUserId) && !empty($group_id)) {
                    $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$group_id}&user_id={$activeUserId}";
                    $memberData = curl_get_contents($checkMemberUrl);

                    if ($memberData !== false) {
                        $memberResult = json_decode($memberData, true);
                        if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active' && ($memberResult[0]['role'] == 'admin')) {
                            $canAccess = true;
                        }
                    }
                }
            }

            // ✅ Restrict access if not authorized
            if (!$canAccess) {
                echo "
                <script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Join Group to View',
                        html: 'You are not a member of this group. <br><b>Join our group to view this blog post.</b>',
                        confirmButtonText: 'Join Group',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{$siteurl}group/{$groupSlug}';
                        } else {
                            window.location.href = '{$siteurl}';
                        }
                    });
                </script>";
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