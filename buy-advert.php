
<?php include "header.php"; ?>
<?php
$advert_slug = $_GET['slug'] ?? '';

if (empty($advert_slug)) {
    echo "<div class='alert alert-danger'>Invalid Selection.</div>";
    exit;
}

// Fetch ad details from API
$url = $siteurl . "script/admin.php?action=advertslug&slug=" . urlencode($advert_slug);
$data = curl_get_contents($url);

if ($data === false) {
    echo "<div class='alert alert-danger'>Unable to load advert details.</div>";
    exit;
}

$adverts = json_decode($data);

// Validate response
if (empty($adverts) || !isset($adverts[0])) {
    echo "<div class='alert alert-danger'>Ad not found.</div>";
    exit;
}

// Extract advert record
$advert = $adverts[0];

// Assign values
$advert_id    = $advert->id;
$placementName = htmlspecialchars($advert->placement_name);
$size          = htmlspecialchars($advert->size);
$pricePerDay   = floatval($advert->price_per_day);
$description   = $advert->description;
?>


    <div class="row">
      <div class="col-md-12">
        <div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Buy Advert: <span id="advert_title"><?= $placementName ?></span></h4>
        </div>
        <div class="card-body">
            <p><strong>Size:</strong> <span id="advert_size"><?= $size ?></span></p>
            <p><strong>Price Per Day:</strong> ₦<span id="advert_price" data-price="<?= $pricePerDay ?>"><?= number_format($pricePerDay,2) ?></span></p>
            <p><strong>Description:</strong> <span id="advert_description"><?= $description ?></span></p>

           <form id="buyAdvertForm" enctype="multipart/form-data">

    <!-- Hidden fields -->
    <input type="hidden" id="advert_id" value="<?= $advert_id ?>">
    <input type="hidden" id="buyer_email" value="<?= !empty($buyerEmail) ? $buyerEmail : '' ?>">

    <!-- Start Date -->
    <div class="mb-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input 
            type="date" 
            id="start_date" 
            class="form-control" 
            required 
            min="<?= date('Y-m-d') ?>"
        >
    </div>

    <!-- End Date -->
    <div class="mb-3">
        <label for="end_date" class="form-label">End Date</label>
        <input 
            type="date" 
            id="end_date" 
            class="form-control" 
            required 
            min="<?= date('Y-m-d') ?>"
        >
    </div>

    <!-- Banner Upload -->
    <div class="mb-3">
        <label for="bannerimage" class="form-label">Upload Advert Banner</label>
        <input 
            type="file" 
            id="bannerimage" 
            name="bannerimage" 
            class="form-control" 
            accept="image/*"
            required
        >
        <small class="text-muted">Accepted: JPG, PNG, GIF (Recommended size: <?= $advert->size ?>)</small>
    </div>

    <!-- Optional Redirect URL -->
    <div class="mb-3">
    <label for="url_redirection" class="form-label">Redirect URL (Optional)</label>
    <input 
        type="url" 
        id="url_redirection" 
        name="url_redirection" 
        class="form-control" 
        placeholder="https://name.com"
    >
    <small class="text-muted">
        If you want users to be redirected when they click your advert, enter your website or landing page URL here.
    </small>
</div>


    <!-- Total Price -->
    <div class="mb-3">
        <label class="form-label">Total Price:</label>
        <p id="total_price" class="fw-bold"></p>
    </div>

    <!-- Pay Button -->
    <button type="button" id="payNowBtn" class="btn btn-success w-100">
        Buy Advert
    </button>

</form>

        </div>
    </div>
</div>
        </div>
        </div>
    </div>


    <?php include "footer.php"; ?>