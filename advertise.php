

<?php include "header.php"; ?>


 <div class="container">

            
            <div class="row">
                <div class="col-md-12">
                <div class="card shadow-sm">
                  <div class="card-header">
                    <h4 class="card-title">ADVERT RATES</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle adverts-table">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Size</th>
                <th>Description</th>
                <th>Price Per Day (₦)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $url = $siteurl . "script/admin.php?action=advertlists";
            $data = curl_get_contents($url);

            if ($data !== false) {
                $adverts = json_decode($data);

                if (!empty($adverts)) {
                    foreach ($adverts as $advert) {
                        if (isset($advert->status) && $advert->status == "active") {
                            $advertId = $advert->id;
                            $title = htmlspecialchars($advert->placement_name);
                            $size = htmlspecialchars($advert->size);
                            $price_per_day = number_format($advert->price_per_day, 2);
                            $description = $advert->description;
                            $slug = $advert->slug;

                            ?>
                            <tr>
                                <td><?= $title ?></td>
                                <td><?= $size ?></td>
                                <td><?= $description ?></td>
                                <td><?= $price_per_day ?></td>
                                <td>
                                    <a href="buy-advert?slug=<?= $slug ?>" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> Buy
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No adverts available.</td></tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">Failed to fetch adverts.</td></tr>';
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