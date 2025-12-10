<?php
include "connect.php";

function getbuyerdata($con, $userId) {
    global $siteprefix;
  $userId = intval($userId);

    $query = "
        SELECT 
            u.*,
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
                    SELECT COALESCE(SUM(wh.amount), 0)
                    FROM {$siteprefix}wallet_history AS wh
                    WHERE wh.user = u.id
                    AND wh.status = 'credit'
                ) AS total_earnings,
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
        WHERE u.id = '$userId'
        LIMIT 1
    ";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    return mysqli_fetch_assoc($result);
}

function getorderdata($con, $orderId) {
    global $siteprefix;

    $orderId = mysqli_real_escape_string($con, $orderId);

    $query = "
        SELECT COALESCE(SUM(total_price), 0) AS total
        FROM {$siteprefix}order_items
        WHERE order_id = '$orderId' AND type IN ('product', 'event')
    ";

    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['status' => 'error', 'message' => mysqli_error($con)];
    }

    $row = mysqli_fetch_assoc($result);
    $order_total = $row['total'] ?? 0;

    return [
        'status' => 'success',
        'order_id' => $orderId,
        'total' => $order_total
    ];
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


function postproductReviewEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $rating = intval($postData['rating'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment) || $rating <= 0) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}reviews (user_id, listing_id, comment, rating, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $rating);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Review posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}

function posteventReviewEndpoint($postData)
{
    global $con, $siteprefix;

    $userId = intval($postData['user_id'] ?? 0);
    $slug = mysqli_real_escape_string($con, trim($postData['event_id'] ?? ''));
    $comment = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $rating = intval($postData['rating'] ?? 0);

    if (empty($userId) || empty($slug) || empty($comment) || $rating <= 0) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}reviews (user_id, event_id, comment, rating, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issi", $userId, $slug, $comment, $rating);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Review posted successfully!'];
    } else {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }
}

function updateuserProductReviewEndpoint($postData)
{
    global $con, $siteprefix;

    $reviewId = intval($postData['review_id'] ?? 0);
    $userId   = intval($postData['user_id'] ?? 0);
    $comment  = mysqli_real_escape_string($con, trim($postData['comment'] ?? ''));
    $rating   = intval($postData['rating'] ?? 0);

    // Validate fields
    if ($reviewId == 0 || $userId == 0 || empty($comment) || $rating <= 0) {
        return ['status' => 'error', 'messages' => 'All fields are required.'];
    }

    // Ensure review belongs to the user
    $check = mysqli_query($con, "
        SELECT id FROM {$siteprefix}reviews 
        WHERE id = '$reviewId' AND user_id = '$userId'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) == 0) {
        return ['status' => 'error', 'messages' => 'Unauthorized action.'];
    }

    // Update review
    $stmt = $con->prepare("
        UPDATE {$siteprefix}reviews 
        SET comment = ?, rating = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("siii", $comment, $rating, $reviewId, $userId);

    if ($stmt->execute()) {
        return ['status' => 'success', 'messages' => 'Review updated successfully!'];
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


function fetchReviewStats($listing_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT 
            COUNT(*) AS total_reviews,
            AVG(rating) AS average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS one_star
        FROM {$siteprefix}reviews
        WHERE listing_id = '$listing_id'
    ";

    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $row = mysqli_fetch_assoc($result);

    if (!$row['total_reviews']) {
        // No reviews yet
        return [
            'total_reviews' => 0,
            'average_rating' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0
        ];
    }

    // Compute percentages
    for ($i = 1; $i <= 5; $i++) {
        $key = match ($i) {
            5 => 'five_star',
            4 => 'four_star',
            3 => 'three_star',
            2 => 'two_star',
            1 => 'one_star',
        };
        $row[$key . '_percent'] = round(($row[$key] / $row['total_reviews']) * 100);
    }

    return $row;
}

function fetcheventReviewStats($event_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT 
            COUNT(*) AS total_reviews,
            AVG(rating) AS average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS one_star
        FROM {$siteprefix}reviews
        WHERE event_id = '$event_id'
    ";

    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $row = mysqli_fetch_assoc($result);

    if (!$row['total_reviews']) {
        // No reviews yet
        return [
            'total_reviews' => 0,
            'average_rating' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0
        ];
    }

    // Compute percentages
    for ($i = 1; $i <= 5; $i++) {
        $key = match ($i) {
            5 => 'five_star',
            4 => 'four_star',
            3 => 'three_star',
            2 => 'two_star',
            1 => 'one_star',
        };
        $row[$key . '_percent'] = round(($row[$key] / $row['total_reviews']) * 100);
    }

    return $row;
}


function fetchCommentsByListing($listing_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT r.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}reviews AS r
        LEFT JOIN {$siteprefix}users AS u ON r.user_id = u.id
        WHERE r.listing_id = '$listing_id'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // ✅ Check verified buyer for each user/listing
    foreach ($reviews as &$review) {
        $user_id = intval($review['user_id']);
        $listing_id = intval($review['listing_id']);

        $checkQuery = "
            SELECT o.order_id 
            FROM {$siteprefix}orders o
            JOIN {$siteprefix}order_items oi ON o.order_id = oi.order_id
            WHERE o.user = '$user_id' 
              AND oi.listing_id = '$listing_id'
              AND o.status = 'paid'
            LIMIT 1
        ";

        $checkResult = mysqli_query($con, $checkQuery);
        $review['verified_buyer'] = ($checkResult && mysqli_num_rows($checkResult) > 0) ? 1 : 0;
    }

    return $reviews;
}


function fetchCommentsByEvent($event_id)
{
    global $con, $siteprefix;

    $query = "
        SELECT r.*, u.first_name, u.last_name, u.photo
        FROM {$siteprefix}reviews AS r
        LEFT JOIN {$siteprefix}users AS u ON r.user_id = u.id
        WHERE r.event_id = '$event_id'
        ORDER BY r.created_at DESC
    ";

    $result = mysqli_query($con, $query);
    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // ✅ Check verified buyer for each user/listing
    foreach ($reviews as &$review) {
        $user_id = intval($review['user_id']);
        $event_id = intval($review['event_id']);

        $checkQuery = "
            SELECT o.order_id 
            FROM {$siteprefix}orders o
            JOIN {$siteprefix}order_items oi ON o.order_id = oi.order_id
            WHERE o.user = '$user_id' 
              AND oi.event_id = '$event_id'
              AND o.status = 'paid'
            LIMIT 1
        ";

        $checkResult = mysqli_query($con, $checkQuery);
        $review['verified_buyer'] = ($checkResult && mysqli_num_rows($checkResult) > 0) ? 1 : 0;
    }

    return $reviews;
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



function fetchQuestionsBySlug($question_id, $sort = 'recent')
{
    global $con, $siteprefix;

    // Left join aggregated vote counts for answers (if table exists)
    $voteJoin = "LEFT JOIN (
        SELECT answer_id,
            SUM(CASE WHEN vote = 1 THEN 1 ELSE 0 END) AS upvotes,
            SUM(CASE WHEN vote = -1 THEN 1 ELSE 0 END) AS downvotes,
            SUM(CASE WHEN vote = 1 THEN 1 WHEN vote = -1 THEN -1 ELSE 0 END) AS score
        FROM {$siteprefix}answer_votes
        GROUP BY answer_id
    ) AS v ON v.answer_id = a.id";

    //  Always prioritize Best → Accepted → Newest
    $order = "
        a.is_best DESC,
        a.is_accepted DESC,
        a.created_at DESC
    ";

    $query = "
        SELECT 
            a.*, 
            u.first_name, 
            u.last_name, 
            u.photo,
            COALESCE(v.upvotes,0) AS upvotes, 
            COALESCE(v.downvotes,0) AS downvotes, 
            COALESCE(v.score,0) AS score
        FROM {$siteprefix}answers AS a
        LEFT JOIN {$siteprefix}users AS u ON a.user_id = u.id
        {$voteJoin}
        WHERE a.question_id = '".mysqli_real_escape_string($con,$question_id)."' 
          AND (a.parent_id IS NULL OR a.parent_id = 0)
        ORDER BY {$order}
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


function addToCartEndpoint($postData)
{
    global $con, $siteprefix;

    // 🧾 Get POST data safely
    $listing_id = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));
    $user_id    = mysqli_real_escape_string($con, trim($postData['user_id'] ?? ''));
    $order_id   = mysqli_real_escape_string($con, trim($postData['order_id'] ?? ''));
    $quantity   = intval($postData['quantity'] ?? 1);
    $variation  = mysqli_real_escape_string($con, trim($postData['variation'] ?? ''));
    $price      = floatval($postData['price'] ?? 0);

    // ✅ Validate inputs
    if (empty($listing_id) || empty($user_id) || empty($order_id)) {
        return ['status' => 'error', 'message' => 'Missing required parameters'];
    }

    // ✅ Fetch product details
    $stmt = $con->prepare("SELECT title, limited_slot FROM {$siteprefix}listings WHERE listing_id = ?");
    $stmt->bind_param("s", $listing_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        return ['status' => 'error', 'message' => 'Product not found'];
    }

    $title = $product['title'];
    $limited_slot = (int)$product['limited_slot'];

    // ✅ Check if the same product + variation already exists in cart
    $stmt = $con->prepare("SELECT * FROM {$siteprefix}order_items WHERE order_id = ? AND listing_id = ? AND variation = ?");
    $stmt->bind_param("sss", $order_id, $listing_id, $variation);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    $newQuantity = $existing['quantity'] + $quantity;

    //  Prevent adding when out of stock
    if ($limited_slot <= 0) {
        return ['status' => 'error', 'message' => 'Out of stock'];
    }

    //  Prevent exceeding available stock
    if ($newQuantity > $limited_slot) {
        return ['status' => 'error', 'message' => 'Quantity exceeds available stock'];
    }

    // ✅ Update existing item
    $newTotal = $newQuantity * $price;
    $stmt = $con->prepare("UPDATE {$siteprefix}order_items 
                           SET quantity = ?, total_price = ? 
                           WHERE id = ?");
    $stmt->bind_param("dsi", $newQuantity, $newTotal, $existing['id']);
    $stmt->execute();

} else {
    // Prevent adding when out of stock
    if ($limited_slot <= 0) {
        return ['status' => 'error', 'message' => 'Out of stock'];
    }

    // Prevent exceeding available stock
    if ($quantity > $limited_slot) {
        return ['status' => 'error', 'message' => 'Quantity exceeds available stock'];
    }

    // ✅ Insert new item
    $total_price = $price * $quantity;
    $type = 'product';

    $stmt = $con->prepare("INSERT INTO {$siteprefix}order_items 
        (order_id, listing_id, user, variation, price, quantity, total_price, type, date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssddss", $order_id, $listing_id, $user_id, $variation, $price, $quantity, $total_price, $type);
    $stmt->execute();
}


    // ✅ Update total amount in orders
    $con->query("
        UPDATE {$siteprefix}orders 
        SET total_amount = (
            SELECT IFNULL(SUM(total_price), 0) 
            FROM {$siteprefix}order_items 
            WHERE order_id = '$order_id'
        )
        WHERE order_id = '$order_id'
    ");

    // ✅ Get updated cart count
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM {$siteprefix}order_items WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $countResult = $stmt->get_result()->fetch_assoc();
    $cartCount = $countResult['count'] ?? 0;

    // ✅ Return response
    return [
        'status' => 'success',
        'message' => 'Item added to cart successfully',
        'cartCount' => $cartCount
    ];
}

// Helper: get cart count
function getupdatedCartCount($con, $siteprefix, $order_id)
{
    $stmt = $con->prepare("
        SELECT COUNT(*) AS count 
        FROM {$siteprefix}order_items 
        WHERE order_id = ?
    ");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $c = $stmt->get_result()->fetch_assoc();

    return $c['count'] ?? 0;
}

function addEventToCartEndpoint($postData)
{
    global $con, $siteprefix;

    // Match JS keys (eventId, userId, orderId)
    $event_id  = mysqli_real_escape_string($con, trim($postData['eventId'] ?? ''));
    $user_id   = mysqli_real_escape_string($con, trim($postData['userId'] ?? ''));
    $order_id  = mysqli_real_escape_string($con, trim($postData['orderId'] ?? ''));
    $variation_ids_raw = trim($postData['variation_ids'] ?? '');

    $variation_ids = $variation_ids_raw ? explode(",", $variation_ids_raw) : [];

    // Validate
    if (empty($event_id) || empty($user_id) || empty($order_id)) {
        return ['status' => 'error', 'message' => 'Missing required parameters'];
    }

    // Fetch event
    $stmt = $con->prepare("SELECT title, pricing_type AS pricing FROM {$siteprefix}events WHERE event_id = ?");
    $stmt->bind_param("s", $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event) {
        return ['status' => 'error', 'message' => 'Event not found'];
    }

    $pricing = $event['pricing'];

    // FREE EVENT
    if ($pricing === "free") {

        $file_id = "free";

        $stmt = $con->prepare("
            SELECT COUNT(*) AS count 
            FROM {$siteprefix}order_items 
            WHERE order_id = ? AND event_id = ? AND item_id = ?");
        $stmt->bind_param("sss", $order_id, $event_id, $file_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc()['count'];

        if ($exists > 0) {
            return [
                'status' => 'success',
                'message' => 'Already added to cart',
                'cartCount' => getupdatedCartCount($con, $siteprefix, $order_id)
            ];
        }

        // Insert
        $stmt = $con->prepare("
            INSERT INTO {$siteprefix}order_items 
                (order_id, event_id, item_id, price, quantity, total_price, type, date)
            VALUES (?, ?, 'free', 0, 1, 0, 'event', NOW())");
        $stmt->bind_param("ss", $order_id, $event_id);
        $stmt->execute();

        return [
            'status' => 'success',
            'message' => 'Free event added to cart',
            'cartCount' => getupdatedCartCount($con, $siteprefix, $order_id)
        ];
    }

    // PAID EVENT VARIATIONS
    foreach ($variation_ids as $file_id_raw) {

        $file_id = mysqli_real_escape_string($con, trim($file_id_raw));
        if ($file_id == "") continue;

        // Check existing
        $stmt = $con->prepare("
            SELECT * FROM {$siteprefix}order_items 
            WHERE order_id = ? AND event_id = ? AND item_id = ?");
        $stmt->bind_param("sss", $order_id, $event_id, $file_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        // Get price
        $stmt = $con->prepare("
            SELECT price FROM {$siteprefix}event_tickets WHERE id = ? LIMIT 1");
        $stmt->bind_param("s", $file_id);
        $stmt->execute();
        $priceData = $stmt->get_result()->fetch_assoc();

        if (!$priceData) {
            continue;
        }

        $price = floatval($priceData['price']);
        $total = $price;

        if ($existing) {
            $stmt = $con->prepare("
                UPDATE {$siteprefix}order_items
                SET price = ?, total_price = ?
                WHERE id = ?");
            $stmt->bind_param("ddi", $price, $total, $existing['id']);
            $stmt->execute();
        } else {
            $stmt = $con->prepare("
                INSERT INTO {$siteprefix}order_items
                    (order_id, event_id, item_id,user, price, quantity, total_price, type, date)
                VALUES (?, ?, ?, ?, ?, 1, ?, 'event', NOW())");
            $stmt->bind_param("ssssdd", $order_id, $event_id, $file_id, $user_id, $price, $total);
            $stmt->execute();
        }
    }

    // Update total
    $con->query("
        UPDATE {$siteprefix}orders 
        SET total_amount = (
            SELECT IFNULL(SUM(total_price), 0) 
            FROM {$siteprefix}order_items 
            WHERE order_id = '$order_id'
        )
        WHERE order_id = '$order_id'
    ");

    return [
        'status' => 'success',
        'message' => 'Event added to cart successfully',
        'cartCount' => getupdatedCartCount($con, $siteprefix, $order_id)
    ];
}


function getbookingorderdata($con, $orderId) {
    global $siteprefix;

    // ✅ Sanitize input
    $orderId = mysqli_real_escape_string($con, $orderId);

    // ✅ Query only one order
    $query = "SELECT * FROM {$siteprefix}service_bookings WHERE order_id = '$orderId' LIMIT 1";
    $result = mysqli_query($con, $query);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}

function gettherapistbookingorderdata($con, $bookingId) {
    global $siteprefix;

    // ✅ Sanitize input
    $bookingId = mysqli_real_escape_string($con, $bookingId);
    // ✅ Query only one order
   $query = "
    SELECT b.id, b.reference, b.booking_status,b.payment_status, b.client_name, b.client_email, 
           b.therapist_id, u.first_name AS therapist_name, u.rate AS price 
    FROM {$siteprefix}bookings b
    LEFT JOIN {$siteprefix}users u ON b.therapist_id = u.id
    WHERE b.reference = '$bookingId'
    LIMIT 1
";
    $result = mysqli_query($con, $query);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : ['error' => mysqli_error($con)];
}




function bookServiceEndpoint($postData)
{
    global $con, $siteprefix, $siteName, $siteMail;

    // 🔒 Sanitize inputs
    $listing_id = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));
    $user_id    = mysqli_real_escape_string($con, trim($postData['user_id'] ?? ''));
    $order_id = 'ORD_' . strtoupper(bin2hex(random_bytes(4)));
    $price      = floatval($postData['price'] ?? 0);
    $full_name  = mysqli_real_escape_string($con, trim($postData['full_name'] ?? ''));
    $contact    = mysqli_real_escape_string($con, trim($postData['contact'] ?? ''));
    $email      = mysqli_real_escape_string($con, trim($postData['email'] ?? ''));
    $datetime   = mysqli_real_escape_string($con, trim($postData['datetime'] ?? ''));
    $location   = mysqli_real_escape_string($con, trim($postData['location'] ?? ''));
    $notes      = mysqli_real_escape_string($con, trim($postData['notes'] ?? ''));
    $variation  = mysqli_real_escape_string($con, trim($postData['variation'] ?? ''));
    $status = 'pending';
    $payment_status = 'unpaid';

    // ✅ Validate input
    if (empty($listing_id) || empty($user_id) || empty($order_id) || empty($datetime)) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Missing required booking details.", "red")
        ];
    }

    // ✅ Fetch service details
    $stmt = $con->prepare("SELECT title, limited_slot FROM {$siteprefix}listings WHERE listing_id = ?");
    $stmt->bind_param("s", $listing_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$service) {
        return [
            'status' => 'error',
            'messages' => generateMessage("Service not found.", "red")
        ];
    }

    $limited_slot = $service['limited_slot'];

    // ✅ Check limited slot availability (only if set)
    if ($limited_slot !== null && $limited_slot !== '' && is_numeric($limited_slot)) {

        $limited_slot = (int)$limited_slot;

        // Sold out
        if ($limited_slot <= 0) {
            return [
                'status' => 'error',
                'messages' => generateMessage("This service is sold out.", "red")
            ];
        }

        // Count only pending or approved bookings
        $stmt = $con->prepare("
            SELECT COUNT(*) AS booked 
            FROM {$siteprefix}service_bookings 
            WHERE listing_id = ? 
              AND status IN ('pending', 'approved')
        ");
        $stmt->bind_param("s", $listing_id);
        $stmt->execute();
        $bookedData = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $bookedCount = (int)($bookedData['booked'] ?? 0);

        if ($bookedCount >= $limited_slot) {
            return [
                'status' => 'error',
                'messages' => generateMessage("No available slot for this service.", "red")
            ];
        }

        // ✅ Decrease available slot immediately
        $stmt = $con->prepare("
            UPDATE {$siteprefix}listings 
            SET limited_slot = limited_slot - 1 
            WHERE listing_id = ? AND limited_slot > 0
        ");
        $stmt->bind_param("s", $listing_id);
        $stmt->execute();
        $stmt->close();
    }

    // ✅ Prevent duplicate active bookings (per variation)
    if (!empty($variation)) {
        $stmt = $con->prepare("
            SELECT id FROM {$siteprefix}service_bookings
            WHERE user_id = ? 
              AND listing_id = ? 
              AND variation = ?
              AND status IN ('pending', 'approved')
        ");
        $stmt->bind_param("sss", $user_id, $listing_id, $variation);
    } else {
        $stmt = $con->prepare("
            SELECT id FROM {$siteprefix}service_bookings
            WHERE user_id = ? 
              AND listing_id = ? 
              AND status IN ('pending', 'approved')
        ");
        $stmt->bind_param("ss", $user_id, $listing_id);
    }
    $stmt->execute();
    $existing = $stmt->get_result()->num_rows;
    $stmt->close();

    if ($existing > 0) {
        return [
            'status' => 'error',
            'messages' => generateMessage("You already have an active booking for this service variation.", "red")
        ];
    }

    // ✅ Insert into service_bookings
    $stmt = $con->prepare("INSERT INTO {$siteprefix}service_bookings 
        (order_id, listing_id, variation, user_id, full_name, contact, email, preferred_datetime, location, notes, price, date, status, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param(
        "sssssssssdsss",
        $order_id,
        $listing_id,
        $variation,
        $user_id,
        $full_name,
        $contact,
        $email,
        $datetime,
        $location,
        $notes,
        $price,
        $status,
        $payment_status
    );
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        $stmt->close();
        return [
            'status' => 'error',
            'messages' => generateMessage("Failed to save booking. Please try again.", "red")
        ];
    }
    $stmt->close();

    // ✅ Insert into order_items (type = 'service')
    $quantity = 1;
    $total_price = $price * $quantity;
    $type = 'service';

    $stmt = $con->prepare("INSERT INTO {$siteprefix}order_items 
        (order_id, listing_id, user, variation, price, quantity, total_price, type, date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param(
        "ssssddss",
        $order_id,
        $listing_id,
        $user_id,
        $variation,
        $price,
        $quantity,
        $total_price,
        $type
    );
    $stmt->execute();
    $stmt->close();

        // ✅ Insert into ma_orders (status = unpaid)
    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}orders (order_id, user, status, total_amount, date)
        VALUES (?, ?, 'unpaid', ?, NOW())
    ");
    $stmt->bind_param("ssd", $order_id, $user_id, $total_price);
    $stmt->execute();
    $stmt->close();
    

    // ✅ Get seller details (owner of listing)
    $stmt = $con->prepare("
        SELECT u.first_name, u.email, l.title 
        FROM {$siteprefix}listings AS l
        LEFT JOIN {$siteprefix}users AS u ON l.user_id = u.id
        WHERE l.listing_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $listing_id);
    $stmt->execute();
    $seller = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // ✅ Notify seller
    if ($seller && !empty($seller['email'])) {
        $sellerFirstName = htmlspecialchars($seller['first_name']);
        $sellerEmail = htmlspecialchars($seller['email']);
        $serviceTitle = htmlspecialchars($seller['title']);

        $emailSubject = "New Booking Request for {$serviceTitle}";
        $emailMessage = "
           
            You have received a new booking request for your service <strong>{$serviceTitle}</strong>.<br>
            Please log in to your dashboard to view the details and approve or reject the request.<br><br>
            <strong>Booking Details:</strong><br>
            Name: {$full_name}<br>
            Contact: {$contact}<br>
            Preferred Date/Time: {$datetime}<br>
            Location: {$location} ";

        sendEmail($sellerEmail, $siteName, $siteMail, $sellerFirstName, $emailMessage, $emailSubject);
    }

    return [
        'status' => 'success',
        'messages' => generateMessage(
            "Service booked successfully! The seller has been notified.",
            "green"
        )
    ];
}


function getCartItems($order_id) {
    global $con, $siteprefix;
    $items = [];

    if (empty($order_id)) {
        return ['error' => 'Missing order_id'];
    }

    // Fetch all order items for products and events
    $sql = "
        SELECT * 
        FROM {$siteprefix}order_items
        WHERE order_id = '".mysqli_real_escape_string($con, $order_id)."'
          AND type IN ('product','event')
    ";

    $query = mysqli_query($con, $sql);
    if (!$query) return ['error' => mysqli_error($con)];

    while ($row = mysqli_fetch_assoc($query)) {

        // PRODUCT
        if (!empty($row['listing_id']) && $row['type'] === 'product') {
            // Fetch product details
            $prodSql = "
                SELECT l.title AS listing_title, l.slug,
                       u.first_name, u.last_name,
                       (SELECT file_name FROM {$siteprefix}listing_images AS li 
                        WHERE li.listing_id = l.listing_id 
                        ORDER BY li.id ASC LIMIT 1) AS main_image
                FROM {$siteprefix}listings AS l
                LEFT JOIN {$siteprefix}users AS u ON l.user_id = u.id
                WHERE l.listing_id = '".mysqli_real_escape_string($con, $row['listing_id'])."'
                LIMIT 1
            ";
            $prodResult = mysqli_query($con, $prodSql);
            if ($prodResult && $prodData = mysqli_fetch_assoc($prodResult)) {
                $row['listing_title'] = $prodData['listing_title'];
                $row['slug'] = $prodData['slug'];
                $row['main_image'] = $prodData['main_image'] ?? '';
                $row['variation'] = $row['variation'] ?? '—';
                // Use price from order_items
                $row['price'] = $row['price'] ?? 0;
            }

        // EVENT
        } elseif (!empty($row['event_id']) && $row['type'] === 'event') {
            $event_id = $row['event_id'];
            $ticket_id = $row['item_id']; // may be number or "free"

            // Get event title & slug
            $eventSql = "SELECT title, slug FROM {$siteprefix}events 
                         WHERE event_id = '".mysqli_real_escape_string($con, $event_id)."' 
                         LIMIT 1";
            $eventResult = mysqli_query($con, $eventSql);
            $eventData = mysqli_fetch_assoc($eventResult);
            $row['listing_title'] = $eventData['title'] ?? 'Event';
            $row['slug'] = $eventData['slug'] ?? '#';

            // Get event image
            $imgSql = "SELECT image_path FROM {$siteprefix}events_images 
                       WHERE event_id = '".mysqli_real_escape_string($con, $event_id)."' 
                       ORDER BY id ASC LIMIT 1";
            $imgResult = mysqli_query($con, $imgSql);
            $imgData = mysqli_fetch_assoc($imgResult);
            $row['main_image'] = $imgData['image_path'] ?? '';

            // Ticket details
            if (is_numeric($ticket_id)) {
                $ticketSql = "SELECT ticket_name 
                              FROM {$siteprefix}event_tickets 
                              WHERE id = '".mysqli_real_escape_string($con, $ticket_id)."' 
                              LIMIT 1";
                $ticketResult = mysqli_query($con, $ticketSql);
                $ticketData = mysqli_fetch_assoc($ticketResult);
                if ($ticketData) {
                    $row['variation'] = $ticketData['ticket_name'];
                } else {
                    $row['variation'] = 'Ticket';
                }
            } else {
                // Free ticket
                $row['variation'] = 'Free';
            }

            // Use price from order_items (important)
            $row['price'] = $row['price'] ?? 0;

            // Events have fixed quantity
            $row['quantity'] = 1;
        }

        $items[] = $row;
    }

    return $items;
}




function getAllCategories($con, $siteprefix)
{
    $categories = [];

    // Fetch parent categories
    $catSql = "SELECT * FROM {$siteprefix}categories WHERE parent_id IS NULL ORDER BY category_name ASC";
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
                'slug' => $catRow['slug'],

                'post_count' => $count
            ];
        }
    }

    return $categories;
}


function bulkUpdateCartEndpoint($postData)
{
    global $con, $siteprefix;

    $itemsData = json_decode($postData['items'] ?? '[]', true);
    if (empty($itemsData)) {
        return ['status' => 'error', 'message' => 'No items provided'];
    }

    $updatedCount = 0;
    $order_id = '';
    $errors = [];

    foreach ($itemsData as $item) {
        $item_id = intval($item['item_id']);
        $quantity = intval($item['quantity']);

        // 🔹 Fetch item details with limited_slot from ma_listings
        $query = "
            SELECT oi.*, ml.limited_slot 
            FROM {$siteprefix}order_items oi
            LEFT JOIN {$siteprefix}listings ml ON oi.listing_id = ml.listing_id
            WHERE oi.id = '$item_id' AND oi.type = 'product'
        ";
        $check = mysqli_query($con, $query);
        $existing = mysqli_fetch_assoc($check);

        if (!$existing) {
            $errors[] = "Item #$item_id not found.";
            continue;
        }

        $order_id = $existing['order_id'];
        $price = (float)$existing['price'];
        $limited_slot = intval($existing['limited_slot'] ?? 0);

   // 🔸 Prevent out of stock or exceeding stock
        if ($limited_slot <= 0) {
            $errors[] = "Item '{$existing['listing_title']}' is out of stock.";
            continue; // ❌ skip this item — do not update
        }

        if ($quantity > $limited_slot) {
            $errors[] = "Item '{$existing['listing_title']}' exceeds available stock ({$limited_slot}).";
            continue; // ❌ skip this item — do not update
        }

        $total = $price * $quantity;

        // 🔹 Update item
        $update = mysqli_query($con, "
            UPDATE {$siteprefix}order_items 
            SET quantity = '$quantity', total_price = '$total' 
            WHERE id = '$item_id' AND type = 'product'
        ");

        if ($update) $updatedCount++;
        else $errors[] = "Failed to update item #$item_id.";
    }

    // 🔹 Update overall order total if at least one valid update
    if (!empty($order_id) && $updatedCount > 0) {
        $query = mysqli_query($con, "
            SELECT COALESCE(SUM(total_price), 0) AS total 
            FROM {$siteprefix}order_items 
            WHERE order_id = '$order_id' AND type = 'product'
        ");
        $row = mysqli_fetch_assoc($query);
        $total = $row['total'] ?? 0;

        mysqli_query($con, "
            UPDATE {$siteprefix}orders 
            SET total_amount = '$total' 
            WHERE order_id = '$order_id'
        ");
    }

    // 🔹 Final response
    if (!empty($errors)) {
        return [
            'status' => 'error',
            'message' => implode(' ', $errors),
            'updated' => $updatedCount
        ];
    }

    return [
        'status' => 'success',
        'message' => "$updatedCount item(s) updated successfully",
        'updated' => $updatedCount
    ];
}


function reuploadPaymentProof($postData, $fileData) {
    global $con, $siteprefix, $siteMail, $siteName;

    $order_id = mysqli_real_escape_string($con, trim($postData['order_id'] ?? ''));
    $user_id  = mysqli_real_escape_string($con, trim($postData['user_id'] ?? ''));
    $date     = date('Y-m-d H:i:s');

    if (empty($order_id) || empty($user_id)) {
        return ['status' => 'error', 'messages' => generateMessage("Order ID and User ID are required.", "red")];
    }

    if (empty($fileData['proof']['name'])) {
        return ['status' => 'error', 'messages' => generateMessage("Please upload a proof of payment.", "red")];
    }

    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileType = mime_content_type($fileData["proof"]["tmp_name"]);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    if (!in_array($fileType, $allowedTypes)) {
        return ['status' => 'error', 'messages' => generateMessage("Invalid file type.", "red")];
    }

    $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($fileData["proof"]["name"]));
    $proofFile = uniqid('proof_') . '_' . $safeName;
    move_uploaded_file($fileData["proof"]["tmp_name"], $uploadDir . $proofFile);

    $updateQuery = "
        UPDATE {$siteprefix}manual_payments
        SET proof='$proofFile', status='pending', date_created='$date'
        WHERE order_id='$order_id' AND user_id='$user_id'
    ";
    if (mysqli_query($con, $updateQuery)) {
        // Email to admin
        $admin_email = $siteMail;
        $user_query = mysqli_query($con, "SELECT * FROM {$siteprefix}users WHERE id='$user_id'");
        $user = mysqli_fetch_assoc($user_query);
        $user_name = $user['first_name'].' '.$user['last_name'];

        $subject = "Manual Payment Proof Reuploaded";
        $message = "
            <p>User reuploaded manual payment proof.</p>
            <p><strong>Order ID:</strong> $order_id</p>
            <p><strong>User:</strong> $user_name ({$user['email']})</p>
            <p><strong>Date:</strong> $date</p>
        ";
        sendEmail($admin_email, "Admin", $siteName, $siteMail, $message, $subject);

        // Admin alert
        insertadminAlert($con, "User reuploaded payment proof for Order ID $order_id", "pending-orders", $date, "manual_payment", "unread");

        return ['status' => 'success', 'messages' => generateMessage("Payment proof reuploaded successfully!", "green")];
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
}


function manualPaymentEndpoint($postData, $fileData) {
    global $con, $siteprefix, $siteMail, $siteName, $sitecurrencyCode;

    // Sanitize inputs
    $order_id = mysqli_real_escape_string($con, trim($postData['order_id'] ?? ''));
    $user_id  = mysqli_real_escape_string($con, trim($postData['user_id'] ?? ''));
    $amount   = mysqli_real_escape_string($con, trim($postData['amount'] ?? ''));
    $date     = date('Y-m-d H:i:s');

    // Validation
    if (empty($order_id) || empty($user_id) || empty($amount)) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    if (!is_numeric($amount)) {
        return ['status' => 'error', 'messages' => generateMessage("Amount must be a number.", "red")];
    }

    // Handle proof of payment upload
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $proofFile = "";
    if (!empty($fileData["proof_of_payment"]["name"])) {
        $fileType = mime_content_type($fileData["proof_of_payment"]["tmp_name"]);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            return ['status' => 'error', 'messages' => generateMessage("Invalid file type. Please upload JPG, PNG, GIF, WEBP, or PDF.", "red")];
        }

        $fileName = basename($fileData["proof_of_payment"]["name"]);
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $proofFile = uniqid('proof_') . '_' . $safeName;

        move_uploaded_file($fileData["proof_of_payment"]["tmp_name"], $uploadDir . $proofFile);
    } else {
        return ['status' => 'error', 'messages' => generateMessage("Please upload a proof of payment.", "red")];
    }

    // Insert into manual_payments table
    $insertQuery = "
        INSERT INTO {$siteprefix}manual_payments
        (order_id, user_id, amount, proof, status, date_created, rejection_reason)
        VALUES
        ('$order_id', '$user_id', '$amount', '$proofFile', 'pending', '$date', '')
    ";

    if (mysqli_query($con, $insertQuery)) {

        // Update order status to 'inprogress'
        $updateOrder = "UPDATE {$siteprefix}orders SET status = 'inprogress' WHERE order_id = '$order_id'";
        mysqli_query($con, $updateOrder);

        // Fetch all items in the order
        $detailsQuery = "SELECT listing_id, event_id, item_id AS ticket_id, quantity 
                         FROM {$siteprefix}order_items 
                         WHERE order_id = '$order_id'";
        $detailsResult = mysqli_query($con, $detailsQuery);

      while ($detail = mysqli_fetch_assoc($detailsResult)) {
    $listing_id = $detail['listing_id'];
    $quantity   = intval($detail['quantity']);
    $event_id   = $detail['event_id'];
    $ticket_id  = $detail['ticket_id'];

    // Only update if listing_id and quantity are valid
    if (!empty($listing_id) && $quantity > 0) {
        $updateListing = "
            UPDATE {$siteprefix}listings
            SET limited_slot = GREATEST(limited_slot - $quantity, 0)
            WHERE listing_id = '$listing_id'
        ";

        if (!mysqli_query($con, $updateListing)) {
            error_log("Failed to update listing $listing_id: " . mysqli_error($con));
        }
    }

    // Reduce event seats ONLY if it's an event ticket
    if (!empty($event_id) && !empty($ticket_id) && $quantity > 0) {
        for ($i = 0; $i < $quantity; $i++) {
            reduceEventSeat($con, $siteprefix, $event_id, $ticket_id);
        }
    }
}


        // Fetch admin info
        $admin_email = $siteMail;
        $admin_name  = "Admin";

        // Fetch user details
        $user_query = "SELECT * FROM {$siteprefix}users WHERE id = '$user_id'";
        $user_result = mysqli_query($con, $user_query);

        if ($user_result && mysqli_num_rows($user_result) > 0) {
            $user = mysqli_fetch_assoc($user_result);
            $user_name  = $user['first_name'] . ' ' . $user['last_name'];
            $user_email = $user['email'];
        } else {
            $user_name  = "Unknown User";
            $user_email = "Unknown Email";
        }

        // Prepare email to admin
        $emailSubject = "New Manual Payment Submitted";
        $emailMessage = "
            <p>A new manual payment has been submitted:</p>
            <p><strong>Order ID:</strong> $order_id</p>
            <p><strong>User:</strong> $user_name ($user_email)</p>
            <p><strong>Amount:</strong> {$sitecurrencyCode}" . formatNumber($amount, 2) . "</p>
            <p><strong>Date:</strong> $date</p>
            <p>Please log in to the admin panel to verify the payment.</p>
        ";

        sendEmail($admin_email, $admin_name, $siteName, $siteMail, $emailMessage, $emailSubject);

        return ['status' => 'success', 'messages' => generateMessage("Proof of payment submitted successfully!", "green")];

    } else {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }
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


function creategroupQuestionEndpoint($postData)
{
    global $con, $siteprefix, $siteName, $siteMail, $siteurl;

    // 🧹 Sanitize input
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $group_id    = intval($postData['group_id'] ?? 0);
    $user        = intval($postData['user'] ?? 0);
    $status      = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $anonymous   = isset($postData['anonymous']) ? 1 : 0;

    // ✅ Validation
    if (empty($title) || empty($article) || $user == 0 || $group_id == 0) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ✅ Generate base slug
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

        if ($row['count'] == 0) break;

        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // ✅ Insert question
    $query = "
        INSERT INTO {$siteprefix}questions 
        (user_id, title, slug, article, categories, subcategories, tags, anonymous, created_at, status, group_id)
        VALUES 
        ('$user', '$title', '$slug', '$article', '$category', '$subcategory', '$tags', '$anonymous', NOW(), '$status', '$group_id')
    ";

    if (!mysqli_query($con, $query)) {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }

    $questionId = mysqli_insert_id($con);
    $date = date("Y-m-d H:i:s");

    // ✅ Get group info
    $groupQ = mysqli_query($con, "SELECT user_id, group_name, slug FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    $groupData = $groupQ ? mysqli_fetch_assoc($groupQ) : null;
    $groupCreatorId = $groupData['user_id'] ?? 0;
    $group_name     = $groupData['group_name'] ?? '';
    $group_slug     = $groupData['slug'] ?? '';
    $groupUrl       = $siteurl . 'group/' . $group_slug;

    // ✅ Get poster info
    $posterQ = mysqli_query($con, "SELECT email, first_name, last_name FROM {$siteprefix}users WHERE id='$user' LIMIT 1");
    $posterData = $posterQ ? mysqli_fetch_assoc($posterQ) : [];
    $posterEmail = $posterData['email'] ?? '';
    $posterName  = $posterData['first_name'] ?? '';

    // ============================
    // CASE 1: Active Question
    // ============================
    if ($status === "active") {

        // Notify all active group members
        $membersQ = mysqli_query($con, "
            SELECT gm.user_id, u.email, u.first_name, u.last_name
            FROM {$siteprefix}group_members gm
            JOIN {$siteprefix}users u ON gm.user_id = u.id
            WHERE gm.group_id='$group_id' AND gm.status='active'
        ");

        if ($membersQ) {
            while ($m = mysqli_fetch_assoc($membersQ)) {
                $memberEmail = $m['email'] ?? '';
                $firstName   = $m['first_name'] ?? '';

                $emailSubject = "New Question Posted in Group";
                $emailMessage = "
                    A new question has been posted in <b>" . htmlspecialchars($group_name) . "</b>: <b>" . htmlspecialchars($title) . "</b>.<br><br>
                    <a href=\"" . htmlspecialchars($groupUrl) . "\">View the question in the group</a>
                ";

                if (!empty($memberEmail)) sendEmail($memberEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

                $alertMessage = "A new question titled '" . addslashes($title) . "' was posted.";
                insertAlert($con, $m['user_id'], $alertMessage, $date, 0);
            }
        }

        // Notify group creator
        if ($groupCreatorId) {
            $creatorQ = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$groupCreatorId' LIMIT 1");
            if ($creatorQ && $creator = mysqli_fetch_assoc($creatorQ)) {
                $creatorEmail = $creator['email'] ?? '';
                $creatorName  = $creator['first_name'] ?? '';

                if (!empty($creatorEmail)) {
                    $subject = "New Question in Your Group";
                    $message = "
                        A new question was posted in your group <b>" . htmlspecialchars($group_name) . "</b>: <b>" . htmlspecialchars($title) . "</b>.<br><br>
                        <a href=\"" . htmlspecialchars($groupUrl) . "\">View the question</a>
                    ";
                    sendEmail($creatorEmail, $siteName, $siteMail, $creatorName, $message, $subject);
                }

                $alertMessage = "A new question titled '" . addslashes($title) . "' was posted in your group.";
                insertAlert($con, $groupCreatorId, $alertMessage, $date, 0);
            }
        }

        // Notify poster
        if (!empty($posterEmail)) {
            $subject = "Your Question Published";
            $message = "
                Your question <b>" . htmlspecialchars($title) . "</b> has been published successfully in <b>" . htmlspecialchars($group_name) . "</b>.<br><br>
                <a href=\"" . htmlspecialchars($groupUrl) . "\">View the question</a>
            ";
            sendEmail($posterEmail, $siteName, $siteMail, $posterName, $message, $subject);
        }
    }

    // ============================
    // CASE 2: Pending Question
    // ============================
    else if ($status === "pending") {
        // Get poster role
        $roleQ = mysqli_query($con, "
            SELECT role FROM {$siteprefix}group_members 
            WHERE group_id='$group_id' AND user_id='$user' LIMIT 1
        ");
        $roleRow = $roleQ ? mysqli_fetch_assoc($roleQ) : null;
        $userRole = strtolower($roleRow['role'] ?? "");

        // Only notify admins/subadmins/creator if poster is not admin/subadmin/creator
        if (!in_array($userRole, ['admin','subadmin']) && $user != $groupCreatorId) {

            // Notify group admins/subadmins
            $adminQ = mysqli_query($con, "
                SELECT u.id, u.email, u.first_name 
                FROM {$siteprefix}group_members gm
                JOIN {$siteprefix}users u ON gm.user_id = u.id
                WHERE gm.group_id='$group_id' AND gm.role IN ('admin','subadmin')
            ");

            if ($adminQ) {
                while ($a = mysqli_fetch_assoc($adminQ)) {
                    $adminEmail = $a['email'] ?? '';
                    $adminName  = $a['first_name'] ?? '';
                    $adminId    = $a['id'] ?? 0;

                    $emailSubject = "Question Pending Approval";
                    $emailMessage = "
                        Hello " . htmlspecialchars($adminName) . ",<br><br>
                        A question titled <b>" . htmlspecialchars($title) . "</b> was submitted and is awaiting approval for the group <b>" . htmlspecialchars($group_name) . "</b>.<br><br>
                        <a href=\"" . htmlspecialchars($groupUrl) . "\">Review the pending question</a>
                    ";
                    if (!empty($adminEmail)) sendEmail($adminEmail, $siteName, $siteMail, $adminName, $emailMessage, $emailSubject);

                    $alertMessage = "Question '" . addslashes($title) . "' is pending approval.";
                    insertAlert($con, $adminId, $alertMessage, $date, 0);
                }
            }

            // Notify group creator
            if ($groupCreatorId) {
                $creatorQ = mysqli_query($con, "SELECT id, email, first_name FROM {$siteprefix}users WHERE id='$groupCreatorId' LIMIT 1");
                if ($creatorQ && $creator = mysqli_fetch_assoc($creatorQ)) {
                    $creatorEmail = $creator['email'] ?? '';
                    $creatorName  = $creator['first_name'] ?? '';
                    $creatorId    = $creator['id'] ?? $groupCreatorId;

                    $emailSubject = "Question Pending Approval";
                    $emailMessage = "
                        Hello " . htmlspecialchars($creatorName) . ",<br><br>
                        A question titled <b>" . htmlspecialchars($title) . "</b> was submitted in your group <b>" . htmlspecialchars($group_name) . "</b> and is awaiting approval.<br><br>
                        <a href=\"" . htmlspecialchars($groupUrl) . "\">Review the pending question</a>
                    ";
                    if (!empty($creatorEmail)) sendEmail($creatorEmail, $siteName, $siteMail, $creatorName, $emailMessage, $emailSubject);

                    $alertMessage = "Question '" . addslashes($title) . "' is pending approval.";
                    insertAlert($con, $creatorId, $alertMessage, $date, 0);
                }
            }
        }
    }

    // ✅ Final response
    return ['status' => 'success', 'messages' => generateMessage("Your question has been submitted successfully!", "green")];
}


function updateGroupQuestionEndpoint($postData)
{
    global $con, $siteprefix, $siteName, $siteMail, $siteurl;

    // 🧹 Sanitize input
    $question_id = intval($postData['question_id'] ?? 0);
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $group_id    = intval($postData['group_id'] ?? 0);
    $user        = intval($postData['user'] ?? 0);
    $status      = mysqli_real_escape_string($con, trim($postData['status'] ?? 'pending'));
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';
    $anonymous   = isset($postData['anonymous']) ? 1 : 0;

    // 🔒 Validation
    if ($question_id == 0 || empty($title) || empty($article) || $user == 0 || $group_id == 0) {
        return ['status' => 'error', 'messages' => generateMessage("All required fields must be filled.", "red")];
    }

    // ============================
    // GET OLD QUESTION DATA
    // ============================
    $oldQ = mysqli_query($con, "
        SELECT slug, title, status 
        FROM {$siteprefix}questions 
        WHERE id='$question_id' LIMIT 1
    ");

    if (!$oldQ || mysqli_num_rows($oldQ) == 0) {
        return ['status' => 'error', 'messages' => generateMessage("Question not found.", "red")];
    }

    $oldData       = mysqli_fetch_assoc($oldQ);
    $oldSlug       = $oldData['slug'];
    $oldTitle      = $oldData['title'];
    $oldStatus     = $oldData['status'];

    // ============================
    // SLUG UPDATE ONLY IF TITLE CHANGED
    // ============================
    if ($oldTitle !== $title) {

        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $queryCheck = "SELECT COUNT(*) AS count FROM {$siteprefix}questions WHERE slug = ? AND id != ?";
            $stmt = $con->prepare($queryCheck);
            $stmt->bind_param("si", $slug, $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] == 0) break;

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

    } else {
        $slug = $oldSlug;
    }

    // ============================
    // UPDATE QUESTION
    // ============================
    $query = "
        UPDATE {$siteprefix}questions SET 
            title='$title',
            slug='$slug',
            article='$article',
            categories='$category',
            subcategories='$subcategory',
            tags='$tags',
            anonymous='$anonymous',
            status='$status',
            group_id='$group_id'
        WHERE id='$question_id'
    ";

    if (!mysqli_query($con, $query)) {
        return ['status' => 'error', 'messages' => generateMessage("Database error: " . mysqli_error($con), "red")];
    }

    $date = date("Y-m-d H:i:s");

    // ============================
    // GET GROUP INFO
    // ============================
    $groupQ = mysqli_query($con, "SELECT user_id, group_name, slug FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    $groupData = $groupQ ? mysqli_fetch_assoc($groupQ) : null;

    $groupCreatorId = $groupData['user_id'] ?? 0;
    $group_name     = $groupData['group_name'] ?? '';
    $group_slug     = $groupData['slug'] ?? '';
    $groupUrl       = $siteurl . 'group/' . $group_slug;

    // ============================
    // GET POSTER INFO
    // ============================
    $posterQ = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$user' LIMIT 1");
    $posterData = mysqli_fetch_assoc($posterQ);
    $posterEmail = $posterData['email'] ?? '';
    $posterName  = $posterData['first_name'] ?? '';

    // ============================
    // STATUS = ACTIVE
    // ============================
    if ($status === "active" && $oldStatus !== "active") {

        // Notify all active group members
        $membersQ = mysqli_query($con, "
            SELECT gm.user_id, u.email, u.first_name
            FROM {$siteprefix}group_members gm
            JOIN {$siteprefix}users u ON gm.user_id = u.id
            WHERE gm.group_id='$group_id' AND gm.status='active'
        ");

        while ($m = mysqli_fetch_assoc($membersQ)) {
            $memberEmail = $m['email'] ?? '';
            $firstName   = $m['first_name'] ?? '';

            $subject = "Updated Question in Group";
            $message = "
                A question titled <b>" . htmlspecialchars($title) . "</b> was updated in <b>" . htmlspecialchars($group_name) . "</b>.<br><br>
                <a href=\"$groupUrl\">View the question</a>
            ";

            if (!empty($memberEmail)) {
                sendEmail($memberEmail, $siteName, $siteMail, $firstName, $message, $subject);
            }

            insertAlert($con, $m['user_id'], "A question '" . addslashes($title) . "' was updated.", $date, 0);
        }
    }

    // ============================
    // STATUS = PENDING
    // ============================
    if ($status === "pending") {

        // Get poster role
        $roleQ = mysqli_query($con, "
            SELECT role FROM {$siteprefix}group_members 
            WHERE group_id='$group_id' AND user_id='$user' LIMIT 1
        ");
        $roleData = mysqli_fetch_assoc($roleQ);
        $userRole = strtolower($roleData['role'] ?? '');

        // Only notify admins/subadmins/creator if poster is NOT admin/subadmin
        if (!in_array($userRole, ['admin','subadmin']) && $user != $groupCreatorId) {

            // Notify admins/subadmins
            $adminQ = mysqli_query($con, "
                SELECT u.id, u.email, u.first_name
                FROM {$siteprefix}group_members gm
                JOIN {$siteprefix}users u ON gm.user_id = u.id
                WHERE gm.group_id='$group_id' AND gm.role IN ('admin','subadmin')
            ");

            while ($a = mysqli_fetch_assoc($adminQ)) {
                $adminEmail = $a['email'];
                $adminName  = $a['first_name'];
                $adminId    = $a['id'];

                $subject = "Updated Question Pending Approval";
                $message = "
                    A question titled <b>" . htmlspecialchars($title) . "</b> was updated and requires approval in <b>" . htmlspecialchars($group_name) . "</b>.<br><br>
                    <a href=\"$groupUrl\">Review the question</a>
                ";

                if (!empty($adminEmail)) {
                    sendEmail($adminEmail, $siteName, $siteMail, $adminName, $message, $subject);
                }

                insertAlert($con, $adminId, "Updated question '" . addslashes($title) . "' is pending approval.", $date, 0);
            }

            // Notify creator
            if ($groupCreatorId) {
                $creatorQ = mysqli_query($con, "SELECT id, email, first_name FROM {$siteprefix}users WHERE id='$groupCreatorId' LIMIT 1");
                $creator = mysqli_fetch_assoc($creatorQ);

                $creatorEmail = $creator['email'];
                $creatorName  = $creator['first_name'];
                $creatorId    = $creator['id'];

                $subject = "Updated Question Pending Approval";
                $message = "
                    A question titled <b>" . htmlspecialchars($title) . "</b> was updated and requires approval in your group <b>" . htmlspecialchars($group_name) . "</b>.<br><br>
                    <a href=\"$groupUrl\">Review the question</a>
                ";

                if (!empty($creatorEmail)) {
                    sendEmail($creatorEmail, $siteName, $siteMail, $creatorName, $message, $subject);
                }

                insertAlert($con, $creatorId, "Updated question '" . addslashes($title) . "' is pending approval.", $date, 0);
            }
        }
    }

    // ============================
    // FINAL RESPONSE
    // ============================
    return ['status' => 'success', 'messages' => generateMessage("Your question has been updated successfully!", "green")];
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

function removeCartItemEndpoint($postData)
{
    global $con, $siteprefix;

    $item_id = trim($postData['item_id'] ?? '');

    if (empty($item_id)) {
        return ['status' => 'error', 'message' => 'Missing item ID'];
    }

    // Step 1: Get order_id of item being removed
    $stmt = $con->prepare("SELECT order_id FROM {$siteprefix}order_items WHERE id = ? ");
    if (!$stmt) {
        return ['status' => 'error', 'message' => 'Query prepare failed: ' . $con->error];
    }

    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_item = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$cart_item) {
        return ['status' => 'error', 'message' => 'Cart item not found'];
    }

    $order_id = $cart_item['order_id'];

    // Step 2: Delete the item
    $stmt = $con->prepare("DELETE FROM {$siteprefix}order_items WHERE id = ?");
    if (!$stmt) {
        return ['status' => 'error', 'message' => 'Delete prepare failed: ' . $con->error];
    }

    $stmt->bind_param("s", $item_id);
    $deleted = $stmt->execute();
    $stmt->close();

    if (!$deleted) {
        return ['status' => 'error', 'message' => 'Failed to delete item: ' . $con->error];
    }

    // Step 3: Recalculate cart count
    $stmt = $con->prepare("SELECT COUNT(*) AS count FROM {$siteprefix}order_items WHERE order_id = ? AND type = 'product'");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $cartCount = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    $stmt->close();

    // Step 4: Recalculate total
    $stmt = $con->prepare("SELECT COALESCE(SUM(total_price), 0) AS total FROM {$siteprefix}order_items WHERE order_id = ? AND type = 'product'");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Step 5: Update order total
    $stmt = $con->prepare("UPDATE {$siteprefix}orders SET total_amount = ? WHERE order_id = ?");
    $stmt->bind_param("ds", $total, $order_id);
    $stmt->execute();
    $stmt->close();

    return [
        'status' => 'success',
        'message' => 'Item removed successfully',
        'cartCount' => (int)$cartCount,
        'total' => formatNumber($total, 2)
    ];
}



function addToWishlistEndpoint($postData)
{
    global $con, $siteprefix;

    $listing_id = mysqli_real_escape_string($con, trim($postData['listing_id'] ?? ''));
    $user_id    = mysqli_real_escape_string($con, trim($postData['user_id'] ?? ''));

    if (empty($listing_id) || empty($user_id)) {
        return ['status' => 'error', 'message' => 'Missing required parameters'];
    }

    $stmt = $con->prepare("SELECT id FROM {$siteprefix}wishlist WHERE user_id = ? AND listing_id = ?");
    $stmt->bind_param("ss", $user_id, $listing_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();

    if ($exists) {
        $stmt = $con->prepare("DELETE FROM {$siteprefix}wishlist WHERE user_id = ? AND listing_id = ?");
        $stmt->bind_param("ss", $user_id, $listing_id);
        $stmt->execute();
        return ['status' => 'removed', 'message' => 'Item removed from wishlist'];
    } else {
        $stmt = $con->prepare("INSERT INTO {$siteprefix}wishlist (user_id, listing_id, date_added) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $user_id, $listing_id);
        $stmt->execute();
        return ['status' => 'success', 'message' => 'Item added to wishlist'];
    }
}

function checkWishlistStatus($user_id, $listing_id)
{
    global $con, $siteprefix;

    if (empty($user_id) || empty($listing_id)) {
        return ['status' => 'error', 'message' => 'Missing parameters'];
    }

    $stmt = $con->prepare("SELECT id FROM {$siteprefix}wishlist WHERE user_id = ? AND listing_id = ?");
    $stmt->bind_param("ss", $user_id, $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return [
        'status' => 'success',
        'isWishlisted' => $result->num_rows > 0
    ];
}

function addForumEndpoint($postData, $fileData)
{
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // 🧹 Sanitize inputs
    $title       = mysqli_real_escape_string($con, trim($postData['title'] ?? ''));
    $article     = mysqli_real_escape_string($con, trim($postData['article'] ?? ''));
    $tags        = mysqli_real_escape_string($con, trim($postData['tags'] ?? ''));
    $user        = intval($postData['user'] ?? 0);
    $status      = 'pending'; // Always pending
    $category    = isset($postData['category']) && is_array($postData['category']) ? implode(',', $postData['category']) : '';
    $subcategory = isset($postData['subcategory']) && is_array($postData['subcategory']) ? implode(',', $postData['subcategory']) : '';

    // ✅ Validation
    if (empty($title) || empty($article) || $user == 0) {
        $messages = generateMessage("All required fields must be filled.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ✅ Fetch author name
    $authorQuery = $con->prepare("SELECT first_name, last_name FROM {$siteprefix}users WHERE id = ?");
    $authorQuery->bind_param("i", $user);
    $authorQuery->execute();
    $authorResult = $authorQuery->get_result();
    $author = $authorResult->fetch_assoc();
    $authorName = !empty($author) ? trim($author['first_name'] . ' ' . $author['last_name']) : 'Unknown User';

    // ✅ Create unique slug
    $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $alt_title = $baseSlug;
    $counter = 1;

    while (true) {
        $query = "SELECT COUNT(*) AS count FROM {$siteprefix}forums WHERE slug = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $alt_title);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            break;
        }

        $alt_title = $baseSlug . '-' . $counter;
        $counter++;
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
        return [
            'status' => 'error',
            'messages' => generateMessage("Duplicate submission detected. Please wait a few seconds before trying again.", "red")
        ];
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

    // ✅ Insert into forums table
    $stmt = $con->prepare("
        INSERT INTO {$siteprefix}forums 
        (user_id, title, article, featured_image, tags, categories, subcategories, status, created_at, slug, views)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 0)
    ");

    if (!$stmt) {
        return ['status' => 'error', 'messages' => 'Database error: ' . $con->error];
    }

    $stmt->bind_param("issssssss", $user, $title, $article, $featuredImage, $tags, $category, $subcategory, $status, $alt_title);

    if (!$stmt->execute()) {
        return ['status' => 'error', 'messages' => 'Database error: ' . $stmt->error];
    }

    // ✅ After successful insert
    $forumId = $stmt->insert_id;
    $date = date("Y-m-d H:i:s");

    // Admin details
    $adminEmail = $siteMail;
    $adminName  = $siteName;
    $msgType = "forum_pending";
    $messageStatus = 0;
    $adminMessage = "A new forum post titled <strong>{$title}</strong> by <strong>{$authorName}</strong> has been submitted and is awaiting approval.";
    $link = "pending-blog.php";

    // Insert admin alert
    insertadminAlert($con, $adminMessage, $link, $date, $msgType, $messageStatus);

    // Send admin email
    $emailSubject = "New Forum Post Awaiting Approval - {$siteName}";
    $emailMessage_admin = "
        <p>A new forum post has been submitted and is awaiting approval:</p>
        <ul>
            <li><strong>Title:</strong> {$title}</li>
            <li><strong>Author:</strong> {$authorName}</li>
            <li><strong>Date:</strong> {$date}</li>
        </ul>
        <p><a href='{$siteurl}{$link}' style='background:#007bff;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;'>Review Post</a></p>
    ";

    sendEmail($adminEmail, $siteName, $siteMail, $adminName, $emailMessage_admin, $emailSubject);

    return [
        'status' => 'success',
        'messages' => 'Forum post created successfully and sent for admin approval.'
    ];
}

function getmultipleQuestionID($con, $question_id) {
    global $siteprefix;

    // Sanitize input
    $question_id = intval($question_id);

    $query = "SELECT * FROM {$siteprefix}questions WHERE id = '$question_id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (!$result) {
        return ['error' => mysqli_error($con)];
    }

    $question = mysqli_fetch_assoc($result);

    return $question ? $question : ['error' => 'Question not found'];
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


function likeGroupEndpoint($postData) {
    global $con, $siteprefix;

    $messages = '';
    $group_id = intval($postData['group_id'] ?? 0);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if ($group_id <= 0) {
        $messages .= generateMessage("Invalid group ID.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // 🔒 Check if already liked
    $check = mysqli_query($con, "SELECT id FROM {$siteprefix}group_likes WHERE group_id = '$group_id' AND user_ip = '$user_ip'");
    if (mysqli_num_rows($check) > 0) {
        $messages .= generateMessage("You already liked this group.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ❤️ Add like
    $insert = mysqli_query($con, "INSERT INTO {$siteprefix}group_likes (group_id, user_ip, liked_at) VALUES ('$group_id', '$user_ip', NOW())");
    if (!$insert) {
        $messages .= generateMessage("Database error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // 🔢 Count total likes
    $countRes = mysqli_query($con, "SELECT COUNT(*) as total FROM {$siteprefix}group_likes WHERE group_id = '$group_id'");
    $countRow = mysqli_fetch_assoc($countRes);
    $likeCount = $countRow['total'] ?? 0;

    $messages .= generateMessage("You liked this post.", "green");

    return [
        'status' => 'success',
        'messages' => $messages,
        'likes' => $likeCount
    ];
}



function getVotesEndpoint($get) {
    global $con, $siteprefix;
    $type = $get['type'] ?? '';
    $id = intval($get['id'] ?? 0);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if (!$type || $id <= 0) {
        return ['status'=>'error','message'=>'Invalid parameters'];
    }

    if ($type === 'question') {
        $table = "{$siteprefix}question_votes";
        $idxCol = "question_id";
    } else {
        $table = "{$siteprefix}answer_votes";
        $idxCol = "answer_id";
    }

    $up = mysqli_query($con, "SELECT COUNT(*) AS c FROM $table WHERE $idxCol = '$id' AND vote = 1");
    $down = mysqli_query($con, "SELECT COUNT(*) AS c FROM $table WHERE $idxCol = '$id' AND vote = -1");

    $upc = ($up ? intval(mysqli_fetch_assoc($up)['c']) : 0);
    $downc = ($down ? intval(mysqli_fetch_assoc($down)['c']) : 0);

    // user vote
    $uv = mysqli_query($con, "SELECT vote FROM $table WHERE $idxCol='$id' AND user_ip='$user_ip' LIMIT 1");
    $userVote = 0;
    if ($uv && mysqli_num_rows($uv) > 0) {
        $userVote = intval(mysqli_fetch_assoc($uv)['vote']);
    }

    return [
        'status'=>'success',
        'upvotes'=>$upc,
        'downvotes'=>$downc,
        'score'=>$upc - $downc,
        'user_vote' => $userVote
    ];
}

function voteEndpoint($postData) {
    global $con, $siteprefix;

    $type = $postData['type'] ?? '';
    $id = intval($postData['id'] ?? 0);
    $vote = intval($postData['vote'] ?? 0);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_id = isset($_COOKIE['user_auth']) ? intval($_COOKIE['user_auth']) : null;

    if (!$type || $id <= 0 || !in_array($vote, [1,-1])) {
        return ['status'=>'error','message'=>'Invalid vote'];
    }

    if ($type === 'question') {
        $table = "{$siteprefix}question_votes";
        $idxCol = "question_id";
    } else {
        $table = "{$siteprefix}answer_votes";
        $idxCol = "answer_id";
    }

    $check = mysqli_query($con, "SELECT id, vote FROM $table WHERE $idxCol='$id' AND user_ip='$user_ip' LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);

        if (intval($row['vote']) === $vote) {
            return ['status'=>'error','message'=>'You already voted'];
        }

        mysqli_query($con,
            "UPDATE $table SET vote='$vote', user_id=" . ($user_id ?: "NULL") . ", created_at=NOW()
             WHERE id='{$row['id']}'"
        );
    } else {
        mysqli_query($con,
            "INSERT INTO $table ($idxCol, user_ip, user_id, vote, created_at)
             VALUES ('$id', '$user_ip', " . ($user_id ?: "NULL") . ", '$vote', NOW())"
        );
    }

    return getVotesEndpoint(['type'=>$type,'id'=>$id]);
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

     if ($_GET['action'] == 'orderdata') {
     $response = isset($_GET['order_id']) ? getorderdata($con, $_GET['order_id']) : ['error' => 'Order ID is required'];}

     if ($_GET['action'] == 'bookingorderdata') {
     $response = isset($_GET['order_id']) ? getbookingorderdata($con, $_GET['order_id']) : ['error' => 'Order ID is required'];}

     if ($_GET['action'] == 'gettherapistbookingorder') {
     $response = isset($_GET['booking_id']) ? gettherapistbookingorderdata($con, $_GET['booking_id']) : ['error' => 'Booking ID is required'];}

     if ($_GET['action'] == 'repliesdata') {
     $response = isset($_GET['comment_id']) ? getAllrepliesData($con, $_GET['comment_id']) : ['error' => 'Comment ID is required'];}

      if ($_GET['action'] == 'answersreplydata') {
     $response = isset($_GET['comment_id']) ? getAllanswerrepliesData($con, $_GET['comment_id']) : ['error' => 'Comment ID is required'];}

    if (!empty($_GET['blog_id'])) {
            $response = fetchCommentsBySlug($_GET['blog_id']);
        }

         if ($_GET['action'] == 'answersdata') {
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';
            $response = fetchQuestionsBySlug($_GET['question_id'], $sort);
        }

        // Return votes for a question or answer
        if (isset($_GET['action']) && $_GET['action'] == 'get_votes') {
            $response = getVotesEndpoint($_GET);
        }

         if ($_GET['action'] == 'editquest') {  
        $response = isset($_GET['question_id']) ? getQuestionID($con, $_GET['question_id']) : ['error' => 'Question ID is required'];
    }

         if ($_GET['action'] == 'getmultipleQuestionID') {
        $response = isset($_GET['question_id']) ? getmultipleQuestionID($con, $_GET['question_id']) : ['error' => 'Question ID is required'];
    }



        if ($_GET['action'] == 'commentsdata') {
            $response = fetchCommentsBygroup($_GET['group_id']);
        }

        if (isset($_GET['action']) && $_GET['action'] == 'productcommentsdata') {
                $response = fetchCommentsByListing($_GET['listing_id']);
        }

          if (isset($_GET['action']) && $_GET['action'] == 'eventcommentsdata') {
                $response = fetchCommentsByEvent($_GET['event_id']);
        }

        

        if (isset($_GET['action']) && $_GET['action'] == 'reviewstats') {
   
            $response = fetchReviewStats($_GET['listing_id']);
           }

           if (isset($_GET['action']) && $_GET['action'] == 'fetcheventReviewStats') {
   
            $response = fetcheventReviewStats($_GET['event_id']);
           }

        
        if ($_GET['action'] == 'reviewtherapisdata') {
            $response = fetchCommentsBytherapist($_GET['therapist_id']);
        }

          if ($_GET['action'] == 'getcartitems') {
            $response = getCartItems($_GET['order_id']);
        }

    if ($_GET['action'] == 'categorieslist') {
        $response = getAllCategories($con, $siteprefix);
    }

    if ($_GET['action'] === 'checkWishlist') {
    $user_id = $_GET['user_id'] ?? '';
    $listing_id = $_GET['listing_id'] ?? '';
    $response = checkWishlistStatus($user_id, $listing_id);
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

         if ($_POST['action'] == 'post_productreview') {
            $response =  postproductReviewEndpoint($_POST);
        }

        if ($_POST['action'] == 'post_eventreview') {
            $response =  posteventReviewEndpoint($_POST);
        }

           if ($_POST['action'] == 'updateproduct_review') {
            $response =  updateuserProductReviewEndpoint($_POST);
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


         if ($_POST['action'] == 'creategroupQuestion') {
        $response =  creategroupQuestionEndpoint($_POST);
    }


    

    
    if ($_POST['action'] == 'paymanual') {
        $response = manualPaymentEndpoint($_POST, $_FILES);
    }

 

     if ($_POST['action'] == 'reuploadmanual') {
        $response = reuploadPaymentProof($_POST, $_FILES);
    }
    
    if ($_POST['action'] == 'create_group') {
        $response = createGroupEndpoint($_POST, $_FILES);
    }

    if ($_POST['action'] == 'bulk_update_cart') {
    $response = bulkUpdateCartEndpoint($_POST);
    }


      if ($_POST['action'] == 'book-service') {
        $response = bookServiceEndpoint($_POST);
    }
  if ($_POST['action'] == 'remove_cart_item') {
        $response = removeCartItemEndpoint($_POST);
    }

        if ($_POST['action'] == 'update_group_question') {
            $response =  updateGroupQuestionEndpoint($_POST);
        }

   

    if (isset($_POST['action']) && $_POST['action'] == 'addtocart') {
    $response = addToCartEndpoint($_POST);
    }

  if (isset($_POST['action']) && $_POST['action'] == 'addtoeventcart') {
    $response = addEventToCartEndpoint($_POST);
}
    // Wishlist
   if (isset($_POST['action']) && $_POST['action'] === 'addtowishlist') {
    $response = addToWishlistEndpoint($_POST);
   }

      // ✅ NEW: Like Blog Endpoint
    if ($_POST['action'] == 'like_blog') {
        $response = likeBlogEndpoint($_POST);
    }

        // ✅ NEW: Like Group Endpoint
        if ($_POST['action'] == 'like_group') {
            $response = likeGroupEndpoint($_POST);
        }
    
    // Vote (questions/answers)
    if (isset($_POST['action']) && $_POST['action'] === 'vote') {
        $response = voteEndpoint($_POST);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}

?>