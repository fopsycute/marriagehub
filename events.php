<?php include "header.php"; ?>

<div id="eventmarketplace">
<main class="main">
<section id="events-section" class="section">
    <div class="container">
        <h2 class="mb-3">Events & Programs</h2>

        <!-- Filters -->
		<section class="category-header section">
        <!-- Small/medium toggle for filters -->
        <div class="row d-lg-none mb-2">
            <div class="col-12">
                <button id="toggleFiltersBtn" class="btn btn-outline-primary w-100" aria-expanded="false" aria-controls="filterBlock">Show filters</button>
            </div>
        </div>

        <div id="filterBlock" class="filter-container mb-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
        <div class="row mb-4 align-items-center">
		
            <div class="col-lg-6 col-md-12">
			 <div class="filter-item search-form">
             <label for="productSearch" class="form-label">Search Events</label>
             
              <input type="text" id="searchInput" class="form-control" placeholder="Search events...">
            
			</div>
			</div>
		
            <div class="col-lg-3 col-md-6">
			<div class="filter-item">
             <label for="category" class="form-label">Category</label>
              
                <select name="category[]" id="eventcategory" class="form-select select-multiple" multiple>
                    <option value="">-- Select Category --</option>
                    <?php
                    $url = $siteurl . "script/register.php?action=eventcategorieslists";
                    $data = curl_get_contents($url);
                    if ($data !== false) {
                        $categories = json_decode($data);
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                echo "<option value='{$category->id}'>{$category->category_name}</option>";
                            }
                        }
                    }
                    ?>
                </select>
           
			 </div>
			  </div>

            <div class="col-md-3">
			<div class="filter-item">
             <label for="subcategory" class="form-label">Subategory</label>
                <select name="subcategory[]" id="eventsubcategory" class="form-select select-multiple" multiple>
                    <option value="">-- Select Sub-Category --</option>
                </select>
            </div>
			</div>
			
			  <div class="col-md-2">
			  <div class="filter-item">
             <label for="subcategory" class="form-label">Pricing</label>
                <select id="pricingType" name="pricing_type" class="form-control">
                    <option value="">-- Select Pricing --</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                    <option value="donate">Donate</option>
                </select>
            </div>
			</div>


            <div class="col-md-2">
			  <div class="filter-item">
             <label for="eventtype" class="form-label">Event Type</label>
                <select id="eventType" name="eventType" class="form-control">
                    <option value="">-- Select Event Type --</option>
                    <?php
                    $url = $siteurl . "script/register.php?action=eventslists";
                    $data = curl_get_contents($url);
                    if ($data !== false) {
                        $events = json_decode($data);
                        if (!empty($events)) {
                            foreach ($events as $event) {
                                echo "<option value='" . strtolower($event->name) . "'>{$event->name}</option>";
                            }
                        }
                    }
                    ?>
                </select>
            </div>
			 </div>

            <div class="col-md-2">
			 <div class="filter-item">
             <label for="format" class="form-label">Format</label>
                <select id="deliveryFormat" name="delivery_format" class="form-control">
                    <option value="">-- Select Format --</option>
                    <option value="physical">Physical (In-person)</option>
                    <option value="online">Online (Webinar/Virtual)</option>
                    <option value="hybrid">Hybrid (Physical & Online)</option>
                    <option value="video">Video</option>
                    <option value="text">Text</option>
                </select>
            </div>
			 </div>
			 

     
            <div class="col-md-2">
			 <div class="filter-item">
             <label for="format" class="form-label">Format</label>
                <select id="state" name="state" class="form-control">
                    <option value="">-- Select State --</option>
                </select>
            </div>
			 </div>

            <div class="d-flex justify-content-end ">
                <button type="button" id="clearFilters" class="btn btn-sm btn-secondary ms-auto">Clear</button>
            </div>
        </div>
		  </div>
		  </section>

        <!-- Events Container -->
		<section id="best-sellers" class="best-sellers section">
        <div class="row" id="eventsContainer">
		
            <?php
            $url = $siteurl . "script/admin.php?action=eventlists";
            $data = curl_get_contents($url);
            if ($data !== false) {
                $events = json_decode($data);
                if (!empty($events)) {
                    foreach ($events as $event) {
                        if (empty($event->next_event_date_time)) continue;

                        list($eventDate, $startTime, $endTime) = explode("|", $event->next_event_date_time);
                        $today = date("Y-m-d");
                        $nowTime = date("H:i:s");
                        if (strtolower($event->status ?? "") !== "active" || ($eventDate < $today || ($eventDate == $today && $endTime < $nowTime))) continue;

                        $title = htmlspecialchars($event->title ?? "Untitled");
                        $slug = $event->slug ?? "";
                        $eventUrl = $siteurl .'event/' $slug;
                        $categorynames = $event->category_names ?? "General";

                        $categoryIds = $event->categories;
                        $subCategoryIds = $event->subcategories;

                        $featuredImg = !empty($event->featured_image)
                            ? $siteurl . $imagePath . $event->featured_image
                            : $siteurl . "assets/img/default-product.jpg";

                        $pricing_type = $event->pricing_type ?? "";
                        $delivery_format = $event->delivery_format ?? "";
                        $state = $event->state ?? "";
                         $pricingType = strtolower($event->pricing_type ?? "paid");
                $displayPrice = "N/A";

                if ($pricingType === "free") {
                    $displayPrice = "Free";
                } elseif ($pricingType === "donation") {
                    $displayPrice = "Donate";
                } else {
                    if (!empty($event->prices)) {
                        $prices = array_map('floatval', explode(",", $event->prices));

                        if (count($prices) === 1) {
                            $displayPrice = $sitecurrency . number_format($prices[0], 2);
                        } else {
                            $minPrice = min($prices);
                            $maxPrice = max($prices);

                            $displayPrice =
                                $sitecurrency . number_format($minPrice, 2) . " - " .
                                $sitecurrency . number_format($maxPrice, 2);
                        }
                    }
                }

                        ?>
                        <div class="col-lg-3 col-md-6 col-6 mb-4 event-card"
                             data-title="<?php echo strtolower($title); ?>"
                             data-category="<?php echo $categoryIds; ?>"
                             data-subcategory="<?php echo $subCategoryIds; ?>"
                             data-type="<?php echo strtolower($event->event_type_name ?? ''); ?>"
                             data-catname="<?php echo strtolower($event->category_names ?? ''); ?>"
                             data-subcatname="<?php echo strtolower($event->subcategory_names ?? ''); ?>"
                             data-pricing_type="<?php echo strtolower($pricing_type); ?>"
                             data-delivery_format="<?php echo strtolower($delivery_format); ?>"
                             data-state="<?php echo strtolower($state); ?>">

                            <div class="product-item">
                                <div class="product-image">
                                     <div class="product-badge trending-badge"><?php echo $categorynames; ?></div>
                                    <a href="<?php echo $eventUrl; ?>">
                                        <img src="<?php echo $featuredImg; ?>" class="img-fluid" alt="<?php echo $title; ?>">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-name"><a href="<?php echo $eventUrl; ?>"><?php echo $title; ?></a></h5>
                                    <p class="product-category"><?php echo $event->event_type_name; ?></p>
                                            <!-- Pricing -->
                            <div class="product-price"><?php echo $displayPrice; ?></div>
                                </div>
                             
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>
		
        </div>
			</section>

        <!-- Pagination -->
        <section id="category-pagination" class="category-pagination section"> 
            <div class="container">
                <nav class="d-flex justify-content-center" aria-label="Page navigation">
                    <ul id="marketplace-pagination-list"></ul>
                </nav>
            </div>
        </section>

            </div>
            </section>
            </main>
            </div>

            <script>
            (function () {
                const btn = document.getElementById('toggleFiltersBtn');
                const block = document.getElementById('filterBlock');
                if (!btn || !block) return;

                function applyInitial() {
                    if (window.innerWidth < 992) { // small + md
                        block.classList.add('d-none');
                        btn.textContent = 'Show filters';
                        btn.setAttribute('aria-expanded', 'false');
                        btn.style.display = '';
                    } else {
                        block.classList.remove('d-none');
                        btn.style.display = 'none';
                        btn.setAttribute('aria-expanded', 'true');
                    }
                }

                btn.addEventListener('click', function () {
                    const hidden = block.classList.toggle('d-none');
                    btn.textContent = hidden ? 'Show filters' : 'Hide filters';
                    btn.setAttribute('aria-expanded', String(!hidden));
                    if (!hidden) block.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                window.addEventListener('resize', applyInitial);
                applyInitial();
            })();
            </script>

    <?php include "footer.php"; ?>
