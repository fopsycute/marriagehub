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


// update event
function userupdateeventsEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // EVENT ID REQUIRED
    if (empty($postData['event_id'])) {
        return ['status' => 'error', 'message' => 'Missing event_id'];
    }

    $event_id = mysqli_real_escape_string($con, $postData['event_id']);

    /* ------------------------------
        1. BASIC EVENT DETAILS
    --------------------------------*/
    $title = mysqli_real_escape_string($con, $postData['title']);
    $description = mysqli_real_escape_string($con, $postData['description']);
    $event_type = mysqli_real_escape_string($con, $postData['eventType']);

    $categories = isset($postData['category']) ? implode(",", $postData['category']) : "";
    $subcategories = isset($postData['subcategory']) ? implode(",", $postData['subcategory']) : "";
    $target_audience = isset($postData['target_audience']) ? implode(",", $postData['target_audience']) : "";

    $delivery_format = $postData['delivery_format'] ?? "";
    $pricing_type = $postData['pricing_type'] ?? "free";
    $status = $postData['status'] ?? "pending";

    /* ------------------------------
        2. DELIVERY FORMAT FIELDS
    --------------------------------*/
    $is_foreign = 0;
    $address = "";
    $state = "";
    $lga = "";
    $country = "";

    if ($delivery_format == "physical") {

        $locationType = $postData['physicalLocationType'];

        if ($locationType == "nigeria") {
            $is_foreign = 0;
            $country = "Nigeria";
            $address = $postData['nigeria_address'] ?? "";
            $state = $postData['state'] ?? "";
            $lga = $postData['lga'] ?? "";
        } else {
            $is_foreign = 1;
            $country = $postData['foreign_country'] ?? "";
            $address = $postData['foreign_address'] ?? "";
        }
    }

    $online_link = ($delivery_format == "online") ? $postData['web_address'] : "";

    $hybrid_physical_address = "";
    $hybrid_web_address = "";
    $hybrid_state = "";
    $hybrid_lga = "";
    $hybrid_country = "";
    $hybrid_foreign_address = "";

    if ($delivery_format == "hybrid") {

        $hybrid_physical_address = $postData['hybrid_physical_address'] ?? "";
        $hybrid_web_address = $postData['hybrid_web_address'] ?? "";

        if ($postData["hybridLocationType"] == "nigeria") {
            $hybrid_country = "Nigeria";
            $hybrid_state = $postData["hybrid_state"];
            $hybrid_lga = $postData["hybrid_lga"];
        } else {
            $hybrid_country = $postData["hybrid_country"] ?? "";
            $hybrid_foreign_address = $postData["hybrid_foreign_address"] ?? "";
        }
    }

    /* ------------------------------
        UPDATE MAIN EVENTS TABLE
    --------------------------------*/
    $update = "
        UPDATE {$siteprefix}events SET 
            title='$title',
            description='$description',
            categories='$categories',
            subcategories='$subcategories',
            event_type='$event_type',
            target_audience='$target_audience',
            delivery_format='$delivery_format',
            pricing_type='$pricing_type',
            is_foreign='$is_foreign',
            address='$address',
            state='$state',
            lga='$lga',
            country='$country',
            online_link='$online_link',
            hybrid_physical_address='$hybrid_physical_address',
            hybrid_web_address='$hybrid_web_address',
            hybrid_state='$hybrid_state',
            hybrid_lga='$hybrid_lga',
            hybrid_country='$hybrid_country',
            hybrid_foreign_address='$hybrid_foreign_address',
            status='$status'
        WHERE event_id='$event_id'
    ";

    mysqli_query($con, $update);

    /* ------------------------------
        3. HANDLE COVER IMAGES
    --------------------------------*/
    if (!empty($fileData['cover_image']['name'][0])) {

        foreach ($fileData['cover_image']['name'] as $key => $name) {

            $tmp = $fileData['cover_image']['tmp_name'][$key];
            $file = uniqid() . "_" . basename($name);
            move_uploaded_file($tmp, "../uploads/" . $file);

            mysqli_query($con,
                "INSERT INTO {$siteprefix}events_images (event_id, image_path)
                 VALUES ('$event_id', '$file')"
            );
        }
    }

   /* ------------------------------
    4. SMART UPDATE: EVENT DATES
--------------------------------*/

// Fetch old IDs
$oldDates = [];
$res = mysqli_query($con, "SELECT s FROM {$siteprefix}event_dates WHERE event_id='$event_id'");
while ($r = mysqli_fetch_assoc($res)) {
    $oldDates[] = $r['s'];
}

$newDateIds = $postData['date_id'] ?? [];

foreach ($oldDates as $oldId) {
    if (!in_array($oldId, $newDateIds)) {
        mysqli_query($con, "DELETE FROM {$siteprefix}event_dates WHERE s ='$oldId'");
    }
}

// Process new + existing
foreach ($newDateIds as $i => $id) {

    $date  = mysqli_real_escape_string($con, $postData['event_dates'][$i]);
    $start = mysqli_real_escape_string($con, $postData['event_start_times'][$i]);
    $end   = mysqli_real_escape_string($con, $postData['event_end_times'][$i]);

    if ($id != "") {
        mysqli_query($con,
            "UPDATE {$siteprefix}event_dates
             SET event_date='$date', start_time='$start', end_time='$end'
             WHERE s='$id'"
        );

    } else {
        mysqli_query($con,
            "INSERT INTO {$siteprefix}event_dates(event_id, event_date, start_time, end_time)
             VALUES('$event_id','$date','$start','$end')"
        );
    }
}


/* ------------------------------
    5. SMART UPDATE: TICKETS
--------------------------------*/
$oldTickets = [];
$res = mysqli_query($con, "SELECT id FROM {$siteprefix}event_tickets WHERE event_id='$event_id'");
while ($r = mysqli_fetch_assoc($res)) {
    $oldTickets[] = $r['id'];
}

// Must be FULL ARRAY — not $postData['ticket_id'][$i]
$newTicketIds = $postData['ticket_id'] ?? [];

// Delete removed tickets
foreach ($oldTickets as $t) {
    if (!in_array($t, $newTicketIds)) {
        mysqli_query($con, "DELETE FROM {$siteprefix}event_tickets WHERE id='$t'");
    }
}

if ($pricing_type === "paid" && !empty($postData['ticket_id']) && is_array($postData['ticket_id'])) {

    foreach ($postData['ticket_id'] as $i => $id) {

        $name     = mysqli_real_escape_string($con, $postData["ticket_name"][$i] ?? '');
        $benefits = mysqli_real_escape_string($con, $postData["ticket_benefits"][$i] ?? '');
        $price    = mysqli_real_escape_string($con, $postData["ticket_price"][$i] ?? '');
        $seats    = mysqli_real_escape_string($con, $postData["ticket_seats"][$i] ?? '');

        // Skip empty tickets
        if ($name === "" && $price === "" && $seats === "") {
            continue;
        }

        if (!empty($id)) {

            mysqli_query($con,
                "UPDATE {$siteprefix}event_tickets SET
                    ticket_name='$name',
                    benefits='$benefits',
                    price='$price',
                    seats='$seats'
                WHERE id='$id'"
            );

        } else {

            mysqli_query($con,
                "INSERT INTO {$siteprefix}event_tickets(event_id, ticket_name, benefits, price, seats)
                 VALUES ('$event_id', '$name', '$benefits', '$price', '$seats')"
            );
        }
    }
}



/* ------------------------------
    6. SMART UPDATE: VIDEO MODULES
--------------------------------*/
// 1. Load old videos
$oldVideos = [];
$res = mysqli_query($con, "SELECT id, file_path FROM {$siteprefix}event_video_modules WHERE event_id='$event_id'");
while ($r = mysqli_fetch_assoc($res)) {
    $oldVideos[$r['id']] = $r['file_path'];
}

$newVideoIds = $postData['video_id'] ?? [];


// 2. Delete removed videos
foreach ($oldVideos as $vid => $fp) {
    if (!in_array($vid, $newVideoIds)) {
        mysqli_query($con, "DELETE FROM {$siteprefix}event_video_modules WHERE id='$vid'");
    }
}


// 3. Update / Insert
if ($delivery_format == "video") {

    foreach ($postData['video_id'] as $i => $id) {

        $title    = mysqli_real_escape_string($con, $postData['video_module_title'][$i]);
        $desc     = mysqli_real_escape_string($con, $postData['video_module_desc'][$i]);
        $duration = mysqli_real_escape_string($con, $postData['video_duration'][$i]);

        // FIXED — correct field name (remove $ sign)
        $total_videos = mysqli_real_escape_string($con, $postData['total_videos'][$i]);

        // Handles multiple qualities ("1080p,720p,480p")
        $quality = isset($postData['video_quality'][$i]) 
            ? implode(",", $postData['video_quality'][$i]) 
            : "";

        // Subtitles (checkbox or array or single value)
        $subs = "";
        if (isset($postData['video_subtitles'][$i])) {
            $subs = is_array($postData['video_subtitles'][$i])
                ? implode(",", $postData['video_subtitles'][$i])
                : $postData['video_subtitles'][$i];
        }

        $link = mysqli_real_escape_string($con, $postData["video_link"][$i] ?? "");

        // Keep old file path unless new file uploaded
        $file_path = $oldVideos[$id] ?? "";

        // NEW UPLOAD?
        if (!empty($fileData['video_file']['name'][$i])) {
            $tmp  = $fileData['video_file']['tmp_name'][$i];
            $file = uniqid() . "_" . basename($fileData['video_file']['name'][$i]);

            move_uploaded_file($tmp, "../secure/" . $file);

            $file_path = $file;
        }


        // UPDATE EXISTING
        if ($id != "") {

            mysqli_query($con,
                "UPDATE {$siteprefix}event_video_modules SET
                    title='$title',
                    module_number='$total_videos',
                    description='$desc',
                    duration='$duration',
                    file_path='$file_path',
                    video_link='$link',
                    video_quality='$quality',
                    subtitles='$subs'
                WHERE id='$id'"
            );

        } 
        
        // INSERT NEW
        else {

            mysqli_query($con,
                "INSERT INTO {$siteprefix}event_video_modules
                (event_id, title, module_number, description, duration, file_path, video_link, video_quality, subtitles)
                VALUES 
                ('$event_id','$title','$total_videos','$desc','$duration','$file_path','$link','$quality','$subs')"
            );
        }
    }
}


/* ------------------------------
    7. SMART UPDATE: TEXT MODULES
--------------------------------*/

// OLD TEXTS
$oldTexts = [];
$res = mysqli_query($con, "SELECT id, file_path FROM {$siteprefix}event_text_modules WHERE event_id='$event_id'");
while ($r = mysqli_fetch_assoc($res)) {
    $oldTexts[$r['id']] = $r['file_path'];
}

$newTextIds = $postData['text_id'] ?? [];

// DELETE removed items
foreach ($oldTexts as $tid => $fp) {
    if (!in_array($tid, $newTextIds)) {
        mysqli_query($con, "DELETE FROM {$siteprefix}event_text_modules WHERE id='$tid'");
    }
}

if ($delivery_format == "text") {

    // 🔥 Upload all files once
    $uploadedTextFiles = handleMultipleFileUpload('text_file', "../secure/");

    foreach ($postData['text_id'] as $i => $id) {

        $title = mysqli_real_escape_string($con, $postData['text_module_title'][$i]);
        $desc  = mysqli_real_escape_string($con, $postData['text_module_desc'][$i]);
        $read  = mysqli_real_escape_string($con, $postData['text_reading_time'][$i]); 
        $module_number = mysqli_real_escape_string($con, $postData['total_lessons'][$i]); 

        // Existing file if any
        $file_path = $oldTexts[$id] ?? "";

        // New file uploaded from helper function
        if (!empty($uploadedTextFiles[$i]) && strpos($uploadedTextFiles[$i], "Failed") === false) {
            $file_path = $uploadedTextFiles[$i];
        }

        // Skip empty module
        if ($title == "" && empty($uploadedTextFiles[$i]) && $desc == "") {
            continue;
        }

        if ($id != "") {

            mysqli_query($con,
                "UPDATE {$siteprefix}event_text_modules SET
                    title='$title',
                    description='$desc',
                    reading_time='$read',
                    file_path='$file_path',
                    module_number = '$module_number'
                WHERE id='$id'"
            );

        } else {

            mysqli_query($con,
                "INSERT INTO {$siteprefix}event_text_modules(event_id, module_number, title, description, reading_time, file_path)
                 VALUES ('$event_id', '$module_number', '$title','$desc','$read','$file_path')"
            );
        }
    }
}


    return [
        'status' => 'success',
        'message' => 'Event updated successfully!'
    ];
}


//add events
function usereventsEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // 🧩 Sanitize inputs
    $title           = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $description     = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $event_type      = mysqli_real_escape_string($con, trim($postData['eventType'] ?? ''));
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $delivery_format = mysqli_real_escape_string($con, trim($postData['delivery_format'] ?? ''));
    $pricing_type    = mysqli_real_escape_string($con, trim($postData['pricing_type'] ?? ''));
    $status          = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending')); // Default to pending for admin approval
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
    $fileuploadDir = "../secure";
    if (!is_dir($fileuploadDir)) mkdir($fileuploadDir, 0755, true);

   // If delivery includes videos
if ($delivery_format === 'video') {
    foreach ($postData['video_module_title'] as $index => $titleVal) {

        $desc      = $postData['video_module_desc'][$index] ?? '';
        $duration  = $postData['video_duration'][$index] ?? '';
        $videoLink = $postData['video_link'][$index] ?? '';
        $qualities = isset($postData['video_quality'][$index]) ? implode(',', $postData['video_quality'][$index]) : '';
        $subtitles = $postData['video_subtitles'][$index] ?? '';
        $filePath  = "";

        // Upload video file (optional)
        if (!empty($fileData['video_file']['name'][$index])) {

            $tmpKey = 'single_video_upload';
            $_FILES[$tmpKey] = [
                'name'     => $fileData['video_file']['name'][$index],
                'type'     => $fileData['video_file']['type'][$index],
                'tmp_name' => $fileData['video_file']['tmp_name'][$index],
                'error'    => $fileData['video_file']['error'][$index],
                'size'     => $fileData['video_file']['size'][$index],
            ];

            $filePath = handleSingleFileUpload($tmpKey, $fileuploadDir);
        }

        $stmt = $con->prepare("
            INSERT INTO {$siteprefix}event_video_modules
            (event_id, module_number, title, description, duration, file_path, video_link, video_quality, subtitles, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $module_no = $index + 1;
        $stmt->bind_param(
            "sisssssss",
            $event_id, $module_no, $titleVal, $desc, $duration,
            $filePath, $videoLink, $qualities, $subtitles
        );
        $stmt->execute();
        $stmt->close();
    }
}


    // If delivery includes text
    // If delivery includes text
if ($delivery_format === 'text') {
    foreach ($postData['text_module_title'] as $index => $titleVal) {

        $desc        = $postData['text_module_desc'][$index] ?? '';
        $readingTime = $postData['text_reading_time'][$index] ?? '';
        $filePath    = "";

        // Upload text file (optional)
        if (!empty($fileData['text_file']['name'][$index])) {

            $tmpKey = 'single_text_upload';
            $_FILES[$tmpKey] = [
                'name'     => $fileData['text_file']['name'][$index],
                'type'     => $fileData['text_file']['type'][$index],
                'tmp_name' => $fileData['text_file']['tmp_name'][$index],
                'error'    => $fileData['text_file']['error'][$index],
                'size'     => $fileData['text_file']['size'][$index],
            ];

            $filePath = handleSingleFileUpload($tmpKey, $fileuploadDir);
        }

        $stmt = $con->prepare("
            INSERT INTO {$siteprefix}event_text_modules
            (event_id, module_number, title, description, reading_time, file_path, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        $module_no = $index + 1;
        $stmt->bind_param("sissss", $event_id, $module_no, $titleVal, $desc, $readingTime, $filePath);
        $stmt->execute();
        $stmt->close();
    }
}


$stmt = $con->prepare("
    INSERT INTO {$siteprefix}events
    (
        event_id,
        user_id,
        title,
        slug,
        description,
        categories,
        subcategories,
        event_type,
        target_audience,
        delivery_format,
        is_foreign,
        address,
        state,
        lga,
        country,
        online_link,
        hybrid_physical_address,
        hybrid_web_address,
        hybrid_state,
        hybrid_lga,
        hybrid_country,
        hybrid_foreign_address,
        pricing_type,
        status,
        created_at
    )
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?, NOW())
");


$stmt->bind_param(
    "sissssssssssssssssssssss",
    $event_id,                   // 1
    $user_id,                    // 2
    $title,                      // 3
    $slug,                       // 4
    $description,                // 5
    $category,                   // 6
    $subcategory,                // 7
    $event_type,                 // 8
    $target_audience,            // 9
    $delivery_format,            // 10
    $is_foreign,                 // 11
    $physical_address,           // 12 (address)
    $physical_state,             // 13 (state)
    $physical_lga,               // 14 (lga)
    $physical_country,           // 15 (country)
    $web_address,                // 16 (online_link)
    $hybrid_physical_address,    // 17
    $hybrid_web_address,         // 18
    $hybrid_state,               // 19
    $hybrid_lga,                 // 20
    $hybrid_country,             // 21
    $hybrid_foreign_address,     // 22
    $pricing_type,               // 23
    $status                      // 24
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


function addAdPlacementEndpoint($postData) {
    global $con, $siteprefix;

    // Sanitize inputs
    $placementName = mysqli_real_escape_string($con, trim($postData['placement_name'] ?? ''));
    $size          = mysqli_real_escape_string($con, trim($postData['size'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $pricePerDay   = isset($postData['price']) ? floatval($postData['price']) : 0;
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'Inactive'));

    // Validation
    if (empty($placementName)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Ad Placement name is required.", "red")
        ];
    }

    if (empty($size)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Ad Size is required.", "red")
        ];
    }

    if ($pricePerDay <= 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Price per day must be greater than 0.", "red")
        ];
    }

    // Generate base slug
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $placementName), '-'));

    // Make slug unique
    $slug = $baseSlug;
    $counter = 1;
    while (true) {
        $slugCheckQuery = "SELECT COUNT(*) AS count FROM {$siteprefix}ad_placements WHERE slug = '$slug'";
        $slugResult = mysqli_query($con, $slugCheckQuery);
        $slugRow = mysqli_fetch_assoc($slugResult);

        if ($slugRow['count'] == 0) {
            break; // slug is unique
        }

        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Check if placement already exists
    $checkQuery = "SELECT COUNT(*) AS count 
                   FROM {$siteprefix}ad_placements 
                   WHERE placement_name = '$placementName'";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage(
                "Ad Placement \"$placementName\" already exists.",
                "red"
            )
        ];
    } else {
        // Insert ad placement
        $insertQuery = "INSERT INTO {$siteprefix}ad_placements 
                        (placement_name, size, description, price_per_day, status, slug) 
                        VALUES ('$placementName', '$size', '$description', '$pricePerDay', '$status', '$slug')";

        if (mysqli_query($con, $insertQuery)) {
            return [
                'status' => 'success',
                'messages' => "Ad Placement \"$placementName\" created successfully!"
            ];
        } else {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "Failed to create Ad Placement: " . mysqli_error($con),
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
    // ✅ Only return active/approved blogs for public display
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
        WHERE f.status IN ('active', 'approved')
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

function getallreports($con)
{
    global $siteprefix;

    // Join reports with users table to get reporter info and fetch item details including slug
    $query = "SELECT r.*, 
                     u.first_name, 
                     u.last_name, 
                     u.email,
                     CASE 
                         WHEN r.item_type = 'blog' THEN (SELECT title FROM {$siteprefix}forums WHERE id = r.item_id)
                         WHEN r.item_type = 'question' THEN (SELECT title FROM {$siteprefix}questions WHERE id = r.item_id)
                         WHEN r.item_type = 'group' THEN (SELECT group_name FROM {$siteprefix}groups WHERE id = r.item_id)
                         ELSE 'Unknown'
                     END as item_title,
                     CASE 
                         WHEN r.item_type = 'blog' THEN (SELECT slug FROM {$siteprefix}forums WHERE id = r.item_id)
                         WHEN r.item_type = 'question' THEN (SELECT slug FROM {$siteprefix}questions WHERE id = r.item_id)
                         WHEN r.item_type = 'group' THEN (SELECT slug FROM {$siteprefix}groups WHERE id = r.item_id)
                         ELSE NULL
                     END as item_slug
              FROM {$siteprefix}reports r
              LEFT JOIN {$siteprefix}users u ON r.user_id = u.id
              ORDER BY r.created_at DESC";
    
    $result = mysqli_query($con, $query);

    if ($result) {
        $reportsData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reportsData[] = $row;
        }
        return $reportsData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getalladplacements($con)
{
    global $siteprefix;

    $query = "SELECT *
            FROM {$siteprefix}ad_placements ORDER BY id DESC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $adPlacementData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $adPlacementData[] = $row;
        }
        return $adPlacementData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getallsubscribers($con)
{
    global $siteprefix;

    $query = "SELECT *
            FROM {$siteprefix}newsletter ORDER BY subscribed_at DESC";
    $result = mysqli_query($con, $query);

    if ($result) {
        $subscriberData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subscriberData[] = $row;
        }
        return $subscriberData;
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


function getDashboardStats($con) {
    global $siteprefix;

    $today = date('Y-m-d');

    $userCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM {$siteprefix}users WHERE user_type='buyer'"))['total'] ?? 0;
    $vendorCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM {$siteprefix}users WHERE user_type='vendor'"))['total'] ?? 0;
    $therapistCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM {$siteprefix}users WHERE user_type='therapist'"))['total'] ?? 0;
    $todayRegistrations = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM {$siteprefix}users WHERE DATE(created_at)='$today'"))['total'] ?? 0;
    $totalBookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM {$siteprefix}service_bookings WHERE DATE(date)='$today'"))['total'] ?? 0;

    $profitsSum = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS total FROM {$siteprefix}profits"))['total'];
    $walletSum  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS total FROM {$siteprefix}wallet_history"))['total'];

    return [
        'user_count' => intval($userCount),
        'vendor_count' => intval($vendorCount),
        'therapist_count' => intval($therapistCount),
        'today_registrations' => intval($todayRegistrations),
        'total_bookings_today' => intval($totalBookings),
        'gross_revenue' => floatval($profitsSum + $walletSum),
        'net_revenue' => floatval($profitsSum)
    ];
}

/**
 * Get comprehensive analytics for admin dashboard
 */
function getAdvancedAnalyticsEndpoint($period = '30') {
    global $con, $siteprefix;
    
    $days = intval($period);
    $startDate = date('Y-m-d', strtotime("-{$days} days"));
    
    // User growth analytics
    $userGrowthQuery = "
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as count,
            user_type
        FROM {$siteprefix}users 
        WHERE DATE(created_at) >= '$startDate'
        GROUP BY DATE(created_at), user_type
        ORDER BY DATE(created_at) ASC
    ";
    $userGrowthResult = mysqli_query($con, $userGrowthQuery);
    $userGrowth = [];
    while ($row = mysqli_fetch_assoc($userGrowthResult)) {
        $userGrowth[] = $row;
    }
    
    // Revenue analytics
    $revenueQuery = "
        SELECT 
            DATE(date) as date,
            SUM(total_amount) as revenue,
            COUNT(*) as order_count
        FROM {$siteprefix}orders 
        WHERE status = 'paid' AND DATE(date) >= '$startDate'
        GROUP BY DATE(date)
        ORDER BY DATE(date) ASC
    ";
    $revenueResult = mysqli_query($con, $revenueQuery);
    $revenue = [];
    while ($row = mysqli_fetch_assoc($revenueResult)) {
        $revenue[] = $row;
    }
    
    // Popular content (most viewed blogs)
    $popularBlogsQuery = "
        SELECT 
            f.id, 
            f.title, 
            f.views, 
            f.slug,
            u.first_name,
            u.last_name,
            (SELECT COUNT(*) FROM {$siteprefix}likes WHERE content_id = f.id AND content_type = 'blog') as likes
        FROM {$siteprefix}forums f
        LEFT JOIN {$siteprefix}users u ON f.user_id = u.id
        WHERE f.status = 'active'
        ORDER BY f.views DESC
        LIMIT 10
    ";
    $popularBlogs = [];
    $result = mysqli_query($con, $popularBlogsQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $popularBlogs[] = $row;
    }
    
    // Popular questions
    $popularQuestionsQuery = "
        SELECT 
            f.id, 
            f.title, 
            f.views, 
            f.slug,
            u.first_name,
            u.last_name,
            (SELECT COUNT(*) FROM {$siteprefix}answers WHERE question_id = f.slug) as answer_count
        FROM {$siteprefix}forums f
        LEFT JOIN {$siteprefix}users u ON f.user_id = u.id
        WHERE f.status IN ('active', 'approved')
        ORDER BY f.views DESC
        LIMIT 10
    ";
    $popularQuestions = [];
    $result = mysqli_query($con, $popularQuestionsQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $popularQuestions[] = $row;
    }
    
    // Booking statistics
    $bookingStatsQuery = "
        SELECT 
            booking_status as status,
            COUNT(*) as count,
            SUM(amount) as total_amount
        FROM {$siteprefix}bookings
        WHERE DATE(created_at) >= '$startDate'
        GROUP BY booking_status
    ";
    $bookingStats = [];
    $result = mysqli_query($con, $bookingStatsQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $bookingStats[] = $row;
    }
    
    // Top therapists by bookings
    $topTherapistsQuery = "
        SELECT 
            u.id,
            u.first_name,
            u.last_name,
            u.photo,
            COUNT(b.id) as booking_count,
            SUM(b.amount) as total_earnings
        FROM {$siteprefix}users u
        LEFT JOIN {$siteprefix}bookings b ON u.id = b.therapist_id
        WHERE u.user_type = 'therapist' AND DATE(b.created_at) >= '$startDate'
        GROUP BY u.id
        ORDER BY booking_count DESC
        LIMIT 10
    ";
    $topTherapists = [];
    $result = mysqli_query($con, $topTherapistsQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $topTherapists[] = $row;
    }
    
    // Top vendors by sales
    $topVendorsQuery = "
        SELECT 
            u.id,
            u.first_name,
            u.last_name,
            u.photo,
            COUNT(DISTINCT o.order_id) as order_count,
            SUM(o.total_amount) as total_sales
        FROM {$siteprefix}users u
        LEFT JOIN {$siteprefix}orders o ON u.id = o.user
        WHERE u.user_type = 'vendor' AND o.status = 'paid' AND DATE(o.date) >= '$startDate'
        GROUP BY u.id
        ORDER BY total_sales DESC
        LIMIT 10
    ";
    $topVendors = [];
    $result = mysqli_query($con, $topVendorsQuery);
    while ($row = mysqli_fetch_assoc($result)) {
        $topVendors[] = $row;
    }
    
    // Engagement metrics
    $engagementQuery = "
        SELECT 
            (SELECT COUNT(*) FROM {$siteprefix}forums WHERE DATE(created_at) >= '$startDate') as new_blogs,
            (SELECT COUNT(*) FROM {$siteprefix}forums WHERE status = 'approved' AND DATE(created_at) >= '$startDate') as new_questions,
            (SELECT COUNT(*) FROM {$siteprefix}answers WHERE DATE(created_at) >= '$startDate') as new_answers,
            (SELECT COUNT(*) FROM {$siteprefix}reviews WHERE DATE(created_at) >= '$startDate') as new_reviews,
            (SELECT COUNT(*) FROM {$siteprefix}likes WHERE DATE(created_at) >= '$startDate') as new_likes
    ";
    $engagement = mysqli_fetch_assoc(mysqli_query($con, $engagementQuery));
    
    return [
        'status' => 'success',
        'period_days' => $days,
        'start_date' => $startDate,
        'user_growth' => $userGrowth,
        'revenue_trend' => $revenue,
        'popular_blogs' => $popularBlogs,
        'popular_questions' => $popularQuestions,
        'booking_stats' => $bookingStats,
        'top_therapists' => $topTherapists,
        'top_vendors' => $topVendors,
        'engagement_metrics' => $engagement
    ];
}

/**
 * Advanced search for therapists with filters
 */
function searchTherapistsAdvancedEndpoint($filters) {
    global $con, $siteprefix;
    
    $conditions = ["u.user_type = 'therapist'", "u.status = 'active'"];
    
    // Specialization filter
    if (!empty($filters['specialization'])) {
        $spec = mysqli_real_escape_string($con, $filters['specialization']);
        $conditions[] = "FIND_IN_SET('$spec', u.specializations)";
    }
    
    // Rate range filter
    if (!empty($filters['min_rate'])) {
        $minRate = floatval($filters['min_rate']);
        $conditions[] = "CAST(u.rate AS DECIMAL(10,2)) >= $minRate";
    }
    if (!empty($filters['max_rate'])) {
        $maxRate = floatval($filters['max_rate']);
        $conditions[] = "CAST(u.rate AS DECIMAL(10,2)) <= $maxRate";
    }
    
    // Location filter
    if (!empty($filters['location'])) {
        $location = mysqli_real_escape_string($con, $filters['location']);
        $conditions[] = "u.address LIKE '%$location%'";
    }
    
    // Availability filter (has no unavailable dates in next 7 days)
    if (isset($filters['available_now']) && $filters['available_now'] == '1') {
        $conditions[] = "u.id NOT IN (
            SELECT therapist_id FROM {$siteprefix}therapist_unavailable 
            WHERE unavailable_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        )";
    }
    
    // Rating filter
    if (!empty($filters['min_rating'])) {
        $minRating = floatval($filters['min_rating']);
        // This assumes avg_rating is calculated and stored
        $conditions[] = "(
            SELECT IFNULL(ROUND(AVG(CAST(rating AS DECIMAL(5,2))),2),0) 
            FROM {$siteprefix}reviews 
            WHERE therapist_id = u.id
        ) >= $minRating";
    }
    
    // Experience years filter
    if (!empty($filters['min_experience'])) {
        $minExp = intval($filters['min_experience']);
        $conditions[] = "CAST(u.experience_years AS UNSIGNED) >= $minExp";
    }
    
    $whereClause = implode(' AND ', $conditions);
    
    // Order by
    $orderBy = "u.created_at DESC";
    if (!empty($filters['sort_by'])) {
        switch ($filters['sort_by']) {
            case 'rate_low':
                $orderBy = "CAST(u.rate AS DECIMAL(10,2)) ASC";
                break;
            case 'rate_high':
                $orderBy = "CAST(u.rate AS DECIMAL(10,2)) DESC";
                break;
            case 'rating':
                $orderBy = "avg_rating DESC";
                break;
            case 'experience':
                $orderBy = "CAST(u.experience_years AS UNSIGNED) DESC";
                break;
        }
    }
    
    $query = "
        SELECT 
            u.*,
            (
                SELECT IFNULL(ROUND(AVG(CAST(rating AS DECIMAL(5,2))),2),0) 
                FROM {$siteprefix}reviews 
                WHERE therapist_id = u.id
            ) as avg_rating,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}reviews 
                WHERE therapist_id = u.id
            ) as review_count
        FROM {$siteprefix}users u
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT 50
    ";
    
    $result = mysqli_query($con, $query);
    $therapists = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $therapists[] = $row;
    }
    
    return ['status' => 'success', 'results' => $therapists, 'count' => count($therapists)];
}

/**
 * Advanced search for products with filters
 */
function searchProductsAdvancedEndpoint($filters) {
    global $con, $siteprefix;
    
    $conditions = ["p.status = 'active'"];
    
    // Price range filter
    if (!empty($filters['min_price'])) {
        $minPrice = floatval($filters['min_price']);
        $conditions[] = "CAST(p.price AS DECIMAL(10,2)) >= $minPrice";
    }
    if (!empty($filters['max_price'])) {
        $maxPrice = floatval($filters['max_price']);
        $conditions[] = "CAST(p.price AS DECIMAL(10,2)) <= $maxPrice";
    }
    
    // Category filter
    if (!empty($filters['category'])) {
        $category = mysqli_real_escape_string($con, $filters['category']);
        $conditions[] = "FIND_IN_SET('$category', p.categories)";
    }
    
    // Rating filter
    if (!empty($filters['min_rating'])) {
        $minRating = floatval($filters['min_rating']);
        $conditions[] = "(
            SELECT IFNULL(ROUND(AVG(CAST(rating AS DECIMAL(5,2))),2),0) 
            FROM {$siteprefix}reviews 
            WHERE product_id = p.id
        ) >= $minRating";
    }
    
    // Vendor filter
    if (!empty($filters['vendor_id'])) {
        $vendorId = intval($filters['vendor_id']);
        $conditions[] = "p.user_id = $vendorId";
    }
    
    // In stock filter
    if (isset($filters['in_stock']) && $filters['in_stock'] == '1') {
        $conditions[] = "(p.stock IS NULL OR CAST(p.stock AS UNSIGNED) > 0)";
    }
    
    // Search query
    if (!empty($filters['search'])) {
        $search = mysqli_real_escape_string($con, $filters['search']);
        $conditions[] = "(p.title LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    
    $whereClause = implode(' AND ', $conditions);
    
    // Order by
    $orderBy = "p.created_at DESC";
    if (!empty($filters['sort_by'])) {
        switch ($filters['sort_by']) {
            case 'price_low':
                $orderBy = "CAST(p.price AS DECIMAL(10,2)) ASC";
                break;
            case 'price_high':
                $orderBy = "CAST(p.price AS DECIMAL(10,2)) DESC";
                break;
            case 'rating':
                $orderBy = "avg_rating DESC";
                break;
            case 'popular':
                $orderBy = "p.views DESC";
                break;
        }
    }
    
    $query = "
        SELECT 
            p.*,
            u.first_name,
            u.last_name,
            (
                SELECT IFNULL(ROUND(AVG(CAST(rating AS DECIMAL(5,2))),2),0) 
                FROM {$siteprefix}reviews 
                WHERE product_id = p.id
            ) as avg_rating,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}reviews 
                WHERE product_id = p.id
            ) as review_count
        FROM {$siteprefix}products p
        LEFT JOIN {$siteprefix}users u ON p.user_id = u.id
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT 50
    ";
    
    $result = mysqli_query($con, $query);
    $products = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return ['status' => 'success', 'results' => $products, 'count' => count($products)];
}

/**
 * Advanced search for content (blogs/questions) with filters
 */
function searchContentAdvancedEndpoint($filters) {
    global $con, $siteprefix;
    
    $type = $filters['type'] ?? 'all'; // 'blog', 'question', or 'all'
    $conditions = [];
    
    // Type filter
    if ($type === 'blog') {
        $conditions[] = "f.status = 'active'";
    } elseif ($type === 'question') {
        $conditions[] = "f.status = 'approved'";
    }
    
    // Date range filter
    if (!empty($filters['from_date'])) {
        $fromDate = mysqli_real_escape_string($con, $filters['from_date']);
        $conditions[] = "DATE(f.created_at) >= '$fromDate'";
    }
    if (!empty($filters['to_date'])) {
        $toDate = mysqli_real_escape_string($con, $filters['to_date']);
        $conditions[] = "DATE(f.created_at) <= '$toDate'";
    }
    
    // Category filter
    if (!empty($filters['category'])) {
        $category = mysqli_real_escape_string($con, $filters['category']);
        $conditions[] = "FIND_IN_SET('$category', f.categories)";
    }
    
    // Author filter
    if (!empty($filters['author_id'])) {
        $authorId = intval($filters['author_id']);
        $conditions[] = "f.user_id = $authorId";
    }
    
    // Search query
    if (!empty($filters['search'])) {
        $search = mysqli_real_escape_string($con, $filters['search']);
        $conditions[] = "(f.title LIKE '%$search%' OR f.article LIKE '%$search%')";
    }
    
    // Minimum views filter
    if (!empty($filters['min_views'])) {
        $minViews = intval($filters['min_views']);
        $conditions[] = "CAST(f.views AS UNSIGNED) >= $minViews";
    }
    
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    // Order by
    $orderBy = "f.created_at DESC";
    if (!empty($filters['sort_by'])) {
        switch ($filters['sort_by']) {
            case 'views':
                $orderBy = "CAST(f.views AS UNSIGNED) DESC";
                break;
            case 'likes':
                $orderBy = "like_count DESC";
                break;
            case 'oldest':
                $orderBy = "f.created_at ASC";
                break;
            case 'title':
                $orderBy = "f.title ASC";
                break;
        }
    }
    
    $query = "
        SELECT 
            f.*,
            u.first_name,
            u.last_name,
            u.photo,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}likes 
                WHERE content_id = f.id AND content_type = 'blog'
            ) as like_count,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}answers 
                WHERE question_id = f.slug
            ) as answer_count
        FROM {$siteprefix}forums f
        LEFT JOIN {$siteprefix}users u ON f.user_id = u.id
        $whereClause
        ORDER BY $orderBy
        LIMIT 50
    ";
    
    $result = mysqli_query($con, $query);
    $content = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $content[] = $row;
    }
    
    return ['status' => 'success', 'results' => $content, 'count' => count($content)];
}

/**
 * Create promotional code
 */
function createPromoCodeEndpoint($postData) {
    global $con, $siteprefix;
    
    $code = strtoupper(mysqli_real_escape_string($con, trim($postData['code'] ?? '')));
    $vendor_id = intval($postData['vendor_id'] ?? 0);
    $discount_type = mysqli_real_escape_string($con, $postData['discount_type'] ?? 'percentage');
    $discount_value = floatval($postData['discount_value'] ?? 0);
    $min_purchase = floatval($postData['min_purchase'] ?? 0);
    $max_discount = !empty($postData['max_discount']) ? floatval($postData['max_discount']) : 'NULL';
    $usage_limit = !empty($postData['usage_limit']) ? intval($postData['usage_limit']) : 'NULL';
    $valid_from = mysqli_real_escape_string($con, $postData['valid_from'] ?? date('Y-m-d H:i:s'));
    $valid_until = mysqli_real_escape_string($con, $postData['valid_until'] ?? '');
    $description = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $applicable_to = mysqli_real_escape_string($con, $postData['applicable_to'] ?? 'all');
    
    // Validation
    if (empty($code) || !$vendor_id || !$discount_value || empty($valid_until)) {
        return ['status' => 'error', 'message' => 'Code, vendor, discount value, and expiry date are required'];
    }
    
    if (!in_array($discount_type, ['percentage', 'fixed'])) {
        return ['status' => 'error', 'message' => 'Invalid discount type'];
    }
    
    if ($discount_type === 'percentage' && ($discount_value < 1 || $discount_value > 100)) {
        return ['status' => 'error', 'message' => 'Percentage discount must be between 1 and 100'];
    }
    
    // Check if code already exists
    $checkQuery = "SELECT id FROM {$siteprefix}promo_codes WHERE code = '$code'";
    if (mysqli_num_rows(mysqli_query($con, $checkQuery)) > 0) {
        return ['status' => 'error', 'message' => 'Promo code already exists'];
    }
    
    // Insert promo code
    $insertQuery = "
        INSERT INTO {$siteprefix}promo_codes 
        (code, vendor_id, discount_type, discount_value, min_purchase, max_discount, 
         usage_limit, valid_from, valid_until, description, applicable_to, created_at)
        VALUES 
        ('$code', '$vendor_id', '$discount_type', '$discount_value', '$min_purchase', 
         $max_discount, $usage_limit, '$valid_from', '$valid_until', '$description', '$applicable_to', NOW())
    ";
    
    if (mysqli_query($con, $insertQuery)) {
        return [
            'status' => 'success',
            'message' => 'Promo code created successfully',
            'promo_id' => mysqli_insert_id($con),
            'code' => $code
        ];
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
}

/**
 * Validate and apply promo code
 */
function validatePromoCodeEndpoint($postData) {
    global $con, $siteprefix;
    
    $code = strtoupper(mysqli_real_escape_string($con, trim($postData['code'] ?? '')));
    $user_id = intval($postData['user_id'] ?? 0);
    $order_amount = floatval($postData['order_amount'] ?? 0);
    $order_type = mysqli_real_escape_string($con, $postData['order_type'] ?? 'all');
    
    if (empty($code) || !$user_id || !$order_amount) {
        return ['status' => 'error', 'message' => 'Code, user, and order amount are required'];
    }
    
    // Get promo code details
    $query = "SELECT * FROM {$siteprefix}promo_codes WHERE code = '$code' LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) == 0) {
        return ['status' => 'error', 'message' => 'Invalid promo code'];
    }
    
    $promo = mysqli_fetch_assoc($result);
    
    // Check if active
    if ($promo['status'] !== 'active') {
        return ['status' => 'error', 'message' => 'Promo code is not active'];
    }
    
    // Check validity dates
    $now = date('Y-m-d H:i:s');
    if ($now < $promo['valid_from']) {
        return ['status' => 'error', 'message' => 'Promo code is not yet valid'];
    }
    if ($now > $promo['valid_until']) {
        return ['status' => 'error', 'message' => 'Promo code has expired'];
    }
    
    // Check usage limit
    if ($promo['usage_limit'] !== null && $promo['usage_count'] >= $promo['usage_limit']) {
        return ['status' => 'error', 'message' => 'Promo code usage limit reached'];
    }
    
    // Check minimum purchase
    if ($order_amount < $promo['min_purchase']) {
        return [
            'status' => 'error',
            'message' => 'Minimum purchase of ₦' . number_format($promo['min_purchase'], 2) . ' required'
        ];
    }
    
    // Check applicable type
    if ($promo['applicable_to'] !== 'all' && $promo['applicable_to'] !== $order_type) {
        return [
            'status' => 'error',
            'message' => 'Promo code only applicable to ' . $promo['applicable_to']
        ];
    }
    
    // Calculate discount
    $discount_amount = 0;
    if ($promo['discount_type'] === 'percentage') {
        $discount_amount = ($order_amount * $promo['discount_value']) / 100;
        
        // Apply max discount cap if set
        if ($promo['max_discount'] !== null && $discount_amount > $promo['max_discount']) {
            $discount_amount = $promo['max_discount'];
        }
    } else {
        $discount_amount = $promo['discount_value'];
    }
    
    // Ensure discount doesn't exceed order amount
    if ($discount_amount > $order_amount) {
        $discount_amount = $order_amount;
    }
    
    $final_amount = $order_amount - $discount_amount;
    
    return [
        'status' => 'success',
        'message' => 'Promo code applied successfully',
        'promo_id' => $promo['id'],
        'code' => $code,
        'discount_type' => $promo['discount_type'],
        'discount_value' => $promo['discount_value'],
        'discount_amount' => round($discount_amount, 2),
        'original_amount' => round($order_amount, 2),
        'final_amount' => round($final_amount, 2),
        'savings' => round($discount_amount, 2)
    ];
}

/**
 * Record promo code usage after successful order
 */
function recordPromoUsageEndpoint($postData) {
    global $con, $siteprefix;
    
    $promo_code_id = intval($postData['promo_code_id'] ?? 0);
    $user_id = intval($postData['user_id'] ?? 0);
    $order_id = mysqli_real_escape_string($con, trim($postData['order_id'] ?? ''));
    $discount_amount = floatval($postData['discount_amount'] ?? 0);
    $order_amount = floatval($postData['order_amount'] ?? 0);
    
    if (!$promo_code_id || !$user_id || empty($order_id)) {
        return ['status' => 'error', 'message' => 'Promo ID, user, and order ID are required'];
    }
    
    // Record usage
    $insertQuery = "
        INSERT INTO {$siteprefix}promo_usage 
        (promo_code_id, user_id, order_id, discount_amount, order_amount, used_at)
        VALUES 
        ('$promo_code_id', '$user_id', '$order_id', '$discount_amount', '$order_amount', NOW())
    ";
    
    if (mysqli_query($con, $insertQuery)) {
        // Increment usage count
        $updateQuery = "UPDATE {$siteprefix}promo_codes 
                        SET usage_count = usage_count + 1 
                        WHERE id = '$promo_code_id'";
        mysqli_query($con, $updateQuery);
        
        return ['status' => 'success', 'message' => 'Promo usage recorded'];
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
}

/**
 * Get vendor's promo codes
 */
function getVendorPromoCodesEndpoint($vendor_id) {
    global $con, $siteprefix;
    
    $vendor_id = intval($vendor_id);
    if (!$vendor_id) {
        return ['status' => 'error', 'message' => 'Vendor ID is required'];
    }
    
    $query = "
        SELECT 
            pc.*,
            (SELECT COUNT(*) FROM {$siteprefix}promo_usage WHERE promo_code_id = pc.id) as total_uses,
            (SELECT COALESCE(SUM(discount_amount), 0) FROM {$siteprefix}promo_usage WHERE promo_code_id = pc.id) as total_discount_given
        FROM {$siteprefix}promo_codes pc
        WHERE pc.vendor_id = '$vendor_id'
        ORDER BY pc.created_at DESC
    ";
    
    $result = mysqli_query($con, $query);
    $promoCodes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Auto-update expired status
        if ($row['status'] === 'active' && $row['valid_until'] < date('Y-m-d H:i:s')) {
            mysqli_query($con, "UPDATE {$siteprefix}promo_codes SET status = 'expired' WHERE id = '{$row['id']}'");
            $row['status'] = 'expired';
        }
        
        $promoCodes[] = $row;
    }
    
    return ['status' => 'success', 'promo_codes' => $promoCodes, 'count' => count($promoCodes)];
}



//get questions

function getallquestions($con)
{
    global $siteprefix;

    // ✅ Join questions with users table to fetch author info
    // ✅ Only return active/approved questions for public display
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
            ) AS subcategory_names,
             (
                SELECT COUNT(*)
                FROM {$siteprefix}answers AS a
                WHERE a.question_id = q.id
            ) AS total_answers
        FROM {$siteprefix}questions AS q
        LEFT JOIN {$siteprefix}users AS u 
            ON q.user_id = u.id
        WHERE q.status IN ('active', 'approved')
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


function deleteeventtextEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["file_name"])) return "Text file name is missing.";
    $fileName = mysqli_real_escape_string($con, $postData["file_name"]);

    // Update DB to clear the file_path only
    $q = "
        UPDATE {$siteprefix}event_text_modules
        SET file_path = ''
        WHERE file_path = '$fileName'
        LIMIT 1
    ";
    $result = mysqli_query($con, $q);

    // Delete the physical file
    $filePath = "../secure/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);

    return $result ? "Text file removed successfully." : "Failed to update text file: " . mysqli_error($con);
}


function deleteeventvideoEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["file_name"])) return "Video file name is missing.";
    $fileName = mysqli_real_escape_string($con, $postData["file_name"]);

    // Update database: remove only the file path, keep the row
    $q = "
        UPDATE {$siteprefix}event_video_modules
        SET file_path = ''
        WHERE file_path = '$fileName'
        LIMIT 1
    ";

    $result = mysqli_query($con, $q);

    // Delete the physical file
    $filePath = "../secure/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);

    return $result ? "Video file removed successfully." : "Failed to update database: " . mysqli_error($con);
}


function deleteeventimageEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["file_name"])) return "Image file name is missing.";
    $fileName = mysqli_real_escape_string($con, $postData["file_name"]);

    // Delete from database
    $query = "DELETE FROM {$siteprefix}events_images WHERE image_path = '$fileName'";
    $result = mysqli_query($con, $query);

    // Delete from uploads folder
    $filePath = "../uploads/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);

    return $result ? "Image deleted successfully." : "Failed to delete image: " . mysqli_error($con);
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


// ✅ Withdrawal Request Endpoint
function withdrawalRequestEndpoint($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency;

    // Validate required fields
    if (empty($postData['user_id']) || empty($postData['amount']) || empty($postData['bank']) || 
        empty($postData['bankname']) || empty($postData['bankno'])) {
        return [
            'status' => 'error',
            'messages' => generateMessage("All fields are required.", "red")
        ];
    }

    $user_id     = intval($postData['user_id']);
    $amount      = floatval($postData['amount']);
    $bank        = mysqli_real_escape_string($con, trim($postData['bank']));
    $bank_name   = mysqli_real_escape_string($con, trim($postData['bankname']));
    $bank_number = mysqli_real_escape_string($con, trim($postData['bankno']));
    $date        = date('Y-m-d H:i:s');

    // Validate amount is positive
    if ($amount <= 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Invalid withdrawal amount.", "red")
        ];
    }

    // Get user wallet balance
    $userQuery = mysqli_query($con, "SELECT wallet, first_name, email FROM {$siteprefix}users WHERE id = '$user_id' LIMIT 1");
    $userData = mysqli_fetch_assoc($userQuery);

    if (!$userData) {
        return [
            'status' => 'error',
            'messages' => generateMessage("User not found.", "red")
        ];
    }

    $wallet_balance = floatval($userData['wallet']);
    $user_name = $userData['first_name'];
    $user_email = $userData['email'];

    // Check if user has sufficient balance
    if ($amount > $wallet_balance) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Insufficient wallet balance. Available: {$sitecurrency}" . number_format($wallet_balance, 2), "red")
        ];
    }

    // Generate unique withdrawal ID
    $withdrawal_id = uniqid('WD_');

    // Insert withdrawal request
    $insertQuery = "INSERT INTO {$siteprefix}withdrawal 
                    (s, user, amount, bank, bank_name, bank_number, status, date) 
                    VALUES 
                    ('$withdrawal_id', '$user_id', '$amount', '$bank', '$bank_name', '$bank_number', 'pending', '$date')";

    $result = mysqli_query($con, $insertQuery);

    if (!$result) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Failed to submit withdrawal request: " . mysqli_error($con), "red")
        ];
    }

    // Deduct amount from wallet
    $new_balance = $wallet_balance - $amount;
    mysqli_query($con, "UPDATE {$siteprefix}users SET wallet = '$new_balance' WHERE id = '$user_id'");

    // Record in wallet history
    $reason = "Withdrawal Request - {$withdrawal_id}";
    mysqli_query($con, "INSERT INTO {$siteprefix}wallet_history 
                        (user, amount, reason, status, date) 
                        VALUES 
                        ('$user_id', '$amount', '$reason', 'debit', '$date')");

    // Send email notification to user
    $emailSubject = "Withdrawal Request Submitted ({$sitecurrency}" . number_format($amount, 2) . ")";
    $emailMessage = "
        <p>Dear {$user_name},</p>
        <p>Your withdrawal request has been successfully submitted and is now <strong>pending approval</strong>.</p>
        <p><strong>Withdrawal Details:</strong></p>
        <ul>
            <li><strong>Amount:</strong> {$sitecurrency}" . number_format($amount, 2) . "</li>
            <li><strong>Bank:</strong> {$bank}</li>
            <li><strong>Account Number:</strong> {$bank_number}</li>
            <li><strong>Account Name:</strong> {$bank_name}</li>
            <li><strong>Request ID:</strong> {$withdrawal_id}</li>
            <li><strong>Date:</strong> " . date("l, F j, Y g:i A") . "</li>
        </ul>
        <p>Your request will be processed within 2-3 business days. You will receive a notification once the payment is completed.</p>
        <p>Thank you for using {$sitename}!</p>
    ";
    sendEmail($user_email, $sitename, $sitemail, $user_name, $emailMessage, $emailSubject);

    // Insert alert for user
    $alertMessage = "Your withdrawal request of {$sitecurrency}" . number_format($amount, 2) . " has been submitted successfully and is pending approval.";
    insertAlert($con, $user_id, $alertMessage, $date, 0);

    // Notify admin (optional - send email to admin)
    $adminEmailSubject = "New Withdrawal Request - {$sitecurrency}" . number_format($amount, 2);
    $adminEmailMessage = "
        <p>A new withdrawal request has been submitted:</p>
        <ul>
            <li><strong>User:</strong> {$user_name} (ID: {$user_id})</li>
            <li><strong>Amount:</strong> {$sitecurrency}" . number_format($amount, 2) . "</li>
            <li><strong>Bank:</strong> {$bank}</li>
            <li><strong>Account Number:</strong> {$bank_number}</li>
            <li><strong>Account Name:</strong> {$bank_name}</li>
            <li><strong>Request ID:</strong> {$withdrawal_id}</li>
        </ul>
        <p>Please review and approve this request in the admin panel.</p>
    ";
    sendEmail($sitemail, $sitename, $sitemail, "Admin", $adminEmailMessage, $adminEmailSubject);

    return [
        'status' => 'success',
        'messages' => generateMessage("Withdrawal request submitted successfully! Your request is now pending approval.", "green")
    ];
}


// ✅ Submit Feedback Endpoint
function submitFeedbackEndpoint($postData) {
    global $con, $siteprefix;

    // Validate required fields
    if (empty($postData['content_id']) || empty($postData['content_type']) || empty($postData['vote'])) {
        return [
            'status' => 'error',
            'message' => 'Missing required fields.'
        ];
    }

    $content_id = intval($postData['content_id']);
    $content_type = mysqli_real_escape_string($con, $postData['content_type']); // 'blog' or 'question'
    $vote = mysqli_real_escape_string($con, $postData['vote']); // 'yes' or 'no'
    $user_id = !empty($postData['user_id']) ? intval($postData['user_id']) : NULL;
    
    // Get user IP address
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    $user_ip = mysqli_real_escape_string($con, $user_ip);

    // Validate vote value
    if (!in_array($vote, ['yes', 'no'])) {
        return [
            'status' => 'error',
            'message' => 'Invalid vote value.'
        ];
    }

    // Check if user/IP already voted on this content
    $checkQuery = "SELECT id FROM {$siteprefix}feedback 
                   WHERE content_id = '$content_id' 
                   AND content_type = '$content_type' 
                   AND ";
    
    if ($user_id) {
        $checkQuery .= "user_id = '$user_id'";
    } else {
        $checkQuery .= "user_ip = '$user_ip'";
    }
    
    $checkQuery .= " LIMIT 1";
    $existingVote = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($existingVote) > 0) {
        // Update existing vote
        $updateQuery = "UPDATE {$siteprefix}feedback 
                       SET vote = '$vote', created_at = NOW() 
                       WHERE content_id = '$content_id' 
                       AND content_type = '$content_type' 
                       AND ";
        
        if ($user_id) {
            $updateQuery .= "user_id = '$user_id'";
        } else {
            $updateQuery .= "user_ip = '$user_ip'";
        }

        mysqli_query($con, $updateQuery);
        
        return [
            'status' => 'success',
            'message' => 'Your feedback has been updated!'
        ];
    } else {
        // Insert new vote
        $insertQuery = "INSERT INTO {$siteprefix}feedback 
                       (content_id, content_type, user_id, user_ip, vote, created_at) 
                       VALUES 
                       ('$content_id', '$content_type', " . ($user_id ? "'$user_id'" : "NULL") . ", '$user_ip', '$vote', NOW())";
        
        $result = mysqli_query($con, $insertQuery);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Thank you for your feedback!'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to submit feedback: ' . mysqli_error($con)
            ];
        }
    }
}


// ✅ Get Feedback Stats for Content
function getFeedbackStats($con, $content_id, $content_type) {
    global $siteprefix;
    
    $content_id = intval($content_id);
    $content_type = mysqli_real_escape_string($con, $content_type);
    
    $query = "SELECT 
                SUM(CASE WHEN vote = 'yes' THEN 1 ELSE 0 END) as yes_count,
                SUM(CASE WHEN vote = 'no' THEN 1 ELSE 0 END) as no_count,
                COUNT(*) as total_votes
              FROM {$siteprefix}feedback 
              WHERE content_id = '$content_id' 
              AND content_type = '$content_type'";
    
    $result = mysqli_query($con, $query);
    
    if ($result) {
        return mysqli_fetch_assoc($result);
    }
    
    return ['yes_count' => 0, 'no_count' => 0, 'total_votes' => 0];
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

    // Validate required fields
    if (empty($postData["image_id"]) || empty($postData["reason"]) || empty($postData["statusbar"])) {
        return ['status' => 'error', 'message' => "Missing payment ID, reason, or action."];
    }

    $id     = mysqli_real_escape_string($con, $postData["image_id"]);
    $reason = mysqli_real_escape_string($con, $postData["reason"]);
    $action = strtolower($postData["statusbar"]); // 'resend' or 'cancel'
    $date   = date('Y-m-d H:i:s');

    // Fetch payment record
    $paymentQuery = mysqli_query($con, "SELECT * FROM {$siteprefix}manual_payments WHERE id = '$id' LIMIT 1");
    $payment = mysqli_fetch_assoc($paymentQuery);

    if (!$payment) {
        return ['status' => 'error', 'message' => "Manual payment record not found."];
    }

    $order_id = $payment['order_id'];
    $user_id  = $payment['user_id'];
    $amount   = $payment['amount'];
    $proof    = $payment['proof'];
    $paymentDate = date("l, F j, Y g:i A", strtotime($payment['date_created']));

    // Fetch user info
    $userQuery = mysqli_query($con, "SELECT first_name, email FROM {$siteprefix}users WHERE id = '$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($userQuery);

    if (!$user) {
        return ['status' => 'error', 'message' => "User record not found."];
    }

    $user_name  = $user['first_name'];
    $user_email = $user['email'];

    // Update payment status
    $new_status = ($action === 'resend') ? 'payment resend' : 'cancelled';
    $update = mysqli_query($con, "
        UPDATE {$siteprefix}manual_payments 
        SET status = '$new_status', rejection_reason = '$reason'
        WHERE id = '$id'
    ");

    if (!$update) {
        return ['status' => 'error', 'message' => "Failed to update payment status: " . mysqli_error($con)];
    }

    // ✅ If action is cancel, restore slots and seats
    if ($action === 'cancel') {
        $detailsQuery = "SELECT listing_id, event_id, item_id AS ticket_id, quantity 
                         FROM {$siteprefix}order_items 
                         WHERE order_id = '$order_id'";
        $detailsResult = mysqli_query($con, $detailsQuery);

        while ($detail = mysqli_fetch_assoc($detailsResult)) {
            $listing_id = $detail['listing_id'];
            $quantity   = intval($detail['quantity']);
            $event_id   = $detail['event_id'];
            $ticket_id  = $detail['ticket_id'];

            // Restore listing slots
            if (!empty($listing_id) && $quantity > 0) {
                mysqli_query($con, "
                    UPDATE {$siteprefix}listings
                    SET limited_slot = limited_slot + $quantity
                    WHERE id = '$listing_id'
                ");
            }

            // Restore event seats
            if (!empty($event_id) && !empty($ticket_id) && $quantity > 0) {
                for ($i = 0; $i < $quantity; $i++) {
                    restoreEventSeat($con, $siteprefix, $event_id, $ticket_id);
                }
            }
        }
    }

    // Prepare email content
    $emailSubject = "Payment Update for Order ID {$order_id}";
    if ($action === 'resend') {
        $emailMessage = "
            <p>Your manual payment made on <strong>{$paymentDate}</strong> for 
            <strong>{$sitecurrency}{$amount}</strong> has been <strong>rejected</strong>.</p>
            <p><strong>Reason:</strong> <em>{$reason}</em></p>
            <p>Please resubmit a valid proof of payment to continue your order processing.</p>
            <p>Thank you for using {$siteName}.</p>
        ";
    } else { // cancel
        $emailMessage = "
            <p>Your manual payment made on <strong>{$paymentDate}</strong> for 
            <strong>{$sitecurrency}{$amount}</strong> has been <strong>cancelled</strong>.</p>
            <p><strong>Reason:</strong> <em>{$reason}</em></p>
            <p>The reserved slots and tickets have been released back to the system.</p>
            <p>Thank you for using {$siteName}.</p>
        ";
    }

    sendEmail($user_email, $siteName, $siteMail, $user_name, $emailMessage, $emailSubject);

    // Insert alert
    $alertMessage = ($action === 'resend') 
        ? "Your manual payment for Order ID {$order_id} was rejected. Reason: {$reason}. Please resubmit."
        : "Your manual payment for Order ID {$order_id} was cancelled. Reason: {$reason}. Slots have been restored.";
    insertAlert($con, $user_id, $alertMessage, $date, 0);

    return [
        'status'  => 'success',
        'message' => "Payment for Order ID {$order_id} has been processed as '{$new_status}' and user notified."
    ];
}


function getRegisteredEvents($con, $user_id) {
    global $siteprefix;

    $user_id = intval($user_id);

    $query = "
        SELECT 
            oi.order_id,
            oi.event_id,
            oi.quantity,
            o.date,
            e.title AS event_title,
            e.slug AS event_slug,
            e.delivery_format,
            
            -- ALL EVENT DATES
            (
                SELECT GROUP_CONCAT(
                    CONCAT(d.event_date, '|', d.start_time, '|', d.end_time)
                    ORDER BY d.event_date ASC SEPARATOR ','
                )
                FROM {$siteprefix}event_dates AS d
                WHERE d.event_id = e.event_id
            ) AS all_event_dates_times

        FROM {$siteprefix}order_items oi
        INNER JOIN {$siteprefix}orders o ON oi.order_id = o.order_id
        INNER JOIN {$siteprefix}events e ON oi.event_id = e.event_id
        WHERE 
            o.user = '$user_id'
            AND o.status = 'paid'
            AND oi.type = 'event'
            AND oi.event_id IS NOT NULL
        ORDER BY o.date DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No orders found'];
    }
}



function approveManualPayment($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency, $documentPath, $escrowfee;

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
       ✅ Fetch Order Items + Sellers (include item_id for tickets)
    ========================================================= */
    $itemQuery = mysqli_query($con, "
        SELECT 
            i.listing_id,
            i.item_id,
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

    // Collect attachments and delivery details for buyer email
    $buyer_attachments = [];
    $buyer_event_details_html = [];

    /* ========================================================
       ✅ Process Each Item (Products, Events, Services)
    ========================================================= */
    foreach ($items as $item) {
        // sanitize local vars
        $listing_id   = $item['listing_id'];
        $ticket_item_id = $item['item_id'] ?? null; // ticket id for events
        $listing      = $item['listing_title'] ?? 'Item';
        $variation    = $item['variation'] ?? '';
        $quantity     = (int)($item['quantity'] ?? 1);
        $total_price  = (float)($item['total_price'] ?? 0.0);
        $seller_id    = $item['seller_id'] ?? 0;
        $seller_name  = $item['seller_name'] ?? 'Seller';
        $seller_email = $item['seller_email'] ?? '';
        $user_type    = $item['seller_type'] ?? '';
        $product_type = strtolower($item['product_type'] ?? 'product');

        // Calculate commission
        $admin_commission = $total_price * ($commissionRate / 100);
        $seller_amount    = $total_price - $admin_commission;

        /* ---------------------------
           EVENT HANDLING
           ---------------------------
           - We support event items here.
           - We DO NOT call reduceEventSeat() (per your note).
           - We gather delivery details & attachments via getEventDeliveryDetails()
        */
        if ($product_type === 'event') {
            // Try to fetch event & seller details (if listings join didn't provide them).
            $safeEventId = mysqli_real_escape_string($con, $listing_id);
            $eventQuery = mysqli_query($con, "
                SELECT e.title, e.slug, e.delivery_format, e.user_id, s.first_name AS seller_name, s.email AS seller_email, s.user_type AS seller_type
                FROM {$siteprefix}events e
                LEFT JOIN {$siteprefix}users s ON e.user_id = s.id
                WHERE e.event_id = '$safeEventId' LIMIT 1
            ");
            $eventData = mysqli_fetch_assoc($eventQuery);

            if ($eventData) {
                $listing = $eventData['title'] ?? $listing;
                // override seller details if available
                if (!empty($eventData['seller_name'])) $seller_name = $eventData['seller_name'];
                if (!empty($eventData['seller_email'])) $seller_email = $eventData['seller_email'];
                if (!empty($eventData['seller_type'])) $user_type = $eventData['seller_type'];
                $delivery_format = $eventData['delivery_format'] ?? '';
            } else {
                $delivery_format = $item['delivery_format'] ?? '';
            }

            // Ticket details (if item has a numeric ticket id)
            if (is_numeric($ticket_item_id)) {
                $safeTicketId = mysqli_real_escape_string($con, $ticket_item_id);
                $ticketQuery = mysqli_query($con, "SELECT ticket_name, price, benefits FROM {$siteprefix}event_tickets WHERE id = '$safeTicketId' LIMIT 1");
                $ticketData = mysqli_fetch_assoc($ticketQuery);
                if ($ticketData) {
                    $variation = $ticketData['ticket_name'] ?? $variation;
                    // if price was 0 or missing, use ticket price
                    if (empty($item['price']) || $item['price'] == 0) {
                        $total_price = (float)$ticketData['price'] * $quantity;
                    }
                    $benefits = $ticketData['benefits'] ?? '';
                } else {
                    $benefits = '';
                }
            } else {
                $benefits = $item['benefits'] ?? '';
            }

            // Insert profits (admin commission) for events or admin direct sale
            if ($user_type === 'admin') {
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}profits (amount, event_id, order_id, type, date)
                    VALUES ('$total_price', '$listing_id', '$order_id', 'Admin Direct Event Sale', '$currentdatetime')
                ");
            } else {
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}profits (amount, event_id, order_id, type, date)
                    VALUES ('$admin_commission', '$listing_id', '$order_id', 'Commission from Event Ticket', '$currentdatetime')
                ");

                // Credit seller wallet
                mysqli_query($con, "
                    UPDATE {$siteprefix}users 
                    SET wallet = wallet + $seller_amount 
                    WHERE id = '$seller_id'
                ");
                insertWallet($con, $seller_id, $seller_amount, "credit", "Earnings from Order #$order_id", $currentdatetime);
                insertAlert($con, $seller_id, "You received {$currency}{$seller_amount} for Order #{$order_id}", $currentdatetime, 0);
            }

            // Prepare event delivery details and collect attachments using existing function
            // Note: getEventDeliveryDetails should accept ($con, $siteprefix, $event_id, $delivery_format, &$documentPath) per your paysuccess usage.
            // We will call it and merge its results into our buyer email collection.
           // placeholder if function expects it by reference
            if (function_exists('getEventDeliveryDetails')) {
                $evResult = getEventDeliveryDetails($con, $siteprefix, $listing_id, $delivery_format ?? '', $documentPath);
                if (!empty($evResult['details'])) {
                    $buyer_event_details_html[] = "<strong>" . htmlspecialchars($listing) . " Delivery Details:</strong><ul>" . $evResult['details'] . "</ul>";
                }
                if (!empty($evResult['attachments']) && is_array($evResult['attachments'])) {
                    $buyer_attachments = array_merge($buyer_attachments, $evResult['attachments']);
                }
            }

        } else {
            /* ---------------------------
               PRODUCT / LISTING HANDLING
               --------------------------- */
            if ($user_type === 'admin') {
                // Admin direct sale
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
                    VALUES ('$total_price', '$listing_id', '$order_id', 'Admin Direct Sale', '$currentdatetime')
                ");
            } else {
                // Commission for product sale
                mysqli_query($con, "
                    INSERT INTO {$siteprefix}profits (amount, listing_id, order_id, type, date)
                    VALUES ('$admin_commission', '$listing_id', '$order_id', 'Commission from Sale', '$currentdatetime')
                ");

                // Credit seller wallet
                mysqli_query($con, "
                    UPDATE {$siteprefix}users 
                    SET wallet = wallet + $seller_amount 
                    WHERE id = '$seller_id'
                ");
                insertWallet($con, $seller_id, $seller_amount, "credit", "Earnings from Order #$order_id", $currentdatetime);
                insertAlert($con, $seller_id, "You received {$currency}{$seller_amount} for Order #{$order_id}", $currentdatetime, 0);
            }

            // Optional: decrease listing limited_slot if you want (was commented in your code)
            // mysqli_query($con, "
            //     UPDATE {$siteprefix}listings 
            //     SET limited_slot = GREATEST(limited_slot - $quantity, 0)
            //     WHERE listing_id = '$listing_id'
            // ");
        }

        // Service bookings handling (same as before)
        if ($product_type === 'service') {
            mysqli_query($con, "
                UPDATE {$siteprefix}service_bookings
                SET status = 'approved', payment_status = 'paid'
                WHERE order_id = '$order_id'
            ");
        }
    } // end foreach items

    /* ========================================================
       ✅ Build & Send Buyer Confirmation Email (with attachments if any)
    ========================================================= */
    // Build table rows for items
    $emailBody = "
    <html><body>
    <h2>Order Confirmation</h2>
    <p>Thank you for your purchase! Your payment was successful.</p>
    <p><strong>Order Reference:</strong> {$order_id}</p>
    <table border='1' cellpadding='6' cellspacing='0' width='100%'>
    <thead><tr>
    <th>Name</th><th>Variation</th><th>Benefits</th><th>Seller</th><th>Quantity</th><th>Total</th>
    </tr></thead><tbody>";

    foreach ($items as $item) {
        $name = htmlspecialchars($item['listing_title'] ?? 'Item');
        $var  = htmlspecialchars($item['variation'] ?? '');
        $ben  = htmlspecialchars($item['benefits'] ?? '');
        $sell = htmlspecialchars($item['seller_name'] ?? '');
        $qty  = (int)($item['quantity'] ?? 1);
        $ttl  = $currency . htmlspecialchars($item['total_price'] ?? '0');

        $emailBody .= "<tr>
            <td>{$name}</td>
            <td>{$var}</td>
            <td>{$ben}</td>
            <td>{$sell}</td>
            <td>{$qty}</td>
            <td>{$ttl}</td>
        </tr>";
    }

    $emailBody .= "</tbody></table>";

    // Append event delivery details if any
    if (!empty($buyer_event_details_html)) {
        foreach ($buyer_event_details_html as $evHtml) {
            // evHtml already contains markup list
            $emailBody .= "<div style='margin-top:12px;'>{$evHtml}</div>";
        }
    }

    $emailBody .= "<p>Thank you for shopping with {$sitename}!</p></body></html>";

    // Send buyer email (with attachments if present)
    if (!empty($buyer_attachments)) {
        if (function_exists('sendEmailWithAttachments')) {
            sendEmailWithAttachments($buyer_email, $sitename, $sitemail, $buyer_name, $emailBody, "Order Confirmation - {$sitename}", $buyer_attachments);
        } else {
            // Fallback: send normal email if attachments function doesn't exist
            sendEmail($buyer_email, $sitename, $sitemail, $buyer_name, $emailBody, "Order Confirmation - {$sitename}");
        }
    } else {
        sendEmail($buyer_email, $sitename, $sitemail, $buyer_name, $emailBody, "Order Confirmation - {$sitename}");
    }

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
        'message' => "Manual payment approved, order marked as paid, seller(s) credited, notifications sent successfully."
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


function rejectAdvert($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // Validate advert ID and reason
    if (empty($postData["advert_id"])) {
        return [
            'status' => 'error',
            'message' => 'Advert ID is missing.'
        ];
    }

    if (empty($postData["reject_reason"])) {
        return [
            'status' => 'error',
            'message' => 'Rejection reason is required.'
        ];
    }

    $advertId = mysqli_real_escape_string($con, $postData["advert_id"]);
    $reason   = mysqli_real_escape_string($con, $postData["reject_reason"]);

    // Fetch advert details along with placement name
    $result = mysqli_query($con, "
        SELECT a.*, p.placement_name
        FROM {$siteprefix}active_adverts a
        LEFT JOIN {$siteprefix}ad_placements p ON a.advert_id = p.id
        WHERE a.id = '$advertId'
        LIMIT 1
    ");
    $advert = mysqli_fetch_assoc($result);

    if (!$advert) {
        return [
            'status' => 'error',
            'message' => 'Advert not found.'
        ];
    }

    // Fetch user info
    $userId = $advert['user_id'];
    $query = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id = '$userId' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if (!$user) {
        return [
            'status' => 'error',
            'message' => 'User not found.'
        ];
    }

    $full_name = $user['first_name'] . ' ' . $user['last_name'];
    $email     = $user['email'];
    $advertTitle = $advert['placement_name'];

    // Update advert status to 'rejected' and save reason
    mysqli_query($con, "
        UPDATE {$siteprefix}active_adverts 
        SET status = 'rejected', reject_reason = '$reason' 
        WHERE id = '$advertId'
    ");

    // Prepare rejection email
    $emailSubject = "Your Advert Has Been Rejected";
    $emailMessage = "
        <p>We regret to inform you that your advert <strong>'{$advertTitle}'</strong> has been <strong>rejected</strong>.</p>
        <p><strong>Reason for rejection:</strong> {$reason}</p>
        <p>If you wish, you can edit your advert and submit again for approval.</p>
        <p>Thank you for using <strong>{$siteName}</strong>.</p>
    ";

    // Send email
    sendEmail($email, $siteName, $siteMail, $full_name, $emailMessage, $emailSubject);

    return [
        'status' => 'success',
        'message' => 'Advert rejected and user notified successfully.'
    ];
}




function approveAdverts($postData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // Validate advert ID
    if (empty($postData["image_id"])) {
        return "Advert ID is missing.";
    }

    $id = mysqli_real_escape_string($con, $postData["image_id"]);

    // Fetch advert details along with placement name
    $result = mysqli_query($con, "
        SELECT a.*, p.placement_name
        FROM {$siteprefix}active_adverts a
        LEFT JOIN {$siteprefix}ad_placements p ON a.advert_id = p.id
        WHERE a.id = '$id'
        LIMIT 1
    ");
    $advert = mysqli_fetch_assoc($result);

    if (!$advert) {
        return "Advert not found.";
    }

    // Extract advert details
    $user_id      = $advert['user_id'];
    $advert_title = $advert['placement_name']; // from ad_placements table
    $start_date   = $advert['start_date'];
    $end_date     = $advert['end_date'];

    // Fetch user info
    $query = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id = '$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($query);
    $email = $user['email'];
    $full_name = $user['first_name'] . ' ' . $user['last_name'];

    // Update advert status
    mysqli_query($con, "UPDATE {$siteprefix}active_adverts SET status = 'active' WHERE id = '$id'");

    // Prepare advert approval email
    $emailSubject = "Your Advert Has Been Approved!";
    $emailMessage = "
        <p>Your advert <strong>'{$advert_title}'</strong> has been <strong>approved</strong> and is now active on our platform.</p>
        <p><strong>Advert Duration:</strong> {$start_date} to {$end_date}</p>
        <p>Thank you for advertising with <strong>{$siteName}</strong>!</p>
    ";

    // Send email
    sendEmail($email, $siteName, $siteMail, $full_name, $emailMessage, $emailSubject);

    return [
        'status' => 'success',
        'message' => "Great! Your advert '{$advert_title}' has been approved and is now live on the platform. The advertiser has been notified via email."
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
        INNER JOIN {$siteprefix}listings AS l ON w.listing_id = l.listing_id
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


// newsletter
function subscribeNewsletter($postData) {
    global $con, $siteprefix;

    // Validate email
    if (empty($postData['email'])) {
        return [
            'status' => 'error',
            'messages' => '<div class="alert alert-danger">Email address is required.</div>'
        ];
    }

    $email = mysqli_real_escape_string($con, $postData['email']);
    $date = date('Y-m-d H:i:s');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'messages' => '<div class="alert alert-danger">Invalid email address format.</div>'
        ];
    }

    // Check if already subscribed
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}newsletter WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        return [
            'status' => 'error',
            'messages' => '<div class="alert alert-warning">You are already subscribed.</div>'
        ];
    }

    // Insert subscriber
    $sql = "INSERT INTO {$siteprefix}newsletter (email, subscribed_at) VALUES ('$email', '$date')";
    
    if (!mysqli_query($con, $sql)) {
        return [
            'status' => 'error',
            'messages' => '<div class="alert alert-danger">Database error: ' . mysqli_error($con) . '</div>'
        ];
    }

    // Insert admin alert
    $adminMessage = "New newsletter subscriber: $email";
    $link = "newsletter_list.php";
    insertadminAlert($con, $adminMessage, $link, $date, 'Newsletter', 0);

    return [
        'status' => 'success',
        'messages' => '<div class="alert alert-success">Thank you for subscribing to our newsletter!</div>'
    ];
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

function editAdPlacementEndpoint($postData) {
    global $con, $siteprefix;

    // Sanitize inputs
    $adverId       = isset($postData['adver_id']) ? intval($postData['adver_id']) : 0;
    $placementName = mysqli_real_escape_string($con, trim($postData['placement_name'] ?? ''));
    $size          = mysqli_real_escape_string($con, trim($postData['size'] ?? ''));
    $description   = mysqli_real_escape_string($con, trim($postData['description'] ?? ''));
    $pricePerDay   = isset($postData['price']) ? floatval($postData['price']) : 0;
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'Inactive'));

    // Validate required fields
    if ($adverId <= 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Invalid Ad Placement ID.", "red")
        ];
    }

    if (empty($placementName)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Ad Placement name is required.", "red")
        ];
    }

    if (empty($size)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Ad Size is required.", "red")
        ];
    }

    if ($pricePerDay <= 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Price per day must be greater than 0.", "red")
        ];
    }

    // Fetch current placement data
    $currentQuery = "SELECT placement_name, slug FROM {$siteprefix}ad_placements WHERE id = $adverId";
    $currentResult = mysqli_query($con, $currentQuery);
    if (!$currentResult || mysqli_num_rows($currentResult) == 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Ad Placement not found.", "red")
        ];
    }

    $currentData = mysqli_fetch_assoc($currentResult);

    // Only generate new slug if name has changed
    if ($placementName !== $currentData['placement_name']) {
        // Generate base slug
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $placementName), '-'));

        // Make slug unique
        $slug = $baseSlug;
        $counter = 1;
        while (true) {
            $slugCheckQuery = "SELECT COUNT(*) AS count 
                               FROM {$siteprefix}ad_placements 
                               WHERE slug = '$slug' AND id != $adverId";
            $slugResult = mysqli_query($con, $slugCheckQuery);
            $slugRow = mysqli_fetch_assoc($slugResult);

            if ($slugRow['count'] == 0) {
                break; // slug is unique
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    } else {
        $slug = $currentData['slug']; // keep current slug
    }

    // Check if placement name already exists (excluding current record)
    $checkQuery = "SELECT COUNT(*) AS count 
                   FROM {$siteprefix}ad_placements 
                   WHERE placement_name = '$placementName' AND id != $adverId";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage(
                "Ad Placement \"$placementName\" already exists.",
                "red"
            )
        ];
    } else {
        // Update ad placement
        $updateQuery = "UPDATE {$siteprefix}ad_placements 
                        SET placement_name='$placementName',
                            size='$size',
                            description='$description',
                            price_per_day='$pricePerDay',
                            status='$status',
                            slug='$slug'
                        WHERE id = $adverId";

        if (mysqli_query($con, $updateQuery)) {
            return [
                'status' => 'success',
                'messages' => "Ad Placement \"$placementName\" updated successfully!"
            ];
        } else {
            return [
                'status' => 'error',
                'messages' => generateMessage(
                    "Failed to update Ad Placement: " . mysqli_error($con),
                    "red"
                )
            ];
        }
    }
}


function reportItem($postData) {
    global $con, $siteprefix, $siteurl, $siteMail, $siteName;

    $reporter_id = intval($postData['reporter_id'] ?? 0);
    $item_id = intval($postData['reported_item_id'] ?? 0);
    $item_type = trim($postData['reported_item_type'] ?? '');
    $reason = trim($postData['reason'] ?? '');
    $custom_reason = trim($postData['custom_reason'] ?? '');

    if (!$reporter_id || !$item_id || !$item_type || !$reason) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Missing required fields.", "red")
        ];
    }


       // Fetch user info
    $userQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM {$siteprefix}users WHERE id = '$reporter_id' LIMIT 1");
    $user = mysqli_fetch_assoc($userQuery);

    if (!$user) {
        return ['status' => 'error', 'message' => "User record not found."];
    }

    $user_name  = $user['first_name'] . ' ' . $user['last_name'];
    $user_email = $user['email'];
    // Insert record into ma_reports
    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}reports (user_id, item_id, item_type, reason, custom_reason)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisss", $reporter_id, $item_id, $item_type, $reason, $custom_reason);

    if ($stmt->execute()) {

        // ----------------------------
        // Fetch Item Title Based on Type
        // ----------------------------
        $item_title = "";

        if ($item_type === "blog") {
            $q = mysqli_query($con, "SELECT title FROM {$siteprefix}forums WHERE id = $item_id");
            if ($q && $row = mysqli_fetch_assoc($q)) {
                $item_title = $row['title'];
            }
        } elseif ($item_type === "question") {
            $q = mysqli_query($con, "SELECT title FROM {$siteprefix}questions WHERE id = $item_id");
            if ($q && $row = mysqli_fetch_assoc($q)) {
                $item_title = $row['title'];
            }
        }
            elseif ($item_type === "group") {
            $q = mysqli_query($con, "SELECT group_name FROM {$siteprefix}groups WHERE id = $item_id");
            if ($q && $row = mysqli_fetch_assoc($q)) {
                $item_title = $row['group_name'];
            }
        } else {
            $item_title = "Unknown Item";
        }

        // ----------------------------
        // Email admin
        // ----------------------------
        $emailSubject = "New {$item_type} Report Submitted";

        $emailMessage = "
            <p>A new {$item_type} has been reported.</p>
            <p><strong>Type:</strong> {$item_type}</p>
            <p><strong>Title:</strong> {$item_title}</p>
            <p><strong>Reporter Name:</strong> {$user_name}</p>
            <p><strong>Reason:</strong> {$reason}</p>"
            . ($custom_reason ? "<p><strong>Details:</strong> {$custom_reason}</p>" : "") .
            "<p><a href='{$siteurl}admin/reported_items.php' 
                style='background:#007bff;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>View Reports</a></p>
        ";

        sendEmail($siteMail, $siteName, $siteMail, $siteName, $emailMessage, $emailSubject);

        // ----------------------------
        // Admin alert
        // ----------------------------
        $alertMessage = "A {$item_type} was reported: {$item_title}";
        $link = $siteurl . "report.php";
        $date = date('Y-m-d H:i:s');
        insertadminAlert($con, $alertMessage, $link, $date, "Item Report", 0);

        $stmt->close();
        return [
            'status' => 'success',
            'messages' => generateMessage("Report submitted successfully!", "green")
        ];
    }

    $stmt->close();
    return [
        'status' => 'error',
        'messages' => generateMessage("Something went wrong, please try again.", "red")
    ];
}

function resolveReport($postData) {
    global $con, $siteprefix;

    $report_id = intval($postData['report_id'] ?? 0);

    if (!$report_id) {
        return [
            'status' => 'error',
            'message' => 'Invalid report ID'
        ];
    }

    // Update report status to 'resolved'
    $stmt = $con->prepare("UPDATE {$siteprefix}reports SET status = 'resolved' WHERE id = ?");
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        $stmt->close();
        return [
            'status' => 'success',
            'message' => 'Report marked as resolved successfully'
        ];
    }

    $stmt->close();
    return [
        'status' => 'error',
        'message' => 'Failed to resolve report'
    ];
}

function deleteReport($postData) {
    global $con, $siteprefix;

    $report_id = intval($postData['report_id'] ?? 0);

    if (!$report_id) {
        return [
            'status' => 'error',
            'message' => 'Invalid report ID'
        ];
    }

    // Delete report from database
    $stmt = $con->prepare("DELETE FROM {$siteprefix}reports WHERE id = ?");
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        $stmt->close();
        return [
            'status' => 'success',
            'message' => 'Report deleted successfully'
        ];
    }

    $stmt->close();
    return [
        'status' => 'error',
        'message' => 'Failed to delete report'
    ];
}


function reportUser($postData) {
    global $con, $siteprefix, $siteurl, $siteMail, $siteName;

    // Validate required fields
    $reporter_id = intval($postData['reporter_id'] ?? 0);
    $reported_user_id = intval($postData['reported_user_id'] ?? 0);
    $reason = trim($postData['reason'] ?? '');
    $custom_reason = trim($postData['custom_reason'] ?? '');

    if (!$reporter_id || !$reported_user_id || !$reason) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Missing required fields.", "red")
        ];
    }

    if ($reporter_id === $reported_user_id) {
        return [
            'status' => 'error',
            'messages' => generateMessage("You cannot report yourself.", "red")
        ];
    }

    // Insert report into DB
    $stmt = $con->prepare("INSERT INTO {$siteprefix}user_reports (reporter_id, reported_user_id, reason, custom_reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $reporter_id, $reported_user_id, $reason, $custom_reason);

    if ($stmt->execute()) {
        // Fetch reporter and reported user info
        $userQuery = mysqli_query($con, "SELECT id, first_name, last_name, email FROM {$siteprefix}users WHERE id IN ($reporter_id, $reported_user_id)");
        $users = [];
        while ($row = mysqli_fetch_assoc($userQuery)) {
            $users[$row['id']] = $row;
        }

        $reporter_name = htmlspecialchars($users[$reporter_id]['first_name'] . ' ' . $users[$reporter_id]['last_name']);
        $reported_name = htmlspecialchars($users[$reported_user_id]['first_name'] . ' ' . $users[$reported_user_id]['last_name']);

        // Prepare admin email
        $emailSubject = "New User Report Submitted";
        $emailMessage = "
            <p>A new user report has been submitted.</p>
            <p><strong>Reporter:</strong> {$reporter_name}</p>
            <p><strong>Reported User:</strong> {$reported_name}</p>
            <p><strong>Reason:</strong> {$reason}</p>
            " . ($custom_reason ? "<p><strong>Details:</strong> {$custom_reason}</p>" : "") . "
            <p><a href='{$siteurl}admin/user_reports.php' style='background:#007bff;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>View Reports</a></p>
        ";

        // Send email to admin
        sendEmail($siteMail, $siteName, $siteMail, $siteName, $emailMessage, $emailSubject);

        // Optional: insert admin alert
        $alertMessage = "A new report has been submitted by {$reporter_name} against {$reported_name}.";
        $link = $siteurl . "admin/user_reports.php";
        $date = date('Y-m-d H:i:s');
        insertadminAlert($con, $alertMessage, $link, $date, 'User Report', 0);

        return [
            'status' => 'success',
            'messages' => generateMessage("Report submitted successfully. Our team will review it shortly.", "green")
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => generateMessage("Failed to submit report. Please try again later.", "red")
        ];
    }

    $stmt->close();
}

function deletelistingEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Listing ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}listings WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete blog: ' . mysqli_error($con);
}

function deleteeventEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Event ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}events WHERE event_id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete event: ' . mysqli_error($con);
}

function acceptAnswerEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Answer ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);

    return mysqli_query(
        $con,
        "UPDATE {$siteprefix}answers SET is_accepted = 1 WHERE id = '$imageId'"
    )
    ? 'Answer Accepted Successfully.'
    : 'Failed to accept answer: ' . mysqli_error($con);
}


function acceptBestAnswerEndpoint($postData) {
    global $con, $siteprefix;

    if (!isset($postData["image_id"])) return "Answer ID is missing.";

    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);

    // Set is_best = 1
    $query = "UPDATE {$siteprefix}answers SET is_best = 1 WHERE id = '$imageId'";

    return mysqli_query($con, $query)
        ? 'Best Answer Marked Successfully.'
        : 'Failed to mark best answer: ' . mysqli_error($con);
}



function deleteadvertEndpoint($postData) {
    global $con,$siteprefix;
    if (!isset($postData["image_id"])) return "Advert ID is missing.";
    $imageId = mysqli_real_escape_string($con, $postData["image_id"]);
    return mysqli_query($con, "DELETE FROM  {$siteprefix}ad_placements WHERE id= '$imageId'") ? 'Deleted Successfully.' : 'Failed to delete blog: ' . mysqli_error($con);
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

// download users data
function downloadSubscribersCSVEndpoint($postData) {
    global $con, $siteprefix;

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=subscribers.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ["S/N", "Email", "Subscribed Date"]);

    $result = mysqli_query($con, "SELECT * FROM {$siteprefix}newsletter ORDER BY id DESC");
    $sn = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [$sn, $row['email'], $row['subscribed_at']]);
        $sn++;
    }

    fclose($output);
    exit; // important
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

function handleFollow($postData) {
    global $con, $siteprefix;

    $userId   = intval($postData['user_id'] ?? 0);     // follower (logged-in)
    $authorId = intval($postData['author_id'] ?? 0);   // user being followed

    // Validate
    if (!$userId || !$authorId || $userId === $authorId) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Invalid follow action.", "red")
        ];
    }

    // Check if author exists
    $userQuery = mysqli_query($con, "
        SELECT id, first_name, last_name, user_type
        FROM {$siteprefix}users
        WHERE id = $authorId LIMIT 1
    ");
    $authorData = mysqli_fetch_assoc($userQuery);

    if (!$authorData) {
        return [
            'status' => 'error',
            'messages' => generateMessage("User not found.", "red")
        ];
    }

    // FOLLOW
    if ($postData['action'] === 'follow') {

        $stmt = $con->prepare("
            INSERT IGNORE INTO {$siteprefix}user_follows (follower_id, following_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $userId, $authorId);
        $stmt->execute();
        $stmt->close();

        return [
            'status' => 'success',
            'messages' => generateMessage("You are now following this user.", "green")
        ];
    }

    // UNFOLLOW
    else {
        $stmt = $con->prepare("
            DELETE FROM {$siteprefix}user_follows
            WHERE follower_id = ? AND following_id = ?
        ");
        $stmt->bind_param("ii", $userId, $authorId);
        $stmt->execute();
        $stmt->close();

        return [
            'status' => 'success',
            'messages' => generateMessage("You unfollowed this user.", "green")
        ];
    }
}


function handleBookmark($postData) {
    global $con, $siteprefix;

    $user_id = intval($postData['user_id'] ?? 0);
    $item_id = intval($postData['item_id'] ?? 0);
    $item_type = trim($postData['item_type'] ?? '');

    if (!$user_id || !$item_id || !$item_type) {
        return ['status'=>'error','messages'=>generateMessage("Missing data.", "red")];
    }

    // Toggle bookmark
    $check = mysqli_query($con, 
        "SELECT id FROM {$siteprefix}bookmarks 
         WHERE user_id=$user_id AND item_id=$item_id AND item_type='$item_type' LIMIT 1"
    );

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($con,
            "DELETE FROM {$siteprefix}bookmarks 
             WHERE user_id=$user_id AND item_id=$item_id AND item_type='$item_type'"
        );
        return ['status'=>'removed','messages'=>"Bookmark removed"];
    }

    mysqli_query($con,
        "INSERT INTO {$siteprefix}bookmarks (user_id,item_id,item_type) 
         VALUES ($user_id,$item_id,'$item_type')"
    );

    return ['status'=>'added','messages'=>"Bookmarked successfully"];
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
    $messageStatus = 0;
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
            'redirect_url' => "{$siteurl}my-bookings.php"
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . mysqli_error($con)
        ];
    }
}

/**
 * Add unavailable date for therapist
 */
function addTherapistUnavailableDateEndpoint($postData) {
    global $con, $siteprefix;
    
    $therapist_id = intval($postData['therapist_id'] ?? 0);
    $unavailable_date = mysqli_real_escape_string($con, trim($postData['unavailable_date'] ?? ''));
    $reason = mysqli_real_escape_string($con, trim($postData['reason'] ?? ''));
    
    if (!$therapist_id || !$unavailable_date) {
        return ['status' => 'error', 'message' => 'Therapist ID and date are required'];
    }
    
    // Validate date format
    $date = date('Y-m-d', strtotime($unavailable_date));
    if ($date < date('Y-m-d')) {
        return ['status' => 'error', 'message' => 'Cannot set unavailable date in the past'];
    }
    
    // Check if date already marked unavailable
    $checkQuery = "SELECT id FROM {$siteprefix}therapist_unavailable 
                   WHERE therapist_id = '$therapist_id' AND unavailable_date = '$date'";
    $checkResult = mysqli_query($con, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return ['status' => 'error', 'message' => 'This date is already marked as unavailable'];
    }
    
    // Insert unavailable date
    $insertQuery = "INSERT INTO {$siteprefix}therapist_unavailable 
                    (therapist_id, unavailable_date, reason, created_at) 
                    VALUES ('$therapist_id', '$date', '$reason', NOW())";
    
    if (mysqli_query($con, $insertQuery)) {
        return ['status' => 'success', 'message' => 'Unavailable date added successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
}

/**
 * Remove unavailable date for therapist
 */
function removeTherapistUnavailableDateEndpoint($postData) {
    global $con, $siteprefix;
    
    $therapist_id = intval($postData['therapist_id'] ?? 0);
    $unavailable_date = mysqli_real_escape_string($con, trim($postData['unavailable_date'] ?? ''));
    
    if (!$therapist_id || !$unavailable_date) {
        return ['status' => 'error', 'message' => 'Therapist ID and date are required'];
    }
    
    $date = date('Y-m-d', strtotime($unavailable_date));
    
    $deleteQuery = "DELETE FROM {$siteprefix}therapist_unavailable 
                    WHERE therapist_id = '$therapist_id' AND unavailable_date = '$date'";
    
    if (mysqli_query($con, $deleteQuery)) {
        if (mysqli_affected_rows($con) > 0) {
            return ['status' => 'success', 'message' => 'Unavailable date removed successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Date not found or already removed'];
        }
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
}

/**
 * Get therapist unavailable dates
 */
function getTherapistUnavailableDatesEndpoint($therapist_id) {
    global $con, $siteprefix;
    
    $therapist_id = intval($therapist_id);
    if (!$therapist_id) {
        return ['status' => 'error', 'message' => 'Therapist ID is required'];
    }
    
    // Get all unavailable dates for this therapist (future dates only)
    $query = "SELECT id, unavailable_date, reason, created_at 
              FROM {$siteprefix}therapist_unavailable 
              WHERE therapist_id = '$therapist_id' AND unavailable_date >= CURDATE()
              ORDER BY unavailable_date ASC";
    
    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }
    
    $unavailableDates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $unavailableDates[] = [
            'id' => $row['id'],
            'date' => $row['unavailable_date'],
            'reason' => $row['reason'],
            'created_at' => $row['created_at']
        ];
    }
    
    return ['status' => 'success', 'dates' => $unavailableDates];
}

/**
 * Check if a specific date is available for a therapist
 */
function checkTherapistAvailabilityEndpoint($therapist_id, $date) {
    global $con, $siteprefix;
    
    $therapist_id = intval($therapist_id);
    $date = mysqli_real_escape_string($con, date('Y-m-d', strtotime($date)));
    
    $query = "SELECT id FROM {$siteprefix}therapist_unavailable 
              WHERE therapist_id = '$therapist_id' AND unavailable_date = '$date'";
    $result = mysqli_query($con, $query);
    
    $isAvailable = (mysqli_num_rows($result) == 0);
    
    return [
        'status' => 'success', 
        'available' => $isAvailable,
        'message' => $isAvailable ? 'Date is available' : 'Date is unavailable'
    ];
}

/**
 * Confirm/Accept therapist booking
 */
function confirmTherapistBookingEndpoint($postData) {
    global $con, $siteprefix, $siteurl, $sitename, $sitemail;
    
    $booking_id = intval($postData['booking_id'] ?? 0);
    $therapist_id = intval($postData['therapist_id'] ?? 0);
    
    if (!$booking_id || !$therapist_id) {
        return ['status' => 'error', 'message' => 'Booking ID and Therapist ID are required'];
    }
    
    // Fetch booking details
    $bookingQuery = "SELECT b.*, u.first_name, u.last_name, u.email 
                     FROM {$siteprefix}bookings b 
                     LEFT JOIN {$siteprefix}users u ON b.user_id = u.id 
                     WHERE b.id = '$booking_id' AND b.therapist_id = '$therapist_id' 
                     LIMIT 1";
    $result = mysqli_query($con, $bookingQuery);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return ['status' => 'error', 'message' => 'Booking not found'];
    }
    
    $booking = mysqli_fetch_assoc($result);
    
    // Update booking status to confirmed
    $updateQuery = "UPDATE {$siteprefix}bookings 
                    SET booking_status = 'confirmed' 
                    WHERE id = '$booking_id'";
    
    if (mysqli_query($con, $updateQuery)) {
        // Get therapist details
        $therapistQuery = mysqli_query($con, "SELECT first_name, last_name FROM {$siteprefix}users WHERE id='$therapist_id' LIMIT 1");
        $therapist = mysqli_fetch_assoc($therapistQuery);
        $therapist_name = trim(($therapist['first_name'] ?? '') . ' ' . ($therapist['last_name'] ?? ''));
        
        // Send confirmation email to client
        $client_name = trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? ''));
        $client_email = $booking['email'];
        $consultation_date = date('l, F j, Y \a\t g:i A', strtotime($booking['consultation_date']));
        
        $emailSubject = "Booking Confirmed - {$sitename}";
        $emailMessage = "
            <p>Hi {$client_name},</p>
            <p>Great news! Your booking with <strong>{$therapist_name}</strong> has been <strong>confirmed</strong>.</p>
            <p><strong>Consultation Details:</strong></p>
            <ul>
                <li><strong>Date & Time:</strong> {$consultation_date}</li>
                <li><strong>Therapist:</strong> {$therapist_name}</li>
                <li><strong>Booking Reference:</strong> {$booking['reference']}</li>
                <li><strong>Amount:</strong> ₦" . number_format($booking['amount'], 2) . "</li>
            </ul>
            <p>Please ensure you're available at the scheduled time. If you need to reschedule, please contact the therapist in advance.</p>
            <p>Thank you for choosing {$sitename}!</p>
        ";
        
        sendEmail($client_email, $sitename, $sitemail, $client_name, $emailMessage, $emailSubject);
        
        // Send alert to client
        $alertMsg = "Your booking with {$therapist_name} for {$consultation_date} has been confirmed.";
        insertAlert($con, $booking['user_id'], $alertMsg, date('Y-m-d H:i:s'), 0);
        
        return ['status' => 'success', 'message' => 'Booking confirmed successfully. Client has been notified.'];
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
}

/**
 * Reject therapist booking
 */
function rejectTherapistBookingEndpoint($postData) {
    global $con, $siteprefix, $siteurl, $sitename, $sitemail;
    
    $booking_id = intval($postData['booking_id'] ?? 0);
    $therapist_id = intval($postData['therapist_id'] ?? 0);
    $reason = mysqli_real_escape_string($con, trim($postData['reason'] ?? 'No reason provided'));
    
    if (!$booking_id || !$therapist_id) {
        return ['status' => 'error', 'message' => 'Booking ID and Therapist ID are required'];
    }
    
    // Fetch booking details
    $bookingQuery = "SELECT b.*, u.first_name, u.last_name, u.email 
                     FROM {$siteprefix}bookings b 
                     LEFT JOIN {$siteprefix}users u ON b.user_id = u.id 
                     WHERE b.id = '$booking_id' AND b.therapist_id = '$therapist_id' 
                     LIMIT 1";
    $result = mysqli_query($con, $bookingQuery);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return ['status' => 'error', 'message' => 'Booking not found'];
    }
    
    $booking = mysqli_fetch_assoc($result);
    
    // Update booking status to rejected
    $updateQuery = "UPDATE {$siteprefix}bookings 
                    SET booking_status = 'rejected', rejection_reason = '$reason' 
                    WHERE id = '$booking_id'";
    
    if (mysqli_query($con, $updateQuery)) {
        // Get therapist details
        $therapistQuery = mysqli_query($con, "SELECT first_name, last_name FROM {$siteprefix}users WHERE id='$therapist_id' LIMIT 1");
        $therapist = mysqli_fetch_assoc($therapistQuery);
        $therapist_name = trim(($therapist['first_name'] ?? '') . ' ' . ($therapist['last_name'] ?? ''));
        
        // Send rejection email to client
        $client_name = trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? ''));
        $client_email = $booking['email'];
        $consultation_date = date('l, F j, Y \a\t g:i A', strtotime($booking['consultation_date']));
        
        $emailSubject = "Booking Declined - {$sitename}";
        $emailMessage = "
            <p>Hi {$client_name},</p>
            <p>We regret to inform you that your booking with <strong>{$therapist_name}</strong> has been <strong>declined</strong>.</p>
            <p><strong>Booking Details:</strong></p>
            <ul>
                <li><strong>Requested Date & Time:</strong> {$consultation_date}</li>
                <li><strong>Booking Reference:</strong> {$booking['reference']}</li>
            </ul>
            <p><strong>Reason:</strong></p>
            <blockquote style='border-left:3px solid #dc3545;padding-left:10px;color:#555;'>{$reason}</blockquote>
            <p>You may contact the therapist directly or try booking a different date/time slot.</p>
            <p>Thank you for your understanding.</p>
        ";
        
        sendEmail($client_email, $sitename, $sitemail, $client_name, $emailMessage, $emailSubject);
        
        // Send alert to client
        $alertMsg = "Your booking with {$therapist_name} has been declined. Reason: {$reason}";
        insertAlert($con, $booking['user_id'], $alertMsg, date('Y-m-d H:i:s'), 0);
        
        return ['status' => 'success', 'message' => 'Booking rejected. Client has been notified.'];
    } else {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
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

function getAllEventsbyslug($con, $slug) 
{
    global $siteprefix;

    $slug = mysqli_real_escape_string($con, $slug);

    $query = "
        SELECT 
            t.*,
            u.first_name,
            u.last_name,
            u.photo,
            et.name AS event_type_name,

            -- ALL TICKETS
            (
                SELECT GROUP_CONCAT(
                    CONCAT(tt.ticket_name, '|', tt.price, '|', tt.seatremain, '|', tt.benefits, '|', tt.id)
                    ORDER BY tt.price ASC SEPARATOR ','
                )
                FROM {$siteprefix}event_tickets AS tt
                WHERE tt.event_id = t.event_id
            ) AS tickets,

            -- ALL IMAGES
            (
                SELECT GROUP_CONCAT(image_path ORDER BY id ASC SEPARATOR ',')
                FROM {$siteprefix}events_images
                WHERE event_id = t.event_id
            ) AS all_images,

            -- ALL EVENT DATES
            (
                SELECT GROUP_CONCAT(
                    CONCAT(d.event_date, '|', d.start_time, '|', d.end_time)
                    ORDER BY d.event_date ASC SEPARATOR ','
                )
                FROM {$siteprefix}event_dates AS d
                WHERE d.event_id = t.event_id
            ) AS all_event_dates_times,

            -- CATEGORIES
            (
                SELECT GROUP_CONCAT(category_name ORDER BY id ASC)
                FROM {$siteprefix}event_categories
                WHERE FIND_IN_SET(id, t.categories)
            ) AS category_names,
            -- SUBCATEGORIES
            (
                SELECT GROUP_CONCAT(category_name ORDER BY id ASC)
                FROM {$siteprefix}event_categories
                WHERE FIND_IN_SET(id, t.subcategories)
            ) AS subcategory_names

        FROM {$siteprefix}events t
        LEFT JOIN {$siteprefix}users u ON t.user_id = u.id
        LEFT JOIN {$siteprefix}event_types et ON t.event_type = et.name
        WHERE t.slug = '$slug'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        return mysqli_fetch_assoc($result);
    }

    return ['error' => mysqli_error($con)];
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

function getmultipleblogID($con, $blog_id) {
    global $siteprefix;

    // Sanitize input
    $blog_id = intval($blog_id);

    $query = "SELECT * FROM {$siteprefix}forums WHERE id = '$blog_id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $blog = mysqli_fetch_assoc($result);

    return $blog ? $blog : ['error' => 'Blog not found'];
}



function getblogID($con, $blog_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}forums WHERE id= '$blog_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getadvertsID($con, $advert_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM  {$siteprefix}ad_placements WHERE id= '$advert_id'";
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
    
    // Sanitize user_id to prevent SQL injection
    $user_id = mysqli_real_escape_string($con, $user_id);
    
    $query = "SELECT * FROM ".$siteprefix."wallet_history WHERE user='$user_id' ORDER BY date DESC";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        error_log("Database error in getwalletID: " . mysqli_error($con));
        return [];
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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

    // --- Fetch order items ---
    $itemQuery = "
        SELECT *
        FROM {$siteprefix}order_items
        WHERE order_id = '$order_id'
    ";

    $itemResult = mysqli_query($con, $itemQuery);
    $items = [];

    if ($itemResult && mysqli_num_rows($itemResult) > 0) {
        while ($row = mysqli_fetch_assoc($itemResult)) {
            $type = $row['type'];

            if ($type === 'product' && !empty($row['listing_id'])) {
                // --- Product details ---
                $listingQuery = "
                    SELECT l.title, l.slug, s.id AS seller_id, s.first_name AS seller_name, s.email AS seller_email, s.user_type AS seller_type
                    FROM {$siteprefix}listings l
                    LEFT JOIN {$siteprefix}users s ON l.user_id = s.id
                    WHERE l.listing_id='{$row['listing_id']}' LIMIT 1
                ";
                $listingRes = mysqli_query($con, $listingQuery);
                $listingData = mysqli_fetch_assoc($listingRes);
                $row['listing_title'] = $listingData['title'] ?? 'Product';
                $row['listing_slug'] = $listingData['slug'] ?? '#';
                $row['seller_id'] = $listingData['seller_id'] ?? 0;
                $row['seller_name'] = $listingData['seller_name'] ?? 'Seller';
                $row['seller_email'] = $listingData['seller_email'] ?? '';
                $row['seller_type'] = $listingData['seller_type'] ?? '';

            } elseif ($type === 'event' && !empty($row['event_id'])) {
                // --- Event details ---
                $eventId = $row['event_id'];
                $ticketId = $row['item_id'];

                // Event title & slug
                $eventQuery = "
                    SELECT e.title, e.slug,e.delivery_format, s.id AS seller_id, s.first_name AS seller_name, s.email AS seller_email, s.user_type AS seller_type
                    FROM {$siteprefix}events e
                    LEFT JOIN {$siteprefix}users s ON e.user_id = s.id
                    WHERE e.event_id='$eventId' LIMIT 1
                ";
                $eventRes = mysqli_query($con, $eventQuery);
                $eventData = mysqli_fetch_assoc($eventRes);

                // Keep a consistent field name so callers can treat listing_id
                // as the primary reference for both products and events.
                $row['listing_title'] = $eventData['title'] ?? 'Event';
                $row['listing_slug'] = $eventData['slug'] ?? '#';
                // mirror event_id into listing_id for compatibility with code paths
                // that expect listing_id (eg. pay_success.php)
                $row['listing_id'] = $eventId;
                 $row['delivery_format'] = $eventData['delivery_format'] ?? '';
                $row['seller_id'] = $eventData['seller_id'] ?? 0;
                $row['seller_name'] = $eventData['seller_name'] ?? 'Seller';
                $row['seller_email'] = $eventData['seller_email'] ?? '';
                $row['seller_type'] = $eventData['seller_type'] ?? '';

                // Event image
                $imgQuery = "SELECT image_path FROM {$siteprefix}events_images WHERE event_id='$eventId' ORDER BY id ASC LIMIT 1";
                $imgRes = mysqli_query($con, $imgQuery);
                $imgData = mysqli_fetch_assoc($imgRes);
                $row['main_image'] = $imgData['image_path'] ?? '';

                // Ticket details
                if (is_numeric($ticketId)) {
                    $ticketQuery = "SELECT ticket_name, price, benefits FROM {$siteprefix}event_tickets WHERE id='$ticketId' LIMIT 1";
                    $ticketRes = mysqli_query($con, $ticketQuery);
                    $ticketData = mysqli_fetch_assoc($ticketRes);
                    if ($ticketData) {
                        $row['variation'] = $ticketData['ticket_name'];
                        $row['price'] = $ticketData['price'];
                        $row['benefits'] = $ticketData['benefits'];
                    } else {
                        $row['variation'] = 'Ticket';
                        $row['price'] = 0;
                        $row['benefits'] = '';
                    }
                } else {
                    $row['variation'] = 'Free';
                    $row['price'] = 0;
                    $row['benefits'] = '';
                }

                $row['quantity'] = $row['quantity'] ?? 1;
            }

            $items[] = $row;
        }
    }

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
            SELECT COUNT(*) 
            FROM {$siteprefix}forums AS fa
            WHERE fa.user_id = u.id
        ) AS total_articles,

        (
            SELECT COUNT(*) 
            FROM {$siteprefix}questions AS q
            WHERE q.user_id = u.id
        ) AS total_questions,

        (
            SELECT COUNT(*) 
            FROM {$siteprefix}answers AS an
            WHERE an.user_id = u.id
        ) AS total_answers,

        (
            SELECT COUNT(*)
            FROM {$siteprefix}answers AS an2
            WHERE an2.user_id = u.id
            AND an2.is_best = 1
        ) AS best_answers,

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
            ) AS review_count ,

             (
                SELECT COUNT(*)
                FROM {$siteprefix}forums AS f
                WHERE f.group_id = g.id
            ) AS forum_count ,
 (
                SELECT COUNT(*)
                FROM {$siteprefix}questions AS q
                WHERE q.group_id = g.id
            ) AS question_count ,


        (
            SELECT COUNT(*) 
            FROM {$siteprefix}group_likes AS gl
            WHERE gl.group_id = g.id
        ) AS like_count


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


// adverts slug

function getalladvertbyslug($con, $slug)
{
    global $siteprefix;

     $query = "SELECT * FROM  {$siteprefix}ad_placements WHERE slug= '" . mysqli_real_escape_string($con, $slug) . "' LIMIT 1";

    $result = mysqli_query($con, $query);

    if ($result) {
        $advertData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $advertData[] = $row;
        }
        return $advertData;
    } else {
        return ['error' => mysqli_error($con)];
    }
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

        (
            SELECT COUNT(*) 
            FROM {$siteprefix}forums AS fa
            WHERE fa.user_id = f.user_id
        ) AS total_articles,

        (
            SELECT COUNT(*) 
            FROM {$siteprefix}questions AS q
            WHERE q.user_id = f.user_id
        ) AS total_questions,

        (
            SELECT COUNT(*) 
            FROM {$siteprefix}answers AS an
            WHERE an.user_id = f.user_id
        ) AS total_answers,

        (
            SELECT COUNT(*)
            FROM {$siteprefix}answers AS an2
            WHERE an2.user_id = f.user_id
            AND an2.is_best = 1
        ) AS best_answers,

        (
            SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
            FROM {$siteprefix}categories AS c
            WHERE FIND_IN_SET(c.id, f.categories)
        ) AS category_names,

        (
            SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
            FROM {$siteprefix}categories AS sc
            WHERE FIND_IN_SET(sc.id, f.subcategories)
        ) AS subcategory_names,

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

function validateImageSize($file, $requiredSize) {
    if (empty($file['name'])) {
        return ["status" => false, "message" => "No file uploaded."];
    }

    if (!file_exists($file['tmp_name'])) {
        return ["status" => false, "message" => "Invalid file uploaded."];
    }

    if (!str_contains($requiredSize, 'x')) {
        return ["status" => false, "message" => "Invalid required size format."];
    }

    list($reqWidth, $reqHeight) = array_map('trim', explode('x', strtolower($requiredSize)));
    $reqWidth  = (int)$reqWidth;
    $reqHeight = (int)$reqHeight;

    $imgInfo = getimagesize($file['tmp_name']);
    if (!$imgInfo) {
        return ["status" => false, "message" => "Uploaded file is not a valid image."];
    }

    $imgWidth  = $imgInfo[0];
    $imgHeight = $imgInfo[1];

    if ($imgWidth != $reqWidth || $imgHeight != $reqHeight) {
        return [
            "status" => false,
            "message" => "Invalid image size. Required: {$reqWidth}×{$reqHeight}px. Uploaded: {$imgWidth}×{$imgHeight}px."
        ];
    }

    return ["status" => true, "message" => "Image size is valid."];
}

function createAdvertOrder($postData, $fileData) {
    global $con, $siteprefix, $siteurl;

    // ===== BASIC VALIDATION =====
    if (empty($postData["advert_id"])) return ["status" => "error", "message" => "Advert ID missing"];
    if (empty($postData["reference"])) return ["status" => "error", "message" => "Missing payment reference"];
    if (empty($postData["user_id"])) return ["status" => "error", "message" => "User not logged in"];

    // Escape inputs
    $advert_id    = mysqli_real_escape_string($con, $postData["advert_id"]);
    $reference    = mysqli_real_escape_string($con, $postData["reference"]);
    $start_date   = mysqli_real_escape_string($con, $postData["start_date"]);
    $end_date     = mysqli_real_escape_string($con, $postData["end_date"]);
    $redirect_url = mysqli_real_escape_string($con, $postData["url_redirection"]);
    $amount       = mysqli_real_escape_string($con, $postData["total_amount"]);
    $user_id      = mysqli_real_escape_string($con, $postData["user_id"]);

    // ===== CHECK DUPLICATE REFERENCE =====
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}advert_orders WHERE reference='$reference' LIMIT 1");
    if (mysqli_num_rows($check) > 0) return ["status" => "error", "message" => "Duplicate reference"];

    // ===== GET REQUIRED IMAGE SIZE FROM DB =====
    $sizeQuery = mysqli_query($con, "SELECT size FROM {$siteprefix}ad_placements WHERE id='$advert_id' LIMIT 1");
    $sizeRow   = mysqli_fetch_assoc($sizeQuery);
    if (!$sizeRow) return ["status" => "error", "message" => "Advert size not found in database."];

    $expectedSize = $sizeRow['size']; // e.g., "728x90"

    // ===== VALIDATE IMAGE SIZE USING SEPARATE FUNCTION =====
    $sizeCheck = validateImageSize($fileData["bannerimage"], $expectedSize);
    if (!$sizeCheck['status']) return ["status" => "error", "message" => $sizeCheck['message']];

    // ===== UPLOAD FILE =====
    $imageName = uniqid() . "_" . basename($fileData["bannerimage"]["name"]);
    $uploadPath = "../uploads/" . $imageName;
    if (!move_uploaded_file($fileData["bannerimage"]["tmp_name"], $uploadPath)) {
        return ["status" => "error", "message" => "Failed to upload banner image."];
    }

    // ===== INSERT ORDER =====
    mysqli_query($con,
        "INSERT INTO {$siteprefix}advert_orders 
        (advert_id, user_id, reference, start_date, end_date, redirect_url, banner, amount, status, date)
        VALUES ('$advert_id', '$user_id', '$reference', '$start_date', '$end_date', '$redirect_url', '$imageName', '$amount', 'pending', NOW())"
    );

    return ["status" => "success", "message" => "Order created successfully. Proceed to payment."];
}




//get questions answer
function getallquestionbyslug($con, $slug)
{
    global $siteprefix;

    $query = "
        SELECT 
            q.*, 
            u.first_name, 
            u.photo,
            u.last_name,
            u.facebook,
            u.instagram,
            u.twitter,
            u.linkedin,
            u.bio,
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
            ) AS comment_count,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}forums AS fa
                WHERE fa.user_id = u.id
            ) AS total_articles,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}questions AS qa
                WHERE qa.user_id = u.id
            ) AS total_questions,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}answers AS an
                WHERE an.user_id = u.id
            ) AS total_answers,
            (
                SELECT COUNT(*)
                FROM {$siteprefix}answers AS an2
                WHERE an2.user_id = u.id
                AND an2.is_best = 1
            ) AS best_answers
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

/**
 * Resolve dispute with wallet refund
 */
function resolveDisputeWithRefundEndpoint($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency;
    
    $dispute_id = mysqli_real_escape_string($con, trim($postData['dispute_id'] ?? ''));
    $refund_to = mysqli_real_escape_string($con, trim($postData['refund_to'] ?? '')); // 'buyer' or 'seller'
    $refund_amount = floatval($postData['refund_amount'] ?? 0);
    $resolution_note = mysqli_real_escape_string($con, trim($postData['resolution_note'] ?? ''));
    
    if (empty($dispute_id) || empty($refund_to) || $refund_amount <= 0) {
        return ['status' => 'error', 'message' => 'Dispute ID, refund recipient, and amount are required'];
    }
    
    // Get dispute details
    $disputeQuery = "SELECT * FROM {$siteprefix}disputes WHERE ticket_number = '$dispute_id' LIMIT 1";
    $disputeResult = mysqli_query($con, $disputeQuery);
    
    if (!$disputeResult || mysqli_num_rows($disputeResult) == 0) {
        return ['status' => 'error', 'message' => 'Dispute not found'];
    }
    
    $dispute = mysqli_fetch_assoc($disputeResult);
    $buyer_id = $dispute['user_id']; // person who opened dispute
    $seller_id = $dispute['recipient_id']; // person dispute is against
    $order_reference = $dispute['order_reference'];
    
    // Determine refund recipient
    $refund_user_id = ($refund_to === 'buyer') ? $buyer_id : $seller_id;
    
    // Credit wallet
    $date = date('Y-m-d H:i:s');
    $refund_reason = "Dispute Resolution: Ticket #{$dispute_id} - {$resolution_note}";
    insertWallet($con, $refund_user_id, $refund_amount, 'credit', $refund_reason, $date);
    
    // Update dispute status to resolved
    updateDisputeStatus($con, $siteprefix, $dispute_id, 'resolved');
    
    // Update dispute with resolution details
    $updateQuery = "UPDATE {$siteprefix}disputes 
                    SET resolution_note = '$resolution_note',
                        refund_amount = '$refund_amount',
                        refund_to = '$refund_to',
                        resolved_at = NOW()
                    WHERE ticket_number = '$dispute_id'";
    mysqli_query($con, $updateQuery);
    
    // Get recipient details for email
    $userQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM {$siteprefix}users WHERE id = '$refund_user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($userQuery);
    $user_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    $user_email = $user['email'];
    
    // Send refund confirmation email
    $emailSubject = "Dispute Resolved - Refund Issued (Ticket #{$dispute_id})";
    $emailMessage = "
        <p>Hi {$user_name},</p>
        <p>We're writing to inform you that the dispute you were involved in (Ticket #{$dispute_id}) has been <strong>resolved</strong>.</p>
        <p><strong>Resolution Details:</strong></p>
        <ul>
            <li><strong>Order Reference:</strong> {$order_reference}</li>
            <li><strong>Refund Amount:</strong> {$sitecurrency}" . number_format($refund_amount, 2) . "</li>
            <li><strong>Refunded To:</strong> " . ucfirst($refund_to) . "</li>
        </ul>
        <p><strong>Resolution Note:</strong></p>
        <blockquote style='border-left:3px solid #28a745;padding-left:10px;color:#555;'>{$resolution_note}</blockquote>
        <p>The refund amount has been credited to your wallet and is now available for use.</p>
        <p>Thank you for your patience during the dispute resolution process.</p>
        <p>Best regards,<br>{$sitename} Team</p>
    ";
    
    sendEmail($user_email, $sitename, $sitemail, $user_name, $emailMessage, $emailSubject);
    
    // Send alert notification
    $alertMsg = "Dispute #{$dispute_id} resolved. Wallet credited with {$sitecurrency}" . number_format($refund_amount, 2);
    insertAlert($con, $refund_user_id, $alertMsg, $date, 0);
    
    // Notify the other party
    $other_party_id = ($refund_to === 'buyer') ? $seller_id : $buyer_id;
    if ($other_party_id) {
        $otherQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM {$siteprefix}users WHERE id = '$other_party_id' LIMIT 1");
        $otherUser = mysqli_fetch_assoc($otherQuery);
        $other_name = trim(($otherUser['first_name'] ?? '') . ' ' . ($otherUser['last_name'] ?? ''));
        $other_email = $otherUser['email'];
        
        $otherEmailSubject = "Dispute Resolved (Ticket #{$dispute_id})";
        $otherEmailMessage = "
            <p>Hi {$other_name},</p>
            <p>The dispute (Ticket #{$dispute_id}) related to order #{$order_reference} has been resolved.</p>
            <p><strong>Resolution Note:</strong></p>
            <blockquote style='border-left:3px solid #28a745;padding-left:10px;color:#555;'>{$resolution_note}</blockquote>
            <p>If you have any questions about this resolution, please contact our support team.</p>
            <p>Best regards,<br>{$sitename} Team</p>
        ";
        
        sendEmail($other_email, $sitename, $sitemail, $other_name, $otherEmailMessage, $otherEmailSubject);
        
        $otherAlertMsg = "Dispute #{$dispute_id} has been resolved.";
        insertAlert($con, $other_party_id, $otherAlertMsg, $date, 0);
    }
    
    // Admin alert
    $adminAlertMsg = "Dispute #{$dispute_id} resolved with refund of {$sitecurrency}{$refund_amount} to {$refund_to}";
    $adminLink = "disputes-chat.php?ticket_number={$dispute_id}";
    insertadminAlert($con, $adminAlertMsg, $adminLink, $date, 'Dispute Resolved', 1);
    
    return [
        'status' => 'success', 
        'message' => "Dispute resolved successfully. Wallet refund of {$sitecurrency}" . number_format($refund_amount, 2) . " credited to {$refund_to}."
    ];
}

/**
 * Update order status with notifications
 * Supports: pending, processing, completed, cancelled
 */
function updateOrderStatusEndpoint($postData) {
    global $con, $siteprefix, $sitename, $sitemail, $sitecurrency;
    
    $order_id = mysqli_real_escape_string($con, trim($postData['order_id'] ?? ''));
    $new_status = mysqli_real_escape_string($con, trim($postData['status'] ?? ''));
    $vendor_id = intval($postData['vendor_id'] ?? 0);
    $notes = mysqli_real_escape_string($con, trim($postData['notes'] ?? ''));
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        return ['status' => 'error', 'message' => 'Invalid status. Must be: pending, processing, completed, or cancelled'];
    }
    
    if (empty($order_id)) {
        return ['status' => 'error', 'message' => 'Order ID is required'];
    }
    
    // Get order details
    $orderQuery = "SELECT o.*, u.first_name, u.last_name, u.email 
                   FROM {$siteprefix}orders o 
                   LEFT JOIN {$siteprefix}users u ON o.user = u.id 
                   WHERE o.order_id = '$order_id' 
                   LIMIT 1";
    $orderResult = mysqli_query($con, $orderQuery);
    
    if (!$orderResult || mysqli_num_rows($orderResult) == 0) {
        return ['status' => 'error', 'message' => 'Order not found'];
    }
    
    $order = mysqli_fetch_assoc($orderResult);
    $buyer_id = $order['user'];
    $buyer_name = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''));
    $buyer_email = $order['email'];
    $total_amount = $order['total_amount'];
    $old_status = $order['status'];
    
    // Update order status
    $updateQuery = "UPDATE {$siteprefix}orders 
                    SET status = '$new_status', 
                        updated_at = NOW()
                    WHERE order_id = '$order_id'";
    
    if (!mysqli_query($con, $updateQuery)) {
        return ['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)];
    }
    
    // Prepare email based on status
    $date = date('Y-m-d H:i:s');
    $emailSubject = "";
    $emailMessage = "";
    $alertMsg = "";
    
    switch ($new_status) {
        case 'processing':
            $emailSubject = "Order #{$order_id} is Being Processed";
            $emailMessage = "
                <p>Hi {$buyer_name},</p>
                <p>Great news! Your order <strong>#{$order_id}</strong> is now being processed.</p>
                <p><strong>Order Details:</strong></p>
                <ul>
                    <li><strong>Order ID:</strong> {$order_id}</li>
                    <li><strong>Total Amount:</strong> {$sitecurrency}" . number_format($total_amount, 2) . "</li>
                    <li><strong>Status:</strong> Processing</li>
                </ul>
                " . (!empty($notes) ? "<p><strong>Note from vendor:</strong> {$notes}</p>" : "") . "
                <p>We'll notify you once your order is ready for delivery/pickup.</p>
                <p>Thank you for shopping with {$sitename}!</p>
            ";
            $alertMsg = "Your order #{$order_id} is now being processed.";
            break;
            
        case 'completed':
            $emailSubject = "Order #{$order_id} Completed Successfully";
            $emailMessage = "
                <p>Hi {$buyer_name},</p>
                <p>Your order <strong>#{$order_id}</strong> has been <strong>completed</strong> successfully!</p>
                <p><strong>Order Summary:</strong></p>
                <ul>
                    <li><strong>Order ID:</strong> {$order_id}</li>
                    <li><strong>Total Amount:</strong> {$sitecurrency}" . number_format($total_amount, 2) . "</li>
                    <li><strong>Status:</strong> Completed</li>
                </ul>
                " . (!empty($notes) ? "<p><strong>Note:</strong> {$notes}</p>" : "") . "
                <p>We hope you enjoyed your purchase! If you have any feedback or concerns, please don't hesitate to reach out.</p>
                <p>Thank you for choosing {$sitename}!</p>
            ";
            $alertMsg = "Your order #{$order_id} has been completed.";
            break;
            
        case 'cancelled':
            $emailSubject = "Order #{$order_id} has been Cancelled";
            $emailMessage = "
                <p>Hi {$buyer_name},</p>
                <p>We regret to inform you that your order <strong>#{$order_id}</strong> has been <strong>cancelled</strong>.</p>
                <p><strong>Order Details:</strong></p>
                <ul>
                    <li><strong>Order ID:</strong> {$order_id}</li>
                    <li><strong>Total Amount:</strong> {$sitecurrency}" . number_format($total_amount, 2) . "</li>
                </ul>
                " . (!empty($notes) ? "<p><strong>Reason:</strong></p><blockquote style='border-left:3px solid #dc3545;padding-left:10px;color:#555;'>{$notes}</blockquote>" : "") . "
                <p>If payment was made, a refund will be processed to your wallet within 24-48 hours.</p>
                <p>We apologize for any inconvenience. Please contact us if you have any questions.</p>
            ";
            $alertMsg = "Your order #{$order_id} has been cancelled." . (!empty($notes) ? " Reason: {$notes}" : "");
            break;
            
        default: // pending
            $emailSubject = "Order #{$order_id} Status Update";
            $emailMessage = "
                <p>Hi {$buyer_name},</p>
                <p>Your order <strong>#{$order_id}</strong> status has been updated to <strong>Pending</strong>.</p>
                <p>We will process your order shortly and keep you updated.</p>
                <p>Thank you for your patience!</p>
            ";
            $alertMsg = "Your order #{$order_id} status updated to pending.";
            break;
    }
    
    // Send email to buyer
    sendEmail($buyer_email, $sitename, $sitemail, $buyer_name, $emailMessage, $emailSubject);
    
    // Send alert to buyer
    insertAlert($con, $buyer_id, $alertMsg, $date, 0);
    
    // If vendor exists, send alert to vendor
    if ($vendor_id > 0) {
        $vendorAlertMsg = "Order #{$order_id} status updated to {$new_status}.";
        insertAlert($con, $vendor_id, $vendorAlertMsg, $date, 0);
    }
    
    // Admin notification
    $adminAlertMsg = "Order #{$order_id} status changed from {$old_status} to {$new_status}";
    $adminLink = "order-details.php?ref={$order_id}";
    insertadminAlert($con, $adminAlertMsg, $adminLink, $date, 'Order Status Update', 1);
    
    return [
        'status' => 'success',
        'message' => "Order status updated to '{$new_status}' successfully. Customer has been notified.",
        'old_status' => $old_status,
        'new_status' => $new_status
    ];
}




function getEarningsBreakdown($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            p.s,
            p.event_id,
            p.advert_id,
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
            t.last_name AS therapist_last_name,
            -- Advert info
            ao.advert_id AS advert_order_id,
            ap.placement_name AS advert_title,
            -- Event info
            e.title AS event_title
        FROM {$siteprefix}profits AS p
        LEFT JOIN {$siteprefix}groups AS g ON p.group_id = g.id
        LEFT JOIN {$siteprefix}listings AS l ON p.listing_id = l.listing_id
        LEFT JOIN {$siteprefix}subscriptions AS s ON p.plan_id = s.id
        LEFT JOIN {$siteprefix}group_payments AS gp ON p.group_id = gp.group_id
        LEFT JOIN {$siteprefix}subscriptions AS sub ON p.plan_id = sub.id
        LEFT JOIN {$siteprefix}order_items AS oi ON p.order_id = oi.order_id AND p.listing_id = oi.listing_id
        LEFT JOIN {$siteprefix}bookings AS b ON b.reference = p.booking_id
        LEFT JOIN {$siteprefix}users AS t ON t.id = b.therapist_id
        -- Join adverts table
        LEFT JOIN {$siteprefix}advert_orders AS ao ON p.advert_id = ao.advert_id
        LEFT JOIN {$siteprefix}ad_placements AS ap ON ao.advert_id = ap.id
        -- Join events table (assuming {$siteprefix}events)
        LEFT JOIN {$siteprefix}events AS e ON p.event_id = e.event_id
        GROUP BY p.order_id, p.booking_id, p.listing_id, oi.variation, p.advert_id, p.event_id
        ORDER BY p.date DESC
    ";

    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $profits = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $source = $source_amount = $source_name = null;

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
            $source = 'Therapist Booking';
            $source_amount = $row['booking_amount'] ?? $row['amount'];
            $source_name = trim(($row['therapist_first_name'] ?? '') . ' ' . ($row['therapist_last_name'] ?? '')) ?: 'Booking';
        } elseif (!empty($row['plan_id'])) {
            $source = 'Subscription Plan';
            $source_amount = $row['subscription_price'] ?? 0;
            $source_name = $row['plan_name'] ?? 'N/A';
        } elseif (!empty($row['advert_id'])) {
            $source = 'Advert Purchase';
            $source_amount = $row['amount'];
            $source_name = $row['advert_title'] ?? 'Advert #' . $row['advert_id'];
        } elseif (!empty($row['event_id'])) {
            $source = 'Event Booking';
            $source_amount = $row['amount'];
            $source_name = $row['event_title'] ?? 'Event #' . $row['event_id'];
        } else {
            $source = 'Other';
            $source_amount = $row['amount'];
            $source_name = 'N/A';
        }

        $profits[] = [
            'id' => $row['s'],
            'event_id' => $row['event_id'] ?? null,
            'event_name' => $row['event_title'] ?? null,
            'advert_id' => $row['advert_id'] ?? null,
            'advert_title' => $row['advert_title'] ?? null,
            'source' => $source,
            'source_name' => $source_name,
            'source_amount' => number_format($source_amount, 2),
            'earned_amount' => number_format($row['amount'], 2),
            'type' => ucfirst($row['type']),
            'date' => $row['date'],
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
function getAllOrders($con) {
    global $siteprefix;

    $query = "
        SELECT o.*, u.first_name, u.last_name
        FROM {$siteprefix}orders AS o
        LEFT JOIN {$siteprefix}users AS u ON o.user = u.id
        ORDER BY o.date DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No orders found'];
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

function getAlladverts($con) {
    global $siteprefix;

    $query = "
        SELECT a.*, p.placement_name, p.size, p.slug, u.first_name, u.last_name
        FROM ".$siteprefix."active_adverts a
        LEFT JOIN ".$siteprefix."ad_placements p ON p.id = a.advert_id
        LEFT JOIN ".$siteprefix."users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            return []; // No adverts found
        }
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getAlladversuser($con, $user_id) {
    global $siteprefix;
        $query = "
        SELECT a.*, p.placement_name, p.size ,p.slug
        FROM ".$siteprefix."active_adverts a
        LEFT JOIN ".$siteprefix."ad_placements p ON p.id = a.advert_id
        WHERE a.user_id = '$user_id'
        ORDER BY a.created_at DESC
    ";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No orders found'];
    }
}


function getAllusermanualPayment($con, $user_id) {
    global $siteprefix;
    $query = "
    SELECT *
    FROM ".$siteprefix."manual_payments a
    WHERE user_id = '$user_id'
    ORDER BY date_created DESC";

    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return ['error' => mysqli_error($con) ?: 'No orders found'];
    }
}

function getAlluserbookmarks($con, $user_id) {
    global $siteprefix;
    $query = "
    SELECT *
    FROM ".$siteprefix."bookmarks a
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC";

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
        SELECT *
        FROM {$siteprefix}order_items
        WHERE order_id = '$order_id'
    ";

    $result = mysqli_query($con, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        return ['error' => 'No items found'];
    }

    $items = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $type = $row['type'];

        /* ----------------------------------------------------
           PRODUCT ITEM
        ----------------------------------------------------- */
        if ($type === 'product' && !empty($row['listing_id'])) {

            $listingQuery = "
                SELECT l.title, l.slug, s.id AS seller_id, s.first_name AS seller_name
                FROM {$siteprefix}listings l
                LEFT JOIN {$siteprefix}users s ON l.user_id = s.id
                WHERE l.listing_id = '{$row['listing_id']}'
                LIMIT 1
            ";

            $res2 = mysqli_query($con, $listingQuery);
            $data2 = mysqli_fetch_assoc($res2);

            $items[] = [
                'seller_id'     => $data2['seller_id'] ?? 0,
                'seller_name'   => $data2['seller_name'] ?? 'Seller',
                'listing_title' => $data2['title'] ?? 'Product',
                'variation'     => $row['variation'] ?? '',
                'type'          => 'Product'
            ];
        }

        /* ----------------------------------------------------
           EVENT ITEM
        ----------------------------------------------------- */
        elseif ($type === 'event' && !empty($row['event_id'])) {

            $eventQuery = "
                SELECT e.title, e.slug, e.delivery_format,
                       u.id AS seller_id, u.first_name AS seller_name
                FROM {$siteprefix}events e
                LEFT JOIN {$siteprefix}users u ON e.user_id = u.id
                WHERE e.event_id = '{$row['event_id']}'
                LIMIT 1
            ";

            $res3  = mysqli_query($con, $eventQuery);
            $data3 = mysqli_fetch_assoc($res3);

            // Ticket name (variation)
            $variation = "Ticket";
            if (!empty($row['item_id'])) {
                $ticketQ = "
                    SELECT ticket_name
                    FROM {$siteprefix}event_tickets
                    WHERE id = '{$row['item_id']}'
                    LIMIT 1
                ";
                $ticketRes = mysqli_query($con, $ticketQ);
                $ticketRow = mysqli_fetch_assoc($ticketRes);
                $variation = $ticketRow['ticket_name'] ?? "Ticket";
            }

            $items[] = [
                'seller_id'      => $data3['seller_id'] ?? 0,
                'seller_name'    => $data3['seller_name'] ?? 'Organizer',
                'listing_title'  => $data3['title'] ?? 'Event',
                'variation'      => $variation,
                'type'           => 'Event'
            ];
        }
    }

    return $items;
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
    ) AS subcategory_names,
    (
        SELECT COUNT(*) 
        FROM {$siteprefix}forums AS fa
        WHERE fa.user_id = u.id
    ) AS total_articles,
    (
        SELECT COUNT(*) 
        FROM {$siteprefix}questions AS q
        WHERE q.user_id = u.id
    ) AS total_questions,
    (
        SELECT COUNT(*) 
        FROM {$siteprefix}answers AS an
        WHERE an.user_id = u.id
    ) AS total_answers,
    (
        SELECT COUNT(*)
        FROM {$siteprefix}answers AS an2
        WHERE an2.user_id = u.id
        AND an2.is_best = 1
    ) AS best_answers
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
    $bank_name = mysqli_real_escape_string($con, $_POST['bank_name'] ?? '');
    $bank_accname = mysqli_real_escape_string($con, $_POST['bank_accname'] ?? '');
    $bank_number = mysqli_real_escape_string($con, $_POST['bank_number'] ?? '');

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

function updateTherapistEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $userId = intval($postData['user_id']);

    // FETCH OLD DATA
    $oldQ = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id='$userId' LIMIT 1");
    $old = mysqli_fetch_assoc($oldQ);

    if (!$old) {
        return ['status' => 'error', 'messages' => 'Invalid user'];
    }

    // Save old values
    $oldStatus = $old['status'];
    $oldEmail  = $old['email'];

    $userEmail = $oldEmail;

    // SANITIZE INPUT
    $title        = mysqli_real_escape_string($con, $postData['title']);
    $bio          = mysqli_real_escape_string($con, $postData['bio']);
    $firstName    = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName   = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName     = mysqli_real_escape_string($con, $postData['last_name']);
    $dob          = mysqli_real_escape_string($con, $postData['dob']);
    $gender       = mysqli_real_escape_string($con, $postData['gender']);
    $nationality  = mysqli_real_escape_string($con, $postData['nationality']);
    $languages    = mysqli_real_escape_string($con, $postData['languages']);

    // CONTACT
    $businessName       = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName          = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone              = mysqli_real_escape_string($con, $postData['phone']);
    $website            = mysqli_real_escape_string($con, $postData['website']);
    $email              = mysqli_real_escape_string($con, $postData['email']);
    $state              = mysqli_real_escape_string($con, $postData['state']);
    $lga                = mysqli_real_escape_string($con, $postData['lga']);
    $address            = mysqli_real_escape_string($con, $postData['address']);

    // SOCIALS
    $facebook  = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter   = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin  = mysqli_real_escape_string($con, $postData['linkedin']);

    // PROFESSIONAL
    $professionalTitle = is_array($postData['professional_title']) ? implode(',', $postData['professional_title']) : '';

    $qualification = mysqli_real_escape_string($con, $postData['highest_qualification']);
    if ($qualification === 'Other' && !empty($postData['other_qualification'])) {
        $qualification = mysqli_real_escape_string($con, $postData['other_qualification']);
    }

    $institution      = mysqli_real_escape_string($con, $postData['institution']);
    $graduationYear   = mysqli_real_escape_string($con, $postData['graduation_year']);
    $certifications   = mysqli_real_escape_string($con, $postData['certifications']);
    $associations     = mysqli_real_escape_string($con, $postData['associations']);
    $experience       = mysqli_real_escape_string($con, $postData['experience']);
    $sessionFormat    = mysqli_real_escape_string($con, $postData['session_format']);

    // SCHEDULE
    $preferred_days = isset($postData['preferred_days']) ? implode(', ', $postData['preferred_days']) : '';
    $start_time = $postData['start_time'] ?? '';
    $end_time   = $postData['end_time'] ?? '';
    $consultation_info = "$preferred_days | $start_time - $end_time";

    $sessionDuration = mysqli_real_escape_string($con, $postData['session_duration']);
    $rate            = mysqli_real_escape_string($con, $postData['rate']);

    $professional_field = is_array($postData['professional_field']) ? implode(',', $postData['professional_field']) : '';
    $specializations    = is_array($postData['specializations']) ? implode(',', $postData['specializations']) : '';
    $sub_specialization = is_array($postData['sub_specialization']) ? implode(',', $postData['sub_specialization']) : '';

    // WORK WITH
    $workWith = '';
    if (!empty($postData['work_with'])) {
        if ($postData['work_with'] === 'Other' && !empty($postData['other_work'])) {
            $workWith = mysqli_real_escape_string($con, $postData['other_work']);
        } else {
            $workWith = mysqli_real_escape_string($con, $postData['work_with']);
        }
    }

    // STATUS & SUSPENSION
    $newStatus = mysqli_real_escape_string($con, $postData['status']);
    $suspendReason = mysqli_real_escape_string($con, trim($postData['suspend_reason'] ?? ''));

    if ($email !== $oldEmail) {
        $emailCheck = mysqli_query($con, "SELECT id FROM {$siteprefix}users WHERE email='$email' AND id!='$userId'");
        if (mysqli_num_rows($emailCheck) > 0) {
            return ['status' => 'error', 'messages' => 'Email already exists for another user'];
        }
        $userEmail = $email;
    }

    // FILE UPLOADS
    $targetDir = "../uploads/";

    // Initialize as null so they won't update DB unless new file uploaded
    $businessLogo = null;
    $photopictures = null;

    // Business logo
    if (!empty($filesData["business_logo"]["name"])) {
        $businessLogo = uniqid() . '_' . basename($filesData["business_logo"]["name"]);
        move_uploaded_file($filesData["business_logo"]["tmp_name"], $targetDir . $businessLogo);
    }

    // Photo
    if (!empty($filesData["photos"]["name"])) {
        $photopictures = uniqid() . '_' . basename($filesData["photos"]["name"]);
        move_uploaded_file($filesData["photos"]["tmp_name"], $targetDir . $photopictures);
    }

    // Passport, CV, License
    $photoFile   = uploadFile($filesData['passport'], $targetDir) ?: $old['passport'];
    $cvFile      = uploadFile($filesData['cv'], $targetDir) ?: $old['cv'];
    $licenseFile = uploadFile($filesData['license'], $targetDir) ?: $old['license'];

    // Portfolio
    $oldPortfolio = !empty($old['portfolio']) ? explode(",", $old['portfolio']) : [];

    if (!empty($postData['remove_files'])) {
        $removeFiles = explode(",", $postData['remove_files']);
        foreach ($removeFiles as $rf) {
            $rf = trim($rf);
            $filePath = $targetDir . $rf;
            if (file_exists($filePath)) unlink($filePath);

            $oldPortfolio = array_diff($oldPortfolio, [$rf]);
        }
    }

    if (!empty($filesData['portfolio']['name'][0])) {
        foreach ($filesData['portfolio']['name'] as $key => $fileName) {
            $newName = uniqid() . "_" . basename($fileName);
            $dest = $targetDir . $newName;

            if (move_uploaded_file($filesData['portfolio']['tmp_name'][$key], $dest)) {
                $oldPortfolio[] = $newName;
            }
        }
    }

    $portfolioCSV = implode(",", $oldPortfolio);

    // BUILD UPDATE QUERY
    $sql = "
        UPDATE {$siteprefix}users SET
            title='$title', first_name='$firstName', middle_name='$middleName', last_name='$lastName',
            dob='$dob', gender='$gender', nationality='$nationality', languages='$languages',
            business_name='$businessName', registered_business_name='$registeredBusiness', owner_name='$ownerName',
            phone='$phone', bio='$bio', website='$website', email='$email',
            state_residence='$state', lga='$lga', address='$address', facebook='$facebook', twitter='$twitter',
            instagram='$instagram', linkedin='$linkedin',
            professional_title='$professionalTitle', professional_field='$professional_field', qualification='$qualification',
            institution='$institution', graduation_year='$graduationYear', certifications='$certifications',
            associations='$associations', experience_years='$experience', specializations='$specializations',
            sub_specialization='$sub_specialization', work_with='$workWith', session_format='$sessionFormat',
            consultation_days='$consultation_info', session_duration='$sessionDuration', rate='$rate',
            cv='$cvFile', license='$licenseFile', passport='$photoFile', portfolio='$portfolioCSV',
            status='$newStatus',
            suspend_reason='$suspendReason'
    ";

    // Append FILE updates ONLY if new file uploaded
    if ($businessLogo !== null) {
        $sql .= ", business_logo='$businessLogo'";
    }

    if ($photopictures !== null) {
        $sql .= ", photo='$photopictures'";
    }

    $sql .= " WHERE id='$userId'";

    // SEND EMAIL ON STATUS CHANGE
    if ($oldStatus !== $newStatus) {

        if ($newStatus === 'active') {
            $emailSubject = "Your $siteName Account is Now Active!";
            $emailMessage = "
                <p>Your account has been activated.</p>
                <p>You can now log in:</p>
                <p><a href='{$siteurl}login.php'>Login</a></p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
        }

        if ($newStatus === 'suspended') {
            $emailSubject = "Your $siteName Account Has Been Suspended";
            $emailMessage = "
                <p>Your account has been <strong>suspended</strong>.</p>
                <p><strong>Reason:</strong> $suspendReason</p>
                <p>If this is a mistake, contact: $siteMail</p>
            ";
            sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
        }
    }

    // RUN UPDATE
    if (mysqli_query($con, $sql)) {
        return ['status' => 'success', 'messages' => 'Therapist updated successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . mysqli_error($con)];
    }
}





function updateMainTherapistEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    $userId = intval($postData['user_id']);

    // FETCH OLD DATA
    $oldQ = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id='$userId' LIMIT 1");
    $old = mysqli_fetch_assoc($oldQ);

    if (!$old) {
        return ['status' => 'error', 'messages' => 'Invalid user'];
    }

    // Save old values
    $oldStatus = $old['status'];
    $oldEmail  = $old['email'];

    $userEmail = $oldEmail;

    // SANITIZE INPUT
    $title        = mysqli_real_escape_string($con, $postData['title']);
    $bank_name = mysqli_real_escape_string($con, $postData['bank_name']);
    $bank_accname = mysqli_real_escape_string($con, $postData['bank_accname']);
    $bank_number = mysqli_real_escape_string($con, $postData['bank_number']);
    $bio          = mysqli_real_escape_string($con, $postData['bio']);
    $firstName    = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName   = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName     = mysqli_real_escape_string($con, $postData['last_name']);
    $dob          = mysqli_real_escape_string($con, $postData['dob']);
    $gender       = mysqli_real_escape_string($con, $postData['gender']);
    $nationality  = mysqli_real_escape_string($con, $postData['nationality']);
    $languages    = mysqli_real_escape_string($con, $postData['languages']);

    // CONTACT
    $businessName       = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName          = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone              = mysqli_real_escape_string($con, $postData['phone']);
    $website            = mysqli_real_escape_string($con, $postData['website']);
    $email              = mysqli_real_escape_string($con, $postData['email']);
    $state              = mysqli_real_escape_string($con, $postData['state']);
    $lga                = mysqli_real_escape_string($con, $postData['lga']);
    $address            = mysqli_real_escape_string($con, $postData['address']);

    // SOCIALS
    $facebook  = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter   = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin  = mysqli_real_escape_string($con, $postData['linkedin']);

    // PROFESSIONAL
    $professionalTitle = is_array($postData['professional_title']) ? implode(',', $postData['professional_title']) : '';

    $qualification = mysqli_real_escape_string($con, $postData['highest_qualification']);
    if ($qualification === 'Other' && !empty($postData['other_qualification'])) {
        $qualification = mysqli_real_escape_string($con, $postData['other_qualification']);
    }

    $institution      = mysqli_real_escape_string($con, $postData['institution']);
    $graduationYear   = mysqli_real_escape_string($con, $postData['graduation_year']);
    $certifications   = mysqli_real_escape_string($con, $postData['certifications']);
    $associations     = mysqli_real_escape_string($con, $postData['associations']);
    $experience       = mysqli_real_escape_string($con, $postData['experience']);
    $sessionFormat    = mysqli_real_escape_string($con, $postData['session_format']);

    // SCHEDULE
    $preferred_days = isset($postData['preferred_days']) ? implode(', ', $postData['preferred_days']) : '';
    $start_time = $postData['start_time'] ?? '';
    $end_time   = $postData['end_time'] ?? '';
    $consultation_info = "$preferred_days | $start_time - $end_time";

    $sessionDuration = mysqli_real_escape_string($con, $postData['session_duration']);
    $rate            = mysqli_real_escape_string($con, $postData['rate']);

    $professional_field = is_array($postData['professional_field']) ? implode(',', $postData['professional_field']) : '';
    $specializations    = is_array($postData['specializations']) ? implode(',', $postData['specializations']) : '';
    $sub_specialization = is_array($postData['sub_specialization']) ? implode(',', $postData['sub_specialization']) : '';

    // WORK WITH
    $workWith = '';
    if (!empty($postData['work_with'])) {
        if ($postData['work_with'] === 'Other' && !empty($postData['other_work'])) {
            $workWith = mysqli_real_escape_string($con, $postData['other_work']);
        } else {
            $workWith = mysqli_real_escape_string($con, $postData['work_with']);
        }
    }

 

    if ($email !== $oldEmail) {
        $emailCheck = mysqli_query($con, "SELECT id FROM {$siteprefix}users WHERE email='$email' AND id!='$userId'");
        if (mysqli_num_rows($emailCheck) > 0) {
            return ['status' => 'error', 'messages' => 'Email already exists for another user'];
        }
        $userEmail = $email;
    }

    // FILE UPLOADS
    $targetDir = "../uploads/";

    // Initialize as null so they won't update DB unless new file uploaded
    $businessLogo = null;
    $photopictures = null;

    // Business logo
    if (!empty($filesData["business_logo"]["name"])) {
        $businessLogo = uniqid() . '_' . basename($filesData["business_logo"]["name"]);
        move_uploaded_file($filesData["business_logo"]["tmp_name"], $targetDir . $businessLogo);
    }

    // Photo
    if (!empty($filesData["photos"]["name"])) {
        $photopictures = uniqid() . '_' . basename($filesData["photos"]["name"]);
        move_uploaded_file($filesData["photos"]["tmp_name"], $targetDir . $photopictures);
    }

    // Passport, CV, License
    $photoFile   = uploadFile($filesData['passport'], $targetDir) ?: $old['passport'];
    $cvFile      = uploadFile($filesData['cv'], $targetDir) ?: $old['cv'];
    $licenseFile = uploadFile($filesData['license'], $targetDir) ?: $old['license'];

    // Portfolio
    $oldPortfolio = !empty($old['portfolio']) ? explode(",", $old['portfolio']) : [];

    if (!empty($postData['remove_files'])) {
        $removeFiles = explode(",", $postData['remove_files']);
        foreach ($removeFiles as $rf) {
            $rf = trim($rf);
            $filePath = $targetDir . $rf;
            if (file_exists($filePath)) unlink($filePath);

            $oldPortfolio = array_diff($oldPortfolio, [$rf]);
        }
    }

    if (!empty($filesData['portfolio']['name'][0])) {
        foreach ($filesData['portfolio']['name'] as $key => $fileName) {
            $newName = uniqid() . "_" . basename($fileName);
            $dest = $targetDir . $newName;

            if (move_uploaded_file($filesData['portfolio']['tmp_name'][$key], $dest)) {
                $oldPortfolio[] = $newName;
            }
        }
    }

    $portfolioCSV = implode(",", $oldPortfolio);

    // BUILD UPDATE QUERY
    $sql = "
        UPDATE {$siteprefix}users SET
            title='$title', first_name='$firstName', middle_name='$middleName', last_name='$lastName',
            dob='$dob', gender='$gender', nationality='$nationality', languages='$languages',
            business_name='$businessName', registered_business_name='$registeredBusiness', owner_name='$ownerName',
            phone='$phone', bio='$bio', website='$website', email='$email',
            state_residence='$state', lga='$lga', address='$address', facebook='$facebook', twitter='$twitter',
            instagram='$instagram', linkedin='$linkedin',
            professional_title='$professionalTitle', professional_field='$professional_field', qualification='$qualification',
            institution='$institution', graduation_year='$graduationYear', certifications='$certifications',
            associations='$associations', experience_years='$experience', specializations='$specializations',
            sub_specialization='$sub_specialization', work_with='$workWith', session_format='$sessionFormat', bank_name = '$bank_name',
            bank_accname = '$bank_accname',
            bank_number = '$bank_number',
            consultation_days='$consultation_info', session_duration='$sessionDuration', rate='$rate',
            cv='$cvFile', license='$licenseFile', passport='$photoFile', portfolio='$portfolioCSV'
    ";

    // Append FILE updates ONLY if new file uploaded
    if ($businessLogo !== null) {
        $sql .= ", business_logo='$businessLogo'";
    }

    if ($photopictures !== null) {
        $sql .= ", photo='$photopictures'";
    }

    $sql .= " WHERE id='$userId'";

   

    // RUN UPDATE
    if (mysqli_query($con, $sql)) {
        return ['status' => 'success', 'messages' => 'Profile updated successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . mysqli_error($con)];
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
    $lga = mysqli_real_escape_string($con, $postData['lga']);
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
     $preferred_days = isset($_POST['preferred_days']) ? implode(', ', $_POST['preferred_days']) : '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $availability = "$preferred_days | $start_time - $end_time ";
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
        services='$services', experience_years='$experience', coverage='$coverage', onsite='$onsite',lga = '$lga',
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
    
    // VPay payment configuration
    $vpay_domain = mysqli_real_escape_string($con, trim($postData['vpay_domain'] ?? 'sandbox'));
    $vpay_test_public_key = mysqli_real_escape_string($con, trim($postData['vpay_test_public_key'] ?? ''));
    $vpay_live_public_key = mysqli_real_escape_string($con, trim($postData['vpay_live_public_key'] ?? ''));
    $payment_provider = mysqli_real_escape_string($con, trim($postData['payment_provider'] ?? 'vpay'));
    
    $google_map = mysqli_real_escape_string($con, trim($postData['google_map'] ?? ''));
    $site_address = mysqli_real_escape_string($con, trim($postData['address'] ?? ''));
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
        vpay_domain='$vpay_domain',
        vpay_test_public_key='$vpay_test_public_key',
        vpay_live_public_key='$vpay_live_public_key',
        payment_provider='$payment_provider',
        account_name='$account_name',
        account_number='$account_number',
        site_address='$site_address',
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


//delete portfollio
function deletePortfolioEndpoint($postData) {
    global $con, $siteprefix, $imagePath;

    if (!isset($postData["file"]) || !isset($postData["user_id"])) {
        return "Missing file or user ID.";
    }

    $file = mysqli_real_escape_string($con, $postData["file"]);
    $userId = intval($postData["user_id"]);

    // Fetch portfolio list
    $q = mysqli_query($con, "SELECT portfolio FROM {$siteprefix}users WHERE id='$userId'");
    $row = mysqli_fetch_assoc($q);

    if (!$row) return "User not found.";

    $portfolio = $row['portfolio'];
    $files = array_filter(array_map('trim', explode(",", $portfolio)));

    // remove selected file
    $newList = [];
    foreach ($files as $f) {
        if ($f !== $file) {
            $newList[] = $f;
        }
    }

    $updated = implode(",", $newList);

    // update DB
    mysqli_query($con, "UPDATE {$siteprefix}users SET portfolio='$updated' WHERE id='$userId'");

    // delete physical file
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath . $file;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }

    return "File deleted successfully.";
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


// get events
function getSingleEventEndpoint($event_id) {
    global $con, $siteprefix;

$event_id = mysqli_real_escape_string($con, $event_id);

    // MAIN EVENT
    $main = mysqli_query($con,
        "SELECT * FROM {$siteprefix}events WHERE event_id='$event_id' LIMIT 1"
    );

    if (!mysqli_num_rows($main)) {
        return ['status' => 'error', 'message' => 'Event not found'];
    }

    $event = mysqli_fetch_assoc($main);

    // IMAGES
    $images = [];
    $imgQ = mysqli_query($con,
        "SELECT image_path FROM {$siteprefix}events_images WHERE event_id='$event_id'"
    );
    while ($row = mysqli_fetch_assoc($imgQ)) {
        $images[] = $row['image_path'];
    }

    // DATES
    $dates = [];
    $dateQ = mysqli_query($con,
        "SELECT s,event_date, start_time, end_time 
         FROM {$siteprefix}event_dates WHERE event_id='$event_id'"
    );
    while ($row = mysqli_fetch_assoc($dateQ)) {
        $dates[] = $row;
    }

    // TICKETS
    $tickets = [];
    $ticketQ = mysqli_query($con,
        "SELECT id, ticket_name, benefits, price, seats 
         FROM {$siteprefix}event_tickets WHERE event_id='$event_id'"
    );
    while ($row = mysqli_fetch_assoc($ticketQ)) {
        $tickets[] = $row;
    }

    // VIDEO MODULES
    $videos = [];
    $vQ = mysqli_query($con,
        "SELECT * FROM {$siteprefix}event_video_modules 
         WHERE event_id='$event_id'"
    );
    while ($row = mysqli_fetch_assoc($vQ)) {
        $videos[] = $row;
    }

    // TEXT MODULES
    $texts = [];
    $tQ = mysqli_query($con,
        "SELECT * FROM {$siteprefix}event_text_modules 
         WHERE event_id='$event_id'"
    );
    while ($row = mysqli_fetch_assoc($tQ)) {
        $texts[] = $row;
    }

    return [
        'status' => 'success',
        'event' => $event,
        'images' => $images,
        'dates' => $dates,
        'tickets' => $tickets,
        'videos' => $videos,
        'texts' => $texts
    ];
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

function getAllEventsfiltering($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            t.*,
            u.first_name,
            u.last_name,
            u.photo,

            -- ALL PRICES
            (
                SELECT GROUP_CONCAT(price ORDER BY price ASC)
                FROM {$siteprefix}event_tickets AS tt
                WHERE tt.event_id = t.event_id
            ) AS prices,

            et.name AS event_type_name,

            -- Featured Image
            (
                SELECT image_path
                FROM {$siteprefix}events_images AS ti
                WHERE ti.event_id = t.event_id
                ORDER BY ti.id ASC
                LIMIT 1
            ) AS featured_image,

            -- Next upcoming date + start time + end time
            (
                SELECT CONCAT(d.event_date, '|', d.start_time, '|', d.end_time)
                FROM {$siteprefix}event_dates AS d
                WHERE d.event_id = t.event_id
                  AND d.event_date >= CURDATE()
                ORDER BY d.event_date ASC
                LIMIT 1
            ) AS next_event_date_time,

            -- Category names
            (
                SELECT GROUP_CONCAT(category_name ORDER BY id ASC)
                FROM {$siteprefix}event_categories
                WHERE FIND_IN_SET(id, t.categories)
            ) AS category_names,

            -- Subcategory names
            (
                SELECT GROUP_CONCAT(subcategory_name ORDER BY id ASC)
                FROM {$siteprefix}event_subcategories
                WHERE FIND_IN_SET(id, t.subcategories)
            ) AS subcategory_names

        FROM {$siteprefix}events t
        LEFT JOIN {$siteprefix}users u ON t.user_id = u.id
        LEFT JOIN {$siteprefix}event_types et ON t.event_type = et.name
        WHERE t.status = 'approved'
        GROUP BY t.event_id
        ORDER BY t.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    return ['error' => mysqli_error($con)];
}


function getAllEvents($con)
{
    global $siteprefix;

    $query = "
        SELECT 
            t.*,
            u.first_name,
            u.last_name,
            u.photo,

            -- ALL PRICES
            (
                SELECT GROUP_CONCAT(price ORDER BY price ASC)
                FROM {$siteprefix}event_tickets AS tt
                WHERE tt.event_id = t.event_id
            ) AS prices,

            et.name AS event_type_name,

            -- Featured Image
            (
                SELECT image_path
                FROM {$siteprefix}events_images AS ti
                WHERE ti.event_id = t.event_id
                ORDER BY ti.id ASC
                LIMIT 1
            ) AS featured_image,

            -- Next upcoming date + start time + end time (paired)
            (
                SELECT CONCAT(d.event_date, '|', d.start_time, '|', d.end_time)
                FROM {$siteprefix}event_dates AS d
                WHERE d.event_id = t.event_id
                  AND d.event_date >= CURDATE()
                ORDER BY d.event_date ASC
                LIMIT 1
            ) AS next_event_date_time,

            -- All category names
            (
                SELECT GROUP_CONCAT(category_name ORDER BY id ASC)
                FROM {$siteprefix}event_categories
                WHERE FIND_IN_SET(id, t.categories)
            ) AS category_names

        FROM {$siteprefix}events t
        LEFT JOIN {$siteprefix}users u ON t.user_id = u.id
        LEFT JOIN {$siteprefix}event_types et ON t.event_type = et.name
        WHERE t.status = 'approved'
        GROUP BY t.event_id
        ORDER BY t.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    return ['error' => mysqli_error($con)];
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


function saveArticleFeedback($post)
{
    global $con, $siteprefix;

    if (!isset($post['content_id'], $post['vote'], $post['content_type'])) {
        return "Invalid request.";
    }

    $content_id = intval($post['content_id']);
    $content_type = mysqli_real_escape_string($con, $post['content_type']);
    $vote = ($post['vote'] == "yes") ? "yes" : "no";

    $user_id = $post['user_id'] ?? null; 
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Prevent duplicate vote
    $check = mysqli_query($con, "
        SELECT id FROM {$siteprefix}feedback
        WHERE content_id = '$content_id'
        AND content_type = '$content_type'
        AND (user_id = '$user_id' OR user_ip = '$user_ip')
    ");

    if (mysqli_num_rows($check) > 0) {
        return "Thank you. You already voted.";
    }

    // Insert new vote
    $insert = mysqli_query($con, "
        INSERT INTO {$siteprefix}feedback 
        (content_id, content_type, user_id, user_ip, vote)
        VALUES ('$content_id', '$content_type', " . ($user_id ? "'$user_id'" : "NULL") . ", '$user_ip', '$vote')
    ");

    return $insert ? "Thank you for your feedback!" : "Error saving feedback.";
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
    
    // Track unique views using session/IP to prevent duplicate counting
    session_start();
    $viewKey = 'blog_view_' . $slug;
    $userIP = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Check if this user/IP already viewed this blog in current session or last 24 hours
    if (!isset($_SESSION[$viewKey])) {
        // Check if IP viewed this blog in last 24 hours
        $checkQuery = "SELECT id FROM {$siteprefix}blog_views 
                       WHERE slug = '$slug' AND ip_address = '$userIP' 
                       AND viewed_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $checkResult = mysqli_query($con, $checkQuery);
        
        if (mysqli_num_rows($checkResult) == 0) {
            // Increment view count
            $updateQuery = "UPDATE {$siteprefix}forums SET views = views + 1 WHERE slug = '$slug'";
            mysqli_query($con, $updateQuery);
            
            // Log this view
            $insertQuery = "INSERT INTO {$siteprefix}blog_views (slug, ip_address, viewed_at) 
                           VALUES ('$slug', '$userIP', NOW())";
            mysqli_query($con, $insertQuery);
            
            // Mark in session to prevent multiple increments in same session
            $_SESSION[$viewKey] = true;
        }
    }
    
    return ['status' => 'success'];
}

function adduserViews($con, $slug) {  
    global $siteprefix;

    // Increment view count
    $updateQuery = "UPDATE {$siteprefix}users SET views = views + 1 WHERE slug = '$slug'";

    if (mysqli_query($con, $updateQuery)) {
        return ['status' => 'success'];
    } else {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }
}


function addViewsquestion($con, $slug) {  
    global $siteprefix;
    
    // Track unique views using session/IP to prevent duplicate counting
    session_start();
    $viewKey = 'question_view_' . $slug;
    $userIP = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Check if this user/IP already viewed this question in current session or last 24 hours
    if (!isset($_SESSION[$viewKey])) {
        // Check if IP viewed this question in last 24 hours
        $checkQuery = "SELECT id FROM {$siteprefix}question_views 
                       WHERE slug = '$slug' AND ip_address = '$userIP' 
                       AND viewed_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $checkResult = mysqli_query($con, $checkQuery);
        
        if (mysqli_num_rows($checkResult) == 0) {
            // Increment view count
            $updateQuery = "UPDATE {$siteprefix}forums SET views = views + 1 WHERE slug = '$slug'";
            mysqli_query($con, $updateQuery);
            
            // Log this view
            $insertQuery = "INSERT INTO {$siteprefix}question_views (slug, ip_address, viewed_at) 
                           VALUES ('$slug', '$userIP', NOW())";
            mysqli_query($con, $insertQuery);
            
            // Mark in session to prevent multiple increments in same session
            $_SESSION[$viewKey] = true;
        }
    }

    return ['status' => 'success'];
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

if (isset($_GET['action']) && $_GET['action'] == 'adduserviews') {


    if (!empty($_GET['slug'])) {
        $response = adduserViews($con, $_GET['slug']);
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

if (isset($_GET['action']) && $_GET['action'] == 'getuseradverts') {
    if (!empty($_GET['user_id'])) {
$response= getAlladversuser($con, $_GET['user_id']);
}
}

if (isset($_GET['action']) && $_GET['action'] == 'getorderitems') {
    if (!empty($_GET['order_id'])) {
        $response = getOrderItems($con, $_GET['order_id']);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'getusermanualpayments') {
    if (!empty($_GET['user_id'])) {
        $response = getAllusermanualPayment($con, $_GET['user_id']);
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'getuserbookmarks') {
    if (!empty($_GET['user_id'])) {
        $response = getAlluserbookmarks($con, $_GET['user_id']);
    }
}



if (isset($_GET['action']) && $_GET['action'] == 'groupuserstatus') {
    if (!empty($_GET['user_id'])) {
        $response = getAllUserGroupsAndMemberships($con, $_GET['user_id']);
    } 
}


if (isset($_GET['action']) && $_GET['action'] === 'dashboardstats') {
    $response = getDashboardStats($con);
}

if (isset($_GET['action']) && $_GET['action'] === 'advanced_analytics') {
    $period = $_GET['period'] ?? '30';
    $response = getAdvancedAnalyticsEndpoint($period);
}

if (isset($_GET['action']) && $_GET['action'] === 'search_therapists') {
    $response = searchTherapistsAdvancedEndpoint($_GET);
}

if (isset($_GET['action']) && $_GET['action'] === 'search_products') {
    $response = searchProductsAdvancedEndpoint($_GET);
}

if (isset($_GET['action']) && $_GET['action'] === 'search_content') {
    $response = searchContentAdvancedEndpoint($_GET);
}

if (isset($_GET['action']) && $_GET['action'] === 'validate_promo_code') {
    $response = validatePromoCodeEndpoint($_POST);
}

if (isset($_GET['action']) && $_GET['action'] === 'get_vendor_promos') {
    $response = isset($_GET['vendor_id']) ? getVendorPromoCodesEndpoint($_GET['vendor_id']) : ['status' => 'error', 'message' => 'Vendor ID required'];
}

if ($_GET['action'] == 'useradvertlists') {
              $response = getAlladverts($con);}


if ($_GET['action'] == 'questionlists') {
              $response = getallquestions($con);} 

if ($_GET['action'] == 'advertlists') {
              $response = getalladplacements($con);} 
              

          if ($_GET['action'] == 'subscriberlists') {
              $response = getallsubscribers($con);
          }

    if ($_GET['action'] == 'subscriptionlists') {
    $response = getallsubscriptions($con);
}



    if ($_GET['action'] == 'Allorderlists') {
    $response = getAllOrders($con);
}




if ($_GET['action'] == 'fetchlistingslug') {
    $response = isset($_GET['slug']) 
        ? getAllListingBySlug($con, $_GET['slug']) 
        : ['error' => 'Slug is required'];
}


if ($_GET['action'] == 'fetcheventslug') {
    $response = isset($_GET['slug']) 
        ? getAllEventsbyslug($con, $_GET['slug']) 
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

if (isset($_GET['action']) && $_GET['action'] === 'registered_events') {
    $user_id = $_GET['user_id'];
    $response = getRegisteredEvents($con, $user_id);
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

     if ($_GET['action'] === 'editevent') {
        $response = isset($_GET['event_id'])
            ? getSingleEventEndpoint($_GET['event_id'])
            : ['status' => 'error', 'message' => 'Event ID is required'];
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

if ($_GET['action'] == 'eventlists') {
$response = getAllEvents($con);}

if ($_GET['action'] == 'eventfiltering') {
$response = getAllEventsfiltering($con);
}

if ($_GET['action'] == 'bloglists') {
$response = getallblog($con);}

if ($_GET['action'] == 'disputeslists') {     
    $response = getalldisputestickets($con);
 }

if ($_GET['action'] == 'reportslists') {     
    $response = getallreports($con);
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

if ($_GET['action'] == 'blogmultiple') {  
        $response = isset($_GET['blog_id']) ? getmultipleblogID($con, $_GET['blog_id']) : ['error' => 'Blog ID is required'];}

        if ($_GET['action'] == 'editadverts') {  
        $response = isset($_GET['advert_id']) ? getadvertsID($con, $_GET['advert_id']) : ['error' => 'Advert ID is required'];}

        if ($_GET['action'] == 'advertslug') {  
        $response = isset($_GET['slug']) ? getalladvertbyslug($con, $_GET['slug']) : ['error' => 'Advert slug is required'];}

         
        
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
        
       if ($_GET['action'] == 'get_therapist_unavailable') {  
        $response = isset($_GET['therapist_id']) ? getTherapistUnavailableDatesEndpoint($_GET['therapist_id']) : ['error' => 'Therapist ID is required'];}
        
       if ($_GET['action'] == 'check_therapist_availability') {  
        $response = (isset($_GET['therapist_id']) && isset($_GET['date'])) ? checkTherapistAvailabilityEndpoint($_GET['therapist_id'], $_GET['date']) : ['error' => 'Therapist ID and date are required'];}

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

        if ($_POST['action'] == 'updatetherapist') {
       $response =  updateTherapistEndpoint($_POST, $_FILES);
        }

        if ($_POST['action'] == 'edittherapist') {
         $response =  updateMainTherapistEndpoint($_POST, $_FILES);
          }

      if ($_POST['action'] == 'updatebooking') {
       $response =  updateBookingEndpoint($_POST, $_FILES);
        }

    // ✅ Withdrawal Request Handler
    if ($_POST['action'] == 'withdraw') {
        $response = withdrawalRequestEndpoint($_POST);
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

    if ($_POST['action'] == 'download_subscribers_csv') {
        downloadSubscribersCSVEndpoint($_POST);
    }

    if($_POST['action'] == 'deleteblog'){
    $response = deleteblogEndpoint($_POST);}

     if($_POST['action'] == 'report_item'){
    $response = reportItem($_POST);}

    if($_POST['action'] == 'resolve_report'){
    $response = resolveReport($_POST);}

    if($_POST['action'] == 'delete_report'){
    $response = deleteReport($_POST);}

        if($_POST['action'] == 'deletereviews'){
    $response = deletereviewEndpoint($_POST);}
    

      if($_POST['action'] == 'deletelistings'){
    $response = deletelistingEndpoint($_POST);}

     if($_POST['action'] == 'deleteevents'){
    $response = deleteeventEndpoint($_POST);}

      if($_POST['action'] == 'acceptanswer'){
     $response = acceptAnswerEndpoint($_POST);}

     if ($_POST['action'] == 'acceptbest') {
    $response = acceptBestAnswerEndpoint($_POST);
}

        if($_POST['action'] == 'editplacement'){
    $response = editAdPlacementEndpoint($_POST);
        }

        if($_POST['action'] == 'deleteadvert'){
    $response = deleteadvertEndpoint($_POST);}

    if($_POST['action'] == 'report_user'){
    $response = reportUser($_POST);
}

    // ✅ Submit Feedback Handler
    if($_POST['action'] == 'submit_feedback'){
        $response = submitFeedbackEndpoint($_POST);
    }

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
    
    
if ($_POST['action'] == 'deletePortfolio') {
   $response = deletePortfolioEndpoint($_POST);
}

    
     if($_POST['action'] == 'join-group'){
    $response = joingroupEndpoint($_POST);}


      if($_POST['action'] == 'request-join-group'){
    $response = request_join_groupEndpoint($_POST);
}

  if($_POST['action'] == 'approve-booking'){
    $response = approvebookings($_POST);
}


  if($_POST['action'] == 'approve-advert'){
    $response = approveAdverts($_POST);
}

if($_POST['action'] == 'reject-advert'){
    $response = rejectAdvert($_POST);
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

if(isset($_POST['action']) && ($_POST['action'] === 'follow' || $_POST['action'] === 'unfollow')) {
    $response = handleFollow($_POST);
}

if ($_POST["action"] == "create-advert-order") {
    $response = createAdvertOrder($_POST, $_FILES);
}

if($_POST['action'] == 'update_order_status'){
    $response = updateOrderStatusEndpoint($_POST);
}

if($_POST['action'] == 'create_promo_code'){
    $response = createPromoCodeEndpoint($_POST);
}

if($_POST['action'] == 'validate_promo'){
    $response = validatePromoCodeEndpoint($_POST);
}

if($_POST['action'] == 'record_promo_usage'){
    $response = recordPromoUsageEndpoint($_POST);
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
    $response =  addcategoryendpoint($_POST);
} // end

   if($_POST['action'] == 'addplacement'){
    $response =  addAdPlacementEndpoint($_POST);
}

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

if($_POST['action'] == 'bookmark'){
    $response = handleBookmark($_POST);
}

     if($_POST['action'] == 'updategroupmember'){
    $response = updatememberEndpoint($_POST);}

    if($_POST['action'] == 'book_therapy_session'){
    $response = bookTherapySessionEndpoint($_POST);}
    
    if($_POST['action'] == 'add_therapist_unavailable'){
    $response = addTherapistUnavailableDateEndpoint($_POST);}
    
    if($_POST['action'] == 'remove_therapist_unavailable'){
    $response = removeTherapistUnavailableDateEndpoint($_POST);}
    
    if($_POST['action'] == 'confirm_therapist_booking'){
    $response = confirmTherapistBookingEndpoint($_POST);}
    
    if($_POST['action'] == 'reject_therapist_booking'){
    $response = rejectTherapistBookingEndpoint($_POST);}

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


  if ($_POST['action'] == 'deletemedia') {
        $response = deleteeventimageEndpoint($_POST);
    }
    
  if ($_POST['action'] == 'deleteeventvideo') {
    $response = deleteeventvideoEndpoint($_POST);
    }

    
  if ($_POST['action'] == 'deleteeventtext') {
    $response = deleteeventtextEndpoint($_POST);
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


    if($_POST['action'] == 'articlefeedback'){
    $response = saveArticleFeedback($_POST);
}

   
     if($_POST['action'] == 'edit_adminvendor'){
    $response = updateVendorEndpoint($_POST, $_FILES);}  

 if($_POST['action'] == 'createsecondticket'){
    $response = sendDisputeMessage($_POST, $_FILES);}  
    

    if($_POST['action'] == 'send_group_message'){
        $response = sendGroupMessageEndpoint($_POST);
    }

     if($_POST['action'] == 'update_event'){
        $response = userupdateeventsEndpoint($_POST, $_FILES);
    }

    

    if($_POST['action'] == 'updateticketstatus'){
    $response = updateDisputeStatusHandler($_POST);

    }
    
    if($_POST['action'] == 'resolve_dispute_with_refund'){
    $response = resolveDisputeWithRefundEndpoint($_POST);
    }

    if($_POST['action'] == 'deletesubcategory'){
    $response = deletesubcategoryEndpoint($_POST);}



     if ($_POST['action'] == 'updatevendorblog') {
        $response = updatevendorBlogEndpoint($_POST, $_FILES);
    }

    
    if ($_POST['action'] == 'markAllNotificationsRead') {
        $response = markAllNotificationsRead($_POST);
    }


    if ($_POST['action'] == 'subscribeNewsletter') {
        $response = subscribeNewsletter($_POST);
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

