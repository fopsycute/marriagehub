<?php include "header.php"; ?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">All Adverts</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">All Adverts</a></li>
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
                            <table id="multi-filter-select" class="display table table-striped table-hover">
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
                                            $today = date('Y-m-d');
                                            foreach ($adverts as $advert) {

                                                // Check if advert has not expired
                                                $expiryDate = $advert->expiry_date ?? null;
                                                if ($expiryDate && $expiryDate < $today) continue;

                                                $advertId = $advert->id;
                                                $title = $advert->placement_name ?? '';
                                                $size = $advert->size ?? '';
                                                $price_per_day = $advert->price_per_day ?? '';
                                                $status = $advert->status ?? '';
                                                $bannerUrl = $advert->banner_url ?? '';
                                                $modalId = 'modal_' . $advertId;
                                                ?>
                                                <tr>
                                                    <td class="blog-thumb" style="vertical-align:middle; width:160px;">
                                                        <?php if ($bannerUrl): ?>
                                                            <img loading="lazy" src="<?php echo $bannerUrl; ?>" 
                                                                 alt="<?php echo htmlspecialchars($title); ?>" 
                                                                 class="img-fluid rounded" 
                                                                 style="max-width:120px; max-height:80px; object-fit:contain; display:block; margin-bottom:6px;">

                                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#<?php echo $modalId; ?>">
                                                                View
                                                            </button>

                                                            <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1">
                                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Banner Preview</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <img src="<?php echo $bannerUrl; ?>" 
                                                                                 alt="<?php echo htmlspecialchars($title); ?>" 
                                                                                 class="img-fluid" 
                                                                                 style="max-height:70vh; object-fit:contain;">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <small class="text-muted">No banner</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $title; ?></td>
                                                    <td><?php echo $size; ?></td>
                                                    <td><?php echo $price_per_day; ?></td>
                                                    <td><span class="badge bg-<?= getBadgeColor($status) ?>"><?= $status ?></span></td>
                                                    <td>
                                                        <a href='edit-advert.php?advert_id=<?php echo $advertId; ?>' 
                                                           class='btn btn-link btn-primary btn-lg'>
                                                            <i class='fa fa-edit'></i>
                                                        </a>
                                                        <a href='#' data-id='<?php echo $advertId; ?>' 
                                                           class='btn btn-link btn-danger deleteadvert'>
                                                            <i class='fa fa-trash'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
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
