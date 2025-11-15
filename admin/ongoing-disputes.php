


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Ongoing Disputes</h3>
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
                  <a href="#">Ongoing Disputes</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Ongoing Disputes</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Ongoing Disputes</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
    <th>Ticket ID</th>
    <th>Category</th>
    <th>Reporter</th>
    <th>Reported Date</th>
    <th>Status</th>
    <th>Actions</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
    <th>Ticket ID</th>
    <th>Category</th>
    <th>Reporter</th>
    <th>Reported Date</th>
    <th>Status</th>
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=disputeslists";
$data = curl_get_contents($url);

if ($data !== false) {
    $disputes = json_decode($data);

    if (!empty($disputes)) {
        foreach ($disputes as $dispute) {
         
            // ✅ Only display disputes where status is "pending"
            if (isset($dispute->status) && strtolower($dispute->status) === 'awaiting-response') {
            $ticket_number = $dispute->ticket_number ?? '';
             $category = $dispute->category ?? '';
             $first_name = $dispute->first_name ?? '';
            $last_name = $dispute->last_name ?? '';
            $name = $first_name . ' ' . $last_name;
             $created_at = $dispute->created_at ?? '';
  
                ?>          
                <tr>
                    <td><?php echo $ticket_number; ?></td>
                    <td><?php echo $category; ?></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $created_at; ?></td>
                    <td><span class="badge bg-warning">awaiting-response</span></td>
              

                 
                    <td>
                       <a href='ticket.php?ticket_number=<?php echo $ticket_number; ?>'>
                        View Ticket
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
  </div></div>



<?php include "footer.php"; ?>