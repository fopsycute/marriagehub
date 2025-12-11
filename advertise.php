

<?php include "header.php"; ?>

<div class="container my-4">
    <div class="card mb-4">
        <div class="card-body widget-item">
            <p>
                Boost your brand visibility and reach the right audience by advertising on <strong><a href="<?php echo $siteurl; ?>">MarriageHub.ng</a></strong>—Nigeria’s trusted hub for marriage, family life, and relationships. We offer high-impact ad options including banner ads, pop-ups, sponsored posts, and featured listings to help you attract quality leads and increase sales.
            </p>
            <p>
                With flexible pricing and strategic placements across our homepage, blog, vendor pages, and community groups, your brand gets premium exposure to an active audience of couples, parents, newlyweds, and relationship-focused individuals.
            </p>

            <h5><strong>Who Should Advertise on MarriageHub.ng?</strong></h5>
            <ul>
                <li><strong>Wedding & Event Services:</strong> Wedding planners, photographers, decorators, bridal houses, caterers, makeup artists, event venues.</li>
                <li><strong>Family & Home Services:</strong> Parenting coaches, baby product sellers, real estate firms, interior decorators, counselors, home service providers.</li>
                <li><strong>Health & Wellness Brands:</strong> Fertility clinics, hospitals, gyms, nutritionists, therapists, health insurance providers.</li>
                <li><strong>Lifestyle & Personal Development:</strong> Relationship coaches, financial advisors, authors, fashion and beauty brands.</li>
                <li><strong>Food, Culture & Education:</strong> Cooking tutors, ethnic food brands, language teachers, restaurants.</li>
                <li><strong>Technology & Travel:</strong> Fintech companies, mobile networks, e-commerce brands, hotels, travel agencies.</li>
            </ul>
            <p>
                Whether you’re promoting a service, product, or brand, MarriageHub.ng offers a targeted and credible platform to reach people making real family and life decisions.
            </p>
            <p><strong>Partner with us today and put your brand where it matters most.</strong></p>
        </div>
    </div>
</div>


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
                                    <a href="buy-advert/<?= $slug ?>" class="btn btn-sm btn-primary">
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
<div class="container my-5">

    <h3 class="text-center mb-4"><strong>How It Works for Advertisers</strong></h3>
    <p class="text-center mb-5">To place your adverts on our platform, follow the steps below:</p>

    <div class="row g-4">

        <!-- Step 1 -->
        <div class="col-md-6 col-lg-3">
            <div class="card contentBox flex-fill text-center p-3">
                <i class="bi bi-person-plus text-primary mb-3" style="font-size:35px;"></i>
                <h5><strong>Create Your Free Profile</strong></h5>
                <p class="mt-2">It is completely free to register on Marriage Hub NG.</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="col-md-6 col-lg-3">
            <div class="card contentBox flex-fill text-center p-3">
                <i class="bi bi-file-earmark-text text-success mb-3" style="font-size:35px;"></i>
                <h5><strong>Provide Advert Details</strong></h5>
                <p class="mt-2">
                    Provide advert details and creatives. Make payment according to the number of views you want.
                </p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="col-md-6 col-lg-3">
            <div class="card contentBox flex-fill text-center p-3">
                <i class="bi bi-check-circle text-info mb-3" style="font-size:35px;"></i>
                <h5><strong>Advert Review & Approval</strong></h5>
                <p class="mt-2">
                    Ads are reviewed and approved if they meet our policy. If rejected, a refund is processed.
                </p>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="col-md-6 col-lg-3">
            <div class="card contentBox flex-fill text-center p-3">
                <i class="bi bi-gear text-warning mb-3" style="font-size:35px;"></i>
                <h5><strong>Advert Management</strong></h5>
                <p class="mt-2">
                    Advertisers can pause, edit adverts, and monitor click-through and view rates.
                </p>
            </div>
        </div>

    </div>

    <!-- Advert Policy -->
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h4><strong>Advert Policy</strong></h4>
            <p>
                Our advertorial policy follows our editorial standards. We accept all commercial, testimonial, and viewpoint adverts  
                except those containing homophobia, pornography, defamation, or abuse of religious and ethnic sensibilities.
            </p>
            <p>
                We accept political and non-political adverts, but all must comply with  
                <strong>Advertising Practitioner Council of Nigeria (APCON)</strong> ethical standards.
            </p>
        </div>
    </div>

</div>

<?php include "footer.php"; ?>