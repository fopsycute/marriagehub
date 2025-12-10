
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
                    <h4 class="card-title mb-0">All Tickets</h4>
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
        <th>Status</th>
        <th>Actions</th>
           
          </tr>
        </thead>
        <tfoot>
          <tr>
        <th>Ticket ID</th>
        <th>Category</th>
        <th>Status</th>
   
          </tr>
        </tfoot>
       <tbody>
                                    <?php
                                    $url = $siteurl . "script/admin.php?action=getusersticket&user_id=" . $buyerId;
                                    $data = curl_get_contents($url);

                                    if ($data !== false) {
                                        $tickets = json_decode($data);
                                        if (!empty($tickets)) {
                                            foreach ($tickets as $ticket):
                                                
                                                $ticket_number    = $ticket->ticket_number ?? '';
                                                $category = $ticket->category ?? '';
                                                $status       = $ticket->status ?? '';
        
                                    ?>

                                    
                                    <tr>
                                        <td><?php echo $ticket_number; ?></td>
                                        <td><?php echo $category; ?></td>
                                          <td><span class="badge bg-<?php echo getBadgeColor($status); ?>"><?= $status; ?></span></td>
                                        <td>
                                           <a href="ticket?ticket_number=<?= $ticket_number; ?>">View Ticket</a>
    
                                        </td>
                                    </tr>

                                   

                                    <?php
                                            endforeach;
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No reviews found for your products.</td></tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center text-danger">Unable to fetch tickets.</td></tr>';
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