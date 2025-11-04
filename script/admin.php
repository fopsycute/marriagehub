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
        SELECT * FROM {$siteprefix}users where user_type = 'buyer'";

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



function getblogID($con, $blog_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}forums WHERE id= '$blog_id'";
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
    global $con, $siteprefix;

    $bookingId      = intval($postData['booking_id'] ?? 0);
    $booking_status = mysqli_real_escape_string($con, trim($postData['booking_status'] ?? ''));

    // 🧠 Validate Booking ID
    if ($bookingId <= 0) {
        return ['status' => 'error', 'messages' => 'Invalid booking ID'];
    }

    // 🧠 Validate Status
    if (empty($booking_status)) {
        return ['status' => 'error', 'messages' => 'Booking status is required'];
    }

    // ✅ Update booking status only
    $query = "
        UPDATE {$siteprefix}bookings 
        SET booking_status = '$booking_status'
        WHERE id = '$bookingId'
    ";

    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => 'Booking status updated successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . mysqli_error($con)];
    }
}
//update blog
function updateBlogEndpoint($postData, $fileData)
{
    global $con, $siteprefix, $siteName, $siteMail;

    $blogId = intval($postData['blog_id'] ?? 0);
    $title = mysqli_real_escape_string($con, trim($postData['blogTitle'] ?? ''));
    $article = $postData['blogContent'] ?? '';
    $tags = mysqli_real_escape_string($con, trim($postData['blogTags'] ?? ''));
    $status = mysqli_real_escape_string($con, trim($postData['status']));
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

    // ✅ Prepare image update if provided
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

    // ✅ Update blog data
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

        // ✅ If status changed from not-active → active, send notification
        if ($oldStatus !== 'active' && $status === 'active') {

            $authorQuery = mysqli_query($con, "SELECT first_name, email, user_type FROM {$siteprefix}users WHERE id = '$authorId' LIMIT 1");
            if ($authorQuery && mysqli_num_rows($authorQuery) > 0) {
                $author = mysqli_fetch_assoc($authorQuery);

                if (strtolower($author['user_type']) !== 'admin') {
                    $firstName = $author['first_name'];
                    $email = $author['email'];
                    $emailSubject = "Your Blog Has Been Approved!";
                    $emailMessage = "
                        <p>Hi {$firstName},</p>
                        <p>Good news! Your blog post titled <strong>\"{$oldTitle}\"</strong> has been approved and published on <strong>{$siteName}</strong>.</p>
                        <p>You can now view it live on the site.</p>
                        <p>Best regards,<br>{$siteName} Team</p>
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
                You can now post blogs and ask or answer questions within the group.<br><br>
                Keep up the good work!
            ";
            $alertMessage = "You’ve been promoted to <b>Subadmin</b> in <b>{$groupName}</b>.";
        } elseif ($newRole === 'admin') {
            $emailSubject = "You’re now an Admin of {$groupName}";
            $emailMessage = "
                You’ve been upgraded to <b>Admin</b> in <b>{$groupName}</b>.<br>
                You can now manage group settings, approve members, and moderate all content.
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

function checkuservendor($con, $slug) {
    global $siteprefix;

    $slug = mysqli_real_escape_string($con, $slug);
    $query = "SELECT * FROM {$siteprefix}users WHERE slug = '$slug' LIMIT 1";
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
    $status= $_POST['status'];

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

// 🎥 Handle Videos (respecting plan limits)
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
    $response = getAllListings($con);
}


      if ($_GET['action'] == 'bloglists') {
              $response = getallblog($con);}
              
    if ($_GET['action'] == 'buyerlists') {
              $response =  getalluser($con);}
              
     if ($_GET['action'] == 'grouplists') {
              $response = getallgroups($con);}  

        
if ($_GET['action'] == 'bookinglist') {
          $response = getBookings($con);}

 if ($_GET['action'] == 'groupmemberlists') {
    $response =   getallgroupsmembers($con);}  
            

if ($_GET['action'] == 'userlists') {
$response = getallusers($con);}
            
if ($_GET['action'] == 'editbooking') {  
$response = isset($_GET['booking_id']) ? getbookingsID($con, $_GET['booking_id']) : ['error' => 'Booking ID is required'];}         

     if ($_GET['action'] == 'editblog') {  
        $response = isset($_GET['blog_id']) ? getblogID($con, $_GET['blog_id']) : ['error' => 'Blog ID is required'];}

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
        
        
        if ($_GET['action'] == 'fetchgroupid') {  
        $response = isset($_GET['group_id']) ? getgroupByid($con, $_GET['group_id']) : ['error' => 'group ID is required'];} 

         if ($_GET['action'] == 'editgroup') {  
        $response = isset($_GET['group_id']) ? getgroupID($con, $_GET['group_id']) : ['error' => 'group ID is required'];} 

            
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

    if($_POST['action'] == 'deleteblog'){
    $response = deleteblogEndpoint($_POST);}
    
    if($_POST['action'] == 'deletequestion'){
    $response = deletequestionEndpoint($_POST);}

    

    if($_POST['action'] == 'deleteusers'){
    $response = deleteusersEndpoint($_POST);} 

    if($_POST['action'] == 'deletegroup'){
    $response = deletegroupEndpoint($_POST);} 
    

    
    if($_POST['action'] == 'deletegroupmembers'){
    $response =  deletegroupmembersEndpoint($_POST);} 

    
     if($_POST['action'] == 'join-group'){
    $response = joingroupEndpoint($_POST);}


      if($_POST['action'] == 'request-join-group'){
    $response = request_join_groupEndpoint($_POST);
}

    if($_POST['action'] == 'exit-group'){
    $response = exitGroupEndpoint($_POST);}

     if($_POST['action'] == 'updategroupmember'){
    $response = updatememberEndpoint($_POST);}

    if($_POST['action'] == 'book_therapy_session'){
    $response = bookTherapySessionEndpoint($_POST);
}


    if($_POST['action'] == 'edit_admingroup'){
    $response = updateAdminGroupEndpoint($_POST, $_FILES);}  
    
    header('Content-Type: application/json');
    echo json_encode($response);

}

?>

