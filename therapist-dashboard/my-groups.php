<?php include "header.php"; ?>
 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">My Tribes & Groups</h3>
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
                  <a href="#">My Tribes & Groups</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">My Tribes & Groups</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">My Tribes & Groups</h4>
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
            <th>Members</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Title</th>
            <th>Creator</th>
            <th>Members</th>
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

            // ✅ Only display blogs where status is "pending"
            if (isset($group->status) && $group->user_id == $buyerId) {
                $groupId = $group->id;
                $title = $group->group_name;
                $status = $group->status;
                $author = $group->first_name . ' ' . $group->last_name;
                $memberCount = isset($group->member_count) ? intval($group->member_count) : 0;
                
                $date = date('M d, Y', strtotime($group->created_at));
                if ($status === "notactive") {
                    $statuslog = 'danger';
                }

                 if ($status === "notactive") {
                    $statuslog = 'danger';
                } else if ($status === "pending") {
                    $statuslog = 'warning';
                } else if ($status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>

                    <td><?php echo $title; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $memberCount; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
                    <td>
                  <?php if ($memberCount > 0): ?>
                      <a href='view-group.php?group_id=<?php echo $groupId; ?>' 
                        class='btn btn-link btn-primary btn-lg' 
                        data-bs-toggle='tooltip' 
                        title='View Members'>
                          <i class='fa fa-eye'></i> View Members (<?php echo $memberCount; ?>)
                      </a>
                  <?php endif; ?>

                  <a href='edit-group.php?group_id=<?php echo $groupId; ?>' 
                    class='btn btn-link btn-primary btn-lg' 
                    data-bs-toggle='tooltip' 
                    title='Edit'>
                      <i class='fa fa-edit'></i> 
                  </a>

                  <a href='#' 
                    id='<?php echo $groupId; ?>' 
                    class='btn btn-link btn-danger deletegroup' 
                    data-bs-toggle='tooltip' 
                    title='Delete'>
                      <i class='fa fa-trash'></i>
                  </a>
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