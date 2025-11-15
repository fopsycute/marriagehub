<?php include "header.php"; ?>

<?php
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

 // Your site API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchgroupmembersid&group_id=" . $group_id;

    // Fetch blog details via API
    $data = curl_get_contents($url);

    if ($data !== false) {
        $blogdetails = json_decode($data);
        if (!empty($blogdetails)) {
            $blogdetail = $blogdetails[0]; 
            $name = $blogdetail->group_name ?? '';
        } else {
            echo "<div class='alert alert-warning'>No members found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching member data. Please try again later.</div>";
    }
} else {
    header("Location: index.php");
    exit;
}

?>

 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3"><?php echo $name; ?> Member</h3>
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
                  <a href="#"><?php echo $name; ?> Member</a>
                </li>
                
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title"><?php echo $name; ?> Member</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
          
            <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Name</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=groupmemberlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $groups = json_decode($data);

    if (!empty($groups)) {
        foreach ($groups as $group) {
            if (isset($group->status) && $group->group_id == $group_id) {
                $groupId = $group->group_id;
                $id = $group->id;
                $title = $group->group_name;
                $userId = $group->user_id;
                $role = $group->role;
                 $status = $group->status;
                $author = $group->first_name . ' ' . $group->last_name;
                $date = date('M d, Y', strtotime($group->joined_at));
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

                    <td><?php echo $author; ?></td>
                    <td><?php echo $role; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
                 

                    <?php
                    echo "
                    <td>
                        <a href='edit-group-members.php?group_id=$groupId&user_id=$userId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$id' class='btn btn-link btn-danger  deletegroupmembers' data-bs-toggle='tooltip' title='Delete'>
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