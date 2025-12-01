
<?php

function curl_get_contents($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function generateMessage($message, $color) {
    return "<p style='color: $color;'>$message</p>";
}


 function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    function hashPassword($password) {
    // Use password_hash() function to securely hash passwords
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    return $hashedPassword;
}


function handleMultipleFileUpload($fileKey, $uploadDir) {
    $uploadedFiles = [];
    $defaultImages = ['default1.jpg', 'default2.jpg', 'default3.jpg', 'default4.jpg', 'default5.jpg'];
    $randomImage = $defaultImages[array_rand($defaultImages)];

    if (isset($_FILES[$fileKey])) {
        $fileCount = count($_FILES[$fileKey]['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES[$fileKey]['error'][$i] === UPLOAD_ERR_OK) {
                $fileExtension = pathinfo($_FILES[$fileKey]['name'][$i], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadedFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'][$i], $uploadedFile)) {
                    $uploadedFiles[] = $fileName; // Add the new file name to the array
                } else {
                    $uploadedFiles[] = "Failed to move the uploaded file.";
                }
            } else {
                $uploadedFiles[] = $randomImage;//"No file uploaded or an error occurred.";
            }
        }
    }

    return $uploadedFiles; // Return the array of uploaded file names or error messages
}



// uploadVideos.php or inside your functions.php
function uploadVideos($files, $uploadDir) {
    // ✅ Ensure upload directory exists
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $videoList = [];

    // ✅ Normalize input (handles both single and multiple uploads)
    if (!is_array($files['name'])) {
        $files = [
            'name' => [$files['name']],
            'tmp_name' => [$files['tmp_name']],
            'type' => [$files['type']],
            'error' => [$files['error']],
            'size' => [$files['size']]
        ];
    }

    // ✅ Loop through uploaded files
    foreach ($files['tmp_name'] as $key => $tmp) {
        if (empty($tmp)) continue;

        $fileType = mime_content_type($tmp);
        $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/webm', 'video/quicktime'];
        if (!in_array($fileType, $allowedTypes)) continue;

        $originalName = basename($files['name'][$key]);
        $cleanName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $originalName);
        $fileName = uniqid('vid_') . "_" . $cleanName;

        // ✅ Move file to secure folder
        if (move_uploaded_file($tmp, $uploadDir . $fileName)) {
            $videoList[] = $fileName;
        }
    }

    return $videoList;
}
// ✅ Normalize file array (so single uploads become arrays)
function normalizeFilesArray($file)
{
    $normalized = [];

    if (!is_array($file['name'])) {
        // Single file
        return [$file];
    }

    // Multiple files
    foreach ($file['name'] as $key => $name) {
        $normalized[] = [
            'name' => $name,
            'type' => $file['type'][$key],
            'tmp_name' => $file['tmp_name'][$key],
            'error' => $file['error'][$key],
            'size' => $file['size'][$key]
        ];
    }

    return $normalized;
}

// ✅ Generic image uploader
function uploadImages($fileData, $uploadDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
{
    $uploadedFiles = [];
    $normalizedFiles = normalizeFilesArray($fileData);

    foreach ($normalizedFiles as $file) {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) continue;

        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) continue;

        $originalName = basename($file['name']);
        $cleanName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $originalName);
        $fileName = uniqid('img_') . "_" . $cleanName;

        // Make sure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            $uploadedFiles[] = $fileName;
        }
    }

    return $uploadedFiles;
}


function loadBanners($con, $siteprefix, $siteurl, $slug) {
    $slug = mysqli_real_escape_string($con, $slug);

    $today = date('Y-m-d');

    $query = "
        SELECT aa.banner, aa.redirect_url, aa.advert_id
        FROM {$siteprefix}active_adverts AS aa
        INNER JOIN {$siteprefix}ad_placements AS ap
            ON aa.advert_id = ap.id
        WHERE ap.slug = '$slug'
        AND aa.status='active'
        AND aa.start_date <= '$today'
        AND aa.end_date >= '$today'
        ORDER BY aa.created_at DESC
    ";

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $banners = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $bannerUrl = $siteurl . "uploads/" . $row['banner'];
            $redirect  = !empty($row['redirect_url']) ? $row['redirect_url'] : '#';

            $banners[] = [
                'banner'   => $bannerUrl,
                'redirect' => $redirect
            ];
        }
        return $banners; // return array instead of echoing
    }

   
}



// ✅ Limit content to a specific number of words
function limitWords($string, $word_limit = 5)
{
    $words = explode(' ', strip_tags($string));
    if (count($words) > $word_limit) {
        return implode(' ', array_slice($words, 0, $word_limit)) . '...';
    }
    return $string;
}

function insertAlert($con, $user_id, $message, $date, $status) {
     $escapedMessage = mysqli_real_escape_string($con, $message);
 
     $query = "INSERT INTO ma_notifications (user, message, date, status) VALUES ('$user_id', '$escapedMessage', '$date', '$status')";
     $submit = mysqli_query($con, $query);
     if ($submit) { echo "";} 
     else { die('Could not connect: ' . mysqli_error($con)); }}

     function insertWallet($con, $user_id, $amount, $type, $note, $date) {

    $query = "INSERT INTO ma_wallet_history (user, amount, reason, status, date) VALUES ('$user_id', '$amount', '$note', '$type' , '$date')";
    $submit = mysqli_query($con, $query);
    if ($submit) { echo "";} 
    else { die('Could not connect: ' . mysqli_error($con)); }}

    function insertadminAlert($con, $message, $link, $date, $msgtype, $status) {
    $escapedMessage = mysqli_real_escape_string($con, $message);

     $query = "INSERT INTO ma_alerts(message,link, date,type, status) VALUES ('$escapedMessage','$link',  '$date', '$msgtype', '$status')";
     $submit = mysqli_query($con, $query);
     if ($submit) { echo "";} 
     else { die('Could not connect: ' . mysqli_error($con)); }}

//render answers
function renderAnswers($parent_id, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink,$canAccept) {
    $replies_url = $sitelink . "user.php?action=answersreplydata&comment_id=" . $parent_id;
    $replies_data = curl_get_contents($replies_url);

    if ($replies_data !== false) {
        $replies = json_decode($replies_data);

        if (!empty($replies)) {
            echo "<div class='nested-replies-container mt-2'>";
            foreach ($replies as $reply) {
                $anonymous = intval($reply->anonymous ?? 0);
                if ($anonymous === 1) {
                    $r_username= "Anonymous";
                } else {
                    $firstName = htmlspecialchars($reply->first_name ?? '');
                    $lastName = htmlspecialchars($reply->last_name ?? '');
                    $r_username = trim("$firstName $lastName") ?: "Unknown User";
                }
                $r_avatar = $siteurl . $imagePath . ($reply->photo ?? 'default.png');
                $r_commentText = htmlspecialchars($reply->answer);
                $r_commentID = $reply->id;
                $r_user_id = $reply->user_id;
                $r_isAccepted = intval($reply->is_accepted ?? 0);
                $r_isBest = intval($reply->is_best ?? 0);
                $r_created_date = date('F d, Y \a\t h:i A', strtotime($reply->created_at));

                echo "<div id='question-$r_commentID' class='comment ms-3 mt-3'>";
                echo "  <div class='d-flex'>";
                echo "    <div class='comment-img'><img src='$r_avatar' alt='Avatar'></div>";
                echo "    <div>";
                echo "      <h5><span>$r_username</span>";

                // Action buttons (Reply / Delete / View Replies)
                echo " <span class='action-buttons ms-2'>";
                if ($activeLog == 1) {
                    echo " <a href='#' class='replyanswer btn btn-sm' data-question-id='$r_commentID'><i class='bi bi-reply-fill'></i> Reply</a>";
                }
                if ($r_user_id == $buyerId) {
                    echo " <a href='#' id='$r_commentID' class='btn btn-sm deleteanswer'><i class='bi bi-trash'></i></a>";
                }
                if ($canAccept && !$r_isAccepted) {
                    echo " <a href='#' id='$r_commentID' class='btn btn-sm acceptanswer'><i class='bi bi-check-circle'></i> Accept</a>";
                }
                if ($canAccept && !$r_isBest) {
                   echo " <a href='#' id='$r_commentID' class='btn btn-sm acceptbest'><i class='bi bi-check-circle'></i> Accept as Best</a>";
                }
                if($r_isAccepted){
                  echo " <span class='badge bg-success ms-2'>Accepted Answer</span>";
                }
                if($r_isBest){
                  echo " <span class='badge bg-primary ms-2'>Best Answer</span>";
                }

                

                // Nested replies toggle button inline
                $nested_replies_url = $sitelink . "user.php?action=answersreplydata&comment_id=" . $r_commentID;
                $nested_data = curl_get_contents($nested_replies_url);
                $nested_replies = json_decode($nested_data);
                if (!empty($nested_replies)) {
                    echo " <a href='#' class='view-answer-link btn btn-sm' data-comment-id='$r_commentID'>View Replies (" . count($nested_replies) . ")</a>";
                }
                echo "</span>";

                echo "      </h5>";
                echo "      <time datetime='{$reply->created_at}'>$r_created_date</time>";
                echo "      <p>$r_commentText</p>";
                echo "    </div>";
                echo "  </div>";
                echo "</div>";

                // Reply form
                echo "
                <form class='answer-form mt-3 ms-3' id='answer-form-$r_commentID' style='display:none;'>
                  <div class='text-center mt-1 message-box'></div>
                  <input type='hidden' name='user_id' value='$buyerId'>
                  <input type='hidden' name='question_id' value='$question_id'>
                  <input type='hidden' name='action' value='post_answers'>
                  <input type='hidden' name='parent_id' value='$r_commentID'>
                  <div class='input-group'>
                    <textarea name='comment' class='editor' placeholder='Write a reply...' required></textarea>
                    <div class='input-group-prepend'>
                      <button type='submit' class='btn btn-sm btn-primary'>Reply</button>
                    </div>
                  </div>
                </form>
                ";

                // Recursive call for deeper nested replies
                echo "<div class='nested-replies' id='replies-$r_commentID' style='display:none;'>";
                renderAnswers($r_commentID, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink, $canAccept);
                echo "</div>";
            }
            echo "</div>";
        }
    }
}

//render replies 
  // 🔁 Recursive reply renderer
function renderReplies($parent_id, $siteurl, $imagePath, $buyerId, $activeLog, $blog_id, $sitelink) {
    $replies_url = $sitelink . "user.php?action=repliesdata&comment_id=" . $parent_id;
    $replies_data = curl_get_contents($replies_url);

    if ($replies_data !== false) {
        $replies = json_decode($replies_data);

        if (!empty($replies)) {
            echo "<div class='nested-replies-container mt-2'>";
            foreach ($replies as $reply) {
                $r_username = trim(($reply->first_name ?? '') . ' ' . ($reply->last_name ?? ''));
                $r_avatar = $siteurl . $imagePath . ($reply->photo ?? 'default.png');
                $r_commentText = htmlspecialchars($reply->comment);
                $r_commentID = $reply->id;
                $r_user_id = $reply->user_id;
                
                $r_created_date = date('F d, Y \a\t h:i A', strtotime($reply->created_at));

                echo "<div id='comment-$r_commentID' class='comment ms-3 mt-3'>";
                echo "  <div class='d-flex'>";
                echo "    <div class='comment-img'><img src='$r_avatar' alt='Avatar'></div>";
                echo "    <div>";
                echo "      <h5><span>$r_username</span>";

                // Action buttons (Reply / Delete / View Replies)
                echo " <span class='action-buttons ms-2'>";
                if ($activeLog == 1) {
                    echo " <a href='#' class='reply btn btn-sm' data-comment-id='$r_commentID'><i class='bi bi-reply-fill'></i> Reply</a>";
                }
                if ($r_user_id == $buyerId) {
                    echo " <a href='#' id='$r_commentID' class='btn btn-sm deletecomment'><i class='bi bi-trash'></i></a>";
                }

                // Nested replies toggle button inline
                $nested_replies_url = $sitelink . "user.php?action=repliesdata&comment_id=" . $r_commentID;
                $nested_data = curl_get_contents($nested_replies_url);
                $nested_replies = json_decode($nested_data);
                if (!empty($nested_replies)) {
                    echo " <a href='#' class='view-replies-link btn btn-sm' data-comment-id='$r_commentID'>View Replies (" . count($nested_replies) . ")</a>";
                }
                echo "</span>";

                echo "      </h5>";
                echo "      <time datetime='{$reply->created_at}'>$r_created_date</time>";
                echo "      <p>$r_commentText</p>";
                echo "    </div>";
                echo "  </div>";
                echo "</div>";

                // Reply form
                echo "
                <form class='reply-form mt-3 ms-3' id='reply-form-$r_commentID' style='display:none;'>
                  <div class='text-center mt-1 message-box'></div>
                  <input type='hidden' name='user_id' value='$buyerId'>
                  <input type='hidden' name='blog_id' value='$blog_id'>
                  <input type='hidden' name='action' value='post_comment'>
                  <input type='hidden' name='parent_id' value='$r_commentID'>
                  <div class='input-group'>
                    <textarea name='comment' class='form-control' placeholder='Write a reply...' required></textarea>
                    <div class='input-group-prepend'>
                      <button type='submit' class='btn btn-sm btn-primary'>Reply</button>
                    </div>
                  </div>
                </form>
                ";

                // Recursive call for deeper nested replies
                echo "<div class='nested-replies' id='replies-$r_commentID' style='display:none;'>";
                renderReplies($r_commentID, $siteurl, $imagePath, $buyerId, $activeLog, $blog_id, $sitelink);
                echo "</div>";
            }
            echo "</div>";
        }
    }
}


// Helper: Convert timestamp to "time ago" format
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) {
        return "Just now";
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($difference < 172800) {
        return "Yesterday";
    } else {
        $days = floor($difference / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}

function updateDisputeStatus($con, $siteprefix, $dispute_id, $status) {
    $status = mysqli_real_escape_string($con, $status);
    $dispute_id = mysqli_real_escape_string($con, $dispute_id);
    
    $sql = "UPDATE " . $siteprefix . "disputes 
            SET status = '$status', 
                created_at = NOW() 
            WHERE ticket_number = '$dispute_id'";
            
    $result = mysqli_query($con, $sql);
    
    if ($result) {
        return true;
    }
    return false;
}


// Check if a user follows another
function isFollowing($followerId, $followingId) {
    global $con, $siteprefix;
    $followerId = intval($followerId);
    $followingId = intval($followingId);
    
    $query = "SELECT id FROM {$siteprefix}user_follows WHERE follower_id = ? AND following_id = ? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $followerId, $followingId);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

// Get number of followers for a user
function getFollowerCount($userId) {
    global $con, $siteprefix;
    $userId = intval($userId);
    $query = "SELECT COUNT(*) FROM {$siteprefix}user_follows WHERE following_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

// Get number of users someone is following
function getFollowingCount($userId) {
    global $con, $siteprefix;
    $userId = intval($userId);
    $query = "SELECT COUNT(*) FROM {$siteprefix}user_follows WHERE follower_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function is_bookmarked($user_id, $item_id, $item_type) {
    global $con, $siteprefix;

    $user_id = intval($user_id);
    $item_id = intval($item_id);
    $item_type = mysqli_real_escape_string($con, $item_type);

    $query = "SELECT id 
              FROM {$siteprefix}bookmarks 
              WHERE user_id = $user_id 
                AND item_id = $item_id 
                AND item_type = '$item_type'
              LIMIT 1";

    $result = mysqli_query($con, $query);
    return ($result && mysqli_num_rows($result) > 0);
}


function isInCart($con, $order_id, $event_id, $pricing, $siteprefix) {
    if (empty($order_id)) return false;

    /* ----------------------------------
       FREE ITEMS
    -----------------------------------*/
    if ($pricing === 'free') {
        $sql = "SELECT COUNT(*) as count 
                FROM {$siteprefix}order_items 
                WHERE event_id = ? AND order_id = ? AND item_id = 'free'";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $event_id, $order_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    /* ----------------------------------
       PAID ITEMS — CHECK ALL TICKETS
    -----------------------------------*/
    // Get all ticket IDs for the event
    $sql = "SELECT id FROM {$siteprefix}event_tickets WHERE event_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $ticket_ids = [];
    while ($row = $result->fetch_assoc()) {
        $ticket_ids[] = $row['id'];
    }
    $stmt->close();

    if (empty($ticket_ids)) return false;

    // Build IN placeholders ?,?,?,?
    $placeholders = implode(',', array_fill(0, count($ticket_ids), '?'));

    // Build parameter types
    $types = "ss" . str_repeat("s", count($ticket_ids)); // event_id + order_id + tickets[]

    // Build SQL
    $sql = "SELECT COUNT(DISTINCT item_id) as count
            FROM {$siteprefix}order_items
            WHERE event_id = ? AND order_id = ? AND item_id IN ($placeholders)";

    $stmt = $con->prepare($sql);

    // Prepare dynamic parameters
    $params = array_merge([$event_id, $order_id], $ticket_ids);

    // Convert params to references for bind_param
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_names[] = &$params[$i];
    }

    // Bind dynamically
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count == count($ticket_ids);
}

/**
 * Helper to handle single file upload safely
 */
function uploadFile($fileData, $targetDir) {
    if (empty($fileData['name'])) return '';
    $fileName = uniqid() . '_' . basename($fileData['name']);
    $targetPath = $targetDir . $fileName;
    move_uploaded_file($fileData['tmp_name'], $targetPath);
    return $fileName;
}





function hasUserPurchased($con, $buyerId, $event_id, $siteprefix) {

    $query = "SELECT 1 
              FROM {$siteprefix}orders o
              JOIN {$siteprefix}order_items oi ON o.order_id = oi.order_id
              WHERE o.user = ? AND oi.event_id = ? AND o.status = 'paid'
              LIMIT 1";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        return false;
    }

    // FIX IS HERE ↓
    $stmt->bind_param("ss", $buyerId, $event_id);

    $stmt->execute();
    $stmt->store_result();

    $purchased = $stmt->num_rows > 0;

    $stmt->close();

    return $purchased;
}


function notifyDisputeRecipient($con, $siteprefix, $dispute_id) {
    // Get recipient ID
    $query = "SELECT recipient_id FROM ".$siteprefix."disputes WHERE ticket_number = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $dispute_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $recipient_id = $row['recipient_id'];
    
    if (!$recipient_id) {
        return false;
    }

    $message = "There has been a new update on dispute ($dispute_id). Please check the ticket for more details.";
    $rDetails = getUserDetails($con, $siteprefix, $recipient_id);
    $r_email = $rDetails['email'];
    $r_name = $rDetails['first_name'];
    $r_emailSubject = "Dispute Update ($dispute_id)";
    $r_emailMessage = "<p>There has been a new update on dispute ($dispute_id). Login to your dashboard to check</p>";
    
    //sendEmail($r_email, $r_name, $siteName, $siteMail, $r_emailMessage, $r_emailSubject);
    
    $date = date('Y-m-d H:i:s');
    $status = 0;
    $link = "ticket.php?ticket_number=$dispute_id";
    $msgtype = "Dispute Update";
    
    return insertAlert($con, $recipient_id, $message, $date, $status);
}


function getBadgeColor($status) {
    switch (strtolower($status)) { // make it case-insensitive
        case 'cancelled':
        case 'inactive':
        case 'rejected':
        case 'under-review':
            return 'danger';

        case 'draft':
        case 'awaiting-response':
            return 'info';

        case 'pending':
        case 'suspended':
        case 'payment resend':
            return 'warning';

        case 'inprogress':
        case 'approved':
        case 'resolved':
            return 'success';

        default:
            return 'success';
    }
}


//function to get username and email
function getUserDetails($con, $siteprefix, $user_id) {
    $query = "SELECT * FROM " . $siteprefix . "users WHERE id = '$user_id'";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

function getCartCount($con, $siteprefix, $order_id) {
    $sql = "SELECT COUNT(*) as count FROM ".$siteprefix."order_items oi 
    LEFT JOIN ".$siteprefix."orders o ON oi.order_id = o.order_id 
    WHERE o.order_id='$order_id'";
    $sql2 = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($sql2);
    return $row['count'];
}

function restoreEventSeat($con, $siteprefix, $event_id, $ticket_id)
{
    $seat_sql = "SELECT seatremain FROM {$siteprefix}event_tickets WHERE id='$ticket_id' LIMIT 1";
    $seat_res = mysqli_query($con, $seat_sql);
    if (!$seat_res || mysqli_num_rows($seat_res) == 0) return;

    $seat_data = mysqli_fetch_assoc($seat_res);
    $current_seats = (int)$seat_data['seatremain'];
    $new_seats = $current_seats + 1;

    $update_seat_sql = "UPDATE {$siteprefix}event_tickets SET seatremain='$new_seats' WHERE id='$ticket_id'";
    mysqli_query($con, $update_seat_sql);
}


function reduceEventSeat($con, $siteprefix, $event_id, $ticket_id)
{
    // Get pricing type
    $pricing_sql = "SELECT pricing_type FROM {$siteprefix}events WHERE event_id='$event_id' LIMIT 1";
    $pricing_res = mysqli_query($con, $pricing_sql);
    $pricing_row = mysqli_fetch_assoc($pricing_res);

    if (!$pricing_row) return;  // No event found → stop

    $pricing_type = strtolower($pricing_row['pricing_type']);

    // Only reduce seats for paid events
    if ($pricing_type !== 'paid') {
        return;
    }

    // Get current remaining seats for the ticket
    $seat_sql = "
        SELECT seatremain 
        FROM {$siteprefix}event_tickets 
        WHERE id='$ticket_id' 
        LIMIT 1
    ";
    $seat_res = mysqli_query($con, $seat_sql);

    if (!$seat_res || mysqli_num_rows($seat_res) == 0) {
        return; // No ticket found
    }

    $seat_data = mysqli_fetch_assoc($seat_res);
    $current_seats = (int)$seat_data['seatremain'];

    // Calculate new seat count
    $new_seats = max(0, $current_seats - 1);

    // Update seatremain for that ticket
    $update_seat_sql = "
        UPDATE {$siteprefix}event_tickets 
        SET seatremain='$new_seats' 
        WHERE id='$ticket_id'
    ";
    mysqli_query($con, $update_seat_sql);
}

function insertWithdraw($con, $user_id, $amount,$bank, $bankname, $bankno, $date, $status) {
    global $siteprefix;
$query = "INSERT INTO {$siteprefix}withdrawal (user,amount,bank,bank_name,bank_number, date, status) VALUES ('$user_id', '$amount', '$bank','$bankname','$bankno','$date', '$status')";
$insert = mysqli_query($con, "UPDATE {$siteprefix}users SET wallet = CAST(wallet AS DECIMAL(10,2)) - CAST('$amount' AS DECIMAL(10,2)) WHERE id ='$user_id'") or die('Could not connect: ' . mysqli_error($con));
    $submit = mysqli_query($con, $query);
    if ($submit) { echo "";} 
    else { die('Could not connect: ' . mysqli_error($con)); }}

function sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject) {
    global $siteimg, $adminlink, $siteurl, $brevokey;

    $htmlBody = "
        <div style='width:600px; padding:40px; background-color:#000000; color:#fff;'>
          <p><img src='" . $siteurl . "assets/img" . $siteimg . "' style='width:10%; height:auto;' /></p>

            <p style='font-size:14px; color:#fff;'>
                <span style='font-size:14px; color:#F57C00;'>Dear $firstName,</span><br>
                $emailMessage
            </p>
            <p>Best regards,<br>
            Marriage Hub Team<br>
            $siteMail | <a href='$siteurl' style='font-size:14px; font-weight:600; color:#F57C00;'>🌐 www.marriagehub.ng</a></p>
        </div>
    ";

    $apiKey = $brevokey;  // Replace with your actual API key

    $data = [
        'sender' => [
            'name' => $siteName,
            'email' => $siteMail
        ],
        'to' => [
            [
                'email' => $email,
                'name' => $firstName
            ]
        ],
        'subject' => "$emailSubject - $siteName",
        'htmlContent' => $htmlBody
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    if ($httpCode === 201) {
        return true;
    } else {
        echo 'Brevo API Error: ' . $response;
        return false;
    }
}



function sendEmailWithAttachments($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject, $attachments = []) {
    global $siteimg, $adminlink, $siteurl, $brevokey;

    $htmlBody = "
        <div style='width:600px; padding:40px; background-color:#000000; color:#fff;'>
            <p><img src='" . $siteurl . "assets/img" . $siteimg . "' style='width:10%; height:auto;' /></p>
            <p style='font-size:14px; color:#F57C00;'>Dear $firstName,</p>
            <p style='font-size:14px; color:#fff;'>$emailMessage</p>
            <p>Best regards,<br>
            Marriage Hub Team<br>
            $siteMail | <a href='$siteurl' style='font-size:14px; font-weight:600; color:#F57C00;'>🌐 www.marriagehub.ng</a></p>
        </div>
    ";

    // 🔥 Prepare attachment array
    $attachmentArray = [];

    if (!empty($attachments) && is_array($attachments)) {
        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                $attachmentArray[] = [
                    'name' => basename($filePath),
                    'content' => base64_encode(file_get_contents($filePath))
                ];
            }
        }
    }

    $data = [
        'sender' => [
            'name' => $siteName,
            'email' => $siteMail
        ],
        'to' => [
            [
                'email' => $email,
                'name' => $firstName
            ]
        ],
        'subject' => "$emailSubject - $siteName",
        'htmlContent' => $htmlBody
    ];

    // Attach if available
    if (!empty($attachmentArray)) {
        $data['attachment'] = $attachmentArray;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $brevokey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    if ($httpCode === 201) {
        return true;
    } else {
        echo "Brevo API Error: " . $response;
        return false;
    }
}


function getEventDeliveryDetails($con, $siteprefix, $event_id, $format = '', $documentPath = '') {
    $details = '';
    $attachments = [];

    // Fetch event data
    $event_sql = "SELECT * FROM {$siteprefix}events WHERE event_id='$event_id' LIMIT 1";
    $event_res = mysqli_query($con, $event_sql);
    $event = mysqli_fetch_assoc($event_res);
    if (!$event) return ['details' => '', 'attachments' => []];

    $format = $format ?: strtolower($event['delivery_format']);

    // Determine fields based on format
    if ($format === 'physical') {
        $fields = [
            'address' => 'Address',
            'state' => 'State',
            'lga' => 'LGA',
            'country' => 'Country'
        ];
    } elseif ($format === 'hybrid') {
        $fields = [
            'hybrid_physical_address' => 'Physical Address',
            'hybrid_web_address' => 'Web Address',
            'hybrid_state' => 'State',
            'hybrid_lga' => 'LGA',
            'hybrid_country' => 'Country',
            'hybrid_foreign_address' => 'Foreign Address'
        ];
    } elseif ($format === 'online') {
        $fields = ['online_link' => 'Link to join'];
    } else {
        $fields = [];
    }

    // Render fields
    foreach ($fields as $col => $label) {
        if (!empty($event[$col])) {
            $value = htmlspecialchars($event[$col]);
            if ($col === 'online_link' || $col === 'hybrid_web_address') {
                $value = "<a href='{$value}'>{$value}</a>";
            }
            $details .= "<li><strong>{$label}:</strong> {$value}</li>";
        }
    }

    // Attachments for Text or Video format
    if (in_array($format, ['text','video'])) {
        $details .= "<li><strong>Materials:</strong> (attached to this email)</li>";

        // Video modules
        if ($format === 'video') {
            $video_sql = "SELECT file_path FROM {$siteprefix}event_video_modules WHERE event_id='{$event_id}'";
            $video_res = mysqli_query($con, $video_sql);
            while ($vm = mysqli_fetch_assoc($video_res)) {
                $filePath = $documentPath . $vm['file_path'];
                if (file_exists($filePath)) $attachments[] = $filePath;
            }
        }

        // Text modules
        if ($format === 'text') {
            $text_sql = "SELECT file_path FROM {$siteprefix}event_text_modules WHERE event_id='{$event_id}'";
            $text_res = mysqli_query($con, $text_sql);
            while ($tm = mysqli_fetch_assoc($text_res)) {
                $filePath = $documentPath . $tm['file_path'];
                if (file_exists($filePath)) $attachments[] = $filePath;
            }
        }
    }

    return ['details' => $details, 'attachments' => $attachments];
}



function formatDateTime($dateTime) {
    if (empty($dateTime)) { return '';  }
    $timestamp = strtotime($dateTime);
    // Check if the input contains both date and time
    $hasTime = strpos($dateTime, 'T') !== false;
    if ($hasTime) { return date('M j, Y \a\t h:i A', $timestamp); } else {
     return date('M j, Y', $timestamp);
}}

 function getWishlistCountByUser($con, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM ma_wishlist WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row = mysqli_fetch_array($result);
        return $row['count'];
    }
    return 0;
}


// ✅ Check vendor plan validity and feature
function canAccess($con, $vendorId, $feature, $siteprefix) {
    $query = $con->prepare("
        SELECT s.*, u.subscription_end 
        FROM {$siteprefix}users u
        JOIN {$siteprefix}subscriptions s ON u.subscription_plan_id = s.id
        WHERE u.id = ?
    ");
    $query->bind_param("i", $vendorId);
    $query->execute();
    $result = $query->get_result();
    $plan = $result->fetch_assoc();
    $query->close();

    if (!$plan) return false;

    // Expired plan check
    $endDate = $plan['subscription_end'] ?? null;
    if ($endDate && strtotime($endDate) < time()) {
        return false;
    }

    // Feature available
    return !empty($plan[$feature]);
}

// ✅ Get feature numeric limit
function getFeatureLimit($con, $vendorId, $feature, $siteprefix) {
    $query = $con->prepare("
        SELECT s.$feature 
        FROM {$siteprefix}users u
        JOIN {$siteprefix}subscriptions s ON u.subscription_plan_id = s.id
        WHERE u.id = ?
    ");
    $query->bind_param("i", $vendorId);
    $query->execute();
    $result = $query->get_result();
    $limit = $result->fetch_column();
    $query->close();

    return strtolower($limit) === 'unlimited' ? 'unlimited' : (int)$limit;
}

function formatNumber($number, $no = 2) {
    if (!is_numeric($number) || !is_numeric($no)) {
        return "0.00";
    }
    try {
        return number_format((float)$number, (int)$no);
    } catch (Exception $e) {
        return "0.00";
    }
}


?>