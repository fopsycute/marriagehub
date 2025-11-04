<?php
include "script/connect.php";

function paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['group_id']) || !isset($_GET['user_id'])) {
        die("Invalid payment request.");
    }

    $reference = mysqli_real_escape_string($con, $_GET['reference']);
    $group_id = intval($_GET['group_id']);
    $group_name = mysqli_real_escape_string($con, $_GET['group_name'] ?? '');
    $duration = mysqli_real_escape_string($con, $_GET['duration'] ?? '');
    $user_id = intval($_GET['user_id']);
    $amount = floatval($_GET['amount'] ?? 0);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    if ($group_id <= 0 || $user_id <= 0 || $amount <= 0) {
        die("Invalid payment details received.");
    }

    switch (strtolower(trim($duration))) {
        case '1 month':  $addDuration = '+1 month'; break;
        case '3 months': $addDuration = '+3 months'; break;
        case '6 months': $addDuration = '+6 months'; break;
        case '12 months': $addDuration = '+12 months'; break;
        default: $addDuration = '+1 month'; break;
    }

    // --- Check if user already exists in group ---
    $check = mysqli_query($con, "SELECT expiry_date FROM {$siteprefix}group_members WHERE group_id='$group_id' AND user_id='$user_id' LIMIT 1");

    if (mysqli_num_rows($check) == 0) {
        $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration));
        mysqli_query($con, "INSERT INTO {$siteprefix}group_members (group_id, user_id, role, status, joined_at, expiry_date)
                            VALUES ('$group_id', '$user_id', 'member', 'active', '$currentdatetime', '$expiry_date')");
    } else {
        $row = mysqli_fetch_assoc($check);
        $current_expiry = $row['expiry_date'];

        if (strtotime($current_expiry) > time()) {
            $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration, strtotime($current_expiry)));
        } else {
            $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration));
        }

        // 👇 Set status to pending again until admin approves
        mysqli_query($con, "UPDATE {$siteprefix}group_members 
                            SET expiry_date='$expiry_date', status='pending' 
                            WHERE group_id='$group_id' AND user_id='$user_id'");
    }

    // --- Record payment ---
    $payment_status_text = "success";
    mysqli_query($con, "INSERT INTO {$siteprefix}group_payments (group_id, order_id, user_id, amount, duration, status, date) 
                        VALUES ('$group_id','$reference','$user_id', '$amount', '$duration', '$payment_status_text', '$currentdatetime')");

    // --- Get seller (group owner) + group name ---
    $sellerResult = mysqli_query($con, "
        SELECT g.user_id AS seller_id, g.group_name, u.email AS seller_email, u.first_name AS seller_name 
        FROM {$siteprefix}groups g 
        JOIN {$siteprefix}users u ON g.user_id = u.id 
        WHERE g.id='$group_id' LIMIT 1
    ");

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $sellerData = mysqli_fetch_assoc($sellerResult);
        $seller_id = $sellerData['seller_id'];
        $group_name = $sellerData['group_name'];
        $seller_email = $sellerData['seller_email'];
        $seller_name = $sellerData['seller_name'];

        // --- Admin commission ---
        $admin_commission = 0; // set your % if needed
        $seller_amount = $amount - $admin_commission;

        if ($admin_commission > 0) {
            mysqli_query($con, "INSERT INTO {$siteprefix}profits (amount, group_id, order_id, type, date)
                                VALUES ('$admin_commission', '$group_id', '$reference', 'Vendor Subscription Payment', '$currentdatetime')");
            $message = "Admin Commission of $sitecurrency$admin_commission from Group Subscription";
            insertadminAlert($con, $message, "profits.php", $date, "profits", 0);
        }

        // --- Credit seller ---
        mysqli_query($con, "UPDATE {$siteprefix}users SET wallet = wallet + $seller_amount WHERE id='$seller_id'");

        // ✅ Use GROUP NAME instead of ID in messages
        insertWallet($con, $seller_id, $seller_amount, "credit", "Payment from Group Subscription ($group_name)", $date);
        insertAlert($con, $seller_id, "You have received $sitecurrency$seller_amount from Group Subscription ($group_name)", $date, 0);

        // ✅ Send email to seller
        $emailSubject = "You received a payment for your group ($group_name)";
        $emailMessage = "
            <p>Hi {$seller_name},</p>
            <p>You have received <strong>{$sitecurrency}{$seller_amount}</strong> from a new subscription to your group <strong>{$group_name}</strong>.</p>
            <p>You can view the transaction details and your wallet balance on your dashboard.</p>
            <p>Thank you for using {$siteName}!</p>
            <p>— The {$siteName} Team</p>
        ";

        sendEmail($seller_email, $siteName, $siteMail, $seller_name, $emailMessage, $emailSubject);
    }

    // --- Redirect to group page (pending approval) ---
    echo "<script>
        alert('Payment successful! Please wait for admin approval before accessing the group.');
        window.location.href='{$siteurl}my-group.php';
    </script>";
    exit;
}


function verifySubscriptionPayment($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['plan_id']) || !isset($_GET['user_id'])) {
        die("Invalid payment request.");
    }

    $reference = mysqli_real_escape_string($con, $_GET['reference']);
    $plan_id = intval($_GET['plan_id']);
    $user_id = intval($_GET['user_id']);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    // --- Fetch plan details ---
    $planQuery = mysqli_query($con, "SELECT name, price, duration_days FROM {$siteprefix}subscriptions WHERE id='$plan_id' LIMIT 1");
    if (!$planQuery || mysqli_num_rows($planQuery) == 0) {
        die("Invalid plan selected.");
    }

    $plan = mysqli_fetch_assoc($planQuery);
    $plan_name = mysqli_real_escape_string($con, $plan['name']);
    $amount = floatval($plan['price']);
    $duration_days = intval($plan['duration_days']);

    // --- Fetch user info ---
    $userQuery = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
    if (!$userQuery || mysqli_num_rows($userQuery) == 0) {
        die("Invalid user record.");
    }
    $user = mysqli_fetch_assoc($userQuery);
    $userEmail = $user['email'];
    $firstName = $user['first_name'];

    // --- Calculate subscription start and end date ---
    $subscription_start = date('Y-m-d');
    $subscription_end = date('Y-m-d', strtotime("+$duration_days days"));

    // --- Update user subscription details ---
    $updateUser = "
        UPDATE {$siteprefix}users 
        SET subscription_status='$plan_name', 
            subscription_plan_id='$plan_id', 
            subscription_start='$subscription_start',
            subscription_end='$subscription_end'
        WHERE id='$user_id'
    ";
    mysqli_query($con, $updateUser);

    // --- Record payment in payments table ---
    $insertPayment = "
        INSERT INTO {$siteprefix}payments 
        (user_id, plan_id, reference, amount, status, date)
        VALUES ('$user_id', '$plan_id', '$reference', '$amount', 'success', '$currentdatetime')
    ";
    mysqli_query($con, $insertPayment);

    // --- Admin Commission ---
    $admin_commission = $amount; // full amount to admin
    $insertCommission = "
        INSERT INTO {$siteprefix}profits (amount, plan_id, order_id, type, date)
        VALUES ('$admin_commission', '$plan_id', '$reference', 'Subscription Payment', '$currentdatetime')
    ";
    mysqli_query($con, $insertCommission);

    // --- Admin alert ---
    $message = "Admin Commission of $sitecurrency$admin_commission from Subscription Plan ($plan_name)";
    $link = "profits.php";
    $msgtype = "profits";
    insertadminAlert($con, $message, $link, $date, $msgtype, 0);

    // --- User alert ---
    insertAlert($con, $user_id, "You have successfully subscribed to $plan_name Plan", $date, 0);

    // --- Send email confirmation to user ---
    $emailSubject = "Subscription Confirmation - $plan_name Plan";
    $emailMessage = "
        <p>Hi {$firstName},</p>
        <p>Thank you for subscribing to the <strong>{$plan_name}</strong> plan on {$siteName}.</p>
        <p>Your subscription is active from <strong>{$subscription_start}</strong> to <strong>{$subscription_end}</strong>.</p>
        <p>We appreciate your trust in us!</p>
    ";
    sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

    // --- Success redirect ---
    echo "<script>
        alert('Payment successful! Your subscription has been activated.');
        window.location.href='{$siteurl}login';
    </script>";
    exit;
}

// ✅ Handle verification call
if (isset($_GET['action']) && $_GET['action'] === 'verify_payment') {
    verifySubscriptionPayment($con, $siteprefix, $siteurl, $sitecurrency, $siteName, $siteMail);
}

// ✅ Handle verification action
if (isset($_GET['action']) && $_GET['action'] === 'verify-group-payment') {
    paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency);
}
?>
