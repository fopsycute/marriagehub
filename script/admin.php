<?php
include "connect.php";



function adminforumEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // Sanitize inputs
    $title         = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article       = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags          = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'active'));
    $categories    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';


// Replace spaces with hyphens and convert to lowercase
$baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));


// Start with the cleaned slug
$alt_title = $baseSlug;
$counter = 1;

// Ensure the alt_title is unique
while (true) {
    $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "forums WHERE slug = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $alt_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        break; // alt_title is unique
    }

    // Append counter to baseSlug if not unique
    $alt_title = $baseSlug . '-' . $counter;
    $counter++;
}
  
    // Validation
    if (empty($title) || empty($article) || $user == 0) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // Prevent duplicate insert (within 10 seconds)
    $duplicateCheck = mysqli_query($con, "
        SELECT id FROM {$siteprefix}forums 
        WHERE user_id = '$user' 
        AND title = '$title' 
        AND created_at >= (NOW() - INTERVAL 10 SECOND)
        LIMIT 1
    ");
    if (mysqli_num_rows($duplicateCheck) > 0) {
        return ['status' => 'error', 'messages' => generateMessage("Duplicate submission detected. Please wait a few seconds before trying again.", "red")];
    }

    // Handle featured image upload
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $featuredImage = "";
    if (!empty($fileData["featured_image"]["name"])) {
        $fileType = mime_content_type($fileData["featured_image"]["tmp_name"]);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileType, $allowedTypes)) {
            return ['status' => 'error', 'messages' => generateMessage("Invalid image type. Please upload JPG, PNG, GIF, or WEBP.", "red")];
        }

        $fileName = basename($fileData["featured_image"]["name"]);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $featuredImage = uniqid('forum_') . '_' . $safeName;

        move_uploaded_file($fileData["featured_image"]["tmp_name"], $uploadDir . $featuredImage);
    }

    // Insert into DB
    $query = "
        INSERT INTO {$siteprefix}forums 
        (user_id, title, article, featured_image, tags, categories, subcategories, status, created_at,slug)
        VALUES (
            '$user',
            '$title',
            '$article',
            '$featuredImage',
            '$tags',
            '$categories',
            '$subcategories',
            '$status',
            NOW(),'$alt_title'
        )
    ";

    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => "Forum post created successfully!"];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
}




function groupforumEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // Sanitize inputs
    $title         = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article       = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags          = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $status         = mysqli_real_escape_string($con, trim($postData['status'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $group_id      = $postData['group_id'];
    $categories    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';


// Replace spaces with hyphens and convert to lowercase
$baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));


// Start with the cleaned slug
$alt_title = $baseSlug;
$counter = 1;

// Ensure the alt_title is unique
while (true) {
    $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "forums WHERE slug = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $alt_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        break; // alt_title is unique
    }

    // Append counter to baseSlug if not unique
    $alt_title = $baseSlug . '-' . $counter;
    $counter++;
}
  
    // Validation
    if (empty($title) || empty($article) || $user == 0) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // Prevent duplicate insert (within 10 seconds)
    $duplicateCheck = mysqli_query($con, "
        SELECT id FROM {$siteprefix}forums 
        WHERE user_id = '$user' 
        AND title = '$title' 
        AND created_at >= (NOW() - INTERVAL 10 SECOND)
        LIMIT 1
    ");
    if (mysqli_num_rows($duplicateCheck) > 0) {
        return ['status' => 'error', 'messages' => generateMessage("Duplicate submission detected. Please wait a few seconds before trying again.", "red")];
    }

    // Handle featured image upload
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $featuredImage = "";
    if (!empty($fileData["featured_image"]["name"])) {
        $fileType = mime_content_type($fileData["featured_image"]["tmp_name"]);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileType, $allowedTypes)) {
            return ['status' => 'error', 'messages' => generateMessage("Invalid image type. Please upload JPG, PNG, GIF, or WEBP.", "red")];
        }

        $fileName = basename($fileData["featured_image"]["name"]);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $featuredImage = uniqid('forum_') . '_' . $safeName;

        move_uploaded_file($fileData["featured_image"]["tmp_name"], $uploadDir . $featuredImage);
    }

    // Insert into DB
    $query = "
        INSERT INTO {$siteprefix}forums 
        (user_id, title, article, featured_image, tags, categories, subcategories, group_id, created_at,slug,status)
        VALUES (
            '$user',
            '$title',
            '$article',
            '$featuredImage',
            '$tags',
            '$categories',
            '$subcategories',
            '$group_id',
            NOW(),'$alt_title','$status'
        )
    ";

    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => "Forum post created successfully!"];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
}

//add events
function usereventsEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // 🧩 Sanitize inputs
    $title           = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $description     = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $event_type      = mysqli_real_escape_string($con, trim($postData['event_type'] ?? ''));
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $delivery_format = mysqli_real_escape_string($con, trim($postData['delivery_format'] ?? ''));
    $pricing_type    = mysqli_real_escape_string($con, trim($postData['pricing_type'] ?? ''));
    $status          = mysqli_real_escape_string($con, trim($postData['status'] ?? 'active'));
    $event_id         = mysqli_real_escape_string($con, trim($postData['event_id']));
    $user_id         = intval($postData['user'] ?? 0);
    

    $target_audience = isset($postData['target_audience']) && is_array($postData['target_audience'])
        ? implode(',', $postData['target_audience'])
        : '';

    // ✅ Generate a unique slug
    $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $slug = $baseSlug;
    $counter = 1;
    while (true) {
        $stmt = $con->prepare("SELECT COUNT(*) AS count FROM {$siteprefix}events WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) break;
        $slug = $baseSlug . '-' . $counter++;
    }
    $stmt->close();

    // ✅ Validation
    if (empty($title) || empty($description) || $user_id == 0) {
        return ['status' => 'error', 'messages' => generateMessage("Please fill all required fields.", "red")];
    }

    // ✅ Prevent duplicates
    $dupCheck = mysqli_query($con, "
        SELECT id FROM {$siteprefix}events 
        WHERE user_id = '$user_id' 
        AND event_id = '$event_id' 
        AND created_at >= (NOW() - INTERVAL 10 SECOND)
        LIMIT 1
    ");
    if (mysqli_num_rows($dupCheck) > 0) {
        return ['status' => 'error', 'messages' => generateMessage("Duplicate submission detected. Please wait and try again.", "red")];
    }

        $uploadDir = '../uploads/';

$imageList = uploadImages($_FILES['cover_image'], $uploadDir);

if (!empty($imageList)) {
    foreach ($imageList as $fileName) {
        $stmt = $con->prepare("
            INSERT INTO {$siteprefix}events_images (event_id, image_path, uploaded_at)
                    VALUES (?, ?, NOW())
                ");
        $stmt->bind_param("ss", $event_id, $fileName);
        $stmt->execute();
        $stmt->close();
    }
}

    // ✅ Insert Event Dates
    if (!empty($postData['event_dates'])) {
        foreach ($postData['event_dates'] as $i => $date) {
            $event_date = mysqli_real_escape_string($con, $date);
            $start_time = mysqli_real_escape_string($con, $postData['event_start_times'][$i] ?? '');
            $end_time   = mysqli_real_escape_string($con, $postData['event_end_times'][$i] ?? '');
            mysqli_query($con, "
                INSERT INTO {$siteprefix}event_dates (event_id, event_date, start_time, end_time)
                VALUES ('$event_id', '$event_date', '$start_time', '$end_time')
            ");
        }
    }

    // ✅ Insert Tickets (if paid)
    if ($pricing_type === 'paid' && !empty($postData['ticket_name'])) {
        foreach ($postData['ticket_name'] as $i => $name) {
            $ticket_name = mysqli_real_escape_string($con, $name);
            $benefits = mysqli_real_escape_string($con, $postData['ticket_benefits'][$i] ?? '');
            $price = floatval($postData['ticket_price'][$i] ?? 0);
            $seats = intval($postData['ticket_seats'][$i] ?? 0);
            mysqli_query($con, "
                INSERT INTO {$siteprefix}event_tickets
                (event_id, ticket_name, benefits, price, seats, seatremain)
                VALUES ('$event_id', '$ticket_name', '$benefits', '$price', '$seats', '$seats')
            ");
        }
    }

    //delivery format handling
$physical_address = $physical_state = $physical_lga = $physical_country = '';
$foreign_address = '';
$web_address = '';
$hybrid_physical_address = $hybrid_web_address = $hybrid_state = $hybrid_lga = $hybrid_country = $hybrid_foreign_address = '';
$is_foreign = 0;
     
//delivery format handling
    if ($delivery_format === 'physical') {
    if ($_POST['physicalLocationType'] === 'nigeria') {
        $physical_address = mysqli_real_escape_string($con, $_POST['nigeria_address']);
        $physical_state = mysqli_real_escape_string($con, $_POST['state']);
        $physical_lga = mysqli_real_escape_string($con, $_POST['lga']);
        $physical_country = 'Nigeria';
          $is_foreign = 0;
    } elseif ($_POST['physicalLocationType'] === 'foreign') {
        $foreign_address = mysqli_real_escape_string($con, $_POST['foreign_address']);
        $is_foreign = 1;
    }

    } elseif ($delivery_format === 'online') {
    $web_address = mysqli_real_escape_string($con, $_POST['web_address']);
} elseif ($delivery_format === 'hybrid') {
    $hybrid_physical_address = mysqli_real_escape_string($con, $_POST['hybrid_physical_address']);
    $hybrid_web_address = mysqli_real_escape_string($con, $_POST['hybrid_web_address']);
    if ($_POST['hybridLocationType'] === 'nigeria') {
        $hybrid_state = mysqli_real_escape_string($con, $_POST['hybrid_state']);
        $hybrid_lga = mysqli_real_escape_string($con, $_POST['hybrid_lga']);
        $hybrid_country = 'Nigeria';
    } elseif ($_POST['hybridLocationType'] === 'foreign') {
        $hybrid_foreign_address = mysqli_real_escape_string($con, $_POST['hybrid_foreign_address']);
    }
}
    $fileuploadDir = "../uploads/modules/";
    if (!is_dir($fileuploadDir)) mkdir($fileuploadDir, 0755, true);

    // If delivery includes videos
    if ($delivery_format === 'video') {
        foreach ($postData['video_module_title'] as $index => $titleVal) {
            $desc = $postData['video_module_desc'][$index] ?? '';
            $duration = $postData['video_duration'][$index] ?? '';
            $videoLink = $postData['video_link'][$index] ?? '';
            $qualities = isset($postData['video_quality'][$index]) ? implode(',', $postData['video_quality'][$index]) : '';
            $subtitles = $postData['video_subtitles'][$index] ?? '';
            $filePath = '';

            // Upload video file (optional)
            if (!empty($fileData['video_file']['name'][$index])) {
                $tmpKey = 'single_video_upload';
                $_FILES[$tmpKey] = [
                    'name' => $fileData['video_file']['name'][$index],
                    'type' => $fileData['video_file']['type'][$index],
                    'tmp_name' => $fileData['video_file']['tmp_name'][$index],
                    'error' => $fileData['video_file']['error'][$index],
                    'size' => $fileData['video_file']['size'][$index],
                ];
                $fileName = handleFileUpload($tmpKey, $fileuploadDir);
                if ($fileName && strpos($fileName, 'Failed') === false) $filePath = $fileName;
            }

            $stmt = $con->prepare("
                INSERT INTO {$siteprefix}event_video_modules
                (event_id, module_number, title, description, duration, file_path, video_link, video_quality, subtitles, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $module_no = $index + 1;
            $stmt->bind_param("iissssss", $event_id, $module_no, $titleVal, $desc, $duration, $filePath, $videoLink, $qualities, $subtitles);
            $stmt->execute();
            $stmt->close();
        }
    }

    // If delivery includes text
    if ($delivery_format === 'text') {
        foreach ($postData['text_module_title'] as $index => $titleVal) {
            $desc = $postData['text_module_desc'][$index] ?? '';
            $readingTime = $postData['text_reading_time'][$index] ?? '';
            $filePath = '';

            // Upload text file (optional)
            if (!empty($fileData['text_file']['name'][$index])) {
                $tmpKey = 'single_text_upload';
                $_FILES[$tmpKey] = [
                    'name' => $fileData['text_file']['name'][$index],
                    'type' => $fileData['text_file']['type'][$index],
                    'tmp_name' => $fileData['text_file']['tmp_name'][$index],
                    'error' => $fileData['text_file']['error'][$index],
                    'size' => $fileData['text_file']['size'][$index],
                ];
                $fileName = handleFileUpload($tmpKey, $fileuploadDir);
                if ($fileName && strpos($fileName, 'Failed') === false) $filePath = $fileName;
            }

            $stmt = $con->prepare("
                INSERT INTO {$siteprefix}event_text_modules
                (event_id, module_number, title, description, reading_time, file_path, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $module_no = $index + 1;
            $stmt->bind_param("iissss", $event_id, $module_no, $titleVal, $desc, $readingTime, $filePath);
            $stmt->execute();
            $stmt->close();
        }
    }
// ✅ Insert Event First
$stmt = $con->prepare("
    INSERT INTO {$siteprefix}events
    (
        event_id,
        user_id,
        title,
        description,
        categories,
        subcategories,
        event_type,
        target_audience,
        delivery_format,
        pricing_type,
        address,
        state,
        is_foreign,
        lga,
        country,
        online_link,
        hybrid_physical_address,
        hybrid_web_address,
        hybrid_state,
        hybrid_lga,
        hybrid_country,
        hybrid_foreign_address,
        slug,
        status,
        created_at
    ) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "ssssssssssssssssssssss", // ✅ 22 's' characters
    $event_id,
    $user_id,
    $title,
    $description,
    $category,
    $subcategory,
    $event_type,
    $target_audience,
    $delivery_format,
    $pricing_type,
    $physical_address,
    $physical_state,
    $is_foreign,
    $physical_lga,
    $physical_country,
    $web_address,
    $hybrid_physical_address,
    $hybrid_web_address,
    $hybrid_state,
    $hybrid_lga,
    $hybrid_country,
    $hybrid_foreign_address,
    $slug,
    $status
);

$stmt->execute();
$stmt->close();

return [
    'status' => 'success',
    'messages' => 'Event created successfully!'
];
}

function getalluser($con)
{
    global $siteprefix;
      $query = "
        SELECT * FROM {$siteprefix}users";

         $result = mysqli_query($con, $query);

    if ($result) {
        $buyerData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $buyerData[] = $row;
        }
        return $buyerData;
    } else {
        return ['error' => mysqli_error($con)];
    }

}


function getallplans($con)
{
    global $siteprefix;
      $query = "
        SELECT * FROM {$siteprefix}subscriptions";

         $result = mysqli_query($con, $query);

    if ($result) {
        $planData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $planData[] = $row;
        }
        return $planData;
    } else {
        return ['error' => mysqli_error($con)];
    }

}


function updateadminListingEndpoint($postData, $fileData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // 🔒 Sanitize Inputs
     $suspendReason = mysqli_real_escape_string($con, trim($postData['suspend_reason'] ?? ''));
    $listingId     = $postData['listing_id'] ?? 0;
    $listingTitle  = mysqli_real_escape_string($con, trim($postData['listingTitle'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $type          = mysqli_real_escape_string($con, trim($postData['itemType'] ?? ''));
    $pricingType   = mysqli_real_escape_string($con, trim($postData['pricingType'] ?? ''));
    $price         = floatval($postData['price'] ?? 0);
    $pricingNotes  = mysqli_real_escape_string($con, trim($postData['pricingNotes'] ?? ''));
    $availability  = mysqli_real_escape_string($con, trim($postData['availability'] ?? ''));
    $capacity      = intval($postData['capacity'] ?? 0);
    $delivery      = mysqli_real_escape_string($con, trim($postData['delivery'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'inactive'));
    $limited_slot  = ($availability == 'Limited Slot') ? mysqli_real_escape_string($con, trim($postData['available_slots'] ?? '')) : '';

    // 🧩 Arrays
    $categories    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $coverage      = isset($postData['coverage']) && is_array($postData['coverage']) ? implode(',', $postData['coverage']) : '';

     // ✅ Get old data
  $result = mysqli_query($con, "
    SELECT l.*, l.status AS listing_status, u.email, u.first_name, u.last_name
    FROM {$siteprefix}listings l
    LEFT JOIN {$siteprefix}users u ON u.id = l.user_id
    WHERE l.listing_id = '$listingId'
    LIMIT 1
");
    if (!$result || mysqli_num_rows($result) === 0) {
        return ['status' => 'error', 'messages' => 'User not found'];
    }

    $oldData = mysqli_fetch_assoc($result);
    $oldStatus = $oldData['listing_status'];
    $email = $oldData['email'];
    $firstName = $oldData['first_name'];
    // 🧮 Compute Display Price
    $displayPrice = 'Custom Quote';
    $priceMin = $priceMax = '';
    if ($pricingType === 'Starting Price' && !empty($price)) {
        $displayPrice = $price;
    } elseif ($pricingType === 'Price Range' && !empty($postData['variation_price'])) {
        $prices = array_filter($postData['variation_price'], fn($v) => is_numeric($v) && $v > 0);
        if (!empty($prices)) {
            $priceMin = min($prices);
            $priceMax = max($prices);
            $displayPrice = "{$priceMin} - {$priceMax}";
        }
    }

    // 🧑‍💼 Check user type to determine limits
    $userCheck = mysqli_fetch_assoc(mysqli_query($con, "SELECT user_type FROM {$siteprefix}users WHERE id = '$user'"));
    $userType = strtolower($userCheck['user_type'] ?? '');

    if ($userType === 'admin') {
        $imageLimit = 'unlimited';
        $videoLimit = 'unlimited';
    } else {
        $imageLimit = getFeatureLimit($con, $user, 'images', $siteprefix);
        $videoLimit = getFeatureLimit($con, $user, 'videos', $siteprefix);
    }

    // 🖼 Existing Media Counts
    $existingImages = mysqli_num_rows(mysqli_query($con, "SELECT id FROM {$siteprefix}listing_images WHERE listing_id = '$listingId'"));
    $existingVideos = mysqli_num_rows(mysqli_query($con, "SELECT id FROM {$siteprefix}listing_videos WHERE listing_id = '$listingId'"));

    $uploadDir = '../uploads/';

    // 🖼 IMAGE Uploads
    $newImages = $fileData['productImages'] ?? null;
    if ($newImages && $newImages['name'][0] != '') {
        $totalAfterUpload = $existingImages + count($newImages['name']);
        if ($imageLimit !== 'unlimited' && $totalAfterUpload > $imageLimit) {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "You already have {$existingImages} images. Your plan allows {$imageLimit}. Delete some before uploading new ones.",
                    "red"
                )
            ];
        }

        $remainingSlots = ($imageLimit === 'unlimited') ? count($newImages['name']) : $imageLimit - $existingImages;
        $limitedFiles = [
            'name' => array_slice($newImages['name'], 0, $remainingSlots),
            'type' => array_slice($newImages['type'], 0, $remainingSlots),
            'tmp_name' => array_slice($newImages['tmp_name'], 0, $remainingSlots),
            'error' => array_slice($newImages['error'], 0, $remainingSlots),
            'size' => array_slice($newImages['size'], 0, $remainingSlots)
        ];

        $uploadedImages = uploadImages($limitedFiles, $uploadDir);
        foreach ($uploadedImages as $fileName) {
            mysqli_query($con, "INSERT INTO {$siteprefix}listing_images (listing_id, file_name, uploaded_at) VALUES ('$listingId', '$fileName', NOW())");
        }
    }

    // 🎬 VIDEO Uploads
    $newVideos = $fileData['videos'] ?? null;
    if ($newVideos && $newVideos['name'][0] != '') {
        $totalAfterUpload = $existingVideos + count($newVideos['name']);
        if ($videoLimit !== 'unlimited' && $totalAfterUpload > $videoLimit) {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "You already have {$existingVideos} videos. Your plan allows {$videoLimit}. Delete some before uploading new ones.",
                    "red"
                )
            ];
        }

        $remainingSlots = ($videoLimit === 'unlimited') ? count($newVideos['name']) : $videoLimit - $existingVideos;
        $limitedVideos = [
            'name' => array_slice($newVideos['name'], 0, $remainingSlots),
            'type' => array_slice($newVideos['type'], 0, $remainingSlots),
            'tmp_name' => array_slice($newVideos['tmp_name'], 0, $remainingSlots),
            'error' => array_slice($newVideos['error'], 0, $remainingSlots),
            'size' => array_slice($newVideos['size'], 0, $remainingSlots)
        ];

        $uploadedVideos = uploadVideos($limitedVideos, $uploadDir);
        foreach ($uploadedVideos as $fileName) {
            mysqli_query($con, "INSERT INTO {$siteprefix}listing_videos (listing_id, file_name, uploaded_at) VALUES ('$listingId', '$fileName', NOW())");
        }
    }

    // 💾 Update Listing Info
    $sql = "
        UPDATE {$siteprefix}listings SET
            title = '$listingTitle',
            categories = '$categories',
            subcategories = '$subcategories',
            description = '$description',
            type = '$type',
            pricing_type = '$pricingType',
            price = '$price',
            price_min = '$priceMin',
            price_max = '$priceMax',
            pricing_notes = '$pricingNotes',
            display_price = '$displayPrice',
            availability = '$availability',
            limited_slot = '$limited_slot',
            capacity = '$capacity',
            delivery = '$delivery',
            coverage = '$coverage',
            status = '$status',
            suspend_reason = '$suspendReason',
            updated_at = NOW()
        WHERE listing_id = '$listingId'
    ";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }

    // ✅ Send notification emails if listing status changed
if ($oldStatus !== $status) {

    // --- LISTING ACTIVATED ---
    if ($status === 'active') {

        $emailSubject = "Your {$siteName} Listing \"{$listingTitle}\" is Now Active!";
        $emailMessage = "

            <p>Great news! Your listing <strong>\"{$listingTitle}\"</strong> on <strong>{$siteName}</strong> is now <span style='color:green;font-weight:bold;'>active</span>.</p>

            <p>You can now start receiving views, enquiries, and potential buyers.</p>

            <p style='margin-top:20px;'>
                <a href='{$siteurl}login' target='_blank'
                   style='display:inline-block;background:#4CAF50;color:#fff;
                          padding:10px 18px;text-decoration:none;border-radius:6px;
                          font-weight:bold;'>
                    Login to Your Dashboard
                </a>
            </p>

            <p>If you have any questions or need support, feel free to reply to this email.</p>";

        sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

    }

    // --- LISTING SUSPENDED ---
    if ($status === 'suspended') {

        $emailSubject = "Your {$siteName} Listing \"{$listingTitle}\" Has Been Suspended";
        
        $emailMessage = "
            <p>Hi {$firstName},</p>

            <p>We regret to inform you that your listing <strong>\"{$listingTitle}\"</strong> has been 
            <span style='color:red;font-weight:bold;'>suspended</span>.</p>

            <p><strong>Reason for suspension:</strong></p>
            <p style='background:#f8d7da;color:#721c24;padding:12px;border-radius:5px;'>
                {$suspendReason}
            </p>

            <p>If you believe this action was taken in error or you have resolved the issue, 
            please reach out to our support team:</p>

            <p><a href='mailto:{$siteMail}'>{$siteMail}</a></p>

            <p>We are here to help.</p>

            <p>Regards,<br>{$siteName} Team</p>
        ";

        sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

    }
}


    // ⚙️ Handle Variations
    if ($pricingType === 'Price Range') {
        $names = $postData['variation_name'] ?? [];
        $prices = $postData['variation_price'] ?? [];
        $ids = $postData['variation_id'] ?? [];

        $existingIds = [];

        for ($i = 0; $i < count($names); $i++) {
            $varId = intval($ids[$i] ?? 0);
            $varName = mysqli_real_escape_string($con, trim($names[$i]));
            $varPrice = floatval($prices[$i]);

            if (empty($varName) || $varPrice <= 0) continue;

            if ($varId > 0) {
                mysqli_query($con, "
                    UPDATE {$siteprefix}listing_variations
                    SET variation_name = '$varName', variation_price = '$varPrice'
                    WHERE id = '$varId' AND listing_id = '$listingId'
                ");
                $existingIds[] = $varId;
            } else {
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}listing_variations (listing_id, variation_name, variation_price)
                    VALUES ('$listingId', '$varName', '$varPrice')
                ");
                $existingIds[] = mysqli_insert_id($con);
            }
        }

        if (!empty($existingIds)) {
            $keepIds = implode(',', array_map('intval', $existingIds));
            mysqli_query($con, "DELETE FROM {$siteprefix}listing_variations WHERE listing_id = '$listingId' AND id NOT IN ($keepIds)");
        } else {
            mysqli_query($con, "DELETE FROM {$siteprefix}listing_variations WHERE listing_id = '$listingId'");
        }
    }

    return ['status' => 'success', 'messages' => 'Listing updated successfully!'];
}



function addvendorForumEndpoint($postData, $fileData)
{
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // 🧹 Sanitize inputs
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $user        = intval($postData['user'] ?? 0);
    $status      = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending')); // now uses post status
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    // ✅ Validation
    if (empty($title) || empty($article) || $user == 0) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Fetch user type
    $userQuery = mysqli_query($con, "SELECT user_type FROM {$siteprefix}users WHERE id = '$user' LIMIT 1");
    if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Invalid user.", "red")];
    }

    $userData = mysqli_fetch_assoc($userQuery);
    $userType = strtolower($userData['user_type']);

    // ✅ Check article limit ONLY if status = active
    if ($status === 'active') {
         $articleLimit = getFeatureLimit($con, $user, 'article_limit', $siteprefix);
        $existingArticles = mysqli_num_rows(mysqli_query($con, "SELECT id FROM {$siteprefix}forums WHERE user_id = '$user' AND status = 'active'"));

        if ($articleLimit !== 'unlimited' && $existingArticles >= $articleLimit) {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "You’ve reached your active article limit ({$articleLimit}). Please upgrade your plan to publish more articles.",
                    "red"
                )
            ];
        }
    }

    // ✅ Create unique slug
    $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $check = $con->prepare("SELECT COUNT(*) AS count FROM {$siteprefix}forums WHERE slug = ?");
        $check->bind_param("s", $slug);
        $check->execute();
        $result = $check->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) break;
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // ✅ Handle featured image upload
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $featuredImage = "";
    if (!empty($fileData["featured_image"]["name"])) {
        $fileName = basename($fileData["featured_image"]["name"]);
        $featuredImage = uniqid('forum_') . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        move_uploaded_file($fileData["featured_image"]["tmp_name"], $targetDir . $featuredImage);
    }

    // ✅ Insert forum record
    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}forums 
        (user_id, title, article, featured_image, tags, categories, subcategories, status, created_at, slug, views)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 0)
    ");
    $stmt->bind_param("issssssss", $user, $title, $article, $featuredImage, $tags, $category, $subcategory, $status, $slug);

    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'messages' => ($status === 'active')
                ? 'Blog published successfully!'
                : 'Blog saved as draft and sent for approval.'
        ];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . $stmt->error, "red")];
    }
}


// add category


function addcategoryendpoint($postData) {
    global $con, $siteprefix;

    // Sanitize inputs
    $categoryName = mysqli_real_escape_string($con, trim($postData['categoryName'] ?? ''));
    $parentId = isset($postData['parentId']) && $postData['parentId'] !== '' ? intval($postData['parentId']) : 'NULL';

    if (empty($categoryName)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Category name is required.", "red")
        ];
    }

    // Generate base slug
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $categoryName), '-'));

    // Make slug unique
    $alt_title = $baseSlug;
    $counter = 1;
    while (true) {
        $slugCheckQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}categories WHERE slug = '$alt_title'";
        $slugResult = mysqli_query($con, $slugCheckQuery);
        $slugRow = mysqli_fetch_assoc($slugResult);

        if ($slugRow['count'] == 0) {
            break; // slug is unique
        }

        $alt_title = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Check if category already exists under same parent
    $checkQuery = "SELECT COUNT(*) AS count 
                   FROM {$siteprefix}categories 
                   WHERE parent_id <=> $parentId 
                   AND category_name = '$categoryName'";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage(
                "Category \"$categoryName\" already exists under the selected parent.",
                "red"
            )
        ];
    } else {
        // Insert category
        $insertQuery = "INSERT INTO {$siteprefix}categories (parent_id, category_name, slug) 
                        VALUES ($parentId, '$categoryName', '$alt_title')";
        if (mysqli_query($con, $insertQuery)) {
            return [
                'status' => 'success',
                'messages' =>
                    "Category \"$categoryName\" created successfully!"
            ];
        } else {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "Failed to create category: " . mysqli_error($con),
                    "red"
                )
            ];
        }
    }
}



function addsubcategoryendpoint($postData) {
    global $con, $siteprefix;

    // Sanitize and validate inputs
    $parentId = isset($_POST['parentId']) ? intval($_POST['parentId']) : 0;
    $subCategoryName = isset($_POST['subCategoryName']) ? trim($_POST['subCategoryName']) : '';

    if ($parentId <= 0 || empty($subCategoryName)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Invalid parent category or sub-category name.", "red")
        ];
    }

    // Escape sub-category name for safety
    $subCategoryNameEsc = mysqli_real_escape_string($con, $subCategoryName);

    // Generate base slug from sub-category name
    $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $subCategoryName));
    $baseSlug = trim($baseSlug, '-');

    // Ensure slug is unique
    $alt_title = $baseSlug;
    $counter = 1;
    $slugQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}categories WHERE slug = ?";
    $stmt = $con->prepare($slugQuery);

    if (!$stmt) {
        return [
            'status' => 'error',
            'messages' => "Database error: " . mysqli_error($con)
        ];
    }

    while (true) {
        $stmt->bind_param("s", $alt_title);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) break;

        $alt_title = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Check for duplicate sub-category under the same parent
    $checkQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}categories WHERE parent_id = ? AND category_name = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->bind_param("is", $parentId, $subCategoryName);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $row = $checkResult->fetch_assoc();

    if ($row['count'] > 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Category \"$subCategoryName\" already exists under the selected parent.", "red")
        ];
    }

    // Insert sub-category with unique slug
    $insertQuery = "INSERT INTO {$siteprefix}categories (parent_id, category_name, slug) VALUES (?, ?, ?)";
    $insertStmt = $con->prepare($insertQuery);
    $insertStmt->bind_param("iss", $parentId, $subCategoryName, $alt_title);

    if ($insertStmt->execute()) {
        return [
            'status' => 'success',
            'messages' => "Sub-category \"$subCategoryName\" added successfully!"
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => "Failed to add sub-category: " . $insertStmt->error
        ];
    }
}


function getallnotifications($con)
{
    global $siteprefix;

    $query = "SELECT * FROM ".$siteprefix."alerts WHERE status = 0 ORDER BY s DESC";

    $result = mysqli_query($con, $query);

    if ($result) {
        $notificationData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $notificationData[] = $row;
        }
        return $notificationData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}




function getallblog($con)
{
    global $siteprefix;

    // ✅ Join forums with users table
    // ✅ Fetch all matching category/subcategory names using GROUP_CONCAT
    $query = "
        SELECT 
            f.*, 
            u.first_name, 
            u.last_name,
            u.photo,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, f.categories)
            ) AS category_names,
           
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, f.subcategories)
            ) AS subcategory_names
        FROM {$siteprefix}forums AS f
        LEFT JOIN {$siteprefix}users AS u 
            ON f.user_id = u.id
        ORDER BY f.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $blogData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $blogData[] = $row;
        }
        return $blogData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getalldisputestickets($con)
{
    global $siteprefix;

    // ✅ Join forums with users table
    // ✅ Fetch all matching category/subcategory names using GROUP_CONCAT
    $query = "SELECT d.*,u.first_name,u.last_name 
            FROM {$siteprefix}disputes d
            LEFT JOIN {$siteprefix}users u ON d.user_id = u.id
            ORDER BY d.created_at DESC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $disputeData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $disputeData[] = $row;
        }
        return $disputeData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getallcategory($con)
{
    global $siteprefix;

    // ✅ Join forums with users table
    // ✅ Fetch all matching category/subcategory names using GROUP_CONCAT
    $query = "SELECT id, category_name FROM {$siteprefix}categories WHERE parent_id IS NULL";

    $result = mysqli_query($con, $query);

    if ($result) {
        $categoryData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryData[] = $row;
        }
        return $categoryData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}




function getallsubcategory($con)
{
    global $siteprefix;

    // ✅ Join forums with users table
    $query = "SELECT 
          sc.id AS sub_id, 
          sc.category_name AS subcategory, 
          pc.category_name AS parent_category
        FROM {$siteprefix}categories sc
        INNER JOIN {$siteprefix}categories pc ON sc.parent_id = pc.id
        WHERE sc.parent_id IS NOT NULL
        GROUP BY sc.id, sc.category_name, pc.category_name
        ORDER BY pc.category_name ASC, sc.category_name ASC ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $categoryData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryData[] = $row;
        }
        return $categoryData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}
// all wallet

function getallwallet($con)
{
global $siteprefix;

$query = "SELECT w.*, u1.first_name,u1.last_name, u1.email AS email, u1.phone AS mobile_number 
        FROM " . $siteprefix . "withdrawal w  
        LEFT JOIN ".$siteprefix."users u1 ON w.user = u1.id 
        ORDER BY w.date DESC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $walletData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $walletData[] = $row;
        }
        return $walletData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getAllManualPayments($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            mp.id AS mid,
            mp.order_id,
            mp.user_id,
            mp.status,
            mp.amount,
            mp.proof,
            mp.date_created,
            u.first_name,
            u.last_name,
            u.email
        FROM {$siteprefix}manual_payments mp
        JOIN {$siteprefix}users u ON mp.user_id = u.id
        ORDER BY mp.date_created DESC
    ";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $manualData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $manualData[] = $row;
    }

    return $manualData;
}


//get questions

function getallquestions($con)
{
    global $siteprefix;

    // ✅ Join questions with users table to fetch author info
    $query = "
        SELECT 
            q.*, 
            u.first_name, 
            u.last_name,
            u.photo,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, q.categories)
            ) AS category_names,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, q.subcategories)
            ) AS subcategory_names
        FROM {$siteprefix}questions AS q
        LEFT JOIN {$siteprefix}users AS u 
            ON q.user_id = u.id
        ORDER BY q.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $questionData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $questionData[] = $row;
        }
        return $questionData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

//user data

function getBookings($con) {
    global $siteprefix;

    $query = "
        SELECT 
            b.*, 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.photo
        FROM {$siteprefix}bookings AS b
        LEFT JOIN {$siteprefix}users AS u 
            ON b.therapist_id = u.id
        ORDER BY b.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $bookingData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $bookingData[] = $row;
        }
        return $bookingData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}



function getallgroups($con)
{
    global $siteprefix;

    // ✅ Join forums with users table to fetch author info
  $query = "
    SELECT 
        g.*, 
        u.first_name, 
        u.last_name,
        u.photo,
        u.email,
        (
            SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
            FROM {$siteprefix}categories AS c
            WHERE FIND_IN_SET(c.id, g.category)
        ) AS category_names,
        (
            SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
            FROM {$siteprefix}categories AS sc
            WHERE FIND_IN_SET(sc.id, g.subcategory)
        ) AS subcategory_names,
        (
            SELECT COUNT(*) 
            FROM {$siteprefix}group_members AS gm 
            WHERE gm.group_id = g.id
        ) AS member_count,
        (
            SELECT COUNT(*) 
            FROM {$siteprefix}group_members AS gm2 
            WHERE gm2.group_id = g.id AND gm2.status = 'pending'
        ) AS pending_count
    FROM {$siteprefix}groups AS g
    LEFT JOIN {$siteprefix}users AS u 
        ON g.user_id = u.id
    ORDER BY g.created_at DESC
";

    $result = mysqli_query($con, $query);

    if ($result) {
        $groupData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $groupData[] = $row;
        }
        return $groupData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getallgroupsmembers($con)
{
    global $siteprefix;

    // ✅ Join forums with users table to fetch author info
    $query = "
        SELECT 
            g.*, g2.group_name,
            u.first_name, 
            u.last_name,
            u.photo
        FROM {$siteprefix}group_members AS g
        LEFT JOIN {$siteprefix}users AS u 
        ON g.user_id = u.id

        LEFT JOIN {$siteprefix}groups AS g2
        ON g.group_id = g2.id
        ORDER BY g.joined_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $groupData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $groupData[] = $row;
        }
        return $groupData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}



function getallusers($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            u.*, 
            (
                SELECT GROUP_CONCAT(name SEPARATOR ', ')
                FROM {$siteprefix}profession AS p
                WHERE FIND_IN_SET(p.id, u.professional_field)
            ) AS professional_field_names,
            (
                SELECT GROUP_CONCAT(name SEPARATOR ', ')
                FROM {$siteprefix}profession AS p
                WHERE FIND_IN_SET(p.id, u.professional_title)
            ) AS professional_title_names
        FROM {$siteprefix}users AS u
        ORDER BY RAND()
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $userData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $userData[] = $row;
        }
        return $userData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function deleteblogEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Blog ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}forums WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete blog: ' . mysqli_error($con);
}


function deletereviewEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Review ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}reviews WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete blog: ' . mysqli_error($con);
}

function deletesubcategoryEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Subcategory ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}categories WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete subcategory: ' . mysqli_error($con);
}

function deletevideoEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["file_name"])) return "Video file name is missing.";
    $fileName = mysqli_real_escape_string($con, $postData["file_name"]);

    // Delete from database
    $query = "DELETE FROM {$siteprefix}listing_videos WHERE file_name = '$fileName'";
    $result = mysqli_query($con, $query);

    // Delete from uploads folder
    $filePath = "../uploads/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);

    return $result ? "Video deleted successfully." : "Failed to delete video: " . mysqli_error($con);
}


function deleteimageEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["file_name"])) return "Image file name is missing.";
    $fileName = mysqli_real_escape_string($con, $postData["file_name"]);

    // Delete from database
    $query = "DELETE FROM {$siteprefix}listing_images WHERE file_name = '$fileName'";
    $result = mysqli_query($con, $query);

    // Delete from uploads folder
    $filePath = "../uploads/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);

    return $result ? "Image deleted successfully." : "Failed to delete image: " . mysqli_error($con);
}


function approvewithdrawal($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency;

    // ✅ Validate required fields
    if (empty($postData["image_id"])) {
        return "Missing withdrawal or user ID.";
    }

    $therow = mysqli_real_escape_string($con, $postData["image_id"]);
    $date = date('Y-m-d H:i:s');

    // ✅ Fetch withdrawal record
    $result = mysqli_query($con, "SELECT * FROM {$siteprefix}withdrawal WHERE s = '$therow' LIMIT 1");
    $withdrawal = mysqli_fetch_assoc($result);

    if (!$withdrawal) {
        return "Withdrawal record not found.";
    }

    // ✅ Extract withdrawal details
    $amount       = $withdrawal['amount'];
    $bank         = $withdrawal['bank'];
    $bank_name    = $withdrawal['bank_name'];
    $user        = $withdrawal['user'];
    $bank_number  = $withdrawal['bank_number'];
    $thedate      = date("l, F j, Y g:i A", strtotime($withdrawal['date']));

    // ✅ Fetch user info
    $uResult = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id = '$user' LIMIT 1");
    $userData = mysqli_fetch_assoc($uResult);

    if (!$userData) {
        return "User not found.";
    }

    // ✅ Use your correct DB column names
    $user_email = $userData['email'];
    $user_name  = $userData['first_name'];
    $user_phone = $userData['phone'];
    $currency   = $sitecurrency;

    // ✅ Update withdrawal status
    $update = mysqli_query($con, "UPDATE {$siteprefix}withdrawal SET status = 'paid' WHERE s = '$therow'");

    if (!$update) {
        return "Failed to update withdrawal status: " . mysqli_error($con);
    }

    // ✅ Email content
    $emailSubject = "Withdrawal Request Paid ({$currency}{$amount})";
    $emailMessage = "
        <p>Dear {$user_name},</p>
        <p>Your withdrawal request made on <strong>{$thedate}</strong> for <strong>{$currency}{$amount}</strong> has been successfully <strong>paid</strong> into your account.</p>
        <p><strong>Bank:</strong> {$bank}<br>
        <strong>Account Number:</strong> {$bank_number}<br>
        <strong>Account Name:</strong> {$bank_name}</p>
        <p>Thank you for using our platform!</p>
    ";

    // ✅ Send email (follow your function format)
    sendEmail($user_email, $sitename, $sitemail, $user_name, $emailMessage, $emailSubject);

    // ✅ Insert alert
    $alertMessage = "Your withdrawal request of {$currency}{$amount} has been paid successfully.";
    insertAlert($con, $user, $alertMessage, $date, 0);

    // ✅ Return structured response
    return [
        'status' => 'success',
        'message' => "Withdrawal approved and user notified successfully."
    ];
}

function rejectManualPayment($postData) {
    global $con, $siteprefix, $siteName, $siteMail, $sitecurrency;

    // ✅ Validate required fields
    if (empty($postData["image_id"]) || empty($postData["reason"])) {
        return "Missing payment ID or rejection reason.";
    }

    $id     = mysqli_real_escape_string($con, $postData["image_id"]);
    $reason = mysqli_real_escape_string($con, $postData["reason"]);
    $date   = date('Y-m-d H:i:s');

    // ✅ Fetch payment record
    $paymentQuery = mysqli_query($con, "SELECT * FROM {$siteprefix}manual_payments WHERE id = '$id' LIMIT 1");
    $payment = mysqli_fetch_assoc($paymentQuery);

    if (!$payment) {
        return "Manual payment record not found.";
    }

    $order_id = $payment['order_id'];
    $user_id  = $payment['user_id'];
    $amount   = $payment['amount'];
    $proof    = $payment['proof'];
    $paymentDate = date("l, F j, Y g:i A", strtotime($payment['date_created']));

    // ✅ Fetch user info
    $userQuery = mysqli_query($con, "SELECT first_name, email FROM {$siteprefix}users WHERE id = '$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($userQuery);

    if (!$user) {
        return "User record not found.";
    }

    $user_name  = $user['first_name'];
    $user_email = $user['email'];

    // ✅ Update payment status
    $update = mysqli_query($con, "
        UPDATE {$siteprefix}manual_payments 
        SET status = 'payment resend', rejection_reason = '$reason'
        WHERE id = '$id'
    ");

    if (!$update) {
        return "Failed to update payment status: " . mysqli_error($con);
    }

    // ✅ Email content
    $emailSubject = "Payment Rejected for Order ID {$order_id}";
    $emailMessage = "
        <p>Your manual payment made on <strong>{$paymentDate}</strong> for 
        <strong>{$sitecurrency}{$amount}</strong> has been <strong>rejected</strong>.</p>
        <p><strong>Reason:</strong> <em>{$reason}</em></p>
        <p>Please resubmit a valid proof of payment to continue your order processing.</p>
        <p>Thank you for using {$siteName}.</p>
    ";

    // ✅ Send email
    sendEmail($user_email, $siteName, $siteMail, $user_name, $emailMessage, $emailSubject);

    // ✅ Insert alert
    $alertMessage = "Your manual payment for Order ID {$order_id} was rejected. Reason: {$reason}";
    insertAlert($con, $user_id, $alertMessage, $date, 0);

    // ✅ Return structured response
    return [
        'status'  => 'success',
        'message' => "Payment for Order ID {$order_id} has been rejected successfully and user notified."
    ];
}


function approveManualPayment($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency, $escrowfee;

    // ✅ Validate required fields
    if (empty($postData["image_id"])) {
        return "Missing manual payment ID.";
    }

    $payment_id = mysqli_real_escape_string($con, $postData["image_id"]);
    $currentdatetime = date('Y-m-d H:i:s');

    /* ========================================================
       ✅ Fetch manual payment record
    ========================================================= */
    $paymentQuery = mysqli_query($con, "SELECT * FROM {$siteprefix}manual_payments WHERE id = '$payment_id' LIMIT 1");
    $payment = mysqli_fetch_assoc($paymentQuery);

    if (!$payment) {
        return "Manual payment record not found.";
    }

    $order_id = $payment['order_id'];
    $amount   = $payment['amount'];

    /* ========================================================
       ✅ Update manual payment status
    ========================================================= */
    $updatePayment = mysqli_query($con, "
        UPDATE {$siteprefix}manual_payments 
        SET status = 'approved', rejection_reason = '' 
        WHERE id = '$payment_id'
    ");

    if (!$updatePayment) {
        return "Failed to update manual payment: " . mysqli_error($con);
    }

    /* ========================================================
       ✅ Fetch Order + Buyer Info
    ========================================================= */
    $orderQuery = mysqli_query($con, "
        SELECT 
            o.*, 
            u.first_name AS buyer_name, 
            u.email AS buyer_email
        FROM {$siteprefix}orders o
        LEFT JOIN {$siteprefix}users u ON o.user = u.id
        WHERE o.order_id = '$order_id'
        LIMIT 1
    ");
    $order = mysqli_fetch_assoc($orderQuery);

    if (!$order) {
        return "Order not found.";
    }

    $buyer_name  = $order['buyer_name'];
    $buyer_email = $order['buyer_email'];
    $user_id     = $order['user'];

    /* ========================================================
       ✅ Update Order Status
    ========================================================= */
    $updateOrder = mysqli_query($con, "
        UPDATE {$siteprefix}orders 
        SET status = 'paid', total_amount = '$amount', date = '$currentdatetime'
        WHERE order_id = '$order_id'
    ");

    if (!$updateOrder) {
        return "Failed to update order status: " . mysqli_error($con);
    }

    /* ========================================================
       ✅ Fetch Order Items + Sellers
    ========================================================= */
    $itemQuery = mysqli_query($con, "
        SELECT 
            i.listing_id,
            i.variation,
            i.price,
            i.type AS product_type,
            i.quantity,
            i.total_price,
            l.title AS listing_title,
            s.id AS seller_id,
            s.first_name AS seller_name,
            s.email AS seller_email,
            s.user_type AS seller_type
        FROM {$siteprefix}order_items i
        LEFT JOIN {$siteprefix}listings l ON i.listing_id = l.listing_id
        LEFT JOIN {$siteprefix}users s ON l.user_id = s.id
        WHERE i.order_id = '$order_id'
    ");
    $items = mysqli_fetch_all($itemQuery, MYSQLI_ASSOC);

    $currency = $sitecurrency ?? '₦';
    $commissionRate = $escrowfee ?? 0; // Default commission %

    /* ========================================================
       ✅ Process Each Item
    ========================================================= */
    foreach ($items as $item) {
        $listing_id   = $item['listing_id'];
        $listing      = $item['listing_title'];
        $variation    = $item['variation'];
        $quantity     = $item['quantity'];
        $total_price  = $item['total_price'];
        $seller_id    = $item['seller_id'];
        $seller_name  = $item['seller_name'];
        $seller_email = $item['seller_email'];
        $user_type    = $item['seller_type'];
        $product_type = strtolower($item['product_type']);

        // ✅ Calculate commission
        $admin_commission = $total_price * ($commissionRate / 100);
        $seller_amount    = $total_price - $admin_commission;

        // ✅ Update stock (limited slot)
        mysqli_query($con, "
            UPDATE {$siteprefix}listings 
            SET limited_slot = GREATEST(limited_slot - $quantity, 0)
            WHERE listing_id = '$listing_id'
        ");

        // ✅ Admin vs Seller listing
        if ($user_type === 'admin') {
            // Admin receives full amount
            mysqli_query($con, "
                INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
                VALUES ('$total_price', '$listing_id', '$order_id', 'Admin Direct Sale', '$currentdatetime')
            ");
        } else {
            // Seller receives balance after commission
            mysqli_query($con, "
                INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
                VALUES ('$admin_commission', '$listing_id', '$order_id', 'Commission from Sale', '$currentdatetime')
            ");

            mysqli_query($con, "
                UPDATE {$siteprefix}users 
                SET wallet = wallet + $seller_amount 
                WHERE id = '$seller_id'
            ");

            insertWallet($con, $seller_id, $seller_amount, "credit", "Earnings from Order #$order_id", $currentdatetime);
            insertAlert($con, $seller_id, "You received {$currency}{$seller_amount} for Order #{$order_id}", $currentdatetime, 0);
        }

        // ✅ Handle service items
        if ($product_type === 'service') {
            mysqli_query($con, "
                UPDATE {$siteprefix}service_bookings
                SET status = 'approved', payment_status = 'paid'
                WHERE order_id = '$order_id'
            ");
        }
    }

    /* ========================================================
       ✅ Buyer Confirmation Email
    ========================================================= */
    $emailSubject = "Payment Approved - Order #{$order_id}";
    $emailMessage = "
        <p>Your payment for <strong>Order #{$order_id}</strong> has been confirmed successfully.</p>
        <p><strong>Total Amount:</strong> {$currency}{$amount}</p>
        <p>Thank you for shopping with {$sitename}!</p>
    ";

    sendEmail($buyer_email, $sitename, $sitemail, $buyer_name, $emailMessage, $emailSubject);

    /* ========================================================
       ✅ Buyer Alert
    ========================================================= */
    $alertMessage = "Your manual payment for Order #{$order_id} has been approved successfully.";
    insertAlert($con, $user_id, $alertMessage, $currentdatetime, 0);

    /* ========================================================
       ✅ Return structured response
    ========================================================= */
    return [
        'status'  => 'success',
        'message' => "Manual payment approved, order marked as paid, and notifications sent successfully."
    ];
}



function approvebookings($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // ✅ Validate booking ID
    if (empty($postData["image_id"])) {
        return "Booking ID is missing.";
    }

    $id = mysqli_real_escape_string($con, $postData["image_id"]);

    // ✅ Fetch booking details
    $result = mysqli_query($con, "SELECT * FROM {$siteprefix}service_bookings WHERE id = '$id' LIMIT 1");
    $booking = mysqli_fetch_assoc($result);

    if (!$booking) {
        return "Booking not found.";
    }

    // ✅ Extract booking details
    $price = $booking['price'];
    $email = $booking['email'];
    $full_name = $booking['full_name'];
    $order_id = $booking['order_id'];

    // ✅ Generate payment link
    $payment_link = "{$siteurl}/pay?order_id={$order_id}";

    // ✅ Update booking status
    mysqli_query($con, "UPDATE {$siteprefix}service_bookings SET status = 'inprogress' WHERE id = '$id'");

    // ✅ Insert into ma_orders if not already exists
    $checkOrder = mysqli_query($con, "SELECT id FROM {$siteprefix}orders WHERE order_id = '$order_id' LIMIT 1");
    if (mysqli_num_rows($checkOrder) == 0) {
        mysqli_query($con, "
            INSERT INTO {$siteprefix}orders (order_id, user, status, total_amount, date)
            VALUES ('$order_id', '{$booking['user_id']}', 'unpaid', '$price', NOW())
        ");
    }

    else {
    // Order exists → update
            mysqli_query($con, "
                UPDATE {$siteprefix}orders 
                SET total_amount = '$price', status = 'unpaid'
                WHERE order_id = '$order_id'
            ");
        }

    // ✅ Prepare email
    $emailSubject = "Your Booking Has Been Approved — Proceed with Payment";
    $emailMessage = "
        <p>Your booking with <strong>Order ID: {$order_id}</strong> has been <strong>approved</strong>.</p>
        <p>Total Amount: ₦" . number_format($price, 2) . "</p>
        <p>You can complete your booking by clicking the link below to make payment:</p>
        <p><a href='{$payment_link}' style='background:#28a745;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>Pay Now</a></p>
        <p>Thank you for choosing our service!</p>
       
    ";

    // ✅ Send email
    sendEmail($email, $siteName, $siteMail, $full_name, $emailMessage, $emailSubject);

    return [
        'status' => 'success',
        'message' => "Booking approved and payment link sent successfully."
    ];
}



// submit ticket
function createDispute($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail, $user_id;

    // ✅ Validate required fields
    if (empty($postData['category']) || empty($postData['recipient']) || empty($postData['order_id']) || empty($postData['issue'])) {
        return ['status' => 'error', 'message' => 'All fields are required.'];
    }

    $category = mysqli_real_escape_string($con, $postData['category']);
    $recipient_id = intval($postData['recipient']);
    $user_id = $_POST['user_id'] ?? ' '; // Use provided user_id or global
    $order_id = mysqli_real_escape_string($con, $postData['order_id']);
    $issue = mysqli_real_escape_string($con, $postData['issue']);
    $ticket_number = "TKT" . time(); // Unique Ticket ID
    $date = date('Y-m-d H:i:s');

    // ✅ Insert dispute
    $sql = "INSERT INTO {$siteprefix}disputes 
            (user_id, recipient_id, ticket_number, category, order_reference, issue, created_at)
            VALUES ('$user_id', '$recipient_id', '$ticket_number', '$category', '$order_id', '$issue', '$date')";
    
    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'message' => "Database error: " . mysqli_error($con)];
    }

    $dispute_id = mysqli_insert_id($con);

    // ✅ Insert initial dispute message
    $sql2 = "INSERT INTO {$siteprefix}dispute_messages 
             (dispute_id, sender_id, message, file, created_at) 
             VALUES ('$ticket_number', '$user_id', '$issue', '', '$date')";
    mysqli_query($con, $sql2);

    // ✅ Handle multiple file uploads
    $uploadedFiles = [];
    if (isset($_FILES['evidence'])) {
        $uploadedFiles = handleMultipleFileUpload('evidence', '../uploads/');
        foreach ($uploadedFiles as $file) {
            $sqlFile = "INSERT INTO {$siteprefix}evidence (dispute_id, file_path, uploaded_at) 
                        VALUES ('$dispute_id', '$file', '$date')";
            mysqli_query($con, $sqlFile);
        }
    }

    // ✅ Send emails
    $emailSubject = "Dispute Submitted Successfully – Ticket No: $ticket_number";
    $emailMessage = "
        <p>Thank you for submitting your dispute. Ticket number: <strong>$ticket_number</strong>.</p>
        <p>Our support team will review and get back to you.</p>
        <p>Track your dispute: <a href='$siteurl'>Marriagehub.ng</a></p>
    ";
    sendEmail($siteMail, $siteName, $siteMail, $siteName, $emailMessage, $emailSubject);

    // ✅ Notify recipient
    if ($recipient_id) {
        $rDetails = getUserDetails($con, $siteprefix, $recipient_id);
        $r_email = $rDetails['email'];
        $r_name = $rDetails['first_name'];
        $r_emailSubject = "New Dispute Assigned – Ticket No: $ticket_number";
        $r_emailMessage = "<p>A new dispute has been submitted with you as the recipient. Login to your dashboard to review it.</p>";
        sendEmail($r_email, $siteName, $siteMail,  $r_name, $r_emailMessage, $r_emailSubject);

        $alertMessage = "New dispute submitted with you as recipient: $ticket_number";
        insertAlert($con, $recipient_id, $alertMessage, $date, 0);
    }

    // ✅ Admin notification
    $adminMessage = "A new dispute has been submitted ($ticket_number)";
    $link = "ticket.php?ticket_number=$ticket_number";
    insertadminAlert($con, $adminMessage, $link, $date, 'New Dispute', 1);

    return ['status' => 'success', 'message' => "Dispute submitted successfully. Ticket ID: $ticket_number"];
}

//reviews respond
function respondReview($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // ✅ Validate Review ID
    if (empty($postData["review_id"])) {
        return [
            'success' => false,
            'message' => "Review ID is missing."
        ];
    }

    $reviewId = mysqli_real_escape_string($con, $postData["review_id"]);
    $response = mysqli_real_escape_string($con, $postData["response"] ?? '');

    if (trim($response) === '') {
        return [
            'success' => false,
            'message' => "Response cannot be empty."
        ];
    }

    // ✅ Fetch Review Details
    $query = "
        SELECT r.*, 
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
               u.email AS customer_email, 
               l.title AS product_title
        FROM {$siteprefix}reviews r
        LEFT JOIN {$siteprefix}users u ON r.user_id = u.id
        LEFT JOIN {$siteprefix}listings l ON r.listing_id = l.listing_id
        WHERE r.id = '$reviewId' LIMIT 1
    ";
    $result = mysqli_query($con, $query);
    $review = mysqli_fetch_assoc($result);

    if (!$review) {
        return [
            'success' => false,
            'message' => "Review not found."
        ];
    }

    // ✅ Extract Info
    $customerEmail = $review['customer_email'] ?? '';
    $customerName  = trim($review['customer_name'] ?? 'Valued Customer');
    $productTitle  = $review['product_title'] ?? 'your product';
    $comment       = $review['comment'] ?? '';

    // ✅ Update Seller Response
    $update = "
        UPDATE {$siteprefix}reviews 
        SET seller_response = '$response', response_date = NOW() 
        WHERE id = '$reviewId'
    ";
    $ok = mysqli_query($con, $update);

    if (!$ok) {
        return [
            'success' => false,
            'message' => "Failed to save response. Please try again."
        ];
    }

    // ✅ Prepare Email
    $emailSubject = "Response to Your Review on {$siteName}";
    $emailMessage = "
        <p>Thank you for taking the time to review <strong>{$productTitle}</strong>.</p>
        <p><strong>Your Review:</strong></p>
        <blockquote style='border-left:3px solid #007bff;padding-left:10px;color:#555;'>{$comment}</blockquote>

        <p><strong>Seller’s Response:</strong></p>
        <blockquote style='border-left:3px solid #28a745;padding-left:10px;color:#555;'>{$response}</blockquote>

        <p>We appreciate your feedback and look forward to serving you again!</p>
    ";

    // ✅ Send email to customer
    if (!empty($customerEmail)) {
        sendEmail($customerEmail, $siteName, $siteMail, $customerName, $emailMessage, $emailSubject);
    }

    return [
        'success' => true,
        'message' => "Response saved successfully and email sent to the customer."
    ];
}


function getallUserWishlist($con, $user_id) {
    global $siteprefix;
    $user_id = mysqli_real_escape_string($con, $user_id);

    $query = "
        SELECT 
            w.id AS wishlist_id,
            w.date_added,
            l.id,
            l.listing_id,
            l.title,
            l.slug,
            l.pricing_type,
            l.price,
            l.price_min,
            l.price_max,
            (
                SELECT file_name
                FROM {$siteprefix}listing_images AS li
                WHERE li.listing_id = l.listing_id
                ORDER BY li.id ASC
                LIMIT 1
            ) AS featured_image,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, l.categories)
            ) AS category_names,
            l.status,
            u.first_name,
            u.last_name,
            u.photo AS seller_photo
        FROM {$siteprefix}wishlist AS w
        INNER JOIN {$siteprefix}listings AS l ON w.listing_id = l.id
        INNER JOIN {$siteprefix}users AS u ON l.user_id = u.id
        WHERE w.user_id = '$user_id'
        ORDER BY w.date_added DESC
    ";

    $result = mysqli_query($con, $query);
    $wishlistData = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $wishlistData[] = $row;
        }
    }
    return $wishlistData;
}



function rejectbookings($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // ✅ Validate booking ID
    if (empty($postData["image_id"])) {
        return "Booking ID is missing.";
    }

    $id = mysqli_real_escape_string($con, $postData["image_id"]);
    $reason = mysqli_real_escape_string($con, $postData["reason"] ?? '');

    // ✅ Fetch booking details
    $result = mysqli_query($con, "SELECT * FROM {$siteprefix}service_bookings WHERE id = '$id' LIMIT 1");
    $booking = mysqli_fetch_assoc($result);

    if (!$booking) {
        return "Booking not found.";
    }

    // ✅ Extract booking details
    $email      = $booking['email'];
    $full_name  = $booking['full_name'];
    $order_id   = $booking['order_id'];
    $listing_id = $booking['listing_id'];
    $price      = $booking['price'];

    // ✅ Update booking status to rejected & store reason
    $updateBooking = "
        UPDATE {$siteprefix}service_bookings 
        SET status = 'rejected', rejection_reason = '$reason' 
        WHERE id = '$id'
    ";
    mysqli_query($con, $updateBooking);

    // ✅ If this booking has a listing, restore 1 slot if applicable
    $listingQuery = mysqli_query($con, "SELECT limited_slot FROM {$siteprefix}listings WHERE listing_id = '$listing_id' LIMIT 1");
    if ($listingQuery && mysqli_num_rows($listingQuery) > 0) {
        $listing = mysqli_fetch_assoc($listingQuery);
        $limited_slot = trim($listing['limited_slot']);

        if ($limited_slot !== '' && is_numeric($limited_slot)) {
            $newSlot = (int)$limited_slot + 1;
            mysqli_query($con, "UPDATE {$siteprefix}listings SET limited_slot = '$newSlot' WHERE listing_id = '$listing_id'");
        }
    }

    // ✅ Prepare email to notify user
    $emailSubject = "Your Booking Has Been Rejected";
    $emailMessage = "
        <p>We regret to inform you that your booking with <strong>Order ID: {$order_id}</strong> has been <strong>rejected</strong>.</p>
        <p><strong>Reason for rejection:</strong></p>
        <blockquote style='border-left:3px solid #dc3545;padding-left:10px;color:#555;'>{$reason}</blockquote>
        <p>If you have any questions or wish to make another booking, please feel free to contact us.</p>
        <p>Thank you for your understanding.</p>
        <br> ";

    // ✅ Send rejection email
    sendEmail($email, $siteName, $siteMail, $full_name, $emailMessage, $emailSubject);

    return [
        'status' => 'success',
        'message' => "Booking rejected successfully and email sent to customer."
    ];
}


function fetchReviewById($reviewId) {
    global $con, $siteprefix;

    $reviewId = mysqli_real_escape_string($con, $reviewId);

    $query = "
        SELECT 
            r.id AS review_id,
            r.listing_id,
            r.user_id AS customer_id,
            r.comment,
            r.rating,
            r.seller_response,
            r.created_at,

            u.first_name AS customer_first_name,
            u.last_name AS customer_last_name,
            u.photo AS customer_photo,

            l.title AS listing_title,

            s.id AS seller_id,
            s.first_name AS seller_first_name,
            s.last_name AS seller_last_name,
            s.photo AS seller_photo

        FROM {$siteprefix}reviews r
        INNER JOIN {$siteprefix}listings l ON r.listing_id = l.listing_id
        INNER JOIN {$siteprefix}users u ON r.user_id = u.id
        INNER JOIN {$siteprefix}users s ON l.user_id = s.id
        WHERE r.id = '$reviewId'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    return ($result && mysqli_num_rows($result) > 0)
        ? mysqli_fetch_assoc($result)
        : null;
}



function fetchUserProductReviews($userId) {
    global $con, $siteprefix;

    $query = "
        SELECT 
            r.id AS review_id,
            r.listing_id,
            r.user_id AS customer_id,
            r.comment,
            r.rating,
            r.seller_response,
            r.created_at,
            u.first_name,
            u.last_name,
            u.photo AS customer_photo,
            l.title AS listing_title,
            s.first_name AS seller_first_name,
            s.last_name AS seller_last_name,
            s.photo AS seller_photo
        FROM {$siteprefix}reviews r
        INNER JOIN {$siteprefix}listings l ON r.listing_id = l.listing_id
        INNER JOIN {$siteprefix}users u ON r.user_id = u.id
        INNER JOIN {$siteprefix}users s ON l.user_id = s.id
        WHERE r.user_id = '$userId'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function fetchSellerProductReviews($sellerUserId) {
    global $con, $siteprefix;

    $query = "
        SELECT 
            r.id AS review_id,
            r.listing_id,
            r.user_id AS customer_id,
            r.comment,
            r.rating,
            r.seller_response,
            r.created_at,
            u.first_name,
            u.last_name,
            u.photo AS customer_photo,
            l.title AS listing_title
        FROM {$siteprefix}reviews r
        INNER JOIN {$siteprefix}listings l ON r.listing_id = l.listing_id
        INNER JOIN {$siteprefix}users u ON r.user_id = u.id
        WHERE l.user_id = '$sellerUserId'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function fetchTherapistReviews($userId) {
    global $con, $siteprefix;

    // sanitize
    $userId = mysqli_real_escape_string($con, $userId);

    $query = "
        SELECT 
            r.id AS review_id,
            r.therapist_id,
            r.user_id AS customer_id,
            r.comment,
            r.rating,
            r.seller_response,
            r.created_at,
            u.first_name,
            u.last_name,
            u.photo AS customer_photo
        FROM {$siteprefix}reviews r
        INNER JOIN {$siteprefix}users u ON r.user_id = u.id
        WHERE r.therapist_id = '$userId'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

//read notifications
function markAllNotificationsRead($postData) {
    global $con, $siteprefix;
    $update = mysqli_query($con, "UPDATE {$siteprefix}alerts SET status = 1 WHERE status = 0");
    return $update ? "All notifications have been marked as read." : "Failed to update notifications: " . mysqli_error($con);
}

function markAlluserNotificationsRead($postData) {
    global $con, $siteprefix;

    // Safely get user_id
    $userid = isset($postData['user_id']) ? intval($postData['user_id']) : 0;

    // Only update for the specified user
    if ($userid > 0) {
        $update = mysqli_query($con, "UPDATE {$siteprefix}notifications SET status = 1 WHERE status = 0 AND user = '$userid'");
    } else {
        return "Invalid user ID.";
    }

    // Return result message
    return $update ? "All notifications have been marked as read." : "Failed to update notifications: " . mysqli_error($con);
}


function deletelistingEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Listing ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}listings WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete blog: ' . mysqli_error($con);
}

function deletecategoryEndpoint($postData) {
    global $con, $siteprefix;

    // Validate input
    if (empty($postData["image_id"])) {
        return [
            'status' => 'error',
            'messages' => "Category ID is missing."
        ];
    }

    $categoryId = intval($postData["image_id"]);
    $table = "categories";

    // Step 1: Delete all subcategories under this category
    $sqlSub = "DELETE FROM {$siteprefix}{$table} WHERE parent_id = ?";
    $stmtSub = $con->prepare($sqlSub);
    if (!$stmtSub) {
        return [
            'status' => 'error',
            'messages' => "Failed to prepare subcategory deletion: " . mysqli_error($con)
        ];
    }
    $stmtSub->bind_param("i", $categoryId);
    $stmtSub->execute();

    // Step 2: Delete the main category
    $sqlMain = "DELETE FROM {$siteprefix}{$table} WHERE id = ?";
    $stmtMain = $con->prepare($sqlMain);
    if (!$stmtMain) {
        return [
            'status' => 'error',
            'messages' => "Failed to prepare main category deletion: " . mysqli_error($con)
        ];
    }
    $stmtMain->bind_param("i", $categoryId);

    if ($stmtMain->execute()) {
        return [
            'status' => 'success',
            'messages' => "Category and its subcategories deleted successfully."
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => "Failed to delete category: " . $stmtMain->error
        ];
    }
}

function deletequestionEndpoint($postData) {
 global $con,$siteprefix;
    if (!isset($postData["question_id"])) return "Question ID is missing.";
    $questionId = mysqli_real_escape_string($con, $postData["question_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}questions WHERE id= '$questionId'") ? 'Deleted Successfully.' : 'Failed to delete question: ' . mysqli_error($con);
}

function deleteusersEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "User ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}users WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete user: ' . mysqli_error($con);
}

function deleteplansEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Plan ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}subscriptions WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete user: ' . mysqli_error($con);
}

function deletegroupEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Group ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}groups WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete group: ' . mysqli_error($con);
}   

function deletegroupmembersEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Group ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}group_members WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete group member: ' . mysqli_error($con);
}  


function request_join_groupEndpoint($postData) {
    global $con, $siteprefix, $siteName, $siteMail;

    $group_id = $postData['group_id'] ?? null;
    $user_id  = $postData['user_id'] ?? null;
    $currentdatetime = date('Y-m-d H:i:s');

    if (!$user_id) {
        return "Please log in to join this group.";
    }

    // Check if already a member or pending
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}group_members WHERE group_id='$group_id' AND user_id='$user_id' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        return "You already requested or joined this group.";
    }

    // Get the user (who is requesting)
    $userQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
    $userData = mysqli_fetch_assoc($userQuery);
    $first_name = $userData['first_name'] ?? '';
    $last_name  = $userData['last_name'] ?? '';

    // Get the group owner (admin of the group)
    $groupQuery = mysqli_query($con, "SELECT user_id, group_name FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    $groupData = mysqli_fetch_assoc($groupQuery);
    $admin_id = $groupData['user_id'] ?? null;
    $group_name = $groupData['group_name'] ?? '';

    if (!$admin_id) {
        return "Error: Group not found.";
    }

    // Insert pending membership request
    $insertQuery = "INSERT INTO {$siteprefix}group_members (group_id, user_id, role, status, joined_at)
                    VALUES ('$group_id', '$user_id', 'member', 'pending', '$currentdatetime')";

    if (mysqli_query($con, $insertQuery)) {

        // Create alert message for the admin
        $message = "User $first_name $last_name has requested to join your group $group_name.";

        // Insert into alerts table (if you have one)
        insertAlert($con, $admin_id, $message, $currentdatetime, 0);

        // (Optional) Send email notification to admin
        $adminQuery = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$admin_id' LIMIT 1");
        $adminData = mysqli_fetch_assoc($adminQuery);
        $adminEmail = $adminData['email'] ?? '';
        $adminName  = $adminData['first_name'] ?? '';

        if (!empty($adminEmail)) {
            $emailSubject = "New Join Request for Your Group";
            $emailMessage = "
                {$first_name} {$last_name} has requested to join your group <b>{$group_name}</b>.<br><br>
                Please review and approve the request in your dashboard.<br><br>
                Best Regards,<br>
                {$siteName} Team
            ";
            sendEmail($adminEmail, $siteName, $siteMail, $adminName, $emailMessage, $emailSubject);
        }

        return "Request sent successfully. Waiting for admin approval.";
    } else {
        return "Error: " . mysqli_error($con);
    }
}


function withdrawWalletEndpoint($postData) {
    global $con,$siteprefix, $currentdatetime, $sitecurrency, $siteName, $siteMail;

    // ✅ Sanitize input
    $user_id = mysqli_real_escape_string($con, $postData['user_id']);
    $bank    = mysqli_real_escape_string($con, $postData['bank']);
    $bankname= mysqli_real_escape_string($con, $postData['bankname']);
    $bankno  = mysqli_real_escape_string($con, $postData['bankno']);
    $amount  = mysqli_real_escape_string($con, $postData['amount']);
    $status  = "pending";
    $date    = $currentdatetime;

    // ✅ Fetch user email and first name
    $userQuery = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
    if (!$userQuery || mysqli_num_rows($userQuery) == 0) {
        return ['status' => 'error', 'messages' => generateMessage("User not found.", "red")];
    }
    $userData = mysqli_fetch_assoc($userQuery);
    $userEmail = $userData['email'];
    $userName  = $userData['first_name'];

    // ✅ Insert withdrawal and update wallet
    insertWithdraw($con, $user_id, $amount, $bank, $bankname, $bankno, $date, $status);

    // ✅ Prepare emails
    $emailSubject = "Withdrawal Request - Received";

    // Email to user
    $emailMessage = "<p>We have successfully received your withdrawal request of ₦$amount. Your request is now being processed and will be completed within the next 24 hours.</p>";
    sendEmail($userEmail, $siteName, $siteMail, $userName, $emailMessage, $emailSubject);

    // Email to admin
    $adminEmail = $siteMail;   // admin email
    $adminName  = $siteName;   // admin name (site name)
    $emailMessage_admin = "<p>A new withdrawal request has been received for ₦$amount. Please login to your dashboard to process it.</p>";
    sendEmail($adminEmail, $siteName, $siteMail, $adminName, $emailMessage_admin, $emailSubject);

    // ✅ Insert admin alert
    $adminMessage = "New Withdrawal Request - $sitecurrency$amount";
    $link = "withdrawals.php";
    $msgType = "New Withdrawal";
    $messageStatus = 1;
    insertadminAlert($con, $adminMessage, $link, $date, $msgType, $messageStatus);

    return ['status' => 'success', 'messages' => generateMessage("Withdrawal request of ₦$amount submitted successfully!", "green")];
}



//book therapy
function bookTherapySessionEndpoint($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $currentdatetime = date('Y-m-d H:i:s');

    // ✅ Extract & sanitize form inputs
    $therapist_id   = $postData['therapist_id'] ?? null;
    $user_id        = $postData['user_id'] ?? null;
    $client_name    = mysqli_real_escape_string($con, trim($postData['client_name'] ?? ''));
    $client_email   = mysqli_real_escape_string($con, trim($postData['client_email'] ?? ''));
    $preferred_day  = mysqli_real_escape_string($con, trim($postData['preferred_day'] ?? ''));
    $preferred_time = mysqli_real_escape_string($con, trim($postData['preferred_time'] ?? ''));
    $message        = mysqli_real_escape_string($con, trim($postData['message'] ?? ''));
    $amount         = floatval($postData['amount'] ?? 0.00);

    // ✅ Basic validation
    if (!$user_id || !$therapist_id) {
        return ['status' => 'error', 'message' => 'Please log in to book a therapist.'];
    }

    if (empty($client_name) || empty($client_email) || empty($preferred_day) || empty($preferred_time)) {
        return ['status' => 'error', 'message' => 'All required fields must be filled.'];
    }

    // ✅ Convert preferred day to next date
    $dayOfWeek = date('w', strtotime($preferred_day));
    $today = date('w');
    $daysUntil = ($dayOfWeek - $today + 7) % 7;
    $targetDate = date('Y-m-d', strtotime("+$daysUntil day"));
    $consultation_date = date('Y-m-d H:i:s', strtotime("$targetDate $preferred_time"));

    // ✅ Generate booking reference
    $reference = strtoupper(uniqid('BOOK_'));

    // ✅ Insert booking record
    $query = "
        INSERT INTO {$siteprefix}bookings 
        (therapist_id, user_id, client_name, client_email, consultation_date, message, booking_status, payment_status, amount, reference, created_at)
        VALUES 
        ('$therapist_id', '$user_id', '$client_name', '$client_email', '$consultation_date', '$message', 'pending', 'unpaid', '$amount', '$reference', '$currentdatetime')
    ";

    if (mysqli_query($con, $query)) {

        // ✅ Fetch therapist info
        $therapistQuery = mysqli_query($con, "SELECT first_name, email FROM {$siteprefix}users WHERE id='$therapist_id' LIMIT 1");
        if ($therapistQuery && mysqli_num_rows($therapistQuery) > 0) {
            $tData = mysqli_fetch_assoc($therapistQuery);
            $therapist_name = $tData['first_name'];
            $therapist_email = $tData['email'];

            // ✅ Prepare email details
            $emailSubject = "New Booking Request from $client_name";
            $emailMessage = "
              
                <p>You have received a new booking request from <strong>$client_name</strong> on <strong>$preferred_day</strong> at <strong>$preferred_time</strong>.</p>
                <p><strong>Client Email:</strong> $client_email</p>
                <p><strong>Message:</strong> " . (!empty($message) ? $message : 'No message provided.') . "</p>
                <p><strong>Booking ID:</strong> $reference</p>
                <p>You can log in to your $siteName dashboard to view and manage this booking:</p>
            ";

            // ✅ Send email to therapist
            sendEmail($therapist_email, $siteName, $siteMail, $therapist_name, $emailMessage, $emailSubject);
        }

        // ✅ Optional: send confirmation email to client
        $clientSubject = "Booking Request Submitted - $siteName";
        $clientMessage = "
       
            <p>Your booking request has been successfully submitted to your therapist.</p>
            <p><strong>Consultation Date:</strong> $preferred_day at $preferred_time</p>
            <p><strong>Booking ID:</strong> $reference</p>
            <p>We’ll notify you once your therapist confirms your appointment.</p>
            <p>Best regards,<br>$siteName Team</p>
        ";
        sendEmail($client_email, $siteName, $siteMail, $client_name, $clientMessage, $clientSubject);

        // ✅ Insert alert notification for therapist (optional)
        $alertMsg = "New booking request from {$client_name} for {$preferred_day} at {$preferred_time}.";
        insertAlert($con, $therapist_id, $alertMsg, $currentdatetime, 0);

        return [
            'status' => 'success',
            'message' => "Your booking request has been sent successfully.",
            'booking_ref' => $reference,
            'redirect_url' => "{$siteurl}confirm-booking.php?ref=$reference"
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . mysqli_error($con)
        ];
    }
}



function joingroupEndpoint($postData) {
    global $con, $siteprefix;

    $group_id = $postData['group_id'] ?? null;
    $user_id  = $postData['user_id'] ?? null;
    $currentdatetime = date('Y-m-d H:i:s');

    if (!$user_id) {
        return "Please log in to join this group.";
    }

    // ✅ Check if already a member
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}group_members WHERE group_id='$group_id' AND user_id='$user_id' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        return "You already joined this group.";
    }

    // ✅ Fetch joining user info
    $userQuery = mysqli_query($con, "SELECT first_name, last_name FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
    $userData = mysqli_fetch_assoc($userQuery);
    $first_name = $userData['first_name'] ?? '';
    $last_name  = $userData['last_name'] ?? '';

    // ✅ Fetch group admin info
    $groupQuery = mysqli_query($con, "SELECT user_id, group_name FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    $groupData = mysqli_fetch_assoc($groupQuery);
    $admin_id = $groupData['user_id'] ?? null;
    $group_name = $groupData['group_name'] ?? '';

    if (!$admin_id) {
        return "Error: Group not found.";
    }

    // ✅ Insert new active membership
    $query = "INSERT INTO {$siteprefix}group_members (group_id, user_id, role, status, joined_at)
              VALUES ('$group_id', '$user_id', 'member', 'active', '$currentdatetime')";

    if (mysqli_query($con, $query)) {

        // ✅ Create alert message for the group admin
        $message = "User {$first_name} {$last_name} has joined your group {$group_name}.";

        // ✅ Insert alert notification
        insertAlert($con, $admin_id, $message, $currentdatetime, 0);

        return "You have successfully joined the group.";
    } else {
        return "Error: " . mysqli_error($con);
    }
}

function getAllListingBySlug($con, $slug)
{
    global $siteprefix;

    
    $slug = mysqli_real_escape_string($con, $slug);

   
    $query = "
        SELECT 
            l.*, 
            u.first_name, 
            u.last_name, 
            u.photo,

            (
                SELECT GROUP_CONCAT(file_name SEPARATOR ',')
                FROM {$siteprefix}listing_images AS li
                WHERE li.listing_id = l.listing_id
            ) AS all_images,

            (
                SELECT GROUP_CONCAT(file_name SEPARATOR ',')
                FROM {$siteprefix}listing_videos AS lv
                WHERE lv.listing_id = l.listing_id
            ) AS all_videos,

            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, l.categories)
            ) AS category_names,

            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, l.subcategories)
            ) AS subcategory_names

        FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u ON l.user_id = u.id
        WHERE l.slug = '$slug'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $listingData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $listing_id = $row['listing_id'];

        // ✅ Fetch variations
        $variationQuery = "
            SELECT 
                id, 
                listing_id, 
                variation_name, 
                variation_price, 
                created_at
            FROM {$siteprefix}listing_variations
            WHERE listing_id = '$listing_id'
            ORDER BY id ASC
        ";

        $variationResult = mysqli_query($con, $variationQuery);

        $variations = [];
        if ($variationResult) {
            while ($v = mysqli_fetch_assoc($variationResult)) {
                $variations[] = $v;
            }
        }

        $row['variations'] = $variations;
        $listingData[] = $row;
    }

    return $listingData;
}


//exit group
function exitGroupEndpoint($postData) {
    global $con, $siteprefix;

    $group_id = mysqli_real_escape_string($con, $postData['group_id']);
    $user_id  = mysqli_real_escape_string($con, $postData['user_id']);

    if (empty($user_id)) {
        return "Login required.";
    }

    $query = "DELETE FROM {$siteprefix}group_members WHERE group_id = '$group_id' AND user_id = '$user_id'";

    if (mysqli_query($con, $query)) {
        return "You have successfully exited the group.";
    } else {
        return "Error leaving group: " . mysqli_error($con);
    }
}

function editVendorEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;
    $messages = '';

    // ✅ Vendor ID (required)
    $vendorId = $postData['user_id'];

    // ✅ Sanitize input
    $title       = mysqli_real_escape_string($con, $postData['title']);
    $firstName   = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName  = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName    = mysqli_real_escape_string($con, $postData['last_name']);
    $dob         = mysqli_real_escape_string($con, $postData['dob']);
    $gender      = mysqli_real_escape_string($con, $postData['gender']);
    $nationality = mysqli_real_escape_string($con, $postData['nationality']);
    $languages   = mysqli_real_escape_string($con, $postData['languages']);

    $businessName       = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness  = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName           = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone               = mysqli_real_escape_string($con, $postData['phone']);
    $website             = mysqli_real_escape_string($con, $postData['website']);
    $email               = mysqli_real_escape_string($con, $postData['email']);
    $stateResidence      = mysqli_real_escape_string($con, $postData['state_residence']);
    $address             = mysqli_real_escape_string($con, $postData['address']);
    $bank_name = mysqli_real_escape_string($con, $_POST['bank_name']);
    $bank_accname = mysqli_real_escape_string($con, $_POST['bank_accname']);
    $bank_number = mysqli_real_escape_string($con, $_POST['bank_number']);

    $facebook  = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter   = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin  = mysqli_real_escape_string($con, $postData['linkedin']);
    $consent   = isset($postData['consent']) ? 1 : 0;

    $categoryId = isset($postData['category']) ? implode(",", $postData['category']) : '';
    $subcategoryId = isset($postData['subcategory']) ? implode(",", $postData['subcategory']) : '';
    $services       = mysqli_real_escape_string($con, $postData['services']);
    $experience     = mysqli_real_escape_string($con, $postData['experience_years']);
    $coverage       = isset($postData['coverage']) ? mysqli_real_escape_string($con, implode(",", $postData['coverage'])) : '';
    $onsite         = mysqli_real_escape_string($con, $postData['onsite']);
    $availability   = mysqli_real_escape_string($con, $postData['availability']);

    // ✅ Handle file uploads
    $targetDir = "../uploads/";

    // Fetch current data (retain existing files)
    $existing = mysqli_fetch_assoc(mysqli_query($con, "SELECT photo, business_logo, portfolio, email FROM {$siteprefix}users WHERE id = $vendorId"));
    if (!$existing) {
        return ['status' => 'error', 'messages' => generateMessage("Vendor not found.", "red")];
    }

    // ✅ Check if email is changing and already exists
    if ($email !== $existing['email']) {
        $checkEmail = mysqli_query($con, "SELECT id FROM {$siteprefix}users WHERE email = '$email' AND id != $vendorId");
        if (mysqli_num_rows($checkEmail) > 0) {
            return ['status' => 'error', 'messages' => generateMessage("Email already exists.", "red")];
        }
    }

    // Photo
    $photoFile = $existing['photo'];
    if (!empty($filesData["photo"]["name"])) {
        $photoFile = uniqid() . '_' . basename($filesData["photo"]["name"]);
        move_uploaded_file($filesData["photo"]["tmp_name"], $targetDir . $photoFile);
    }

    // Business logo
    $logoFile = $existing['business_logo'];
    if (!empty($filesData["business_logo"]["name"])) {
        $logoFile = uniqid() . '_' . basename($filesData["business_logo"]["name"]);
        move_uploaded_file($filesData["business_logo"]["tmp_name"], $targetDir . $logoFile);
    }

    // Portfolio
    $portfolioCSV = $existing['portfolio'];
    if (!empty($filesData["portfolio"]["name"][0])) {
        $portfolioFiles = [];
        foreach ($filesData["portfolio"]["name"] as $key => $fileName) {
            $portfolioFile = uniqid() . '_' . basename($fileName);
            if (move_uploaded_file($filesData["portfolio"]["tmp_name"][$key], $targetDir . $portfolioFile)) {
                $portfolioFiles[] = $portfolioFile;
            }
        }
        $portfolioCSV = implode(",", $portfolioFiles);
    }

    // ✅ Update query
    $sql = "
        UPDATE {$siteprefix}users SET
            title = '$title',
            first_name = '$firstName',
            middle_name = '$middleName',
            last_name = '$lastName',
            photo = '$photoFile',
            dob = '$dob',
            gender = '$gender',
            nationality = '$nationality',
            languages = '$languages',
            business_name = '$businessName',
            registered_business_name = '$registeredBusiness',
            owner_name = '$ownerName',
            business_logo = '$logoFile',
            portfolio = '$portfolioCSV',
            phone = '$phone',
            website = '$website',
            email = '$email',
            state_residence = '$stateResidence',
            bank_name = '$bank_name',
            bank_accname = '$bank_accname',
            bank_number = '$bank_number',
            address = '$address',
            facebook = '$facebook',
            twitter = '$twitter',
            instagram = '$instagram',
            linkedin = '$linkedin',
            category_id = '$categoryId',
            subcategory_id = '$subcategoryId',
            services = '$services',
            experience_years = '$experience',
            coverage = '$coverage',
            onsite = '$onsite',
            availability = '$availability'
        WHERE id = $vendorId
    ";

    if (mysqli_query($con, $sql)) {

        return ['status' => 'success', 'messages' => generateMessage("Vendor profile updated successfully!", "green")];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database Error: " . mysqli_error($con), "red")];
    }
}

function updateSubscriptionEndpoint($postData) {
    global $con, $siteprefix;

    $messages = '';

    // ✅ Validate and sanitize
    $id = intval($postData['id'] ?? 0);
    if ($id <= 0) {
        return ['status' => 'error', 'messages' => generateMessage("Invalid plan ID.", "red")];
    }

    $name = mysqli_real_escape_string($con, trim($postData['name'] ?? ''));
    $price = floatval($postData['price'] ?? 0.00);
    $duration_days = intval($postData['duration_days'] ?? 365);

    if ($name === '') {
        return ['status' => 'error', 'messages' => generateMessage("Plan name is required.", "red")];
    }

    // ✅ Ensure plan name is unique (excluding current ID)
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}subscriptions WHERE name = '$name' AND id != $id");
    if (mysqli_num_rows($check) > 0) {
        return ['status' => 'error', 'messages' => generateMessage("A plan with this name already exists.", "red")];
    }

    // ✅ Boolean toggles
    $vendor_profile_page  = isset($postData['vendor_profile_page']) ? 1 : 0;
    $homepage_visibility  = isset($postData['homepage_visibility']) ? 1 : 0;
    $featured_badge       = isset($postData['featured_badge']) ? 1 : 0;
    $dashboard_access     = isset($postData['dashboard_access']) ? 1 : 0;
    $messaging_system     = isset($postData['messaging_system']) ? 1 : 0;
    $directory_appearance = isset($postData['directory_appearance']) ? 1 : 0;
    $review_response      = isset($postData['review_response']) ? 1 : 0;
    $highlighted_listing  = isset($postData['highlighted_listing']) ? 1 : 0;

    // ✅ Limits (numeric or "unlimited")
    $product_limit        = mysqli_real_escape_string($con, trim($postData['product_limit'] ?? '0'));
    $lead_request_limit   = mysqli_real_escape_string($con, trim($postData['lead_request_limit'] ?? '0'));
    $portfolio_limit      = mysqli_real_escape_string($con, trim($postData['portfolio_limit'] ?? '0'));
    $specialization_limit = mysqli_real_escape_string($con, trim($postData['specialization_limit'] ?? '0'));
    $article_limit        = mysqli_real_escape_string($con, trim($postData['article_limit'] ?? '0'));
    $images               = mysqli_real_escape_string($con, trim($postData['images'] ?? '0'));
    $videos               = mysqli_real_escape_string($con, trim($postData['videos'] ?? '0'));

    // ✅ Ensure plan exists
    $exists = mysqli_query($con, "SELECT id FROM {$siteprefix}subscriptions WHERE id = $id");
    if (mysqli_num_rows($exists) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Plan not found.", "red")];
    }

    // ✅ Update query
    $sql = "
        UPDATE {$siteprefix}subscriptions SET
            name = '$name',
            price = '$price',
            duration_days = '$duration_days',
            vendor_profile_page = '$vendor_profile_page',
            homepage_visibility = '$homepage_visibility',
            featured_badge = '$featured_badge',
            dashboard_access = '$dashboard_access',
            messaging_system = '$messaging_system',
            directory_appearance = '$directory_appearance',
            review_response = '$review_response',
            highlighted_listing = '$highlighted_listing',
            product_limit = '$product_limit',
            lead_request_limit = '$lead_request_limit',
            portfolio_limit = '$portfolio_limit',
            specialization_limit = '$specialization_limit',
            article_limit = '$article_limit',
            images = '$images',
            videos = '$videos'
        WHERE id = $id
    ";

    if (mysqli_query($con, $sql)) {
        return ['status' => 'success', 'messages' => generateMessage("Subscription plan updated successfully!", "green")];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database Error: " . mysqli_error($con), "red")];
    }
}


function addSubscriptionEndpoint($postData) {
    global $con, $siteprefix;

    $messages = '';

    // ✅ Sanitize and validate required fields
    $name = mysqli_real_escape_string($con, trim($postData['name'] ?? ''));
    $price = floatval($postData['price'] ?? 0.00);
    $duration_days = intval($postData['duration_days'] ?? 365);

    if ($name === '') {
        return ['status' => 'error', 'messages' => generateMessage("Plan name is required.", "red")];
    }

    // ✅ Check if plan already exists
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}subscriptions WHERE name = '$name'");
    if (mysqli_num_rows($check) > 0) {
        return ['status' => 'error', 'messages' => generateMessage("A plan with this name already exists.", "red")];
    }

    // ✅ Boolean fields (1 or 0)
    $vendor_profile_page  = isset($postData['vendor_profile_page']) ? 1 : 0;
    $homepage_visibility  = isset($postData['homepage_visibility']) ? 1 : 0;
    $featured_badge       = isset($postData['featured_badge']) ? 1 : 0;
    $dashboard_access     = isset($postData['dashboard_access']) ? 1 : 0;
    $messaging_system     = isset($postData['messaging_system']) ? 1 : 0;
    $directory_appearance = isset($postData['directory_appearance']) ? 1 : 0;
    $review_response      = isset($postData['review_response']) ? 1 : 0;
    $highlighted_listing  = isset($postData['highlighted_listing']) ? 1 : 0;

    // ✅ Limits (can be numbers or 'unlimited')
    $product_limit        = mysqli_real_escape_string($con, trim($postData['product_limit'] ?? '0'));
    $lead_request_limit   = mysqli_real_escape_string($con, trim($postData['lead_request_limit'] ?? '0'));
    $portfolio_limit      = mysqli_real_escape_string($con, trim($postData['portfolio_limit'] ?? '0'));
    $specialization_limit = mysqli_real_escape_string($con, trim($postData['specialization_limit'] ?? '0'));
    $article_limit        = mysqli_real_escape_string($con, trim($postData['article_limit'] ?? '0'));
    $images               = mysqli_real_escape_string($con, trim($postData['images'] ?? '0'));
    $videos               = mysqli_real_escape_string($con, trim($postData['videos'] ?? '0'));

    // ✅ Insert query
    $sql = "
        INSERT INTO {$siteprefix}subscriptions (
            name, price, duration_days, vendor_profile_page, homepage_visibility,
            featured_badge, dashboard_access, messaging_system, directory_appearance,
            review_response, highlighted_listing, product_limit, lead_request_limit,
            portfolio_limit, specialization_limit, article_limit, images, videos
        ) VALUES (
            '$name', '$price', '$duration_days', '$vendor_profile_page', '$homepage_visibility',
            '$featured_badge', '$dashboard_access', '$messaging_system', '$directory_appearance',
            '$review_response', '$highlighted_listing', '$product_limit', '$lead_request_limit',
            '$portfolio_limit', '$specialization_limit', '$article_limit', '$images', '$videos'
        )
    ";

    if (mysqli_query($con, $sql)) {
        return ['status' => 'success', 'messages' => generateMessage("Subscription plan added successfully!", "green")];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database Error: " . mysqli_error($con), "red")];
    }
}


function getblogID($con, $blog_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}forums WHERE id= '$blog_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getallusersnotifications($con, $user_id)
{
    global $siteprefix;

    $query = "SELECT * FROM ".$siteprefix."notifications WHERE status = 0 AND user = '$user_id' ORDER BY s DESC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $notificationData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $notificationData[] = $row;
        }
        return $notificationData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getcategoriesID($con, $id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}categories WHERE id= '$id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getsubcategoriesID($con, $id) {
    global $con,$siteprefix;
    $query = "SELECT category_name, parent_id FROM {$siteprefix}categories WHERE id = $id";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}


function getwalletID($con, $user_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM ".$siteprefix."wallet_history WHERE user='$user_id' ORDER BY date DESC";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}
function getplanID($con, $plan_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM  {$siteprefix}subscriptions WHERE id= '$plan_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}


function getVendorByID($con, $user_id) {
      global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}users WHERE id= '$user_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getTherapistByID($con, $user_id) {
     global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}users WHERE id= '$user_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}


function getbookingsID($con, $booking_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}bookings WHERE id= '$booking_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getUserID($con, $user_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}users WHERE id= '$user_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}


function getMemberID($con,$group_id, $user_id) {
    global $siteprefix;

    $query = "
        SELECT 
            gm.*, 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.photo
        FROM {$siteprefix}group_members AS gm
        LEFT JOIN {$siteprefix}users AS u 
            ON gm.user_id = u.id
        WHERE gm.user_id = '$user_id' 
          AND gm.group_id = '$group_id'";

   $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}
// FUNCTION: Fetch full order details with order items
// ✅ FUNCTION: Fetch full order details with items
function getOrderdetailsID($con, $order_id)
{
    global $siteprefix;

    // --- Fetch main order + buyer info ---
    $orderQuery = "
        SELECT 
            o.*, 
            u.first_name AS buyer_name, 
            u.email AS buyer_email
        FROM {$siteprefix}orders o
        LEFT JOIN {$siteprefix}users u ON o.user = u.id
        WHERE o.order_id = '$order_id'
        LIMIT 1
    ";

    $orderResult = mysqli_query($con, $orderQuery);
    if (!$orderResult || mysqli_num_rows($orderResult) === 0) {
        return ['error' => 'Order not found.'];
    }

    $order = mysqli_fetch_assoc($orderResult);

    // --- Fetch order items + seller info ---
    $itemQuery = "
        SELECT 
            i.listing_id,
            i.variation,
            i.price,
            i.type AS product_type,
            i.quantity,
            i.total_price,
            i.type,
            l.title AS listing_title,
            s.id AS seller_id,
            s.first_name AS seller_name,
            s.email AS seller_email,
            s.user_type AS seller_type
        FROM {$siteprefix}order_items i
        LEFT JOIN {$siteprefix}listings l ON i.listing_id = l.listing_id
        LEFT JOIN {$siteprefix}users s ON l.user_id = s.id
        WHERE i.order_id = '$order_id'
    ";

    $itemResult = mysqli_query($con, $itemQuery);
    $items = [];

    if ($itemResult && mysqli_num_rows($itemResult) > 0) {
        while ($row = mysqli_fetch_assoc($itemResult)) {
            $items[] = $row;
        }
    }

    // --- Combine ---
    $order['items'] = $items;

    return [$order];
}


/*----------------------------------------------------------
| Fetch Buyer Info
----------------------------------------------------------*/
function getBuyerInfo($con, $user_id) {
    global $siteprefix;

    $query = "SELECT id, first_name, email FROM {$siteprefix}users WHERE id = '$user_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

/*----------------------------------------------------------
| Fetch Listing + Seller Info
----------------------------------------------------------*/
function getListingAndSeller($con, $listing_id) {
    global $siteprefix;

    $listingQuery = "SELECT id, title, user_id, limited_slot FROM {$siteprefix}listings WHERE listing_id = '$listing_id'";
    $listingResult = mysqli_query($con, $listingQuery);
    if (!$listingResult) return ['error' => mysqli_error($con)];

    $listings = mysqli_fetch_all($listingResult, MYSQLI_ASSOC);

    foreach ($listings as &$listing) {
        $sellerQuery = "SELECT id, first_name, email, user_type 
                        FROM {$siteprefix}users 
                        WHERE id = '{$listing['user_id']}'";
        $sellerResult = mysqli_query($con, $sellerQuery);
        $listing['seller'] = $sellerResult ? mysqli_fetch_all($sellerResult, MYSQLI_ASSOC) : [];
    }

    return $listings;
}



function getAllUserGroupsAndMemberships($con, $user_id)
{
    global $siteprefix;

    if (!$user_id) {
        return ['error' => 'User ID is required'];
    }

    $data = [
        'owned_groups' => [],
        'joined_groups' => [],
    ];

    // ✅ 1. Fetch groups created by the user (the owner)
    $queryOwned = "
        SELECT 
            g.id AS group_id,
            g.slug AS group_slug,
            g.group_name,
            g.group_description,
            g.created_at,
            g.status,
            COUNT(gm.id) AS total_members
        FROM {$siteprefix}groups AS g
        LEFT JOIN {$siteprefix}group_members AS gm
            ON gm.group_id = g.id
        WHERE g.user_id = '$user_id'
        GROUP BY g.id
        ORDER BY g.created_at DESC
    ";

    $resultOwned = mysqli_query($con, $queryOwned);
    if ($resultOwned) {
        while ($row = mysqli_fetch_assoc($resultOwned)) {
            $data['owned_groups'][] = $row;
        }
    } else {
        $data['owned_error'] = mysqli_error($con);
    }

    // ✅ 2. Fetch groups where the user exists in group_members (any role)
    $queryJoined = "
        SELECT 
            g.id AS group_id,
            g.group_name,
            g.group_description,
            g.user_id AS owner_id,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
            gm.role,
            gm.joined_at,
            g.status,
            g.slug AS group_slug
        FROM {$siteprefix}group_members AS gm
        INNER JOIN {$siteprefix}groups AS g
            ON gm.group_id = g.id
        INNER JOIN {$siteprefix}users AS u
            ON g.user_id = u.id
        WHERE gm.user_id = '$user_id'
          AND g.user_id != '$user_id'
        ORDER BY gm.joined_at DESC
    ";

    $resultJoined = mysqli_query($con, $queryJoined);
    if ($resultJoined) {
        while ($row = mysqli_fetch_assoc($resultJoined)) {
            $data['joined_groups'][] = $row;
        }
    } else {
        $data['joined_error'] = mysqli_error($con);
    }

    return $data;
}



function getgroupID($con, $group_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}groups WHERE id= '$group_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function gettherapistByslug($con, $slug) {
    global $siteprefix;   
        $query = "
    SELECT 
        u.*,
        (
            SELECT GROUP_CONCAT(name SEPARATOR ', ')
            FROM {$siteprefix}profession AS p
            WHERE FIND_IN_SET(p.id, u.professional_field)
        ) AS professional_field_names,

        (
            SELECT GROUP_CONCAT(name SEPARATOR ', ')
            FROM {$siteprefix}profession AS p
            WHERE FIND_IN_SET(p.id, u.professional_title)
        ) AS professional_title_names,

        (
            SELECT GROUP_CONCAT(name SEPARATOR ', ')
            FROM {$siteprefix}specialization AS c
            WHERE FIND_IN_SET(c.id, u.specializations)
        ) AS specializations_names,

        (
            SELECT GROUP_CONCAT(name SEPARATOR ', ')
            FROM {$siteprefix}specialization AS sc
            WHERE FIND_IN_SET(sc.id, u.sub_specialization)
        ) AS subspecializations_names,
        (
            SELECT IFNULL(ROUND(AVG(r.rating),1),0)
            FROM {$siteprefix}reviews AS r
            WHERE r.therapist_id = u.id
        ) AS avg_rating,

        (
            SELECT COUNT(*)
            FROM {$siteprefix}reviews AS r2
            WHERE r2.therapist_id = u.id
        ) AS review_count

    FROM {$siteprefix}users AS u
    WHERE u.slug = '" . mysqli_real_escape_string($con, $slug) . "'
    LIMIT 1
";

    $result = mysqli_query($con, $query);

    if ($result) {
        $therapistData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $therapistData[] = $row;
        }
        return $therapistData;
    } else {
        return ['error' => mysqli_error($con)];
    }

}


/**
 * Return avg rating and review count for a therapist from ma_reviews table.
 * ['avg_rating' => float, 'review_count' => int] or ['error'=>string]
 */
function getTherapistRating($con, $therapist_id) {
    global $siteprefix;
    $tid = mysqli_real_escape_string($con, $therapist_id);
    $query = "SELECT IFNULL(ROUND(AVG(CAST(rating AS DECIMAL(5,2))),2),0) AS avg_rating, COUNT(*) AS review_count FROM {$siteprefix}reviews WHERE therapist_id = '" . $tid . "'";
    $res = mysqli_query($con, $query);
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        // ensure numeric types
        $row['avg_rating'] = isset($row['avg_rating']) ? floatval($row['avg_rating']) : 0.0;
        $row['review_count'] = isset($row['review_count']) ? intval($row['review_count']) : 0;
        return $row;
    } else {
        return ['avg_rating' => 0.0, 'review_count' => 0, 'error' => mysqli_error($con)];
    }
}

function getgroupByslug($con, $slug) {
    global $siteprefix;

    $query = "
        SELECT 
            g.*, 
            u.first_name, 
            u.last_name,

            -- ✅ Category names
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, g.category)
            ) AS category_names,

            -- ✅ Subcategory names
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, g.subcategory)
            ) AS subcategory_names,

            -- ✅ Active member count
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}group_members AS gm
                WHERE gm.group_id = g.id 
                  AND gm.status IN ('active', 'active')
            ) AS member_count,
                -- Average rating (one decimal) and total reviews (only active reviews)
            (
                SELECT IFNULL(ROUND(AVG(r.rating),1),0)
                FROM {$siteprefix}reviews AS r
                WHERE r.group_id = g.id
            ) AS avg_rating,

            (
                SELECT COUNT(*)
                FROM {$siteprefix}reviews AS r2
                WHERE r2.group_id = g.id
            ) AS review_count

        FROM {$siteprefix}groups AS g
        LEFT JOIN {$siteprefix}users AS u 
            ON g.user_id = u.id
        WHERE g.slug = '" . mysqli_real_escape_string($con, $slug) . "'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $groupData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $groupData[] = $row;
        }
        return $groupData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getgroupByid($con, $group_id) {
      global $siteprefix;

     $query = "
        SELECT 
            g.*, 
            u.first_name, 
            u.last_name,

            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, g.category)
            ) AS category_names,

            -- ✅ Subcategory names
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, g.subcategory)
            ) AS subcategory_names

        FROM {$siteprefix}groups AS g
        LEFT JOIN {$siteprefix}users AS u 
            ON g.user_id = u.id
        WHERE g.id ='" . mysqli_real_escape_string($con, $group_id) . "'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $groupData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $groupData[] = $row;
        }
        return $groupData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getgroupBygroupid($con, $group_id) {
    global $siteprefix;

    $query = "
        SELECT 
            g.*, 
            g2.group_name,
            u.first_name, 
            u.last_name
        FROM {$siteprefix}group_members AS g
        LEFT JOIN {$siteprefix}users AS u 
            ON u.id = g.user_id
        LEFT JOIN {$siteprefix}groups AS g2
            ON g2.id = g.group_id
        WHERE g.group_id = '" . mysqli_real_escape_string($con, $group_id) . "'
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $groupData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $groupData[] = $row;
        }
        return $groupData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getAllListings($con)
{
    global $siteprefix;

    // ✅ Fetch listings joined with user & subscription
    // Only include vendors whose plan allows homepage visibility
       $query = "
        SELECT 
            l.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            s.homepage_visibility,
            s.price AS subscription_price,
            (
                SELECT file_name
                FROM {$siteprefix}listing_images AS li 
                WHERE li.listing_id = l.listing_id 
                ORDER BY li.id ASC 
                LIMIT 1
            ) AS featured_image,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, l.categories)
            ) AS category_names,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, l.subcategories)
            ) AS subcategory_names
        FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u 
            ON l.user_id = u.id
        LEFT JOIN {$siteprefix}subscriptions AS s 
            ON u.subscription_plan_id = s.id
        WHERE s.homepage_visibility = 1
        ORDER BY 
            s.price DESC,     
            l.created_at DESC 
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $listingData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listingData[] = $row;
        }
        return $listingData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

/**
 * Return listings filtered by provided params. Filters supported:
 * - search (text)
 * - price_range (under25, 25-50, 50-100, 100-200, 200plus)
 * - sort (featured, price_asc, price_desc, rating, newest)
 * - items_per_page (int) -- optional LIMIT
 */
function getAllListingsFiltered($con, $filters = [])
{
    global $siteprefix;

    // Base WHERE: only vendors with homepage_visibility and active listings
    $where = "WHERE s.homepage_visibility = 1 AND l.status = 'active' ";

    // Search text (tokenized): split on whitespace and require each token to appear
    // in one of the searchable fields (title, slug, description, seller name parts)
    if (!empty($filters['search'])) {
        $raw = trim($filters['search']);
        // split into tokens (handles multiple spaces)
        $parts = preg_split('/\s+/', $raw);
        $tokenConds = [];
        foreach ($parts as $p) {
            $tok = mysqli_real_escape_string($con, $p);
            if ($tok === '') continue;
            $tokenConds[] = "(
                l.title LIKE '%$tok%' OR l.slug LIKE '%$tok%' OR l.description LIKE '%$tok%' OR
                u.first_name LIKE '%$tok%' OR u.last_name LIKE '%$tok%' OR CONCAT(u.first_name, ' ', u.last_name) LIKE '%$tok%' OR
                EXISTS(SELECT 1 FROM {$siteprefix}categories AS c WHERE FIND_IN_SET(c.id, l.categories) AND c.category_name LIKE '%$tok%') OR
                EXISTS(SELECT 1 FROM {$siteprefix}categories AS c2 WHERE FIND_IN_SET(c2.id, l.subcategories) AND c2.category_name LIKE '%$tok%')
            )";
        }
        if (!empty($tokenConds)) {
            // require all tokens to match somewhere (AND between tokens)
            $where .= ' AND (' . implode(' AND ', $tokenConds) . ')';
        }
    }

    // Price range parsing - support numeric buckets like "min-max" or "max+"
    $priceMin = 0; $priceMax = 0;
    $pr = trim($filters['price_range'] ?? '');
    if ($pr !== '') {
        if (strpos($pr, '-') !== false) {
            list($pmn, $pmx) = explode('-', $pr, 2);
            $priceMin = floatval($pmn);
            $priceMax = floatval($pmx);
        } elseif (substr($pr, -1) === '+') {
            $priceMin = floatval(rtrim($pr, '+'));
            $priceMax = 0;
        } else {
            // legacy labels
            switch ($pr) {
                case 'under25': $priceMax = 25; break;
                case '25-50': $priceMin = 25; $priceMax = 50; break;
                case '50-100': $priceMin = 50; $priceMax = 100; break;
                case '100-200': $priceMin = 100; $priceMax = 200; break;
                case '200plus': $priceMin = 200; $priceMax = 0; break;
            }
        }
    }

    if ($priceMin > 0 || $priceMax > 0) {
        if ($priceMin > 0 && $priceMax > 0) {
            $where .= " AND ( (l.price BETWEEN $priceMin AND $priceMax) OR (l.price_min BETWEEN $priceMin AND $priceMax) OR (l.price_max BETWEEN $priceMin AND $priceMax) )";
        } elseif ($priceMin > 0) {
            $where .= " AND (l.price >= $priceMin OR l.price_max >= $priceMin)";
        } elseif ($priceMax > 0) {
            $where .= " AND (l.price <= $priceMax OR l.price_min <= $priceMax)";
        }
    }

    // Explicit category/subcategory filters (selected from dropdowns)
    // category: accept either id or slug (slug used for nicer UI). If slug provided, resolve to id.
    $cid = 0; $scid = 0;
    if (!empty($filters['category_id'])) {
        $cid = intval($filters['category_id']);
    } elseif (!empty($filters['category_slug'])) {
        $slug = mysqli_real_escape_string($con, trim($filters['category_slug']));
        $qcid = "SELECT id FROM {$siteprefix}categories WHERE LOWER(REPLACE(category_name, ' ', '-')) = '$slug' LIMIT 1";
        $rc = mysqli_query($con, $qcid);
        if ($rc && $rr = mysqli_fetch_assoc($rc)) $cid = intval($rr['id']);
    }
    if ($cid > 0) {
        $where .= " AND FIND_IN_SET($cid, l.categories) ";
    }

    // subcategory: accept either id or slug
    if (!empty($filters['subcategory_id'])) {
        $scid = intval($filters['subcategory_id']);
    } elseif (!empty($filters['subcategory_slug'])) {
        $sslug = mysqli_real_escape_string($con, trim($filters['subcategory_slug']));
        $qscid = "SELECT id FROM {$siteprefix}categories WHERE LOWER(REPLACE(category_name, ' ', '-')) = '$sslug' LIMIT 1";
        $rsc = mysqli_query($con, $qscid);
        if ($rsc && $rs = mysqli_fetch_assoc($rsc)) $scid = intval($rs['id']);
    }
    if ($scid > 0) {
        $where .= " AND FIND_IN_SET($scid, l.subcategories) ";
    }

    // Sorting
    $order = "ORDER BY s.price DESC, l.created_at DESC"; // default
    switch ($filters['sort'] ?? '') {
        case 'price_asc': $order = "ORDER BY l.price ASC"; break;
        case 'price_desc': $order = "ORDER BY l.price DESC"; break;
        case 'newest': $order = "ORDER BY l.created_at DESC"; break;
        case 'rating': /* rating not implemented in listing table; keep default */ break;
        case 'featured': $order = "ORDER BY s.price DESC, l.created_at DESC"; break;
    }

    // Pagination: items per page and current page
    $itemsPerPage = (!empty($filters['items_per_page']) && intval($filters['items_per_page']) > 0) ? intval($filters['items_per_page']) : 12;
    $page = (!empty($filters['page']) && intval($filters['page']) > 0) ? intval($filters['page']) : 1;
    $offset = ($page - 1) * $itemsPerPage;

    // Count total matching rows for pagination
    $countQuery = "SELECT COUNT(*) AS cnt FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u ON l.user_id = u.id
        LEFT JOIN {$siteprefix}subscriptions AS s ON u.subscription_plan_id = s.id
        $where";
    $countResult = mysqli_query($con, $countQuery);
    $total = 0;
    if ($countResult) {
        $crow = mysqli_fetch_assoc($countResult);
        $total = intval($crow['cnt']);
    }

    $limit = " LIMIT $offset, $itemsPerPage";

    $query = "SELECT 
            l.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            s.homepage_visibility,
            s.price AS subscription_price,
            (
                SELECT file_name
                FROM {$siteprefix}listing_images AS li 
                WHERE li.listing_id = l.listing_id 
                ORDER BY li.id ASC 
                LIMIT 1
            ) AS featured_image,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, l.categories)
            ) AS category_names,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, l.subcategories)
            ) AS subcategory_names
        FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u 
            ON l.user_id = u.id
        LEFT JOIN {$siteprefix}subscriptions AS s 
            ON u.subscription_plan_id = s.id
        $where
        $order
        $limit
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $listingData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listingData[] = $row;
        }
        // If ajax flag requested, return structured object with total count
        if (!empty($filters['ajax'])) {
            return ['total' => $total, 'data' => $listingData];
        }
        return $listingData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getListingPriceBounds($con)
{
    global $siteprefix;

    $query = "SELECT l.price, l.price_min, l.price_max
              FROM {$siteprefix}listings l
              LEFT JOIN {$siteprefix}users u ON l.user_id = u.id
              LEFT JOIN {$siteprefix}subscriptions s ON u.subscription_plan_id = s.id
              WHERE s.homepage_visibility = 1 AND l.status = 'active'";

    $result = mysqli_query($con, $query);
    if (!$result) return ['error' => mysqli_error($con)];

    $min = null; $max = null;
    while ($row = mysqli_fetch_assoc($result)) {
        foreach (['price', 'price_min', 'price_max'] as $col) {
            $v = isset($row[$col]) ? floatval($row[$col]) : 0;
            if ($v <= 0) continue;
            if ($min === null || $v < $min) $min = $v;
            if ($max === null || $v > $max) $max = $v;
        }
    }

    // Fallback defaults
    if ($min === null) $min = 0;
    if ($max === null) $max = 0;

    return ['min' => $min, 'max' => $max];
}



function getallblogbyslug($con, $slug)
{
    global $siteprefix;

     $query = "
        SELECT 
            f.*, 
            u.first_name, 
            u.last_name,
            u.bio,
            u.photo,
            u.facebook,
            u.instagram,
            u.twitter,
            u.linkedin,

            -- ✅ Category names
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, f.categories)
            ) AS category_names,

            -- ✅ Subcategory names
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, f.subcategories)
            ) AS subcategory_names,

            -- ✅ Comment count
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}comments AS cm
                WHERE cm.blog_id = f.id
            ) AS comment_count,

            (
                SELECT COUNT(*) 
                FROM {$siteprefix}blog_likes AS bl
                WHERE bl.blog_id = f.id
            ) AS like_count

        FROM {$siteprefix}forums AS f
        LEFT JOIN {$siteprefix}users AS u 
            ON f.user_id = u.id
        WHERE f.slug = '" . mysqli_real_escape_string($con, $slug) . "'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $blogData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $blogData[] = $row;
        }
        return $blogData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}




//get questions answer
function getallquestionbyslug($con, $slug)
{
    global $siteprefix;

    $query = "
        SELECT 
            q.*, 
            u.first_name, 
            u.last_name,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, q.categories)
            ) AS category_names,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, q.subcategories)
            ) AS subcategory_names,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}answers AS cm
                WHERE cm.question_id = q.id
            ) AS comment_count
        FROM {$siteprefix}questions AS q
        LEFT JOIN {$siteprefix}users AS u 
            ON q.user_id = u.id
        WHERE q.slug = '$slug'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $blogData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $blogData[] = $row;
        }
        return $blogData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function updateBookingEndpoint($postData, $fileData)
{
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // 🧠 Sanitize inputs
    $bookingId      = intval($postData['booking_id'] ?? 0);
    $booking_status = mysqli_real_escape_string($con, trim($postData['booking_status'] ?? ''));
    $client_name    = mysqli_real_escape_string($con, trim($postData['client_name'] ?? ''));
    $client_email   = mysqli_real_escape_string($con, trim($postData['client_email'] ?? ''));
    $reference      = mysqli_real_escape_string($con, trim($postData['reference'] ?? ''));
    $therapist_id   = intval($postData['therapist_id'] ?? 0);
    $status_reason  = mysqli_real_escape_string($con, trim($postData['status_reason'] ?? ''));

    // 🧠 Validate Booking ID
    if ($bookingId <= 0) {
        return ['status' => 'error', 'messages' => 'Invalid booking ID'];
    }

    // 🧠 Validate Status
    if (empty($booking_status)) {
        return ['status' => 'error', 'messages' => 'Booking status is required'];
    }

    // 🧠 Get therapist rate as price
    $price = 0;
    if ($therapist_id > 0) {
        $rateQuery = mysqli_query($con, "SELECT rate FROM {$siteprefix}users WHERE id = '$therapist_id' LIMIT 1");
        if ($rateQuery && mysqli_num_rows($rateQuery) > 0) {
            $rateRow = mysqli_fetch_assoc($rateQuery);
            $price = floatval($rateRow['rate']);
        }
    }

    // ✅ Update booking status and reason if cancelled
    $reason_sql = ($booking_status === 'cancelled') ? ", cancel_reason='$status_reason'" : '';
    $query = "
        UPDATE {$siteprefix}bookings 
        SET booking_status = '$booking_status' , amount = '$price' $reason_sql
        WHERE id = '$bookingId'
    ";

    if (!mysqli_query($con, $query)) {
        return ['status' => 'error', 'messages' => 'Database error: ' . mysqli_error($con)];
    }

    // ✅ Send email if booking is confirmed
    if ($booking_status === 'confirmed') {
        $payment_link = "{$siteurl}/therapist-booking?booking_id={$reference}";
        $emailSubject = "Your Booking Has Been Approved — Proceed with Payment";
        $emailMessage = "
            <p>Your booking has been <strong>approved</strong>.</p>
            <p>Total Amount: ₦" . number_format($price, 2) . "</p>
            <p>You can complete your booking by clicking the link below to make payment:</p>
            <p><a href='{$payment_link}' style='background:#28a745;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>Pay Now</a></p>
            <p>Thank you for choosing our service!</p>
        ";
        sendEmail($client_email, $siteName, $siteMail, $client_name, $emailMessage, $emailSubject);
    }

    // ✅ Send email if booking is cancelled
    if ($booking_status === 'cancelled' && !empty($status_reason)) {
        $emailSubject = "Your Booking Has Been Cancelled";
        $emailMessage = "
            <p>We regret to inform you that your booking has been <strong>cancelled</strong>.</p>
            <p>Reason: {$status_reason}</p>
            <p>If you have any questions, please contact us.</p>
        ";
        sendEmail($client_email, $siteName, $siteMail, $client_name, $emailMessage, $emailSubject);
    }

    return ['status' => 'success', 'messages' => 'Booking status updated successfully!'];
}



function getServiceBookings($con) {
    global $con, $siteprefix;
    $bookings = [];

    $sql = "
    SELECT 
        b.*, 
        l.user_id AS seller_user_id,
        l.title AS listing_title,
        l.price AS listing_price,
        u.first_name AS seller_first_name,
        u.last_name AS seller_last_name
    FROM {$siteprefix}service_bookings AS b
    LEFT JOIN {$siteprefix}listings AS l 
        ON b.listing_id = l.listing_id
    LEFT JOIN {$siteprefix}users AS u
        ON l.user_id = u.id
    ORDER BY b.date DESC
";


    $query = mysqli_query($con, $sql);

    if (!$query) {
        return ['error' => mysqli_error($con)];
    }

    while ($row = mysqli_fetch_assoc($query)) {
        $bookings[] = $row;
    }

    return $bookings ?: ['message' => 'No bookings found'];
}

function updateDisputeWallet($postData) {
    global $con, $siteprefix, $sitecurrency, $siteName, $siteMail;

    // ✅ Validate required fields
    if (empty($postData['user']) || empty($postData['amount']) || empty($postData['dispute_id']) || empty($postData['wallet-action'])) {
        return ['status' => 'error', 'message' => 'All fields are required.'];
    }

    $user = mysqli_real_escape_string($con, $postData['user']);
    $amount = floatval($postData['amount']);
    $dispute_id = mysqli_real_escape_string($con, $postData['dispute_id']);
    $walletAction = mysqli_real_escape_string($con, $postData['wallet-action']);

    // ✅ Get user details
    $rDetails = getUserDetails($con, $siteprefix, $user);
    if (!$rDetails) {
        return ['status' => 'error', 'message' => 'User not found.'];
    }

    $r_email = $rDetails['email'];
    $r_name  = $rDetails['display_name'];

    // ✅ Prepare wallet update
    if ($walletAction === 'add') {
        $type = "credit";
        $emailMessage = "Your wallet has been credited with $sitecurrency$amount";
        $sql = "UPDATE {$siteprefix}users SET wallet = wallet + $amount WHERE id = '$user'";
        $message = "Wallet credited successfully.";
    } elseif ($walletAction === 'deduct') {
        $type = "debit";
        $emailMessage = "Your wallet has been debited with $sitecurrency$amount";
        $sql = "UPDATE {$siteprefix}users SET wallet = wallet - $amount WHERE id = '$user'";
        $message = "Wallet debited successfully.";
    } else {
        return ['status' => 'error', 'message' => 'Invalid wallet action.'];
    }

    // ✅ Execute wallet update
    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }

    // ✅ Record transaction & alerts
    $note = "Dispute Resolution: $dispute_id";
    $date = date("Y-m-d H:i:s");
    $status = 0;
    $alertMessage = "Your wallet amount has been modified. Kindly check your wallet for details.";

    insertWallet($con, $user, $amount, $type, $note, $date);
    insertAlert($con, $user, $alertMessage, $date, $status);

    // Optional: send email
    // sendEmail($r_email, $r_name, $siteName, $siteMail, $emailMessage, "Wallet Update");

    // ✅ Return API-style response
    return ['status' => 'success', 'message' => $message];
}



function updateDisputeStatusHandler($postData) {
    global $con, $siteprefix, $siteName, $siteMail;

    // ✅ Validate required fields
    if (empty($postData['ticket_id']) || empty($postData['status'])) {
        return ['status' => 'error', 'message' => 'Ticket ID and status are required.'];
    }

    $dispute_id = mysqli_real_escape_string($con, $postData['ticket_id']);
    $new_status = mysqli_real_escape_string($con, $postData['status']);
    $date = date("Y-m-d H:i:s");

    // ✅ Update dispute status in DB
    if (!updateDisputeStatus($con, $siteprefix, $dispute_id, $new_status)) {
        return ['status' => 'error', 'message' => 'Failed to update dispute status in the database.'];
    }

    // ✅ Get dispute details
    $sql = "SELECT * FROM {$siteprefix}disputes WHERE ticket_number='$dispute_id'";
    $result = mysqli_query($con, $sql);
    if (!$result || mysqli_num_rows($result) == 0) {
        return ['status' => 'error', 'message' => 'Dispute not found.'];
    }
    $row = mysqli_fetch_assoc($result);
    $ticket_number = $row['ticket_number'];
    $recipient_id = $row['recipient_id'];
    $sender_id = $row['user_id'];

    // ✅ Prepare messages
    $message = "Dispute status updated to $new_status: $ticket_number";
    $emailSubject = "Dispute Updated ($ticket_number)";
    $emailMessage = "<p>This dispute status has been updated to <strong>$new_status</strong>.</p>";

    if ($new_status === "resolved") {
        $emailSubject = "Your Issue Has Been Resolved ($ticket_number)";
        $emailMessage = "
            <p>Thank you for bringing your concern to our attention.<br>
            We’re pleased to inform you that the issue you raised (Ticket: $ticket_number) has now been successfully resolved.</p>
            <p>If you have further questions, please reach out. We appreciate your continued trust in ProjectReportHub.ng.</p>
        ";
    }

    // ✅ Notify sender
    $sDetails = getUserDetails($con, $siteprefix, $sender_id);
    if (!$sDetails) return ['status' => 'error', 'message' => 'Sender details not found.'];

    $s_email = $sDetails['email'];
    $s_name = $sDetails['first_name'] ?? $sDetails['display_name'] ?? 'User';

    if (!sendEmail($s_email, $siteName, $siteMail, $s_name, $emailMessage, $emailSubject)) {
        return ['status' => 'error', 'message' => 'Failed to send email to sender.'];
    }

    if (!insertAlert($con, $sender_id, $message, $date, 0)) {
        return ['status' => 'error', 'message' => 'Failed to insert alert for sender.'];
    }

    // ✅ Notify recipient if exists
    if ($recipient_id) {
        $rDetails = getUserDetails($con, $siteprefix, $recipient_id);
        if (!$rDetails) return ['status' => 'error', 'message' => 'Recipient details not found.'];

        $r_email = $rDetails['email'];
        $r_name = $rDetails['first_name'] ?? $rDetails['display_name'] ?? 'User';

        if (!sendEmail($r_email, $siteName, $siteMail, $r_name, $emailMessage, $emailSubject)) {
            return ['status' => 'error', 'message' => 'Failed to send email to recipient.'];
        }

        if (!insertAlert($con, $recipient_id, $message, $date, 0)) {
            return ['status' => 'error', 'message' => 'Failed to insert alert for recipient.'];
        }
    }

    return ['status' => 'success', 'message' => 'Dispute status updated successfully!'];
}




function sendDisputeMessage($postData, $files) {
    global $con, $siteprefix, $user_id, $siteName, $siteMail;

    // ✅ Validate required fields
    if (empty($postData['dispute_id']) || empty($postData['message'])) {
        return ['status' => 'error', 'message' => 'Dispute ID and message are required.'];
    }

    $dispute_id = mysqli_real_escape_string($con, $postData['dispute_id']);
    $sender_id  = $_POST['user_id']; // currently logged-in user
    $message    = mysqli_real_escape_string($con, $postData['message']);
    $date       = date('Y-m-d H:i:s');
    $new_status = 'awaiting-response';

    // ✅ Handle multiple file uploads
    $uploadedFiles = [];
    if (isset($files['attachment'])) {
        $uploadedFiles = handleMultipleFileUpload('attachment', '../uploads/');
    }
    $uploadedFilesStr = !empty($uploadedFiles) ? implode(', ', $uploadedFiles) : '';

    // ✅ Insert dispute message
    $sql = "INSERT INTO {$siteprefix}dispute_messages 
            (dispute_id, sender_id, message, file, created_at)
            VALUES ('$dispute_id', '$sender_id', '$message', '$uploadedFilesStr', '$date')";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }

    // ✅ Update dispute status
    updateDisputeStatus($con, $siteprefix, $dispute_id, $new_status);

    // ✅ Notify recipient
    notifyDisputeRecipient($con, $siteprefix, $dispute_id);

    // ✅ Insert admin alert
    $alertMessage = "A new message has been sent on dispute $dispute_id";
    $link = "ticket.php?ticket_number=$dispute_id";
    insertadminAlert($con, $alertMessage, $link, $date, 'Dispute Update', 0);

    return ['status' => 'success', 'message' => 'Message sent successfully!'];
}


function getEarningsBreakdown($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            p.s AS id,
            p.amount,
            p.group_id,
            p.listing_id,
            p.plan_id,
            p.order_id,
            p.type,
            p.date,
            g.group_name,
            l.title AS listing_title,
            s.name AS plan_name,
            gp.amount AS group_payment_amount,
            sub.price AS subscription_price,
            oi.total_price AS order_total,
            oi.variation AS listing_variation,
            p.booking_id AS profit_booking_id,
            b.amount AS booking_amount,
            b.client_name AS booking_client_name,
            b.therapist_id AS booking_therapist_id,
            b.reference AS booking_reference,
            t.first_name AS therapist_first_name,
            t.last_name AS therapist_last_name
        FROM {$siteprefix}profits AS p
        LEFT JOIN {$siteprefix}groups AS g 
            ON p.group_id = g.id
        LEFT JOIN {$siteprefix}listings AS l 
            ON p.listing_id = l.listing_id
        LEFT JOIN {$siteprefix}subscriptions AS s 
            ON p.plan_id = s.id
        LEFT JOIN {$siteprefix}group_payments AS gp 
            ON p.group_id = gp.group_id
        LEFT JOIN {$siteprefix}subscriptions AS sub 
            ON p.plan_id = sub.id
        LEFT JOIN {$siteprefix}order_items AS oi 
            ON p.order_id = oi.order_id AND p.listing_id = oi.listing_id
        /* Join bookings: profits.booking_id stores booking reference or id */
        LEFT JOIN {$siteprefix}bookings AS b
            ON b.reference = p.booking_id 
        /* Fetch therapist/user info for bookings */
        LEFT JOIN {$siteprefix}users AS t
            ON t.id = b.therapist_id
        GROUP BY p.order_id, p.booking_id, p.listing_id, oi.variation
        ORDER BY p.date DESC
    ";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $profits = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $source = '';
        $source_amount = 0;
        $source_name = 'N/A';

        if (!empty($row['group_id'])) {
            $source = 'Group Payment';
            $source_amount = $row['group_payment_amount'] ?? 0;
            $source_name = $row['group_name'] ?? 'N/A';
        } elseif (!empty($row['listing_id'])) {
            $source = 'Listing Sale';
            $source_amount = $row['order_total'] ?? $row['amount'];
            $source_name = $row['listing_title'] ?? 'N/A';
            if (!empty($row['listing_variation'])) {
                $source_name .= ' - ' . $row['listing_variation'];
            }
        } elseif (!empty($row['booking_amount'])) {
            // Profits that are tied to bookings (bookings.amount) — order_id stored as booking reference or id
            $source = 'Therapist Booking';
            $source_amount = $row['booking_amount'] ?? $row['amount'];
            // prefer therapist name for source_name when available
            if (!empty($row['therapist_first_name']) || !empty($row['therapist_last_name'])) {
                $source_name = trim(($row['therapist_first_name'] ?? '') . ' ' . ($row['therapist_last_name'] ?? '')) ?: 'Booking';
            } else {
                $source_name = 'Booking';
            }
        } elseif (!empty($row['plan_id'])) {
            $source = 'Subscription Plan';
            $source_amount = $row['subscription_price'] ?? 0;
            $source_name = $row['plan_name'] ?? 'N/A';
        } else {
            $source = 'Other';
            $source_amount = $row['amount'];
        }

        $profits[] = [
            'id' => $row['id'],
            'source' => $source,
            'source_name' => $source_name,
            'source_amount' => number_format($source_amount, 2),
            'earned_amount' => number_format($row['amount'], 2),
            'type' => ucfirst($row['type']),
            'date' => $row['date'],
            // booking-specific fields (if present)
            'booking_reference' => $row['booking_reference'] ?? null,
            'booking_client_name' => $row['booking_client_name'] ?? null,
            'booking_therapist_id' => $row['booking_therapist_id'] ?? null,
            'booking_therapist_name' => (!empty($row['therapist_first_name']) || !empty($row['therapist_last_name'])) ? trim(($row['therapist_first_name'] ?? '') . ' ' . ($row['therapist_last_name'] ?? '')) : null
        ];
    }

    return $profits;
}

/**
 * Send message(s) to group leader(s).
 * POST params:
 * - user_id (optional) single user id
 * - user_ids (optional) comma-separated user ids
 * - send_all (optional) 1 to send to all active group leaders
 * - message (required) message body
 * - title (optional) message title
 */
function sendGroupMessageEndpoint($postData) {
    global $con, $siteprefix;
    $currentdatetime = date('Y-m-d H:i:s');

    $message = isset($postData['message']) ? mysqli_real_escape_string($con, trim($postData['message'])) : '';
    $title = isset($postData['title']) ? mysqli_real_escape_string($con, trim($postData['title'])) : '';
    $send_all = isset($postData['send_all']) && intval($postData['send_all']) === 1;

    if ($message === '') {
        return ['status' => 'error', 'messages' => 'Message is required'];
    }

    $sent = 0;

    if ($send_all) {
        // send to all active group leaders (owners of active groups)
        $sql = "SELECT DISTINCT user_id FROM {$siteprefix}groups WHERE status = 'active' AND user_id IS NOT NULL";
        $res = mysqli_query($con, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $uid = $row['user_id'];
                if (!empty($uid)) {
                    insertAlert($con, $uid, $message, $currentdatetime, 0);
                    $sent++;
                }
            }
        }
    } else {
        // single or multiple user ids
        if (!empty($postData['user_ids'])) {
            $ids = explode(',', $postData['user_ids']);
            foreach ($ids as $uid) {
                $uid = intval(trim($uid));
                if ($uid > 0) {
                    insertAlert($con, $uid, $message, $currentdatetime, 0);
                    $sent++;
                }
            }
        } elseif (!empty($postData['user_id'])) {
            $uid = intval($postData['user_id']);
            if ($uid > 0) {
                insertAlert($con, $uid, $message, $currentdatetime, 0);
                $sent++;
            }
        }
    }

    return ['status' => 'success', 'messages' => "Message sent to {$sent} leader(s)"];
}


//update blog
// ✅ Update Blog Endpoint
function updateBlogEndpoint($postData, $fileData)
{
    global $con, $siteprefix, $siteName, $siteMail;

    $blogId = intval($postData['blog_id'] ?? 0);
    $title = mysqli_real_escape_string($con, trim($postData['blogTitle'] ?? ''));
    $article = $postData['blogContent'] ?? '';
    $tags = mysqli_real_escape_string($con, trim($postData['blogTags'] ?? ''));
    $status = mysqli_real_escape_string($con, trim($postData['status'] ?? ''));
    $reject_reason = mysqli_real_escape_string($con, trim($postData['reject_reason'] ?? ''));
    $categories = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    if (empty($blogId) || empty($title) || empty($article)) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Fetch current blog info
    $existing = mysqli_query($con, "SELECT status, user_id, title FROM {$siteprefix}forums WHERE id = '$blogId'");
    if (!$existing || mysqli_num_rows($existing) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Blog not found.", "red")];
    }

    $blog = mysqli_fetch_assoc($existing);
    $oldStatus = $blog['status'];
    $authorId = $blog['user_id'];
    $oldTitle = $blog['title'];

    // ✅ Handle image upload if new file is provided
    $featuredImageSql = '';
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    if (!empty($fileData["blogCover"]["name"])) {
        $fileName = basename($fileData["blogCover"]["name"]);
        $featuredImage = uniqid('forum_') . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        move_uploaded_file($fileData["blogCover"]["tmp_name"], $targetDir . $featuredImage);
        $featuredImageSql = ", featured_image = '$featuredImage'";
    }

    // ✅ Regenerate slug only if title changed
    $slugSql = '';
    if ($oldTitle !== $title) {
        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $slug = $baseSlug;
        $counter = 1;

        // Ensure unique slug
        while (true) {
            $check = $con->prepare("SELECT COUNT(*) AS count FROM {$siteprefix}forums WHERE slug = ? AND id != ?");
            $check->bind_param("si", $slug, $blogId);
            $check->execute();
            $result = $check->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] == 0) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $slugSql = ", slug = '$slug'";
    }

    // ✅ Handle reject_reason based on status
    if ($status === 'rejected') {
        $rejectReasonSql = ", reject_reason = '$reject_reason'";
    } else {
        // Clear previous reason if approving or pending
        $rejectReasonSql = ", reject_reason = ''";
    }

    // ✅ Update blog
    $query = "
        UPDATE {$siteprefix}forums 
        SET 
            title = '$title',
            article = '$article',
            tags = '$tags',
            categories = '$categories',
            subcategories = '$subcategories',
            status = '$status'
            $featuredImageSql
            $slugSql
            $rejectReasonSql
        WHERE id = '$blogId'
    ";

    if (mysqli_query($con, $query)) {

        // ✅ Fetch author info
        $authorQuery = mysqli_query($con, "SELECT first_name, email, user_type FROM {$siteprefix}users WHERE id = '$authorId' LIMIT 1");
        if ($authorQuery && mysqli_num_rows($authorQuery) > 0) {
            $author = mysqli_fetch_assoc($authorQuery);
            if (strtolower($author['user_type']) !== 'admin') {
                $firstName = $author['first_name'];
                $email = $author['email'];

                // ✅ If status changed to active — send approval email
                if ($oldStatus !== 'active' && $status === 'active') {
                    $emailSubject = "Your Blog Has Been Approved!";
                    $emailMessage = "
                        <p>Good news! Your blog post titled <strong>\"{$oldTitle}\"</strong> has been approved and published on <strong>{$siteName}</strong>.</p>
                        <p>You can now view it live on the site.</p>
                    ";
                    sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
                }

                // ✅ If rejected — send rejection email
                if ($status === 'rejected') {
                    $emailSubject = "Your Blog Post Was Rejected";
                    $emailMessage = "
                        <p>Unfortunately, your blog post titled <strong>\"{$oldTitle}\"</strong> has been rejected.</p>
                        <p><strong>Reason:</strong> {$reject_reason}</p>
                        <p>You can edit and resubmit your post for review.</p>
                    ";
                    sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
                }
            }
        }

        return ['status' => 'success', 'messages' => 'Blog updated successfully!'];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
}

//update group
function updatememberEndpoint($postData)
{
    global $con, $siteprefix, $siteName, $siteMail;

    $groupId = intval($postData['group_id'] ?? 0);
    $userId  = intval($postData['user_id'] ?? 0);
    $newStatus = mysqli_real_escape_string($con, trim($postData['status'] ?? ''));
    $newRole   = mysqli_real_escape_string($con, trim($postData['role'] ?? ''));
    $currentdatetime = date('Y-m-d H:i:s');

    if (!$groupId || !$userId) {
        return ['status' => 'error', 'messages' => generateMessage("Invalid request parameters", "red")];
    }

    // ✅ Get current member info
    $checkQuery = "
        SELECT gm.status, gm.role, u.email, u.first_name, u.last_name, g.group_name, g.user_id AS admin_id
        FROM {$siteprefix}group_members AS gm
        LEFT JOIN {$siteprefix}users AS u ON gm.user_id = u.id
        LEFT JOIN {$siteprefix}groups AS g ON gm.group_id = g.id
        WHERE gm.group_id = '$groupId' AND gm.user_id = '$userId'
        LIMIT 1
    ";
    $checkResult = mysqli_query($con, $checkQuery);
    if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
        return ['status' => 'error', 'messages' => 'Member not found'];
    }

    $oldData = mysqli_fetch_assoc($checkResult);
    $oldStatus = $oldData['status'];
    $oldRole   = $oldData['role'];
    $email     = $oldData['email'];
    $firstName = $oldData['first_name'];
    $lastName  = $oldData['last_name'];
    $groupName = $oldData['group_name'];
    $adminId   = $oldData['admin_id'];

    // ✅ Perform the update
    $updateQuery = "
        UPDATE {$siteprefix}group_members 
        SET status = '$newStatus', role = '$newRole'
        WHERE group_id = '$groupId' AND user_id = '$userId'
    ";

    if (!mysqli_query($con, $updateQuery)) {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }

    // ✅ Prepare messages
    $emailSubject = "";
    $emailMessage = "";
    $alertMessage = "";

    // === Status Changed (pending → active) ===
    if ($oldStatus !== $newStatus && $newStatus === 'active') {
        $emailSubject = "You’ve successfully joined {$groupName}!";
        $emailMessage = "
            Your request to join <b>{$groupName}</b> has been approved! 🎉<br>
            You can now view all blogs, posts, questions, and interact with other members.<br><br>
            Welcome aboard!
        ";
        $alertMessage = "Your membership request for <b>{$groupName}</b> was approved.";
    }

    // === Role Changed ===
    if ($oldRole !== $newRole) {
        if ($newRole === 'subadmin') {
            $emailSubject = "You’ve been made a Subadmin in {$groupName}";
            $emailMessage = "
                Congratulations! 🎉 You’ve been promoted to <b>Subadmin</b> in <b>{$groupName}</b>.<br>
                You can now post blogs and ask or answer questions within the group and also approve blog and questions.<br><br>
                Keep up the good work!
            ";
            $alertMessage = "You’ve been promoted to <b>Subadmin</b> in <b>{$groupName}</b>.";
        } elseif ($newRole === 'admin') {
            $emailSubject = "You’re now an Admin of {$groupName}";
            $emailMessage = "
                You’ve been upgraded to <b>Admin</b> in <b>{$groupName}</b>.<br>
                You can now manage group settings, approve members, and moderate all content (blog, questions).
            ";
            $alertMessage = "You’re now an <b>Admin</b> of <b>{$groupName}</b>.";
        }
    }

    // ✅ Insert alert if applicable
    if (!empty($alertMessage)) {
        insertAlert($con, $userId, $alertMessage, $currentdatetime, 0);
    }

    // ✅ Send email if needed
    if (!empty($emailSubject) && !empty($emailMessage)) {
        sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
    }

    // ✅ Return success
    $messages = generateMessage("Member updated successfully!", "green");
    return ['status' => 'success', 'messages' => 'Member updated successfully!'];
}

     

function checkGroupMember($con, $group_id, $user_id) {
    global $con, $siteprefix;

    $query = "SELECT role, status FROM {$siteprefix}group_members WHERE group_id = '$group_id' AND user_id = '$user_id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No membership found'];
    }
}



// Fetch all user orders
function getAllOrdersuser($con, $user_id) {
    global $siteprefix;
    $query = "SELECT * FROM ".$siteprefix."orders WHERE user = '$user_id' ORDER BY date DESC";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No orders found'];
    }
}
// Fetch order items for a given order
function getOrderItems($con, $order_id) {
    global $siteprefix;
    $query = "
        SELECT 
            i.listing_id,
            i.variation,
            i.type,
            l.title AS listing_title,
            s.id AS seller_id,
            s.first_name AS seller_name
        FROM {$siteprefix}order_items i
        LEFT JOIN {$siteprefix}listings l ON i.listing_id = l.listing_id
        LEFT JOIN {$siteprefix}users s ON l.user_id = s.id
        WHERE i.order_id = '$order_id'
    ";
    $result = mysqli_query($con, $query);
    if($result && mysqli_num_rows($result) > 0){
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No items found'];
    }
}


function getTicketDetails($con, $ticket_number)
{
    global $siteprefix;

    $query = "SELECT * FROM {$siteprefix}disputes WHERE ticket_number = '$ticket_number' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }

    if (mysqli_num_rows($result) === 0) {
        return ['status' => 'empty'];
    }

    return [
        'status' => 'success',
        'ticket' => mysqli_fetch_assoc($result)
    ];
}

function getTicketEvidence($con, $dispute_id)
{
    global $siteprefix;

    $query = "SELECT * FROM {$siteprefix}evidence WHERE dispute_id = '$dispute_id'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return [
        'status' => 'success',
        'evidence' => $data
    ];
}

function getTicketMessages($con, $ticket_number)
{
    global $siteprefix;

    $query = "SELECT m.*, u.first_name AS name, u.photo AS profile_image
              FROM {$siteprefix}dispute_messages m
              JOIN {$siteprefix}users u ON m.sender_id = u.id
              WHERE m.dispute_id = '$ticket_number'
              ORDER BY m.created_at DESC";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }

    return [
        'status' => 'success',
        'messages' => mysqli_fetch_all($result, MYSQLI_ASSOC)
    ];
}



function getAllTicketsbyuser($con, $user_id)
{
    global $siteprefix;

    $query = "SELECT * FROM ".$siteprefix."disputes WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No tickets found'];
    }
}

function getAllListingsbyuser($con, $user_id)
{
    global $siteprefix;

    // ✅ Fetch listings joined with user & subscription
    // Only include vendors whose plan allows homepage visibility
       $query = "
        SELECT 
            l.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            s.homepage_visibility,
            s.price AS subscription_price,
            (
                SELECT file_name
                FROM {$siteprefix}listing_images AS li 
                WHERE li.listing_id = l.listing_id 
                ORDER BY li.id ASC 
                LIMIT 1
            ) AS featured_image,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS c
                WHERE FIND_IN_SET(c.id, l.categories)
            ) AS category_names,
            (
                SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
                FROM {$siteprefix}categories AS sc
                WHERE FIND_IN_SET(sc.id, l.subcategories)
            ) AS subcategory_names
        FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u 
            ON l.user_id = u.id
        LEFT JOIN {$siteprefix}subscriptions AS s 
            ON u.subscription_plan_id = s.id
        WHERE l.user_id = $user_id
        ORDER BY 
            s.price DESC,     
            l.created_at DESC 
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $listingData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listingData[] = $row;
        }
        return $listingData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function checkuservendor($con, $slug) {
    global $siteprefix;

    $slug = mysqli_real_escape_string($con, $slug);
    $query = "SELECT 
    u.*, 
    (
        SELECT GROUP_CONCAT(c.category_name SEPARATOR ', ')
        FROM {$siteprefix}categories AS c
        WHERE FIND_IN_SET(c.id, u.category_id)
    ) AS category_names,
    (
        SELECT GROUP_CONCAT(sc.category_name SEPARATOR ', ')
        FROM {$siteprefix}categories AS sc
        WHERE FIND_IN_SET(sc.id, u.subcategory_id)
        ) AS subcategory_names
    FROM {$siteprefix}users AS u
    WHERE u.slug = '$slug'
    LIMIT 1";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $vendorData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $vendorData[] = $row;
    }

    return $vendorData;
}



function createadminGroupEndpoint($postData, $fileData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $messages = '';
    $user_id = $_POST['user'];
    // Sanitize inputs
    $group_name = mysqli_real_escape_string($con, trim($postData['group_name'] ?? ''));
    $group_description = mysqli_real_escape_string($con, trim($postData['group_description'] ?? ''));
    $group_type = mysqli_real_escape_string($con, $postData['group_type'] ?? 'open'); // open|closed
    $group_access = mysqli_real_escape_string($con, $postData['group_access'] ?? 'free'); // free|paid
    $status= $_POST['status'] ?? 'pending';

    // fees: ensure numeric
    $fee_1m = isset($postData['fee_1m']) ? floatval($postData['fee_1m']) : 0;
    $fee_3m = isset($postData['fee_3m']) ? floatval($postData['fee_3m']) : 0;
    $fee_6m = isset($postData['fee_6m']) ? floatval($postData['fee_6m']) : 0;
    $fee_12m = isset($postData['fee_12m']) ? floatval($postData['fee_12m']) : 0;

    // category & subcategory (single selects in your example)
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    $group_rules = mysqli_real_escape_string($con, trim($postData['group_rules'] ?? ''));

    
        // Replace spaces with hyphens and convert to lowercase
        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $group_name), '-'));


        // Start with the cleaned slug
        $alt_title = $baseSlug;
        $counter = 1;

        // Ensure the alt_title is unique
        while (true) {
            $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "groups WHERE slug = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $alt_title);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] == 0) {
                break; // alt_title is unique
            }

            // Append counter to baseSlug if not unique
            $alt_title = $baseSlug . '-' . $counter;
            $counter++;
        }

    // Basic validation
    if ($group_name === '' || $group_description === '' || $group_type === '' || $group_access === '') {
        $messages .= generateMessage("Please fill all required fields.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // If paid, require at least one fee to be > 0
    if ($group_access === 'paid' && ($fee_1m <= 0 && $fee_3m <= 0 && $fee_6m <= 0 && $fee_12m <= 0)) {
        $messages .= generateMessage("For paid groups at least one subscription fee must be provided.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // Handle banner upload (save only filename)
    $bannerFileName = '';
    if (!empty($fileData['group_banner']['name'])) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $original = basename($fileData['group_banner']['name']);
        // sanitize filename
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $original);
        $bannerFileName = uniqid('group_') . '_' . $safeName;
        $target = $uploadDir . $bannerFileName;

        // validate extension
        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $messages .= generateMessage("Banner must be JPG or PNG.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }

        if (!move_uploaded_file($fileData['group_banner']['tmp_name'], $target)) {
            $messages .= generateMessage("Failed to upload banner image.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }
    }

    // Insert into DB using mysqli_query (your style)
    $group_name_q = mysqli_real_escape_string($con, $group_name);
    $group_description_q = mysqli_real_escape_string($con, $group_description);
    $group_type_q = mysqli_real_escape_string($con, $group_type);
    $group_access_q = mysqli_real_escape_string($con, $group_access);
    $category_q = mysqli_real_escape_string($con, $category);
    $subcategory_q = mysqli_real_escape_string($con, $subcategory);
    $group_rules_q = mysqli_real_escape_string($con, $group_rules);
    $banner_q = mysqli_real_escape_string($con, $bannerFileName);

    $sql = "INSERT INTO {$siteprefix}groups 
        (group_name, group_description, group_type, group_access,
         fee_1m, fee_3m, fee_6m, fee_12m,
         category, subcategory, group_rules, banner, status, created_at, user_id,slug)
     VALUES
        ('$group_name_q', '$group_description_q', '$group_type_q', '$group_access_q',
         '$fee_1m', '$fee_3m', '$fee_6m', '$fee_12m',
         '$category_q', '$subcategory_q', '$group_rules_q', '$banner_q', '$status', NOW(), '$user_id','$alt_title')";

    if (mysqli_query($con, $sql)) {
        $messages .= generateMessage("Group created successfully.", "green");
        return ['status' => 'success', 'messages' => $messages];
    } else {
        $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }
}
//edit user
function updateUserEndpoint($postData, $filesData)
{
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $userId = intval($postData['user_id'] ?? 0);
    if ($userId <= 0) {
        return ['status' => 'error', 'messages' => 'Invalid user ID'];
    }

    // Sanitize inputs
    $title        = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $firstName    = mysqli_real_escape_string($con, trim($postData['first_name'] ?? ''));
    $middleName   = mysqli_real_escape_string($con, trim($postData['middle_name'] ?? ''));
    $lastName     = mysqli_real_escape_string($con, trim($postData['last_name'] ?? ''));
    $dob          = mysqli_real_escape_string($con, trim($postData['dob'] ?? ''));
    $gender       = mysqli_real_escape_string($con, trim($postData['gender'] ?? ''));
    $nationality  = mysqli_real_escape_string($con, trim($postData['nationality'] ?? ''));
    $languages    = mysqli_real_escape_string($con, trim($postData['languages'] ?? ''));
    $phone        = mysqli_real_escape_string($con, trim($postData['phone'] ?? ''));
    $website      = mysqli_real_escape_string($con, trim($postData['website'] ?? ''));
    $email        = mysqli_real_escape_string($con, trim($postData['email'] ?? ''));
    $state        = mysqli_real_escape_string($con, trim($postData['state_residence'] ?? ''));
    $address      = mysqli_real_escape_string($con, trim($postData['address'] ?? ''));
    $facebook     = mysqli_real_escape_string($con, trim($postData['facebook'] ?? ''));
    $twitter      = mysqli_real_escape_string($con, trim($postData['twitter'] ?? ''));
    $instagram    = mysqli_real_escape_string($con, trim($postData['instagram'] ?? ''));
    $linkedin     = mysqli_real_escape_string($con, trim($postData['linkedin'] ?? ''));
    $bio          = mysqli_real_escape_string($con, trim($postData['bio'] ?? ''));
    $newStatus    = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $suspendReason = mysqli_real_escape_string($con, trim($postData['suspend_reason'] ?? ''));
    $bank_name = mysqli_real_escape_string($con, $_POST['bank_name']);
    $bank_accname = mysqli_real_escape_string($con, $_POST['bank_accname']);
    $bank_number = mysqli_real_escape_string($con, $_POST['bank_number']);

    // ✅ Get old data
    $result = mysqli_query($con, "SELECT status, photo, email, first_name, last_name FROM {$siteprefix}users WHERE id = '$userId' LIMIT 1");
    if (!$result || mysqli_num_rows($result) === 0) {
        return ['status' => 'error', 'messages' => 'User not found'];
    }

    $oldData = mysqli_fetch_assoc($result);
    $oldStatus = $oldData['status'];
    $oldPhoto = $oldData['photo'];
    $oldEmail = $oldData['email'];
    $userEmail = $oldEmail;
    $userName = $oldData['first_name'] . ' ' . $oldData['last_name'];

    // ✅ Check if email changed
    if ($email !== $oldEmail) {
        $emailCheckQuery = "SELECT id FROM {$siteprefix}users WHERE email = '$email' AND id != '$userId' LIMIT 1";
        $emailCheck = mysqli_query($con, $emailCheckQuery);
        if ($emailCheck && mysqli_num_rows($emailCheck) > 0) {
            return ['status' => 'error', 'messages' => generateMessage("Email address already exists for another user.", "red")];
        }
        // Update the email variable used in notifications
        $userEmail = $email;
    }

    // ✅ Handle photo upload
    $photoFile = $oldPhoto;
    if (!empty($filesData['photo']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $photoFile = uniqid() . '_' . basename($filesData['photo']['name']);
        $targetPath = $targetDir . $photoFile;

        if (move_uploaded_file($filesData['photo']['tmp_name'], $targetPath)) {
            if (!empty($oldPhoto) && file_exists($targetDir . $oldPhoto)) {
                unlink($targetDir . $oldPhoto);
            }
        } else {
            return ['status' => 'error', 'messages' => generateMessage("Failed to upload new photo.", "red")];
        }
    }

    // ✅ Update query
    $query = "
        UPDATE {$siteprefix}users 
        SET 
            title = '$title',
            first_name = '$firstName',
            middle_name = '$middleName',
            last_name = '$lastName',
            dob = '$dob',
            gender = '$gender',
            nationality = '$nationality',
            languages = '$languages',
            phone = '$phone',
            website = '$website',
            email = '$email',
            state_residence = '$state',
            address = '$address',
            facebook = '$facebook',
            twitter = '$twitter',
            instagram = '$instagram',
            linkedin = '$linkedin',
            bio = '$bio',
            status = '$newStatus',
            suspend_reason = '$suspendReason',
            bank_name = '$bank_name',
            bank_accname = '$bank_accname',
            bank_number = '$bank_number',
            photo = '$photoFile'
        WHERE id = '$userId'
    ";

    if (mysqli_query($con, $query)) {

        // ✅ Send notification emails on status change
        if ($oldStatus !== $newStatus) {

            if ($newStatus === 'active') {
                $emailSubject = "Your $siteName Account is Now Active!";
                $emailMessage = "
                    <p>Your account on <strong>$siteName</strong> has been activated.</p>
                    <p>You can now log in and enjoy all features:</p>
                    <p><a href='{$siteurl}login.php' style='background:#4CAF50;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>Login Now</a></p>
                ";
                sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
            }

            if ($newStatus === 'suspended') {
                $emailSubject = "Your $siteName Account Has Been Suspended";
                $emailMessage = "
                    <p>We regret to inform you that your account has been <strong>suspended</strong>.</p>
                    <p><strong>Reason:</strong> $suspendReason</p>
                    <p>If you believe this was a mistake, please contact support at <a href='mailto:$siteMail'>$siteMail</a>.</p>
                ";
                sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
            }
        }

        return ['status' => 'success', 'messages' => 'User updated successfully.'];
    } else {
        return ['status' => 'error', 'messages' => "Error: " . mysqli_error($con)];
    }
}

function updateVendorEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $vendorId = intval($postData['user_id']); // sanitize ID
    $messages = '';

    // Fetch current vendor data
    $result = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id = $vendorId LIMIT 1");
    if (!$result || mysqli_num_rows($result) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Vendor not found.", "red")];
    }
    $oldData = mysqli_fetch_assoc($result);

    // Sanitize inputs
    $title       = mysqli_real_escape_string($con, $postData['title']);
    $firstName   = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName  = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName    = mysqli_real_escape_string($con, $postData['last_name']);
    $dob         = mysqli_real_escape_string($con, $postData['dob']);
    $gender      = mysqli_real_escape_string($con, $postData['gender']);
    $nationality = mysqli_real_escape_string($con, $postData['nationality']);
    $languages   = mysqli_real_escape_string($con, $postData['languages']);
    $businessName       = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName          = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone              = mysqli_real_escape_string($con, $postData['phone']);
    $website            = mysqli_real_escape_string($con, $postData['website']);
    $email              = mysqli_real_escape_string($con, $postData['email']);
    $stateResidence     = mysqli_real_escape_string($con, $postData['state_residence']);
    $address            = mysqli_real_escape_string($con, $postData['address']);
    $facebook           = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter            = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram          = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin           = mysqli_real_escape_string($con, $postData['linkedin']);
    $consent            = isset($postData['consent']) ? 1 : 0;
    $categoryId         = isset($postData['category']) ? implode(",", $postData['category']) : '';
    $subcategoryId      = isset($postData['subcategory']) ? implode(",", $postData['subcategory']) : '';
    $services           = mysqli_real_escape_string($con, $postData['services']);
    $experience         = mysqli_real_escape_string($con, $postData['experience_years']);
    $coverage           = isset($postData['coverage']) ? mysqli_real_escape_string($con, implode(",", $postData['coverage'])) : '';
    $onsite             = mysqli_real_escape_string($con, $postData['onsite']);
    $availability       = mysqli_real_escape_string($con, $postData['availability']);
    $newStatus          = mysqli_real_escape_string($con, trim($postData['status'] ?? $oldData['status']));
    $suspendReason      = mysqli_real_escape_string($con, $postData['suspend_reason'] ?? '');

    $targetDir = "../uploads/";

    // Handle files
    $photoFile = $oldData['photo'];
    if (!empty($filesData["photo"]["name"])) {
        $photoFile = uniqid() . '_' . basename($filesData["photo"]["name"]);
        move_uploaded_file($filesData["photo"]["tmp_name"], $targetDir . $photoFile);
    }

    $logoFile = $oldData['business_logo'];
    if (!empty($filesData["business_logo"]["name"])) {
        $logoFile = uniqid() . '_' . basename($filesData["business_logo"]["name"]);
        move_uploaded_file($filesData["business_logo"]["tmp_name"], $targetDir . $logoFile);
    }

    $portfolioCSV = $oldData['portfolio'];
    if (!empty($filesData["portfolio"]["name"][0])) {
        $portfolioFiles = [];
        foreach ($filesData["portfolio"]["name"] as $key => $fileName) {
            $portfolioFile = uniqid() . '_' . basename($fileName);
            if (move_uploaded_file($filesData["portfolio"]["tmp_name"][$key], $targetDir . $portfolioFile)) {
                $portfolioFiles[] = $portfolioFile;
            }
        }
        $portfolioCSV = implode(",", $portfolioFiles);
    }

    // Check email change
    $userEmail = $oldData['email'];
    if ($email !== $oldData['email']) {
        $emailCheck = mysqli_query($con, "SELECT id FROM {$siteprefix}users WHERE email = '$email' AND id != $vendorId");
        if (mysqli_num_rows($emailCheck) > 0) {
            return ['status' => 'error', 'messages' => generateMessage("Email already exists.", "red")];
        }
        $userEmail = $email;
    }

    // Update query
    $sql = "UPDATE {$siteprefix}users SET
        title='$title', first_name='$firstName', middle_name='$middleName', last_name='$lastName',
        photo='$photoFile', dob='$dob', gender='$gender', nationality='$nationality', languages='$languages',
        business_name='$businessName', registered_business_name='$registeredBusiness', owner_name='$ownerName',
        business_logo='$logoFile', portfolio='$portfolioCSV', phone='$phone', website='$website', email='$email',
        state_residence='$stateResidence', address='$address', facebook='$facebook', twitter='$twitter',
        instagram='$instagram', linkedin='$linkedin', category_id='$categoryId', subcategory_id='$subcategoryId',
        services='$services', experience_years='$experience', coverage='$coverage', onsite='$onsite',
        availability='$availability', consent='$consent', status='$newStatus'
        WHERE id = $vendorId";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'messages' => generateMessage("Database Error: " . mysqli_error($con), "red")];
    }

    // Send email if status changed
    if ($oldData['status'] !== $newStatus) {
        $userName = $firstName . ' ' . $lastName;
        if ($newStatus === 'active') {
            $emailSubject = "Your $siteName Account is Now Active!";
            $emailMessage = "
                <p>Your account on <strong>$siteName</strong> has been activated.</p>
                <p><a href='{$siteurl}login.php' style='background:#4CAF50;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>Login Now</a></p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
        } elseif ($newStatus === 'suspended') {
            $emailSubject = "Your $siteName Account Has Been Suspended";
            $emailMessage = "
                <p>Your account has been <strong>suspended</strong>.</p>
                <p><strong>Reason:</strong> $suspendReason</p>
                <p>If you believe this is an error, contact support at <a href='mailto:$siteMail'>$siteMail</a>.</p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
        }
    }

    return ['status' => 'success', 'messages' => generateMessage("Vendor profile updated successfully!", "green")];
}


// edit adminforumEndpoint
function updateAdminSettingsEndpoint($postData, $fileData) {
    global $con, $siteprefix;

    $messages = '';

    // Required fields
    $site_name = mysqli_real_escape_string($con, trim($postData['site_name'] ?? ''));
    $site_keywords = mysqli_real_escape_string($con, trim($postData['site_keywords'] ?? ''));
    $site_url = mysqli_real_escape_string($con, trim($postData['site_url'] ?? ''));
    $site_description = mysqli_real_escape_string($con, trim($postData['site_description'] ?? ''));
    $site_mail = mysqli_real_escape_string($con, trim($postData['site_mail'] ?? ''));
    $site_number = mysqli_real_escape_string($con, trim($postData['site_number'] ?? ''));
    $site_bank = mysqli_real_escape_string($con, trim($postData['site_bank'] ?? ''));
    $account_name = mysqli_real_escape_string($con, trim($postData['account_name'] ?? ''));
    $account_number = mysqli_real_escape_string($con, trim($postData['account_number'] ?? ''));
    $minimum_withdrawal = mysqli_real_escape_string($con, trim($postData['minimum_withdrawal'] ?? ''));
    $paystack_key = mysqli_real_escape_string($con, trim($postData['paystack_key'] ?? ''));
    $google_map = mysqli_real_escape_string($con, trim($postData['google_map'] ?? ''));
    $com_fee = floatval($postData['com_fee'] ?? 0);
    $affiliate_percentage = floatval($postData['affiliate_percentage'] ?? 0);
    $tinymce = mysqli_real_escape_string($con, trim($postData['tinymce'] ?? ''));
    $terms = $_POST['terms'];
    $privacy = $_POST['privacy'];

    // 🟢 Handle Site Logo Upload
    $logoFileName = '';
    if (!empty($fileData['site_logo']['name'])) {
        $uploadDir = "../assets/img/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $original = basename($fileData['site_logo']['name']);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $original);
        $logoFileName = uniqid('logo_') . '_' . $safeName;
        $target = $uploadDir . $logoFileName;

        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $messages .= generateMessage("Logo must be JPG or PNG.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }

        if (!move_uploaded_file($fileData['site_logo']['tmp_name'], $target)) {
            $messages .= generateMessage("Failed to upload logo.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }

        // Optional: delete old logo
        $oldLogo = mysqli_fetch_assoc(mysqli_query($con, "SELECT site_logo FROM {$siteprefix}site_settings WHERE s=1"));
        if (!empty($oldLogo['site_logo']) && file_exists($uploadDir . $oldLogo['site_logo'])) {
            unlink($uploadDir . $oldLogo['site_logo']);
        }
    }

    // 🟢 Build SQL dynamically
    $updateFields = "
        site_name='$site_name',
        site_keywords='$site_keywords',
        site_url='$site_url',
        site_description='$site_description',
        site_mail='$site_mail',
        site_number='$site_number',
        site_bank='$site_bank',
        paystack_key = '$paystack_key',
        account_name='$account_name',
        account_number='$account_number',
        terms_policy = '$terms',
        privacy_policy = '$privacy',
        minimum_withdrawal='$minimum_withdrawal',
        google_map='$google_map',
        commision_fee='$com_fee',
        affliate_percentage='$affiliate_percentage',
        tinymce='$tinymce'
    ";

    if ($logoFileName !== '') {
        $updateFields .= ", site_logo='$logoFileName'";
    }

    $sql = "UPDATE {$siteprefix}site_settings SET $updateFields WHERE s=1";

    if (mysqli_query($con, $sql)) {
        $messages .= generateMessage("Settings updated successfully.", "green");
        return ['status' => 'success', 'messages' => 'Settings updated successfully'];
    } else {
        $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }
}


// wallet total
// ✅ Function to get wallet totals for a user
function getWalletTotals($con, $user_id) {
    global $siteprefix;

    // Total Cleared Withdrawals
    $cleared_query = "SELECT SUM(amount) AS total_cleared 
                      FROM {$siteprefix}withdrawal 
                      WHERE user='$user_id' AND status='paid'";
    $cleared_result = mysqli_query($con, $cleared_query);
    $total_cleared = $cleared_result ? (mysqli_fetch_assoc($cleared_result)['total_cleared'] ?? 0) : 0;

    // Total Pending Withdrawals
    $pending_query = "SELECT SUM(amount) AS total_pending 
                      FROM {$siteprefix}withdrawal 
                      WHERE user='$user_id' AND status='pending'";
    $pending_result = mysqli_query($con, $pending_query);
    $total_pending = $pending_result ? (mysqli_fetch_assoc($pending_result)['total_pending'] ?? 0) : 0;

    // Total Requested Withdrawals (All)
    $total_query = "SELECT SUM(amount) AS total_requested 
                    FROM {$siteprefix}withdrawal 
                    WHERE user='$user_id'";
    $total_result = mysqli_query($con, $total_query);
    $total_requested = $total_result ? (mysqli_fetch_assoc($total_result)['total_requested'] ?? 0) : 0;

    // Wallet Balance
    $wallet_query = "SELECT wallet FROM {$siteprefix}users WHERE id='$user_id'";
    $wallet_result = mysqli_query($con, $wallet_query);
    $wallet_balance = $wallet_result ? (mysqli_fetch_assoc($wallet_result)['wallet'] ?? 0) : 0;

    $cleared_query = "SELECT
        SUM(CASE
            WHEN reason LIKE '%Dispute Resolution:%' AND status = 'credit' THEN amount
            ELSE 0
        END) AS total_dispute_amount FROM {$siteprefix}wallet_history WHERE user='$user_id'";
    $cleared_result = mysqli_query($con, $cleared_query);
    $total_dispute_amount = $cleared_result ? (mysqli_fetch_assoc($cleared_result)['total_dispute_amount'] ?? 0) : 0;

    // ✅ Total Amount Earned (credits)
    $earned_query = "SELECT SUM(amount) AS total_earned 
                     FROM {$siteprefix}wallet_history 
                     WHERE user='$user_id' AND status='credit'";
    $earned_result = mysqli_query($con, $earned_query);
    $total_earned = $earned_result ? (mysqli_fetch_assoc($earned_result)['total_earned'] ?? 0) : 0;

    return [
        'total_cleared'   => $total_cleared,
        'total_pending'   => $total_pending,
        'total_requested' => $total_requested,
        'wallet_balance'  => $wallet_balance,
        'total_earned'    => $total_earned,
        'total_dispute_amount' => $total_dispute_amount
    ];
}

//edit group
function updateAdminGroupEndpoint($postData, $fileData) {
    global $con, $siteprefix;

    $messages = '';

    // Required fields
    $group_id = intval($postData['group_id'] ?? 0);
    $user_id = intval($postData['user'] ?? 0);
    $group_name = mysqli_real_escape_string($con, trim($postData['group_name'] ?? ''));
    $group_description = mysqli_real_escape_string($con, trim($postData['group_description'] ?? ''));
    $group_type = mysqli_real_escape_string($con, $postData['group_type'] ?? 'open');
    $group_access = mysqli_real_escape_string($con, $postData['group_access'] ?? 'free');
    $status = mysqli_real_escape_string($con, $postData['status'] ?? 'pending');

    // Fees
    $fee_1m = isset($postData['fee_1m']) ? floatval($postData['fee_1m']) : 0;
    $fee_3m = isset($postData['fee_3m']) ? floatval($postData['fee_3m']) : 0;
    $fee_6m = isset($postData['fee_6m']) ? floatval($postData['fee_6m']) : 0;
    $fee_12m = isset($postData['fee_12m']) ? floatval($postData['fee_12m']) : 0;

    // Category & Subcategory
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    $group_rules = mysqli_real_escape_string($con, trim($postData['group_rules'] ?? ''));

    if ($group_id <= 0 || $group_name === '' || $group_description === '') {
        $messages .= generateMessage("Missing required fields.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // 🟢 Handle Banner Upload
    $bannerFileName = '';
    if (!empty($fileData['group_banner']['name'])) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $original = basename($fileData['group_banner']['name']);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $original);
        $bannerFileName = uniqid('group_') . '_' . $safeName;
        $target = $uploadDir . $bannerFileName;

        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $messages .= generateMessage("Banner must be JPG or PNG.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }

        if (!move_uploaded_file($fileData['group_banner']['tmp_name'], $target)) {
            $messages .= generateMessage("Failed to upload banner image.", "red");
            return ['status' => 'error', 'messages' => $messages];
        }

        // Optional: delete old banner if exists
        $oldBanner = mysqli_fetch_assoc(mysqli_query($con, "SELECT banner FROM {$siteprefix}groups WHERE id='$group_id'"));
        if (!empty($oldBanner['banner']) && file_exists($uploadDir . $oldBanner['banner'])) {
            unlink($uploadDir . $oldBanner['banner']);
        }
    }

    // 🟢 Build SQL dynamically
    $updateFields = "
        group_name='$group_name',
        group_description='$group_description',
        group_type='$group_type',
        group_access='$group_access',
        fee_1m='$fee_1m',
        fee_3m='$fee_3m',
        fee_6m='$fee_6m',
        fee_12m='$fee_12m',
        category='$category',
        subcategory='$subcategory',
        group_rules='$group_rules',
        status='$status',
        updated_at=NOW(),
        user_id='$user_id'
    ";

    if ($bannerFileName !== '') {
        $updateFields .= ", banner='$bannerFileName'";
    }

    $sql = "UPDATE {$siteprefix}groups SET $updateFields WHERE id='$group_id'";

    if (mysqli_query($con, $sql)) {
        $messages .= generateMessage("Group updated successfully.", "green");
        return ['status' => 'success', 'messages' =>'Group updated successfully'];
    } else {
        $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }
}


// get listings

function getListingData($listingId) {
    global $con, $siteprefix;

    $listingId = mysqli_real_escape_string($con, $listingId);
    $response = ['status' => 'error', 'message' => 'Listing not found', 'data' => []];

    if (!empty($listingId)) {
        // Fetch main listing
        $query = "SELECT * FROM {$siteprefix}listings WHERE listing_id = '$listingId' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $listing = mysqli_fetch_assoc($result);

            // Fetch variations (only if pricing type is Price Range)
            $variations = [];
            if ($listing['pricing_type'] === 'Price Range') {
                $varQuery = "SELECT variation_name, variation_price FROM {$siteprefix}listing_variations WHERE listing_id='$listingId'";
                $varResult = mysqli_query($con, $varQuery);
                while ($row = mysqli_fetch_assoc($varResult)) {
                    $variations[] = $row;
                }
            }

            // Fetch images
            $images = [];
            $imgQuery = "SELECT file_name FROM {$siteprefix}listing_images WHERE listing_id='$listingId'";
            $imgResult = mysqli_query($con, $imgQuery);
            while ($row = mysqli_fetch_assoc($imgResult)) {
                $images[] = $row['file_name'];
            }

            // Fetch videos
            $videos = [];
            $vidQuery = "SELECT file_name FROM {$siteprefix}listing_videos WHERE listing_id='$listingId'";
            $vidResult = mysqli_query($con, $vidQuery);
            while ($row = mysqli_fetch_assoc($vidResult)) {
                $videos[] = $row['file_name'];
            }

            // Combine and build response
            $response = [
                'status' => 'success',
                'message' => 'Listing fetched successfully',
                'data' => array_merge($listing, [
                    'variations' => $variations,
                    'images' => $images,
                    'videos' => $videos
                ])
            ];
        }
    }

    return $response;
}


// edit admin group

function updatenewAdminGroupEndpoint($postData, $fileData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $messages = '';

    // Required fields
    $group_id = intval($postData['group_id'] ?? 0);
    $user_id = intval($postData['user'] ?? 0);
    $group_name = mysqli_real_escape_string($con, trim($postData['group_name'] ?? ''));
    $group_description = mysqli_real_escape_string($con, trim($postData['group_description'] ?? ''));
    $group_type = mysqli_real_escape_string($con, $postData['group_type'] ?? 'open');
    $group_access = mysqli_real_escape_string($con, $postData['group_access'] ?? 'free');
    $newStatus = mysqli_real_escape_string($con, $postData['status'] ?? 'pending');
    $suspendReason = mysqli_real_escape_string($con, trim($postData['suspend_reason'] ?? ''));

    // Fees
    $fee_1m = isset($postData['fee_1m']) ? floatval($postData['fee_1m']) : 0;
    $fee_3m = isset($postData['fee_3m']) ? floatval($postData['fee_3m']) : 0;
    $fee_6m = isset($postData['fee_6m']) ? floatval($postData['fee_6m']) : 0;
    $fee_12m = isset($postData['fee_12m']) ? floatval($postData['fee_12m']) : 0;

    // Category & Subcategory
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    $group_rules = mysqli_real_escape_string($con, trim($postData['group_rules'] ?? ''));

    if ($group_id <= 0 || $group_name === '' || $group_description === '') {
        return ['status' => 'error', 'messages' => generateMessage("Missing required fields.", "red")];
    }

    // Fetch old group data
    $oldDataQuery = mysqli_query($con, "SELECT status, group_name FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    if (!$oldDataQuery || mysqli_num_rows($oldDataQuery) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Group not found.", "red")];
    }
    $oldData = mysqli_fetch_assoc($oldDataQuery);
    $oldStatus = $oldData['status'];

    // Handle Banner Upload
    $bannerFileName = '';
    if (!empty($fileData['group_banner']['name'])) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $original = basename($fileData['group_banner']['name']);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $original);
        $bannerFileName = uniqid('group_') . '_' . $safeName;
        $target = $uploadDir . $bannerFileName;

        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return ['status' => 'error', 'messages' => generateMessage("Banner must be JPG or PNG.", "red")];
        }

        if (!move_uploaded_file($fileData['group_banner']['tmp_name'], $target)) {
            return ['status' => 'error', 'messages' => generateMessage("Failed to upload banner image.", "red")];
        }

        // Delete old banner if exists
        if (!empty($oldData['banner']) && file_exists($uploadDir . $oldData['banner'])) {
            unlink($uploadDir . $oldData['banner']);
        }
    }

    // Build update query
    $updateFields = "
        group_name='$group_name',
        group_description='$group_description',
        group_type='$group_type',
        group_access='$group_access',
        fee_1m='$fee_1m',
        fee_3m='$fee_3m',
        fee_6m='$fee_6m',
        fee_12m='$fee_12m',
        category='$category',
        subcategory='$subcategory',
        group_rules='$group_rules',
        status='$newStatus',
        suspend_reason = '$suspendReason',
        updated_at=NOW(),
        user_id='$user_id'
    ";

    if ($bannerFileName !== '') {
        $updateFields .= ", banner='$bannerFileName'";
    }

    $sql = "UPDATE {$siteprefix}groups SET $updateFields WHERE id='$group_id'";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'messages' => generateMessage("Database Error: " . mysqli_error($con), "red")];
    }

    // ✅ Send email notification if status changed
    if ($oldStatus !== $newStatus) {
        $userNameQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
        $userData = mysqli_fetch_assoc($userNameQuery);
        $userEmail = $userData['email'] ?? '';
        $userName = trim($userData['first_name'] . ' ' . $userData['last_name']);

        if ($newStatus === 'active') {
            $emailSubject = "Your Group '$group_name' is Now Active!";
            $emailMessage = "
                <p>Hello $userName,</p>
                <p>Your group <strong>$group_name</strong> has been activated on $siteName.</p>
                <p><a href='{$siteurl}groups.php' style='background:#4CAF50;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>View Group</a></p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $userName, $emailMessage, $emailSubject);
        } elseif ($newStatus === 'suspended') {
            $emailSubject = "Your Group '$group_name' Has Been Suspended";
            $emailMessage = "
                <p>Hello $userName,</p>
                <p>Your group <strong>$group_name</strong> has been suspended.</p>
                <p><strong>Reason:</strong> $suspendReason</p>
                <p>If you think this is a mistake, contact support at <a href='mailto:$siteMail'>$siteMail</a>.</p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $userName, $emailMessage, $emailSubject);
        }
    }

    return ['status' => 'success', 'messages' => generateMessage("Group updated successfully.", "green")];
}

function addListingEndpoint($postData, $fileData) {
    global $con, $siteprefix;

    // 🧩 Sanitize inputs
    $listingTitle  = mysqli_real_escape_string($con, trim($postData['listingTitle'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $type          = mysqli_real_escape_string($con, trim($postData['itemType'] ?? '')); // updated to match your form
    $pricingType   = mysqli_real_escape_string($con, trim($postData['pricingType'] ?? ''));
    $price         = mysqli_real_escape_string($con, trim($postData['price'] ?? ''));
    $pricingNotes  = mysqli_real_escape_string($con, trim($postData['pricingNotes'] ?? ''));
    $availability  = mysqli_real_escape_string($con, trim($postData['availability'] ?? ''));
    $capacity      = mysqli_real_escape_string($con, trim($postData['capacity'] ?? ''));
    $delivery      = mysqli_real_escape_string($con, trim($postData['delivery'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $listingId     = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));

    $limited_slot = '';

if ($availability == 'Limited Slot') {
    $limited_slot = isset($_POST['available_slots']) ? $_POST['available_slots'] : '';
} else {
    $limited_slot = ''; // other availability types don't need slot value
}
// Replace spaces with hyphens and convert to lowercase
$baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $listingTitle), '-'));


// Start with the cleaned slug
$alt_title = $baseSlug;
$counter = 1;

// Ensure the alt_title is unique
while (true) {
    $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "listings WHERE slug = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $alt_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        break; // alt_title is unique
    }

    // Append counter to baseSlug if not unique
    $alt_title = $baseSlug . '-' . $counter;
    $counter++;
}

    // 🧩 Handle arrays (categories, subcategories, coverage)
    $categories    = isset($postData['category']) && is_array($postData['category'])
                     ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory'])
                     ? implode(',', $postData['subcategory']) : '';
    $coverage      = isset($postData['coverage']) && is_array($postData['coverage'])
                     ? implode(',', $postData['coverage']) : '';

                     // ✅ Step 1: Get product listing limit for this user
$productLimit = htmlspecialchars(getFeatureLimit($con, $user, 'product_limit', $siteprefix));

// ✅ Step 2: Get listing status from request (default = 'inactive' or 'draft')
$status = mysqli_real_escape_string($con, trim($postData['status'] ?? 'inactive'));

// ✅ Step 3: If the listing is being activated, check user’s limit
if ($status === 'active') {
    // Count user’s current ACTIVE listings
    $countQuery = mysqli_query($con, "
        SELECT COUNT(*) AS total_active
        FROM {$siteprefix}listings
        WHERE user_id = '$user' AND status = 'active'
    ");
    $countData = mysqli_fetch_assoc($countQuery);
    $currentActiveListings = intval($countData['total_active']);

    // Compare count with limit
    if ($productLimit !== 'unlimited' && $currentActiveListings >= intval($productLimit)) {
        return [
            'status' => 'error',
            'messages' => generateMessage(
                "You have reached your active product listing limit ({$productLimit}). Please upgrade your plan to activate more listings.",
                "red"
            )
        ];
    }
}

    // 🧩 Compute Display Price
    $displayPrice = 'Custom Quote';
    $priceMin = $priceMax = '';

    if ($pricingType === 'Starting Price' && !empty($price)) {
        $displayPrice = $price;
    } elseif ($pricingType === 'Price Range' && !empty($postData['variation_price'])) {
        $prices = array_filter($postData['variation_price'], fn($v) => is_numeric($v) && $v > 0);
        if (!empty($prices)) {
            $priceMin = min($prices);
            $priceMax = max($prices);
            $displayPrice = $priceMin . ' - ' . $priceMax;
        }
    }

    // ✅ Validation
    if (empty($listingTitle) || empty($description) || empty($type)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("All required fields must be filled.", "red")
        ];
    }

// 🔒 Get vendor upload limits
$imageLimit = getFeatureLimit($con, $user, 'images', $siteprefix);
$videoLimit = getFeatureLimit($con, $user, 'videos', $siteprefix);

// 🖼 Handle Images (respecting plan limits)
$uploadDir = '../uploads/';
$imageFiles = $_FILES['productImages'] ?? null;

if ($imageFiles && $imageFiles['name'][0] != '') {
    $maxImages = ($imageLimit === 'unlimited') ? count($imageFiles['name']) : min(count($imageFiles['name']), $imageLimit);

    if ($imageLimit !== 'unlimited' && count($imageFiles['name']) > $imageLimit) {
        return [
            'status' => 'error',
            'messages' => generateMessage("You can only upload up to {$imageLimit} images with your plan.", "red")
        ];
    }

    // Limit uploads
    $limitedFiles = [
        'name' => array_slice($imageFiles['name'], 0, $maxImages),
        'type' => array_slice($imageFiles['type'], 0, $maxImages),
        'tmp_name' => array_slice($imageFiles['tmp_name'], 0, $maxImages),
        'error' => array_slice($imageFiles['error'], 0, $maxImages),
        'size' => array_slice($imageFiles['size'], 0, $maxImages)
    ];

    $imageList = uploadImages($limitedFiles, $uploadDir);

    if (!empty($imageList)) {
        foreach ($imageList as $fileName) {
            $stmt = $con->prepare("INSERT INTO {$siteprefix}listing_images (listing_id, file_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $listingId, $fileName);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle Videos (respecting plan limits)
$videoFiles = $_FILES['videos'] ?? null;
if ($videoFiles && $videoFiles['name'][0] != '') {
    $maxVideos = ($videoLimit === 'unlimited') ? count($videoFiles['name']) : min(count($videoFiles['name']), $videoLimit);

    if ($videoLimit !== 'unlimited' && count($videoFiles['name']) > $videoLimit) {
        return [
            'status' => 'error',
            'messages' => generateMessage("You can only upload up to {$videoLimit} videos with your plan.", "red")
        ];
    }

    // Limit uploads
    $limitedVideos = [
        'name' => array_slice($videoFiles['name'], 0, $maxVideos),
        'type' => array_slice($videoFiles['type'], 0, $maxVideos),
        'tmp_name' => array_slice($videoFiles['tmp_name'], 0, $maxVideos),
        'error' => array_slice($videoFiles['error'], 0, $maxVideos),
        'size' => array_slice($videoFiles['size'], 0, $maxVideos)
    ];

    $videoList = uploadVideos($limitedVideos, $uploadDir);
    if (!empty($videoList)) {
        foreach ($videoList as $fileName) {
            mysqli_query($con, "
                INSERT INTO {$siteprefix}listing_videos (listing_id, file_name, uploaded_at)
                VALUES ('$listingId', '$fileName', NOW())
            ");
        }
    }
}

    // 💾 Insert main listing record
    $query = "
        INSERT INTO {$siteprefix}listings (
            listing_id, user_id, title, categories, subcategories, description,
            type, pricing_type, price, price_min, price_max, pricing_notes,
            display_price, availability, limited_slot, capacity, delivery, coverage, created_at,status,slug
        ) VALUES (
            '$listingId', '$user', '$listingTitle', '$categories', '$subcategories', '$description',
            '$type', '$pricingType', '$price', '$priceMin', '$priceMax', '$pricingNotes',
            '$displayPrice', '$availability','$limited_slot', '$capacity', '$delivery', '$coverage', NOW(),'$status','$alt_title'
        )
    ";

    if (!mysqli_query($con, $query)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Database error: " . mysqli_error($con), "red")
        ];
    }

    // 🧩 If pricing type = "Price Range", insert variations
    if ($pricingType === 'Price Range' && !empty($postData['variation_name']) && !empty($postData['variation_price'])) {
        $names = $postData['variation_name'];
        $prices = $postData['variation_price'];

        for ($i = 0; $i < count($names); $i++) {
            $varName = mysqli_real_escape_string($con, trim($names[$i]));
            $varPrice = floatval($prices[$i]);

            if (!empty($varName) && $varPrice > 0) {
                $stmt = $con->prepare("
                    INSERT INTO {$siteprefix}listing_variations (listing_id, variation_name, variation_price)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("ssd", $listingId, $varName, $varPrice);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    return [
        'status' => 'success',
        'messages' => 'Listing submitted successfully!'
    ];
}


function updateSubCategoryEndpoint($postData) {
    global $con, $siteprefix;

    // Validate input
    $subcategory_id = isset($postData['subcategory_id']) ? intval($postData['subcategory_id']) : 0;
    $subCategoryName = isset($postData['subCategoryName']) ? trim($postData['subCategoryName']) : '';
    $parentId = isset($postData['parentId']) ? $postData['parentId'] : null;

    if ($subcategory_id <= 0 || $subCategoryName === '') {
        return [
            'status' => 'error',
            'messages' => "Please provide a valid sub-category name and ID."
        ];
    }

    $escapedName = mysqli_real_escape_string($con, $subCategoryName);

    // Step 1: Fetch existing sub-category info
    $query = "SELECT category_name, slug FROM {$siteprefix}categories WHERE id = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        return [
            'status' => 'error',
            'messages' => "Database error while fetching sub-category: " . mysqli_error($con)
        ];
    }
    $stmt->bind_param("i", $subcategory_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        return [
            'status' => 'error',
            'messages' => "Sub-category not found."
        ];
    }

    $oldSubName = $row['category_name'];
    $oldSlug = $row['slug'];

    // Step 2: Check for duplicate under the same parent (exclude current subcategory)
    if ($parentId === 'NULL') {
        $checkQuery = "SELECT id FROM {$siteprefix}categories WHERE category_name = ? AND parent_id IS NULL AND id != ?";
        $checkStmt = $con->prepare($checkQuery);
        $checkStmt->bind_param("si", $escapedName, $subcategory_id);
    } else {
        $parentIdInt = intval($parentId);
        $checkQuery = "SELECT id FROM {$siteprefix}categories WHERE category_name = ? AND parent_id = ? AND id != ?";
        $checkStmt = $con->prepare($checkQuery);
        $checkStmt->bind_param("sii", $escapedName, $parentIdInt, $subcategory_id);
    }
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        return [
            'status' => 'error',
            'messages' => "A sub-category with the same name already exists under the selected parent category."
        ];
    }

    // Step 3: Generate new slug if name changed
    $altSlug = $oldSlug;
    if ($subCategoryName !== $oldSubName) {
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $subCategoryName), '-'));
        $altSlug = $baseSlug;
        $counter = 1;

        $slugCheckQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}categories WHERE slug = ? AND id != ?";
        $slugStmt = $con->prepare($slugCheckQuery);
        if (!$slugStmt) {
            return [
                'status' => 'error',
                'messages' => "Database error while checking slug: " . mysqli_error($con)
            ];
        }

        while (true) {
            $slugStmt->bind_param("si", $altSlug, $subcategory_id);
            $slugStmt->execute();
            $slugResult = $slugStmt->get_result();
            $slugCount = $slugResult->fetch_assoc();

            if ($slugCount['count'] == 0) break;
            $altSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

    // Step 4: Update sub-category
    if ($parentId === 'NULL') {
        if ($subCategoryName !== $oldSubName) {
            $updateQuery = "UPDATE {$siteprefix}categories SET category_name = ?, parent_id = NULL, slug = ? WHERE id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("ssi", $escapedName, $altSlug, $subcategory_id);
        } else {
            $updateQuery = "UPDATE {$siteprefix}categories SET category_name = ?, parent_id = NULL WHERE id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("si", $escapedName, $subcategory_id);
        }
    } else {
        $parentIdInt = intval($parentId);
        if ($subCategoryName !== $oldSubName) {
            $updateQuery = "UPDATE {$siteprefix}categories SET category_name = ?, parent_id = ?, slug = ? WHERE id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("sisi", $escapedName, $parentIdInt, $altSlug, $subcategory_id);
        } else {
            $updateQuery = "UPDATE {$siteprefix}categories SET category_name = ?, parent_id = ? WHERE id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("sii", $escapedName, $parentIdInt, $subcategory_id);
        }
    }

    if ($updateStmt->execute()) {
        return [
            'status' => 'success',
            'messages' => "Sub-category \"$subCategoryName\" updated successfully."
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => "Failed to update sub-category: " . $updateStmt->error
        ];
    }
}


function adminListingEndpoint($postData, $fileData) {
    global $con, $siteprefix;

    // 🧩 Sanitize inputs
    $listingTitle  = mysqli_real_escape_string($con, trim($postData['listingTitle'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $type          = mysqli_real_escape_string($con, trim($postData['itemType'] ?? ''));
    $pricingType   = mysqli_real_escape_string($con, trim($postData['pricingType'] ?? ''));
    $price         = mysqli_real_escape_string($con, trim($postData['price'] ?? ''));
    $pricingNotes  = mysqli_real_escape_string($con, trim($postData['pricingNotes'] ?? ''));
    $availability  = mysqli_real_escape_string($con, trim($postData['availability'] ?? ''));
    $capacity      = mysqli_real_escape_string($con, trim($postData['capacity'] ?? ''));
    $delivery      = mysqli_real_escape_string($con, trim($postData['delivery'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $listingId     = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'active'));

    // Handle Limited Slot
    $limited_slot = ($availability == 'Limited Slot')
        ? mysqli_real_escape_string($con, trim($postData['available_slots'] ?? ''))
        : '';

    // 🔤 Generate unique slug
    $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $listingTitle), '-'));
    $alt_title = $baseSlug;
    $counter = 1;

    while (true) {
        $query = "SELECT COUNT(*) AS count FROM {$siteprefix}listings WHERE slug = '$alt_title'";
        $res = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($res);
        if ($row['count'] == 0) break;
        $alt_title = $baseSlug . '-' . $counter;
        $counter++;
    }

    // 🧩 Handle arrays (categories, subcategories, coverage)
    $categories    = isset($postData['category']) && is_array($postData['category'])
        ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory'])
        ? implode(',', $postData['subcategory']) : '';
    $coverage      = isset($postData['coverage']) && is_array($postData['coverage'])
        ? implode(',', $postData['coverage']) : '';

    // 🧮 Compute Display Price
    $displayPrice = 'Custom Quote';
    $priceMin = $priceMax = '';

    if ($pricingType === 'Starting Price' && !empty($price)) {
        $displayPrice = $price;
    } elseif ($pricingType === 'Price Range' && !empty($postData['variation_price'])) {
        $prices = array_filter($postData['variation_price'], fn($v) => is_numeric($v) && $v > 0);
        if (!empty($prices)) {
            $priceMin = min($prices);
            $priceMax = max($prices);
            $displayPrice = $priceMin . ' - ' . $priceMax;
        }
    }

    // ✅ Validation
    if (empty($listingTitle) || empty($description) || empty($type)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("All required fields must be filled.", "red")
        ];
    }

    // 🖼 Handle Images (no limit for admin)
    $uploadDir = '../uploads/';
    $imageFiles = $_FILES['productImages'] ?? null;

    if ($imageFiles && $imageFiles['name'][0] != '') {
        $imageList = uploadImages($imageFiles, $uploadDir);
        if (!empty($imageList)) {
            foreach ($imageList as $fileName) {
                $stmt = $con->prepare("INSERT INTO {$siteprefix}listing_images (listing_id, file_name) VALUES (?, ?)");
                $stmt->bind_param("ss", $listingId, $fileName);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // 🎥 Handle Videos (no limit for admin)
    $videoFiles = $_FILES['videos'] ?? null;
    if ($videoFiles && $videoFiles['name'][0] != '') {
        $videoList = uploadVideos($videoFiles, $uploadDir);
        if (!empty($videoList)) {
            foreach ($videoList as $fileName) {
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}listing_videos (listing_id, file_name, uploaded_at)
                    VALUES ('$listingId', '$fileName', NOW())
                ");
            }
        }
    }

    // 💾 Insert main listing record
    $query = "
        INSERT INTO {$siteprefix}listings (
            listing_id, user_id, title, categories, subcategories, description,
            type, pricing_type, price, price_min, price_max, pricing_notes,
            display_price, availability, limited_slot, capacity, delivery,
            coverage, created_at, status, slug
        ) VALUES (
            '$listingId', '$user', '$listingTitle', '$categories', '$subcategories', '$description',
            '$type', '$pricingType', '$price', '$priceMin', '$priceMax', '$pricingNotes',
            '$displayPrice', '$availability', '$limited_slot', '$capacity', '$delivery',
            '$coverage', NOW(), '$status', '$alt_title'
        )
    ";

    if (!mysqli_query($con, $query)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Database error: " . mysqli_error($con), "red")
        ];
    }

    // 🧩 If pricing type = "Price Range", insert variations
    if ($pricingType === 'Price Range' && !empty($postData['variation_name']) && !empty($postData['variation_price'])) {
        $names = $postData['variation_name'];
        $prices = $postData['variation_price'];

        for ($i = 0; $i < count($names); $i++) {
            $varName = mysqli_real_escape_string($con, trim($names[$i]));
            $varPrice = floatval($prices[$i]);

            if (!empty($varName) && $varPrice > 0) {
                $stmt = $con->prepare("
                    INSERT INTO {$siteprefix}listing_variations (listing_id, variation_name, variation_price)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("ssd", $listingId, $varName, $varPrice);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    return [
        'status' => 'success',
        'messages' => 'Listing submitted successfully!'
    ];
}

function updateCategoryEndpoint($postData) {
    global $con, $siteprefix;

    // Validate input
    $category_id = isset($postData['category_id']) ? intval($postData['category_id']) : 0;
    $new_category_name = isset($postData['category_name']) ? trim($postData['category_name']) : '';

    if ($category_id <= 0 || $new_category_name === '') {
        return [
            'status' => 'error',
            'messages' => "Please provide a valid category name."
        ];
    }

    // Step 1: Fetch existing category info
    $query = "SELECT category_name, slug FROM {$siteprefix}categories WHERE id = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        return [
            'status' => 'error',
            'messages' => "Database error while fetching category: " . mysqli_error($con)
        ];
    }

    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        return [
            'status' => 'error',
            'messages' => "Category not found."
        ];
    }

    $old_category_name = $row['category_name'];
    $old_slug = $row['slug'];

    // Step 2: Check if name actually changed
    if ($new_category_name === $old_category_name) {
        return [
            'status' => 'info',
            'messages' => "Category name unchanged. No updates made."
        ];
    }

    // Step 3: Generate a new unique slug
    $category_name_safe = mysqli_real_escape_string($con, $new_category_name);
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $new_category_name), '-'));
    $alt_slug = $baseSlug;
    $counter = 1;

    $slugCheckQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}categories WHERE slug = ? AND id != ?";
    $slugStmt = $con->prepare($slugCheckQuery);

    if (!$slugStmt) {
        return [
            'status' => 'error',
            'messages' => "Database error while checking slug: " . mysqli_error($con)
        ];
    }

    while (true) {
        $slugStmt->bind_param("si", $alt_slug, $category_id);
        $slugStmt->execute();
        $slugResult = $slugStmt->get_result();
        $slugCount = $slugResult->fetch_assoc();

        if ($slugCount['count'] == 0) break;

        $alt_slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Step 4: Update category name and slug
    $updateQuery = "UPDATE {$siteprefix}categories SET category_name = ?, slug = ? WHERE id = ?";
    $updateStmt = $con->prepare($updateQuery);

    if (!$updateStmt) {
        return [
            'status' => 'error',
            'messages' => "Database error while preparing update: " . mysqli_error($con)
        ];
    }

    $updateStmt->bind_param("ssi", $category_name_safe, $alt_slug, $category_id);

    if ($updateStmt->execute()) {
        return [
            'status' => 'success',
            'messages' => "Category \"$new_category_name\" updated successfully with new slug."
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => "Failed to update category: " . $updateStmt->error
        ];
    }
}


function updateListingEndpoint($postData, $fileData) {
    global $con, $siteprefix;

    // 🔒 Sanitize Inputs
    $listingId     = $postData['listing_id'] ?? 0;
    $listingTitle  = mysqli_real_escape_string($con, trim($postData['listingTitle'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $type          = mysqli_real_escape_string($con, trim($postData['itemType'] ?? ''));
    $pricingType   = mysqli_real_escape_string($con, trim($postData['pricingType'] ?? ''));
    $price         = floatval($postData['price'] ?? 0);
    $pricingNotes  = mysqli_real_escape_string($con, trim($postData['pricingNotes'] ?? ''));
    $availability  = mysqli_real_escape_string($con, trim($postData['availability'] ?? ''));
    $capacity      = intval($postData['capacity'] ?? 0);
    $delivery      = mysqli_real_escape_string($con, trim($postData['delivery'] ?? ''));
    $user          = intval($postData['user'] ?? 0);
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'inactive'));
    $limited_slot  = ($availability == 'Limited Slot') ? mysqli_real_escape_string($con, trim($postData['available_slots'] ?? '')) : '';

    // 🧩 Arrays
    $categories    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $coverage      = isset($postData['coverage']) && is_array($postData['coverage']) ? implode(',', $postData['coverage']) : '';

    // Compute Display Price
    $displayPrice = 'Custom Quote';
    $priceMin = $priceMax = '';
    if ($pricingType === 'Starting Price' && !empty($price)) {
        $displayPrice = $price;
    } elseif ($pricingType === 'Price Range' && !empty($postData['variation_price'])) {
        $prices = array_filter($postData['variation_price'], fn($v) => is_numeric($v) && $v > 0);
        if (!empty($prices)) {
            $priceMin = min($prices);
            $priceMax = max($prices);
            $displayPrice = "{$priceMin} - {$priceMax}";
        }
    }


    // ✅ Step 1: Get product listing limit for this user
$productLimit = htmlspecialchars(getFeatureLimit($con, $user, 'product_limit', $siteprefix));

// ✅ Step 2: Check active listing limit if status is active
if ($status === 'active') {
    $countQuery = mysqli_query($con, "
        SELECT COUNT(*) AS total_active
        FROM {$siteprefix}listings
        WHERE user_id = '$user' AND status = 'active' AND listing_id != '$listingId'
    ");
    $countData = mysqli_fetch_assoc($countQuery);
    $currentActiveListings = intval($countData['total_active']);

    if ($productLimit !== 'unlimited' && $currentActiveListings >= intval($productLimit)) {
        return [
            'status' => 'error',
            'messages' => generateMessage(
                "You have reached your active product listing limit ({$productLimit}). Please deactivate another listing before activating this one.",
                "red"
            )
        ];
    }
}


    // ✅ Validation

    // Vendor Upload Limits
    $imageLimit = getFeatureLimit($con, $user, 'images', $siteprefix);
    $videoLimit = getFeatureLimit($con, $user, 'videos', $siteprefix);

    //  Existing Media Counts
    $existingImages = mysqli_num_rows(mysqli_query($con, "SELECT id FROM {$siteprefix}listing_images WHERE listing_id = '$listingId'"));
    $existingVideos = mysqli_num_rows(mysqli_query($con, "SELECT id FROM {$siteprefix}listing_videos WHERE listing_id = '$listingId'"));

    $uploadDir = '../uploads/';

    //  IMAGE Uploads (respecting plan limits)
    $newImages = $fileData['productImages'] ?? null;
    if ($newImages && $newImages['name'][0] != '') {
        $totalAfterUpload = $existingImages + count($newImages['name']);
        if ($imageLimit !== 'unlimited' && $totalAfterUpload > $imageLimit) {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "You already have {$existingImages} images. Your plan allows {$imageLimit}. Delete some before uploading new ones.",
                    "red"
                )
            ];
        }

        $remainingSlots = ($imageLimit === 'unlimited') ? count($newImages['name']) : $imageLimit - $existingImages;
        $limitedFiles = [
            'name' => array_slice($newImages['name'], 0, $remainingSlots),
            'type' => array_slice($newImages['type'], 0, $remainingSlots),
            'tmp_name' => array_slice($newImages['tmp_name'], 0, $remainingSlots),
            'error' => array_slice($newImages['error'], 0, $remainingSlots),
            'size' => array_slice($newImages['size'], 0, $remainingSlots)
        ];

        $uploadedImages = uploadImages($limitedFiles, $uploadDir);
        foreach ($uploadedImages as $fileName) {
            mysqli_query($con, "INSERT INTO {$siteprefix}listing_images (listing_id, file_name, uploaded_at) VALUES ('$listingId', '$fileName', NOW())");
        }
    }

    //  VIDEO Uploads (respecting plan limits)
    $newVideos = $fileData['videos'] ?? null;
    if ($newVideos && $newVideos['name'][0] != '') {
        $totalAfterUpload = $existingVideos + count($newVideos['name']);
        if ($videoLimit !== 'unlimited' && $totalAfterUpload > $videoLimit) {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "You already have {$existingVideos} videos. Your plan allows {$videoLimit}. Delete some before uploading new ones.",
                    "red"
                )
            ];
        }

        $remainingSlots = ($videoLimit === 'unlimited') ? count($newVideos['name']) : $videoLimit - $existingVideos;
        $limitedVideos = [
            'name' => array_slice($newVideos['name'], 0, $remainingSlots),
            'type' => array_slice($newVideos['type'], 0, $remainingSlots),
            'tmp_name' => array_slice($newVideos['tmp_name'], 0, $remainingSlots),
            'error' => array_slice($newVideos['error'], 0, $remainingSlots),
            'size' => array_slice($newVideos['size'], 0, $remainingSlots)
        ];

        $uploadedVideos = uploadVideos($limitedVideos, $uploadDir);
        foreach ($uploadedVideos as $fileName) {
            mysqli_query($con, "INSERT INTO {$siteprefix}listing_videos (listing_id, file_name, uploaded_at) VALUES ('$listingId', '$fileName', NOW())");
        }
    }

    // Update Main Listing
    $sql = "
        UPDATE {$siteprefix}listings SET
            title = '$listingTitle',
            categories = '$categories',
            subcategories = '$subcategories',
            description = '$description',
            type = '$type',
            pricing_type = '$pricingType',
            price = '$price',
            price_min = '$priceMin',
            price_max = '$priceMax',
            pricing_notes = '$pricingNotes',
            display_price = '$displayPrice',
            availability = '$availability',
            limited_slot = '$limited_slot',
            capacity = '$capacity',
            delivery = '$delivery',
            coverage = '$coverage',
            status = '$status',
            updated_at = NOW()
        WHERE listing_id = '$listingId'
    ";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }

    // Handle Variations (smart update)
    if ($pricingType === 'Price Range') {
        $names = $postData['variation_name'] ?? [];
        $prices = $postData['variation_price'] ?? [];
        $ids = $postData['variation_id'] ?? []; // hidden field in form

        $existingIds = [];

        for ($i = 0; $i < count($names); $i++) {
            $varId = intval($ids[$i] ?? 0);
            $varName = mysqli_real_escape_string($con, trim($names[$i]));
            $varPrice = floatval($prices[$i]);

            if (empty($varName) || $varPrice <= 0) continue;

            if ($varId > 0) {
                // Update existing variation
                $update = $con->prepare("
                    UPDATE {$siteprefix}listing_variations
                    SET variation_name = ?, variation_price = ?
                    WHERE id = ? AND listing_id = ?
                ");
                $update->bind_param("ssss", $varName, $varPrice, $varId, $listingId);
                $update->execute();
                $update->close();
                $existingIds[] = $varId;
            } else {
                // Add new variation
                $insert = $con->prepare("
                    INSERT INTO {$siteprefix}listing_variations (listing_id, variation_name, variation_price)
                    VALUES (?, ?, ?)
                ");
                $insert->bind_param("sss", $listingId, $varName, $varPrice);
                $insert->execute();
                $newId = $con->insert_id;
                $existingIds[] = $newId;
                $insert->close();
            }
        }

        // Remove variations no longer in form
        if (!empty($existingIds)) {
            $keepIds = implode(',', array_map('intval', $existingIds));
            mysqli_query($con, "
                DELETE FROM {$siteprefix}listing_variations
                WHERE listing_id = '$listingId' AND id NOT IN ($keepIds)
            ");
        } else {
            // All removed
            mysqli_query($con, "DELETE FROM {$siteprefix}listing_variations WHERE listing_id = '$listingId'");
        }
    }

    return ['status' => 'success', 'messages' => 'Listing updated successfully!'];
}


function updatevendorBlogEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    $blogId = intval($postData['blog_id'] ?? 0);
    $user = intval($postData['user'] ?? 0);
    $title = mysqli_real_escape_string($con, trim($postData['blogTitle'] ?? ''));
    $article = $postData['blogContent'] ?? '';
    $tags = mysqli_real_escape_string($con, trim($postData['blogTags'] ?? ''));
    $status = mysqli_real_escape_string($con, trim($postData['status'] ?? ''));
    $categories = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategories = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    // ✅ Required fields check
    if (empty($blogId) || empty($title) || empty($article)) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Fetch current blog info
    $existing = mysqli_query($con, "SELECT status, user_id, title FROM {$siteprefix}forums WHERE id = '$blogId'");
    if (!$existing || mysqli_num_rows($existing) === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Blog not found.", "red")];
    }

    $blog = mysqli_fetch_assoc($existing);
    $oldStatus = $blog['status'];
    $authorId = $blog['user_id'];
    $oldTitle = $blog['title'];

    // ✅ If trying to set active, enforce article limit
    if ($status === 'active') {
        // Get user's allowed active post limit (if applicable)
        $limitQuery = mysqli_query($con, "SELECT article_limit FROM {$siteprefix}users WHERE id = '$authorId' LIMIT 1");
        $limitData = mysqli_fetch_assoc($limitQuery);
        $articleLimit = intval($limitData['article_limit'] ?? 0);

        if ($articleLimit > 0) {
            // Count how many active articles user already has (excluding current one)
            $activeCountQuery = mysqli_query($con, "
                SELECT COUNT(*) AS total 
                FROM {$siteprefix}forums 
                WHERE user_id = '$authorId' AND status = 'active' AND id != '$blogId'
            ");
            $activeCount = mysqli_fetch_assoc($activeCountQuery)['total'];

            if ($activeCount >= $articleLimit) {
                return ['status' => 'error', 'messages' => generateMessage("You’ve reached your active article limit ({$articleLimit}). Please deactivate another before activating this one.", "red")];
            }
        }
    }

    // ✅ Handle image upload if new file is provided
    $featuredImageSql = '';
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    if (!empty($fileData["blogCover"]["name"])) {
        $fileName = basename($fileData["blogCover"]["name"]);
        $featuredImage = uniqid('forum_') . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        move_uploaded_file($fileData["blogCover"]["tmp_name"], $targetDir . $featuredImage);
        $featuredImageSql = ", featured_image = '$featuredImage'";
    }

    // ✅ Regenerate slug only if title changed
    $slugSql = '';
    if ($oldTitle !== $title) {
        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $slug = $baseSlug;
        $counter = 1;

        // Ensure slug uniqueness
        while (true) {
            $check = $con->prepare("SELECT COUNT(*) AS count FROM {$siteprefix}forums WHERE slug = ? AND id != ?");
            $check->bind_param("si", $slug, $blogId);
            $check->execute();
            $result = $check->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] == 0) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $slugSql = ", slug = '$slug'";
    }

    // ✅ Update blog
    $query = "
        UPDATE {$siteprefix}forums 
        SET 
            title = '$title',
            article = '$article',
            tags = '$tags',
            categories = '$categories',
            subcategories = '$subcategories',
            status = '$status'
            $featuredImageSql
            $slugSql
        WHERE id = '$blogId'
    ";

    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => 'Blog updated successfully!'];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
}


function changePasswordEndpoint($postData) {
    global $con, $siteprefix;

    $userId = $postData['user_id'];
    $currentPassword = $postData['currentPassword'] ?? '';
    $newPassword = $postData['newPassword'] ?? '';
    $confirmPassword = $postData['confirmPassword'] ?? '';

    // Fetch current hashed password
    $stmt = $con->prepare("SELECT password FROM {$siteprefix}users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        return ['status' => 'error', 'messages' => 'User not found.'];
    }

    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // Verify current password
    if (!password_verify($currentPassword, $hashedPassword)) {
        return ['status' => 'error', 'messages' => 'Current password is incorrect.'];
    }

    // Validate new password
    if ($newPassword !== $confirmPassword) {
        return ['status' => 'error', 'messages' => 'New passwords do not match.'];
    }

    if (strlen($newPassword) < 8 || 
        !preg_match('/[A-Z]/', $newPassword) || 
        !preg_match('/[a-z]/', $newPassword) || 
        !preg_match('/\d/', $newPassword)) {
        return ['status' => 'error', 'messages' => 'Password must be at least 8 characters and include uppercase, lowercase, and numbers.'];
    }

    // Hash new password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update in database
    $update = $con->prepare("UPDATE {$siteprefix}users SET password = ? WHERE id = ?");
    $update->bind_param("si", $newHashedPassword, $userId);

    if ($update->execute()) {
        return ['status' => 'success', 'messages' => 'Password changed successfully.'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . mysqli_error($con)];
    }
}


function getallsubscriptions($con)
{
    global $siteprefix;

    $query = "SELECT * FROM {$siteprefix}subscriptions ORDER BY price ASC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $subscriptions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subscriptions[] = $row;
        }
        return $subscriptions;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function addViews($con, $slug) {  
    global $siteprefix;

    // Increment view count
    $updateQuery = "UPDATE {$siteprefix}forums SET views = views + 1 WHERE slug = '$slug'";

    if (mysqli_query($con, $updateQuery)) {
        return ['status' => 'success'];
    } else {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }
}


function addViewsquestion($con, $slug) {  
    global $siteprefix;

    // Increment view count
    $updateQuery = "UPDATE {$siteprefix}questions SET views = views + 1 WHERE slug = '$slug'";

    if (mysqli_query($con, $updateQuery)) {
        return ['status' => 'success'];
    } else {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
if (isset($_GET['action']) && $_GET['action'] == 'addviews') {
    if (!empty($_GET['slug'])) {
        $response = addViews($con, $_GET['slug']);
    } 
}


if (isset($_GET['action']) && $_GET['action'] == 'addviewsquest') {
    if (!empty($_GET['slug'])) {
        $response = addViewsquestion($con, $_GET['slug']);
    } 
}


if (isset($_GET['action']) && $_GET['action'] == 'getlistingsbyuser') {
    if (!empty($_GET['user_id'])) {
        $response = getAllListingsbyuser($con, $_GET['user_id']);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'getusersticket') {
    if (!empty($_GET['user_id'])) {
        $response = getAllTicketsbyuser($con, $_GET['user_id']);
    }
}

if ($_GET['action'] == 'getTicketDetails') {
    $ticketNumber = $_GET['ticket_number'] ?? '';
   $response = getTicketDetails($con, $ticketNumber);
 
}

if ($_GET['action'] == 'getTicketEvidence') {
   $response = getTicketEvidence($con, $_GET['dispute_id']);
  
}

if ($_GET['action'] == 'getTicketMessages') {
    $response = getTicketMessages($con, $_GET['ticket_number']);
 
}



if (isset($_GET['action']) && $_GET['action'] == 'getuserorders') {
    if (!empty($_GET['user_id'])) {
        $response = getAllOrdersuser($con, $_GET['user_id']);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'getorderitems') {
    if (!empty($_GET['order_id'])) {
        $response = getOrderItems($con, $_GET['order_id']);
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'groupuserstatus') {
    if (!empty($_GET['user_id'])) {
        $response = getAllUserGroupsAndMemberships($con, $_GET['user_id']);
    } 
}



if ($_GET['action'] == 'questionlists') {
              $response = getallquestions($con);} 

    if ($_GET['action'] == 'subscriptionlists') {
    $response = getallsubscriptions($con);
}


if ($_GET['action'] == 'fetchlistingslug') {
    $response = isset($_GET['slug']) 
        ? getAllListingBySlug($con, $_GET['slug']) 
        : ['error' => 'Slug is required'];
}


if ($_GET['action'] == 'listinglists') {
    // Read filter params (from marketplace GET form)
    $filters = [
        'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
        'price_range' => isset($_GET['price_range']) ? trim($_GET['price_range']) : '',
        'sort' => isset($_GET['sort']) ? trim($_GET['sort']) : '',
        'category_id' => isset($_GET['category_id']) ? intval($_GET['category_id']) : 0,
        'subcategory_id' => isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0,
        'items_per_page' => isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 12,
        'page' => isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1,
        'ajax' => isset($_GET['ajax']) && $_GET['ajax'] == '1',
    ];
    $response = getAllListingsFiltered($con, $filters);
}

if ($_GET['action'] == 'listing_price_bounds') {
    $response = getListingPriceBounds($con);
}

// Return flat list of categories for the frontend filter selects
if ($_GET['action'] == 'category_list') {
    $cats = [];
    // return stored slug from categories table (assumes a `slug` column exists)
    $q = "SELECT id, category_name, slug FROM {$siteprefix}categories ORDER BY category_name ASC";
    $r = mysqli_query($con, $q);
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) $cats[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($cats);
    exit;
}

// Return subcategories for a given category (assumes categories table has parent_id)
if ($_GET['action'] == 'subcategory_list' && (isset($_GET['category_id']) || isset($_GET['category_slug']))) {
    $subs = [];
    $parentId = 0;
    if (isset($_GET['category_id'])) {
        $parentId = intval($_GET['category_id']);
    } elseif (isset($_GET['category_slug'])) {
    $slug = mysqli_real_escape_string($con, trim($_GET['category_slug']));
    // resolve slug to id using stored slug column
    $q2 = "SELECT id FROM {$siteprefix}categories WHERE slug = '$slug' LIMIT 1";
        $r2 = mysqli_query($con, $q2);
        if ($r2 && $row2 = mysqli_fetch_assoc($r2)) $parentId = intval($row2['id']);
    }
    if ($parentId > 0) {
    $q = "SELECT id, category_name, slug FROM {$siteprefix}categories WHERE parent_id = $parentId ORDER BY category_name ASC";
        $r = mysqli_query($con, $q);
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) $subs[] = $row;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($subs);
    exit;
}

 if ($_GET['action'] === 'editlist') {
        $response = isset($_GET['listing_id'])
            ? getListingData($_GET['listing_id'])
            : ['status' => 'error', 'message' => 'Listing ID is required'];
    } 

if (isset($_GET['action']) && $_GET['action'] === 'all_seller_product_reviews') {
    if (isset($_GET['seller_id'])) {
        $response = fetchSellerProductReviews($_GET['seller_id']);
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'all_therapist_reviews') {
    if (isset($_GET['therapist_id'])) {
        $response = fetchTherapistReviews($_GET['therapist_id']);
    }
}



if (isset($_GET['action']) && $_GET['action'] === 'all_user_product_reviews') {
    if (isset($_GET['user_id'])) {
        $response = fetchUserProductReviews($_GET['user_id']);
    }
}


if (isset($_GET['action']) && $_GET['action'] === 'fetch_review_by_id') {
    if (isset($_GET['review_id'])) {
        $response = fetchReviewById($_GET['review_id']);
    } else {
        $response = ['error' => 'Review ID is required'];
    }
}

 if ($_GET['action'] == 'mylists') {
              $response = getAllListings($con);}

      if ($_GET['action'] == 'bloglists') {
              $response = getallblog($con);}

    if ($_GET['action'] == 'disputeslists') {     
    $response = getalldisputestickets($con);
 }

              if ($_GET['action'] == 'categorylists') {
              $response = getallcategory($con);}


              

                if ($_GET['action'] == 'notificationlists') {
              $response = getallnotifications($con);}

            if ($_GET['action'] == 'usernotificationlists') {
            $response = getallusersnotifications($con, $_GET['user_id']);
            }

        if ($_GET['action'] == 'walletlists') {
              $response = getallwallet($con);
        }

    if ($_GET['action'] == 'manualpaymentlists') {
              $response =  getAllManualPayments($con);
        }
              
    if ($_GET['action'] == 'buyerlists') {
              $response =  getalluser($con);}

              
  if($_GET['action'] == 'allsub'){
    $response = getallsubcategory($con);
}
              
     if ($_GET['action'] == 'grouplists') {
              $response = getallgroups($con);}  

        
if ($_GET['action'] == 'bookinglist') {
          $response = getBookings($con);}

 if ($_GET['action'] == 'groupmemberlists') {
    $response =   getallgroupsmembers($con);}  
            

if ($_GET['action'] == 'userlists') {
$response = getallusers($con);}

// New: therapist rating endpoint (uses ma_reviews table)
if ($_GET['action'] == 'therapist_rating') {
    $therapist_id = $_GET['therapist_id'] ?? '';
    if (!empty($therapist_id)) {
        $response = getTherapistRating($con, $therapist_id);
    } else {
        $response = ['error' => 'therapist_id is required'];
    }
}


if ($_GET['action'] == 'planlists') {
$response = getallplans($con);}

            
if ($_GET['action'] == 'editbooking') {  
$response = isset($_GET['booking_id']) ? getbookingsID($con, $_GET['booking_id']) : ['error' => 'Booking ID is required'];}         

     if ($_GET['action'] == 'editblog') {  
        $response = isset($_GET['blog_id']) ? getblogID($con, $_GET['blog_id']) : ['error' => 'Blog ID is required'];}

         if ($_GET['action'] == 'editcategory') {  
        $response = isset($_GET['category_id']) ?  getcategoriesID($con, $_GET['category_id']) : ['error' => 'Category ID is required'];}

        if ($_GET['action'] == 'editsubcategory') {  
        $response = isset($_GET['subcategory_id']) ?  getsubcategoriesID($con, $_GET['subcategory_id']) : ['error' => 'Category ID is required'];}
        
        if ($_GET['action'] == 'walletuser') {  
        $response = isset($_GET['user_id']) ? getwalletID($con, $_GET['user_id']) : ['error' => 'Wallet ID is required'];}
        

        if ($_GET['action'] == 'editplan') {  
        $response = isset($_GET['plan_id']) ? getplanID($con, $_GET['plan_id']) : ['error' => 'Plan ID is required'];}    
        
        if ($_GET['action'] == 'editvendor') {  
        $response = isset($_GET['vendor_id']) ? getVendorByID($con, $_GET['vendor_id']) : ['error' => 'Vendor ID is required'];}
        
         if ($_GET['action'] == 'edittherapist') {  
        $response = isset($_GET['therapist_id']) ? getTherapistByID($con, $_GET['therapist_id']) : ['error' => 'Therapist ID is required'];}
        
        if ($_GET['action'] == 'edituser') {  
        $response = isset($_GET['user_id']) ? getUserID($con, $_GET['user_id']) : ['error' => 'User ID is required'];}

if ($_GET['action'] == 'memberid') {
    $group_id = $_GET['group_id'] ?? '';
    $user_id  = $_GET['user_id'] ?? '';
 $response = getMemberID($con, $group_id, $user_id);
}
  
        // ✅ Check if User is Group Member
 if ($_GET['action'] == 'checkuserMember') {
    $group_id = $_GET['group_id'] ?? '';
    $user_id  = $_GET['user_id'] ?? '';

    if (!empty($group_id) && !empty($user_id)) {
        $response = checkGroupMember($con, $group_id, $user_id);
    } else {
        $response = ['error' => 'Missing group_id or user_id'];
    }
}
        if ($_GET['action'] == 'fetchblogslug') {  
        $response = isset($_GET['slug']) ? getallblogbyslug($con, $_GET['slug']) : ['error' => 'slug ID is required'];}

        if ($_GET['action'] == 'vendorslug') {  
        $response = isset($_GET['slug']) ? checkuservendor($con, $_GET['slug']) : ['error' => 'slug ID is required'];}

       if ($_GET['action'] == 'fetchgroupslug') {  
        $response = isset($_GET['slug']) ? getgroupByslug($con, $_GET['slug']) : ['error' => 'slug ID is required'];} 

       if ($_GET['action'] == 'fetchtherapistslug') {  
        $response = isset($_GET['slug']) ? gettherapistByslug($con, $_GET['slug']) : ['error' => 'slug ID is required'];}  
        

         if ($_GET['action'] == 'fetchorderdetails') {  
        $response = isset($_GET['ref']) ? getOrderdetailsID($con, $_GET['ref']) : ['error' => 'order ID is required'];}
        
        
    if ($_GET['action'] == 'fetchbuyerinfo') {
        if (!empty($_GET['user_id'])) {
            $response = getBuyerInfo($con, $_GET['user_id']);
        }
    }


    if ($_GET['action'] == 'getusersWishlist') {
        if (!empty($_GET['user_id'])) {
            $response = getallUserWishlist($con, $_GET['user_id']);
        }
    }

    if ($_GET['action'] == 'fetchlistingandseller') {
        if (!empty($_GET['listing_id'])) {
            $response = getListingAndSeller($con, $_GET['listing_id']);
        }
    }

      if ($_GET['action'] == 'getorderdetails') {
        if (!empty($_GET['order_id'])) {
            $response = getOrderItemsWithSeller($con, $_GET['order_id']);
        }
    }


    
    

    
if ($_GET['action'] == 'fetchwallet') {  
$response = isset($_GET['user_id']) ? getWalletTotals($con, $_GET['user_id']) : ['error' => 'User ID is required'];} 
        
        if ($_GET['action'] == 'fetchgroupid') {  
        $response = isset($_GET['group_id']) ? getgroupByid($con, $_GET['group_id']) : ['error' => 'group ID is required'];} 

         if ($_GET['action'] == 'editgroup') {  
        $response = isset($_GET['group_id']) ? getgroupID($con, $_GET['group_id']) : ['error' => 'group ID is required'];} 
            
        if ($_GET['action'] == 'earnings_breakdown') {
            $response = getEarningsBreakdown($con);}

            if ($_GET['action'] == 'get_service_bookings') {
            $response = getServiceBookings($con);}
            
            
        if ($_GET['action'] == 'fetchgroupmembersid') {  
            $response = isset($_GET['group_id']) ? getgroupBygroupid($con, $_GET['group_id']) : ['error' => 'group ID is required'];} 

    if ($_GET['action'] == 'fetchquestionslug') {  
        $response = isset($_GET['slug']) ? getallquestionbyslug($con, $_GET['slug']) : ['error' => 'slug ID is required'];}
 
        header('Content-Type: application/json');
    echo json_encode($response);
}



// ✅ API Endpoint Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    if ($_POST['action'] == 'addforum') {
        $response = adminforumEndpoint($_POST, $_FILES);
    }

     if ($_POST['action'] == 'update_user') {
        $response = updateUserEndpoint($_POST, $_FILES);
    }

 if ($_POST['action'] == 'addlisting') {
        $response = addListingEndpoint($_POST, $_FILES);
    }

     if ($_POST['action'] == 'adminlisting') {
        $response = adminListingEndpoint($_POST, $_FILES);
    }
    

    
        if ($_POST['action'] == 'updateblog') {
       $response =  updateBlogEndpoint($_POST, $_FILES);
        }

 if ($_POST['action'] == 'updatebooking') {
       $response =  updateBookingEndpoint($_POST, $_FILES);
        }

    if ($_POST['action'] == 'create_admingroup') {
        $response = createadminGroupEndpoint($_POST, $_FILES);
    }

       if ($_POST['action'] == 'addevents') {
        $response = usereventsEndpoint($_POST, $_FILES);
    }

    if ($_POST['action'] == 'addgroupforum') {
        $response = groupforumEndpoint($_POST, $_FILES);
    }

      if ($_POST['action'] == 'addvendorforum') {
        $response = addvendorForumEndpoint($_POST, $_FILES);
    }
    

    if($_POST['action'] == 'deleteblog'){
    $response = deleteblogEndpoint($_POST);}

        if($_POST['action'] == 'deletereviews'){
    $response = deletereviewEndpoint($_POST);}
    

      if($_POST['action'] == 'deletelistings'){
    $response = deletelistingEndpoint($_POST);}

    if($_POST['action'] == 'deletecategory'){
    $response = deletecategoryEndpoint($_POST);}

      if($_POST['action'] == 'respond_review'){
    $response = respondReview($_POST);}
    
    if($_POST['action'] == 'deletequestion'){
    $response = deletequestionEndpoint($_POST);}

    

    if($_POST['action'] == 'deleteusers'){
    $response = deleteusersEndpoint($_POST);} 


     if($_POST['action'] == 'deleteplans'){
    $response = deleteplansEndpoint($_POST);} 
    

    if($_POST['action'] == 'deletegroup'){
    $response = deletegroupEndpoint($_POST);} 
    

    
    if($_POST['action'] == 'deletegroupmembers'){
    $response =  deletegroupmembersEndpoint($_POST);} 

    
     if($_POST['action'] == 'join-group'){
    $response = joingroupEndpoint($_POST);}


      if($_POST['action'] == 'request-join-group'){
    $response = request_join_groupEndpoint($_POST);
}

  if($_POST['action'] == 'approve-booking'){
    $response = approvebookings($_POST);
}

if($_POST['action'] == 'updatewallet'){
    $response = updateDisputeWallet($_POST);
}

  if($_POST['action'] == 'updateadmin'){
    $response = updateAdminSettingsEndpoint($_POST, $_FILES);}  


  if($_POST['action'] == 'reject-booking'){
    $response = rejectbookings($_POST);
}
  if($_POST['action'] == 'approvewallet'){
    $response = approvewithdrawal($_POST);
}

if($_POST['action'] == 'editcategoryadmin'){
    $response = updateCategoryEndpoint($_POST);
} // end

   if($_POST['action'] == 'approvemanual'){
    $response = approveManualPayment($_POST);
} // end

  if($_POST['action'] == 'subscription_plans'){
    $response = addSubscriptionEndpoint($_POST);
}


   if($_POST['action'] == 'addcategory'){
    $response = addCategoryEndpoint($_POST);
} // end

   if($_POST['action'] == 'changePassword'){
    $response = changePasswordEndpoint($_POST);
}


   if($_POST['action'] == 'addsubcategory'){
    $response = addSubCategoryEndpoint($_POST);
} // end


 if($_POST['action'] == 'updateplan'){
    $response = updateSubscriptionEndpoint($_POST);
}


   if($_POST['action'] == 'edit_vendorsettings'){
    $response = editVendorEndpoint($_POST, $_FILES);}  
    
    if($_POST['action'] == 'exit-group'){
    $response = exitGroupEndpoint($_POST);}

    if($_POST['action'] == 'withdraw'){
    $response = withdrawWalletEndpoint($_POST);}

     if($_POST['action'] == 'updategroupmember'){
    $response = updatememberEndpoint($_POST);}

    if($_POST['action'] == 'book_therapy_session'){
    $response = bookTherapySessionEndpoint($_POST);}

    if($_POST['action'] == 'edit_admingroup'){
    $response = updateAdminGroupEndpoint($_POST, $_FILES);}  

if($_POST['action'] == 'edit_newadmingroup'){
    $response = updatenewAdminGroupEndpoint($_POST, $_FILES);}
    
 if($_POST['action'] == 'edit_listing'){
    $response = updateListingEndpoint($_POST, $_FILES);}   
    
    
      // ✅ DELETE IMAGE
    if ($_POST['action'] == 'deleteimage') {
        $response = deleteimageEndpoint($_POST);
    }

    if($_POST['action'] == 'rejectmanual'){
        $response = rejectManualPayment($_POST);
    }

    // ✅ DELETE VIDEO
    if ($_POST['action'] == 'deletevideo') {
        $response = deletevideoEndpoint($_POST);
    }
    

         if($_POST['action'] == 'editadmin_listing'){
    $response =  updateadminListingEndpoint($_POST, $_FILES);} 


    if($_POST['action'] == 'editsubcategoryadmin'){
    $response = updateSubCategoryEndpoint($_POST);} 

   

     if($_POST['action'] == 'edit_adminvendor'){
    $response = updateVendorEndpoint($_POST, $_FILES);}  

 if($_POST['action'] == 'createsecondticket'){
    $response = sendDisputeMessage($_POST, $_FILES);}  
    

    if($_POST['action'] == 'send_group_message'){
        $response = sendGroupMessageEndpoint($_POST);
    }

    if($_POST['action'] == 'updateticketstatus'){
    $response = updateDisputeStatusHandler($_POST);

    }

    if($_POST['action'] == 'deletesubcategory'){
    $response = deletesubcategoryEndpoint($_POST);}



     if ($_POST['action'] == 'updatevendorblog') {
        $response = updatevendorBlogEndpoint($_POST, $_FILES);
    }

    
    if ($_POST['action'] == 'markAllNotificationsRead') {
        $response = markAllNotificationsRead($_POST);
    }
   

    if ($_POST['action'] == 'markuserNotificationsRead') {
          $response = markAlluserNotificationsRead($_POST);
     }
  if ($_POST['action'] == 'createticket') {
          $response = createDispute($_POST);
     }
    
    header('Content-Type: application/json');
    echo json_encode($response);

}

?>

