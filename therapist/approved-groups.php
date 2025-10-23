


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Approved Tribes & Groups</h3>
              <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                  <a href="#">
                    <i class="icon-home"></i>
                  </a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Tribes & Groups</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Tribes & Groups</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Approved Tribes & Groups</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
          
            <th>Title</th>
            <th>Creator</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Title</th>
            <th>Creator</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
          <?php
$url = $siteurl . "script/admin.php?action=grouplists";
$data = curl_get_contents($url);

if ($data !== false) {
    $groups = json_decode($data);

    if (!empty($groups)) {
        foreach ($groups as $group) {

            // ✅ Only display groups where status is "active"
            if (isset($group->status) && strtolower($group->status) === 'active') {
                $groupId = $group->id;
                $title = $group->group_name;
                $author = $group->first_name . ' ' . $group->last_name;
                $date = date('M d, Y', strtotime($group->created_at));
                $memberCount = isset($group->member_count) ? intval($group->member_count) : 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($title); ?></td>
                    <td><?php echo htmlspecialchars($author); ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <a href='edit-group.php?group_id=<?php echo $groupId; ?>' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>

                        <a href='#' id='<?php echo $groupId; ?>' class='btn btn-link btn-danger deletegroup' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>

                        <?php if ($memberCount > 0): ?>
                            <a href='view-group.php?group_id=<?php echo $groupId; ?>' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='View Members'>
                                <i class='fa fa-eye'></i> View Members (<?php echo $memberCount; ?>)
                            </a>
                        <?php endif; ?>
                    </td>
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



<?php include "footer.php"; ?>