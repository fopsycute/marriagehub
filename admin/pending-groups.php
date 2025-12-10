

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Pending Tribes & Groups</h3>
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
                  <a href="#">Pending Tribes & Groups</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Pending Tribes & Groups</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Pending Tribes & Groups</h4>
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
            if (strtolower($group->status) === 'pending') {
                $groupId = $group->id;
                $title = $group->group_name;
                $author = $group->first_name . ' ' . $group->last_name;
                $date = date('M d, Y', strtotime($group->created_at));
                ?>
                <tr>

                    <td><?php echo $title; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-warning">Pending</span></td>
                  

                    <?php
                    echo "
                    <td>
                        <a href='edit-group.php?group_id=$groupId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$groupId' class='btn btn-link btn-danger  deletegroup' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>
                    </td>";
                    ?>
                        <!-- Action buttons here -->
                   
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