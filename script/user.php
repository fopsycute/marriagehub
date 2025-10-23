<?php
include "connect.php";

function getbuyerdata($con, $userId) {
    global $siteprefix;
    $query ="SELECT * FROM {$siteprefix}users WHERE id = '$userId'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_assoc($result) : ['error' => mysqli_error($con)];
}


function postCommentEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['blog_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $parentId = intval($postData['parent_id'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment)) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}comments (user_id, blog_id, comment, parent_id, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $parentId);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Comment posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}

function postReviewEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['group_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $rating = intval($postData['rating'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment) || $rating <= 0) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}reviews (user_id, group_id, comment, rating, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $rating);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Review posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}


function posttherapistReviewEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['group_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $rating = intval($postData['rating'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment) || $rating <= 0) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}reviews (user_id, therapist_id, comment, rating, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $rating);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Review posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}

function postAnswerEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['question_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $parentId = intval($postData['parent_id'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment)) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}answers (user_id, question_id, answer, parent_id, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $parentId);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Answer posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}

function fetchCommentsBySlug($blog_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT c.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}comments AS c
        LEFT JOIN {$siteprefix}users AS u ON c.user_id = u.id
        WHERE c.blog_id = '$blog_id' 
          AND (c.parent_id IS NULL OR c.parent_id = 0)
        ORDER BY c.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function fetchCommentsBygroup($group_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT r.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}reviews AS r
        LEFT JOIN {$siteprefix}users AS u ON r.user_id = u.id
        WHERE r.group_id = '$group_id' 
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}


function fetchCommentsBytherapist($user_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT r.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}reviews AS r
        LEFT JOIN {$siteprefix}users AS u ON r.user_id = u.id
        WHERE r.therapist_id = '$user_id'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}



function fetchQuestionsBySlug($question_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT a.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}answers AS a
        LEFT JOIN {$siteprefix}users AS u ON a.user_id = u.id
        WHERE a.question_id = '$question_id' 
          AND (a.parent_id IS NULL OR a.parent_id = 0)
        ORDER BY a.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function getAllrepliesData($con, $comment_id)
{
    global $siteprefix;

    // Escape the comment_id to avoid SQL injection
    $comment_id = mysqli_real_escape_string($con, $comment_id);

   // Fetch replies + count how many replies each of them has
    $query = "
        SELECT 
            c.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}comments AS r 
                WHERE r.parent_id = c.id
            ) AS reply_count
        FROM {$siteprefix}comments AS c
        LEFT JOIN {$siteprefix}users AS u ON c.user_id = u.id
        WHERE c.parent_id = '$comment_id'
        ORDER BY c.created_at ASC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}



function getAllanswerrepliesData($con, $comment_id)
{
    global $siteprefix;

    // Escape the comment_id to avoid SQL injection
    $comment_id = mysqli_real_escape_string($con, $comment_id);

   // Fetch replies + count how many replies each of them has
    $query = "
        SELECT 
            a.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            (
                SELECT COUNT(*) 
                FROM {$siteprefix}answers AS a 
                WHERE a.parent_id = a.id
            ) AS reply_count
        FROM {$siteprefix}answers AS a
        LEFT JOIN {$siteprefix}users AS u ON a.user_id = u.id
        WHERE a.parent_id = '$comment_id'
        ORDER BY a.created_at ASC
    ";

    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}



function getAllCategories($con, $siteprefix)
{
    $categories = [];

    // Fetch parent categories
    $catSql = "SELECT id, category_name FROM {$siteprefix}categories WHERE parent_id IS NULL ORDER BY category_name ASC";
    $catRes = mysqli_query($con, $catSql);

    if ($catRes && mysqli_num_rows($catRes) > 0) {
        while ($catRow = mysqli_fetch_assoc($catRes)) {
            $catId = $catRow['id'];

            // Count number of posts in this category
            $countRes = mysqli_query($con, "SELECT COUNT(*) as cnt FROM {$siteprefix}forums WHERE FIND_IN_SET($catId, categories) AND status='active'");
            $countRow = mysqli_fetch_assoc($countRes);
            $count = $countRow['cnt'] ?? 0;

            $categories[] = [
                'id' => $catId,
                'category_name' => $catRow['category_name'],
                'post_count' => $count
            ];
        }
    }

    return $categories;
}

function getAllCategoriesByGroup($con, $siteprefix, $group_id)
{
    $categories = [];

    // Fetch all parent categories (we'll include all, even if some have zero posts)
    $catSql = "SELECT id, category_name 
               FROM {$siteprefix}categories 
               WHERE parent_id IS NULL 
               ORDER BY category_name ASC";
    $catRes = mysqli_query($con, $catSql);

    if ($catRes && mysqli_num_rows($catRes) > 0) {
        while ($catRow = mysqli_fetch_assoc($catRes)) {
            $catId = $catRow['id'];

            // ✅ Count how many active posts belong to this group under this category
            $countSql = "
                SELECT COUNT(*) AS cnt 
                FROM {$siteprefix}forums 
                WHERE FIND_IN_SET($catId, categories)
                  AND group_id = '" . intval($group_id) . "'
                  AND status = 'active'
            ";

            $countRes = mysqli_query($con, $countSql);
            $countRow = mysqli_fetch_assoc($countRes);
            $count = $countRow['cnt'] ?? 0;

            // ✅ Always include all categories, even with 0 posts
            $categories[] = [
                'id' => $catId,
                'category_name' => $catRow['category_name'],
                'post_count' => $count
            ];
        }
    }

    return $categories;
}
function createQuestionEndpoint($postData)
{
    global $con, $siteprefix;

    // 🧹 Sanitize input
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $group_id    = $postData['group_id'] ?? '';
    $user        = intval($postData['user'] ?? 0);
    $status     = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $anonymous   = isset($postData['anonymous']) ? 1 : 0;

    // ✅ Validation
    if (empty($title) || empty($article)) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Generate base slug (SEO-friendly)
    $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $slug = $baseSlug;
    $counter = 1;

    // ✅ Ensure slug is unique in `questions`
    while (true) {
        $queryCheck = "SELECT COUNT(*) AS count FROM {$siteprefix}questions WHERE slug = ?";
        $stmt = $con->prepare($queryCheck);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            break; // slug is unique
        }

        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // ✅ Insert new question
    $query = "
        INSERT INTO {$siteprefix}questions 
        (user_id, title, slug, article, categories, subcategories, tags, anonymous, created_at, status, group_id)
        VALUES 
        ('$user', '$title', '$slug', '$article', '$category', '$subcategory', '$tags', '$anonymous', NOW(), '$status','$group_id')
    ";

    if (mysqli_query($con, $query)) {
        return [
            'status' => 'success',
            'messages' => generateMessage("Your question has been submitted successfully!", "green")
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => generateMessage("Database error: " . mysqli_error($con), "red")
        ];
    }
}


//update question
function updateQuestionEndpoint($postData)
{
    global $con, $siteprefix;

    // 🧹 Sanitize input
    $questionId  = intval($postData['question_id'] ?? 0);
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $status      = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $anonymous   = isset($postData['anonymous']) ? 1 : 0;

    // ✅ Validation
    if ($questionId <= 0 || empty($title) || empty($article)) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Ensure question exists
    $checkQuery = "SELECT title, slug FROM {$siteprefix}questions WHERE id = ?";
    $stmtCheck = $con->prepare($checkQuery);
    $stmtCheck->bind_param("i", $questionId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        return ['status' => 'error', 'messages' => generateMessage("Question not found.", "red")];
    }

    $oldData = $resultCheck->fetch_assoc();
    $oldTitle = $oldData['title'];
    $oldSlug = $oldData['slug'];

    // ✅ Determine if we need to regenerate slug
    $slug = $oldSlug;
    if ($oldTitle !== $title) {
        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $slug = $baseSlug;
        $counter = 1;

        // Ensure unique slug (ignore current question)
        while (true) {
            $querySlug = "SELECT COUNT(*) AS count FROM {$siteprefix}questions WHERE slug = ? AND id != ?";
            $stmtSlug = $con->prepare($querySlug);
            $stmtSlug->bind_param("si", $slug, $questionId);
            $stmtSlug->execute();
            $resultSlug = $stmtSlug->get_result();
            $rowSlug = $resultSlug->fetch_assoc();

            if ($rowSlug['count'] == 0) break;

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

    // ✅ Update question
    $updateQuery = "
        UPDATE {$siteprefix}questions 
        SET 
            title = ?, 
            slug = ?, 
            article = ?, 
            categories = ?, 
            subcategories = ?, 
            tags = ?, 
            anonymous = ?, 
            status = ?
        WHERE id = ?
    ";

    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("ssssssssi", $title, $slug, $article, $category, $subcategory, $tags, $anonymous, $status, $questionId);

    if ($stmt->execute()) {
        return [
            'status' => 'success',
            'messages' => generateMessage("Your question has been updated successfully!", "green")
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => generateMessage("Database error: " . $stmt->error, "red")
        ];
    }
}



function addForumEndpoint($postData, $fileData)
{
    global $con, $siteprefix;

    // 🧹 Sanitize inputs
    $title = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $status        = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $user = intval($postData['user'] ?? 0);
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

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
    // ✅ Validation
    if (empty($title) || empty($article) || $user == 0) {
        $messages = generateMessage("All required fields must be filled.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ✅ Prevent duplicate insert within 10 seconds
    $checkQuery = $con->prepare("
        SELECT id FROM {$siteprefix}forums 
        WHERE user_id = ? AND title = ? 
        AND created_at >= (NOW() - INTERVAL 10 SECOND)
        LIMIT 1
    ");
    $checkQuery->bind_param("is", $user, $title);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        return ['status' => 'error', 'messages' => generateMessage("Duplicate submission detected. Please wait a few seconds before trying again.", "red")];
    }

    // ✅ Handle featured image
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $featuredImage = "";
    if (!empty($fileData["featured_image"]["name"])) {
        $fileName = basename($fileData["featured_image"]["name"]);
        $featuredImage = uniqid('forum_') . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        move_uploaded_file($fileData["featured_image"]["tmp_name"], $targetDir . $featuredImage);
    }

 
  // ✅ Insert into forums table
    $stmt = $con->prepare("
    INSERT INTO {$siteprefix}forums 
    (user_id, title, article, featured_image, tags, categories, subcategories, status, created_at, slug, views)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 0)
");

if (!$stmt) {
    return ['status' => 'error', 'messages' => 'Database error: ' . $con->error];
}

// ✅ Corrected: 9 type definitions for 9 parameters
$stmt->bind_param("issssssss", $user, $title, $article, $featuredImage, $tags, $category, $subcategory, $status, $alt_title);

if (!$stmt->execute()) {
    return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
}

return [
    'status' => 'success',
    'messages' => 'Forum post created successfully! Awaiting approval.'
];

}


function getQuestionID($con, $question_id) {
    global $con,$siteprefix;
    $query = "SELECT * FROM   {$siteprefix}questions WHERE id= '$question_id'";
    $result = mysqli_query($con, $query);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

/**
 * Create group endpoint
 */
function createGroupEndpoint($postData, $fileData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;
    $user_id = $_POST['user'];

    $messages = '';

    // Sanitize inputs
    $group_name = mysqli_real_escape_string($con, trim($postData['group_name'] ?? ''));
    $group_description = mysqli_real_escape_string($con, trim($postData['group_description'] ?? ''));
    $group_type = mysqli_real_escape_string($con, $postData['group_type'] ?? 'open'); // open|closed
    $group_access = mysqli_real_escape_string($con, $postData['group_access'] ?? 'free'); // free|paid

    // fees: ensure numeric
    $fee_1m = isset($postData['fee_1m']) ? floatval($postData['fee_1m']) : 0;
    $fee_3m = isset($postData['fee_3m']) ? floatval($postData['fee_3m']) : 0;
    $fee_6m = isset($postData['fee_6m']) ? floatval($postData['fee_6m']) : 0;
    $fee_12m = isset($postData['fee_12m']) ? floatval($postData['fee_12m']) : 0;

    // category & subcategory (single selects in your example)
    $category = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    $group_rules = mysqli_real_escape_string($con, trim($postData['group_rules'] ?? ''));
    $agree_commission = isset($postData['agree_commission']) ? 1 : 0;
    $agree_guidelines = isset($postData['agree_guidelines']) ? 1 : 0;
    $agree_terms = isset($postData['agree_terms']) ? 1 : 0;

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
         category, subcategory, group_rules, banner, agree_commission, agree_guidelines, agree_terms, status, created_at, user_id, slug)
     VALUES
        ('$group_name_q', '$group_description_q', '$group_type_q', '$group_access_q',
         '$fee_1m', '$fee_3m', '$fee_6m', '$fee_12m',
         '$category_q', '$subcategory_q', '$group_rules_q', '$banner_q', '$agree_commission', '$agree_guidelines', '$agree_terms', 'pending', NOW(), '$user_id', '$alt_title')";

    if (mysqli_query($con, $sql)) {
        $messages .= generateMessage("Group created successfully.", "green");
        return ['status' => 'success', 'messages' => $messages];
    } else {
        $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }
}


function likeBlogEndpoint($postData) {
    global $con, $siteprefix;

    $messages = '';
    $blog_id = intval($postData['blog_id'] ?? 0);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if ($blog_id <= 0) {
        $messages .= generateMessage("Invalid blog ID.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // 🔒 Check if already liked
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}blog_likes WHERE blog_id = '$blog_id' AND user_ip = '$user_ip'");
    if (mysqli_num_rows($check) > 0) {
        $messages .= generateMessage("You already liked this post.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ❤️ Add like
    $insert = mysqli_query($con, "INSERT INTO {$siteprefix}blog_likes (blog_id, user_ip, liked_at) VALUES ('$blog_id', '$user_ip', NOW())");
    if (!$insert) {
        $messages .= generateMessage("Database error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // 🔢 Count total likes
    $countRes = mysqli_query($con, "SELECT COUNT(*) as total FROM {$siteprefix}blog_likes WHERE blog_id = '$blog_id'");
    $countRow = mysqli_fetch_assoc($countRes);
    $likeCount = $countRow['total'] ?? 0;

    $messages .= generateMessage("You liked this post.", "green");

    return [
        'status' => 'success',
        'messages' => $messages,
        'likes' => $likeCount
    ];
}

function deleteAnswerEndpoint($postData)
{
    global $con, $siteprefix;

    // Frontend sends 'image_id' (comment ID)
    $commentId = intval($postData['image_id'] ?? 0);

    if (empty($commentId)) {
        return ['status' => 'error', 'messages' => 'Invalid request.'];
    }

    // 🗑️ Delete the comment and all its child replies
    $query = "DELETE FROM {$siteprefix}answers WHERE id = '$commentId' OR parent_id = '$commentId'";
    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => 'Answer deleted successfully.'];
    } else {
        return ['status' => 'error', 'messages' => 'Failed to delete comment.'];
    }
}

function deleteCommentEndpoint($postData)
{
    global $con, $siteprefix;

    // Frontend sends 'image_id' (comment ID)
    $commentId = intval($postData['image_id'] ?? 0);

    if (empty($commentId)) {
        return ['status' => 'error', 'messages' => 'Invalid request.'];
    }

    // 🗑️ Delete the comment and all its child replies
    $query = "DELETE FROM {$siteprefix}comments WHERE id = '$commentId' OR parent_id = '$commentId'";
    if (mysqli_query($con, $query)) {
        return ['status' => 'success', 'messages' => 'Comment deleted successfully.'];
    } else {
        return ['status' => 'error', 'messages' => 'Failed to delete comment.'];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    
if ($_GET['action'] == 'buyerdata') {
     $response = isset($_GET['buyer']) ? getbuyerdata($con, $_GET['buyer']) : ['error' => 'Buyer ID is required'];}

     if ($_GET['action'] == 'repliesdata') {
     $response = isset($_GET['comment_id']) ? getAllrepliesData($con, $_GET['comment_id']) : ['error' => 'Comment ID is required'];}

      if ($_GET['action'] == 'answersreplydata') {
     $response = isset($_GET['comment_id']) ? getAllanswerrepliesData($con, $_GET['comment_id']) : ['error' => 'Comment ID is required'];}

    if (!empty($_GET['blog_id'])) {
            $response = fetchCommentsBySlug($_GET['blog_id']);
        }

         if ($_GET['action'] == 'answersdata') {
            $response = fetchQuestionsBySlug($_GET['question_id']);
        }

         if ($_GET['action'] == 'editquest') {  
        $response = isset($_GET['question_id']) ? getQuestionID($con, $_GET['question_id']) : ['error' => 'Question ID is required'];
    }


        if ($_GET['action'] == 'commentsdata') {
            $response = fetchCommentsBygroup($_GET['group_id']);
        }

        
        if ($_GET['action'] == 'reviewtherapisdata') {
            $response = fetchCommentsBytherapist($_GET['therapist_id']);
        }

    if ($_GET['action'] == 'categorieslist') {
        $response = getAllCategories($con, $siteprefix);
    }

    
    if ($_GET['action'] == 'categorieslistbygroup') {
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
    $response = getAllCategoriesByGroup($con, $siteprefix, $group_id);
    }

    header('Content-Type: application/json');  
    echo json_encode($response);
        
}


// ✅ API Endpoint Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    if ($_POST['action'] == 'addforum') {
        $response = addForumEndpoint($_POST, $_FILES);
    }
      if ($_POST['action'] == 'post_comment') {
        $response =  postCommentEndpoint($_POST);
    }

        if ($_POST['action'] == 'post_review') {
            $response =  postReviewEndpoint($_POST);
        }

        
        if ($_POST['action'] == 'post_reviewtherapist') {
            $response =   posttherapistReviewEndpoint($_POST);
        }
       

       if ($_POST['action'] == 'post_answers') {
        $response =  postAnswerEndpoint($_POST);
    }

     if ($_POST['action'] == 'deletecomment') {
        $response =  deleteCommentEndpoint($_POST);
    }

    if ($_POST['action'] == 'deleteanswer') {
        $response =  deleteAnswerEndpoint($_POST);
    }
      if ($_POST['action'] == 'updateQuestion') {
        $response =  updateQuestionEndpoint($_POST);
    }


     if ($_POST['action'] == 'createQuestion') {
        $response =  createQuestionEndpoint($_POST);
    }
    
    if ($_POST['action'] == 'create_group') {
        $response = createGroupEndpoint($_POST, $_FILES);
    }

      // ✅ NEW: Like Blog Endpoint
    if ($_POST['action'] == 'like_blog') {
        $response = likeBlogEndpoint($_POST);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}

?>