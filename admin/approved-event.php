



<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Approved Events</h3>
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
                  <a href="#">Approved Events</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Events</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Approved Events</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Title</th>
            <th>Seller Name</th>
            <th>Pricing Type</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Title</th>
            <th>Seller Name</th>
            <th>Pricing Type</th>
            <th>Date</th>
            <th>Status</th>
        
          </tr>
        </tfoot>
        <tbody>
 <?php
$url = $siteurl . "script/admin.php?action=eventlists";
$data = curl_get_contents($url);
$count = 0;

if ($data !== false) {
    $listings = json_decode($data);

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && strtolower($listing->status) === 'active') {
                $eventId   = $listing->event_id;   
                $title       = htmlspecialchars($listing->title); 
                $pricing_type   = $listing->pricing_type;
                $slug        = htmlspecialchars($listing->slug ?? '');
                $status       = htmlspecialchars($listing->status ?? '');
                $created_at =   $listing->created_at ?? '';
                $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                $category    = htmlspecialchars(trim($categoryNames[0]));
                // SELLER
                $sellerName = htmlspecialchars(trim(($event->first_name ?? "") . " " . ($event->last_name ?? "")));
                 $featuredImg = !empty($listing->featured_image)
                    ? $siteurl . $imagePath . $listing->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                $eventUrl  = $siteurl . "event/" . $slug;
                ?>
                <tr>

                    <td><?php echo $title; ?></td>
                    <td><?php echo $sellerName; ?></td>
                    <td><?php echo $pricing_type; ?></td>
                    <td><?php echo $created_at; ?></td>
                    <td><span class="badge bg-success">Approved</span></td>
                  

                    <?php
                    echo "
                    <td>
                        <a href='edit-event.php?event_id=$eventId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$eventId' class='btn btn-link btn-danger  deleteevent' data-bs-toggle='tooltip' title='Delete'>
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