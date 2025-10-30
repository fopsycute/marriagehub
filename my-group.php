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
            <h4 class="card-title mb-0">My Groups</h4>
            <a href="<?php echo $siteurl; ?>create-tribes-group.php" class="btn btn-primary btn-sm">
              <i class="bi bi-plus"></i> Add Group
            </a>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select"
                        class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
<?php
$url = $siteurl . "script/admin.php?action=groupuserstatus&user_id=$buyerId";
$data = curl_get_contents($url);

if ($data !== false) {
    $response = json_decode($data, true);

    if (!empty($response['owned_groups']) || !empty($response['joined_groups'])) {
        $sn = 1;

        // ✅ Display owned groups
        if (!empty($response['owned_groups'])) {
            foreach ($response['owned_groups'] as $group) {
                $groupId   = $group['group_id'];
                $groupName = htmlspecialchars($group['group_name']);
                $role      = "Creator";
                $status     = $group['status'];
                $created   = htmlspecialchars($group['created_at']);
                $viewLink  = $siteurl . "group.php/" . $group['group_slug'];
?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $groupName; ?></td>
                    <td><span class="badge bg-success"><?php echo $role; ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($created)); ?></td>
                    <td> <?php if($status == 'active'){ ?>
                      <a href="<?php echo $viewLink; ?>" class="btn btn-sm btn-primary">View Group</a>
                    <?php } else { ?>
                      <span class="text-danger">Pending Group</span>
                    <?php } ?></td>
                     <?php
                    echo "
                    <td>
                        <a href='edit-group.php?group_id=$groupId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='bi bi-pencil'></i> 
                        </a>
                        <a href='#' id='$groupId' class='btn btn-link btn-danger  deletegroup' data-bs-toggle='tooltip' title='Delete'>
                            <i class='bi bi-trash'></i>
                        </a>
                    </td>";
                    ?>
                </tr>
<?php
            }
        }

        // ✅ Display joined groups
        if (!empty($response['joined_groups'])) {
            foreach ($response['joined_groups'] as $group) {
                $groupId   = $group['group_id'];
                $groupName = htmlspecialchars($group['group_name']);
                $role      = $group['role'];
                $joined    = htmlspecialchars($group['joined_at']);
                $viewLink  = $siteurl . "group.php/" . $group['group_slug'];
?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $groupName; ?></td>
                    <td><span class="badge bg-info"><?php echo $role; ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($joined)); ?></td>
                    <td><a href="<?php echo $viewLink; ?>" class="btn btn-sm btn-primary">View Group</a></td>
                  
                </tr>
<?php
            }
        }
    } 
} 
?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
