

<?php include "header.php"; ?>
 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">All Listings</h3>
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
                  <a href="#">All Listings</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">All Listings</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">All Listings</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Title</th>
            <th>type</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
          <th>Title</th>
           <th>type</th>
            <th>Date</th>
            <th>Status</th>
        
          </tr>
        </tfoot>
        <tbody>
 <?php
$url = $siteurl . "script/admin.php?action=mylists";
$data = curl_get_contents($url);
$count = 0;

if ($data !== false) {
    $listings = json_decode($data);

    if (!empty($listings)) {
        foreach ($listings as $listing) {
            // ✅ Only active listings
            if (isset($listing->status) && $listing->user_id == $buyerId) {
                 $listingId   = $listing->id;
                 $listingIde = $listing->listing_id;
                $title       = htmlspecialchars($listing->title); 
                $type   =     $listing->type;
                $slug        = htmlspecialchars($listing->slug ?? '');
                $pricingType = htmlspecialchars($listing->pricing_type ?? '');
                $price       = htmlspecialchars($listing->price ?? '');
                $status       = $listing->status ?? '';
                $priceMin    = htmlspecialchars($listing->price_min ?? '');
                $priceMax    = htmlspecialchars($listing->price_max ?? '');
                $created_at =   $listing->created_at ?? '';
                $categoryNames = !empty($listing->category_names) ? explode(',', $listing->category_names) : ['General'];
                $category    = htmlspecialchars(trim($categoryNames[0]));
                $featuredImg = !empty($listing->featured_image)
                    ? $siteurl . $imagePath . $listing->featured_image
                    : $siteurl . "assets/img/default-product.jpg";
                $listingUrl  = $siteurl . "products.php?slug=" . $slug;

                if(strtolower($status) === 'active'){
                    $statusBadge = '<span class="badge bg-success">Active</span>';
                } elseif (strtolower($status) === 'pending') {
                    $statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                } elseif (strtolower($status) === 'suspended') {
                    $statusBadge = '<span class="badge bg-danger">Suspended</span>';
                } else {
                    $statusBadge = '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
                }
                ?>
                <tr>

                    <td><?php echo $title; ?></td>
                    <td><?php echo $type; ?></td>
                    <td><?php echo $created_at; ?></td>
                    <td><?php echo $statusBadge; ?></td>
                  

                    <?php
                    echo "
                    <td>
                        <a href='edit-listing.php?listing_id=$listingIde' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$listingId' class='btn btn-link btn-danger  deletelisting' data-bs-toggle='tooltip' title='Delete'>
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