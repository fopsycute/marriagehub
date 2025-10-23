
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
function renderAnswers($parent_id, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink) {
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
                    <textarea name='comment' class='form-control' placeholder='Write a reply...' required></textarea>
                    <div class='input-group-prepend'>
                      <button type='submit' class='btn btn-sm btn-primary'>Reply</button>
                    </div>
                  </div>
                </form>
                ";

                // Recursive call for deeper nested replies
                echo "<div class='nested-replies' id='replies-$r_commentID' style='display:none;'>";
                renderAnswers($r_commentID, $siteurl, $imagePath, $buyerId, $activeLog, $question_id, $sitelink);
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

function sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject) {
    global $siteimg, $adminlink, $siteurl, $brevokey;

    $htmlBody = "
        <div style='width:600px; padding:40px; background-color:#000000; color:#fff;'>
          <p><img src='" . $siteurl . "uploads/" . $siteimg . "' style='width:10%; height:auto;' /></p>

            <p style='font-size:14px; color:#fff;'>
                <span style='font-size:14px; color:#F57C00;'>Dear $firstName,</span><br>
                $emailMessage
            </p>
            <p>Best regards,<br>
            Learnora Team<br>
            $siteMail | <a href='$siteurl' style='font-size:14px; font-weight:600; color:#F57C00;'>🌐 www.learnora.ng</a></p>
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
?>