

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">All Adverts</h3>
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
                  <a href="#">All Adverts</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">All Adverts</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">All Adverts</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Title</th>
            <th>Size</th>
            <th>Price Per Day</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
             <th>Title</th>
            <th>Size</th>
            <th>Price Per Day</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=advertlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $adverts = json_decode($data);

    if (!empty($adverts)) {
        foreach ($adverts as $advert) {

            // ✅ Only display blogs where status is "pending"
            if (isset($advert->status)) {
                $advertId = $advert->id;
                $title = $advert->placement_name;
                $size = $advert->size;
                $price_per_day = $advert->price_per_day;
                $status = $advert->status;

                ?>
                <tr>
                    
                    <td><?php echo $title; ?></td>
                    <td><?php echo $size; ?></td>
                    <td><?php echo $price_per_day; ?></td>
                    <td><span class="badge bg-<?= getBadgeColor($status) ?>"><?= $status ?></span></td>
               

                    <?php
                    echo "
                    <td>
                        <a href='edit-advert.php?advert_id=$advertId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$advertId' class='btn btn-link btn-danger  deleteadvert' data-bs-toggle='tooltip' title='Delete'>
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