

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">All s</h3>
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
                  <a href="#">All Users</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">All Users</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">All Users</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>        
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
          <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=buyerlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $users = json_decode($data);

    if (!empty($users)) {
        foreach ($users as $user) {

            // ✅ Only display blogs where status is "pending"
            if (isset($user->status)) {
                $userId = $user->id;
                $email = $user->email;
                $author = $user->first_name . ' ' . $user->last_name;
                if ($user->status === "notactive") {
                    $statuslog = 'danger';
                }

                 if ($user->status === "suspended") {
                    $statuslog = 'danger';
                } else if ($user->status === "pending") {
                    $statuslog = 'warning';
                } else if ($user->status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>

                    <td><?php echo $author; ?></td>
                    <td><?php echo $email; ?></td>              
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($user->status); ?></span></td>
                    <td>
                        <a href='edit-user.php?user_id=<?php echo $userId; ?>' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i>
                        </a>
                        <a href='#' id='<?php echo $userId; ?>' class='btn btn-link btn-danger  deleteuser' data-bs-toggle='tooltip' title='Delete'>
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